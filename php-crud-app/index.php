<?php
require_once 'config.php';

$check_table = $conn->query("SHOW TABLES LIKE '$table_name'");
if ($check_table->num_rows == 0) {
    $create_table = "CREATE TABLE $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        address TEXT NOT NULL,
        phone VARCHAR(20) NOT NULL,
        major VARCHAR(100) NOT NULL
    )";
    
    if ($conn->query($create_table) === TRUE) {
        echo "<script>console.log('Table created successfully');</script>";
    } else {
        echo "<script>console.error('Error creating table: " . $conn->error . "');</script>";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $conn->begin_transaction();
    
    try {

        $delete_query = "DELETE FROM $table_name WHERE id = $id";
        $conn->query($delete_query);
        

        $select_query = "SELECT * FROM $table_name ORDER BY id";
        $result = $conn->query($select_query);
        

        $records = [];
        while ($row = $result->fetch_assoc()) {
            $records[] = [
                'name' => $conn->real_escape_string($row['name']),
                'email' => $conn->real_escape_string($row['email']),
                'address' => $conn->real_escape_string($row['address']),
                'phone' => $conn->real_escape_string($row['phone']),
                'major' => $conn->real_escape_string($row['major'])
            ];
        }
        $truncate_query = "TRUNCATE TABLE $table_name";
        $conn->query($truncate_query);

        foreach ($records as $record) {
            $insert_query = "INSERT INTO $table_name (name, email, address, phone, major) 
                            VALUES ('{$record['name']}', '{$record['email']}', '{$record['address']}', 
                                   '{$record['phone']}', '{$record['major']}')";
            $conn->query($insert_query);
        }
        
        $conn->commit();
        
        header("Location: index.php?msg=deleted");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

$count_query = "SELECT COUNT(*) as total FROM $table_name";
$count_result = $conn->query($count_query);
$total_students = $count_result->fetch_assoc()['total'];

$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

$total_pages = ceil($total_students / $records_per_page);

$query = "SELECT * FROM $table_name LIMIT $offset, $records_per_page";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <header class="app-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="fas fa-graduation-cap"></i> Student Management System</h1>
                    <p>Manage your students with ease</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-inline-block bg-white bg-opacity-25 px-3 py-2 rounded">
                        <span class="text-white">Today: <?php echo date('F j, Y'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container fade-in">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-info">
                        <h3><?php echo $total_students; ?></h3>
                        <p>Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-icon green">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stats-info">
                        <h3>Active</h3>
                        <p>Student Status</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-icon red">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stats-info">
                        <h3><?php echo date('Y'); ?></h3>
                        <p>Academic Year</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-list"></i> Student List</h4>
                <a href="add.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Student
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Record added successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Record updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Record deleted successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table id="studentTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Major</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td><i class='fas fa-user text-primary me-2'></i>" . $row['name'] . "</td>";
                                    echo "<td><i class='fas fa-envelope text-muted me-2'></i>" . $row['email'] . "</td>";
                                    echo "<td><i class='fas fa-map-marker-alt text-danger me-2'></i>" . $row['address'] . "</td>";
                                    echo "<td><i class='fas fa-phone text-success me-2'></i>" . $row['phone'] . "</td>";
                                    echo "<td><i class='fas fa-graduation-cap text-info me-2'></i>" . $row['major'] . "</td>";
                                    echo "<td class='action-buttons'>
                                        <a href='edit.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>
                                            <i class='fas fa-edit'></i> Edit
                                        </a>
                                        <button class='btn btn-danger btn-sm delete-btn' data-id='" . $row['id'] . "'>
                                            <i class='fas fa-trash'></i> Delete
                                        </button>
                                    </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Enhanced Pagination -->
                <div class="pagination-container">
                    <div class="pagination-info">
                        Showing <?php echo min(($page - 1) * $records_per_page + 1, $total_students); ?> to 
                        <?php echo min($page * $records_per_page, $total_students); ?> of 
                        <?php echo $total_students; ?> entries
                    </div>
                    
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-md justify-content-end">
                            <!-- First Page Button -->
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=1" aria-label="First">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>
                            
                            <!-- Previous Button -->
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <i class="fas fa-angle-left"></i>
                                </a>
                            </li>
                            
                            <!-- Page Numbers -->
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">
                                    <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
                                </li>';
                            }
                            
                            if ($end_page < $total_pages) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            ?>
                            
                            <!-- Next Button -->
                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <i class="fas fa-angle-right"></i>
                                </a>
                            </li>
                            
                            <!-- Last Page Button -->
                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $total_pages; ?>" aria-label="Last">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <footer class="app-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Student Management System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.delete-btn').click(function() {
                const id = $(this).data('id');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f72585',
                    cancelButtonColor: '#4361ee',
                    confirmButtonText: '<i class="fas fa-trash"></i> Yes, delete it!',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php?delete=' + id;
                    }
                });
            });

            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
</body>
</html>
