<?php
require_once 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $major = $_POST['major'];

    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($major)) {
        $errors[] = "Major is required";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE $table_name SET name = ?, email = ?, address = ?, phone = ?, major = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $address, $phone, $major, $id);
        
        if ($stmt->execute()) {
            header("Location: index.php?msg=updated");
            exit();
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT * FROM $table_name WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <p>Edit student information</p>
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
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-user-edit"></i> Edit Student</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user text-primary me-2"></i>Name
                                </label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                <div class="invalid-feedback">
                                    Please enter a name.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope text-primary me-2"></i>Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>Address
                                </label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($row['address']); ?></textarea>
                                <div class="invalid-feedback">
                                    Please enter an address.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone text-primary me-2"></i>Phone Number
                                </label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" required>
                                <div class="invalid-feedback">
                                    Please enter a phone number.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="major" class="form-label">
                                    <i class="fas fa-graduation-cap text-primary me-2"></i>Major
                                </label>
                                <select class="form-select" id="major" name="major" required>
                                    <option value="" disabled>Select a major</option>
                                    <option value="Computer Science" <?php echo ($row['major'] == 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                                    <option value="Information Technology" <?php echo ($row['major'] == 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                                    <option value="Data Science" <?php echo ($row['major'] == 'Data Science') ? 'selected' : ''; ?>>Data Science</option>
                                    <option value="Software Engineering" <?php echo ($row['major'] == 'Software Engineering') ? 'selected' : ''; ?>>Software Engineering</option>
                                    <option value="Cybersecurity" <?php echo ($row['major'] == 'Cybersecurity') ? 'selected' : ''; ?>>Cybersecurity</option>
                                    <option value="Artificial Intelligence" <?php echo ($row['major'] == 'Artificial Intelligence') ? 'selected' : ''; ?>>Artificial Intelligence</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a major.
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to List
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Student
                                </button>
                            </div>
                        </form>
                    </div>
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
    <script>
        (function () {
            'use strict'

            var forms = document.querySelectorAll('.needs-validation')

            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>
