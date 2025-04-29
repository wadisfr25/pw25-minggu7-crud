CREATE DATABASE IF NOT EXISTS crud_094;

USE crud_094;

CREATE TABLE IF NOT EXISTS crud_094 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    major VARCHAR(100) NOT NULL
);

INSERT INTO crud_094 (name, email, address, phone, major) VALUES
('Wadis Friendly', 'wadisfr25@gmail.com', 'Griya Mandara', '+6281235762132', 'Computer Science'),
('Safiran Safitri', 'safiran31@gmail.com', 'Dasan Agung', '+6285238259305', 'Accounting'),
('Ananta Pejabat', 'ananta69@gmail.com', 'Cakranegara', '+6288148225700', 'Data Science'),
('Qiqi CJ', 'qiqi69@gmail.com', 'Pajang Timur', '+6287862350996', 'Software Engineering'),
('Majdi Travel', 'majditravel@gmail.com', 'Pantai Viral', '+6287857633799', 'Cybersecurity');
