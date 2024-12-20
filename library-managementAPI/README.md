# Library Management API

## Description
This is a Library Management RESTful API. It provides endpoints for managing a library database with operations like creating, reading, updating, and deleting books.

---

## Requirements
1. XAMPP or a local server with MySQL and PHP support.
2. MySQL database named `library_db`.
3. Set up a database using the schema provided below.

---

## Installation
1. Clone or download the repository.
2. Import the database schema to MySQL:
   ```sql
   CREATE TABLE `books` (
       `id` INT AUTO_INCREMENT PRIMARY KEY,
       `title` VARCHAR(255) NOT NULL,
       `author` VARCHAR(255) NOT NULL,
       `genre` VARCHAR(255) NOT NULL,
       `release_date` DATE NOT NULL,
       `available` BOOLEAN DEFAULT true,
       `quantity` INT NOT NULL
   );
