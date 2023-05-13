<?php

// include 'config.php';
require_once('config.php');
require_once('DB_class.php');

session_start();

// Redirect function
function redirect($url)
{
    header("Location: $url");
    exit();
}

// Check if the user is logged in
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}


function check_session() {
    // Start or resume the current session

    // Check if the 'user_id' session variable is set
    if (!isset($_SESSION['user_id'])) {
        // User is not logged in, redirect to the login page or perform any desired action
        header('Location: login.php');
        exit();
    }

    // Additional session checks or validations can be added here

    // User is logged in and session is valid
}



function is_admin()
{
    // check if the user is logged in and their role is admin
    if (is_logged_in() && $_SESSION['role'] == 'admin') {
        return true;
    } else {
        return false;
    }
}

function validate_category_form($name)
{
    $errors = array();
    // Perform validation on the category name
    if (empty($name)) {
        $errors[] = 'Category name is required';
    } elseif (strlen($name) > 50) {
        $errors[] = 'Category name should not exceed 50 characters';
    }
    return $errors;
}






// Calculate total price of selected items
function calculate_total_price($items)
{
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Get all products from the database
function get_products()
{
    // Replace with your database credentials

    $host = 'localhost';
    $username = 'root';
    $password = 'Salama@99';
    $dbname = 'cafeteria_db';

    // $host = 'localhost';
    // $username = 'phpuser';
    // $password = 'Iti123456';
    // $dbname = 'cafeteria_db';

    try {
        // Create a new PDO instance
        $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

        // Set the PDO error mode to exception
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch all products from the database
        $stmt = $db->query('SELECT * FROM products');
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $products;
    } catch (PDOException $e) {
        // Handle database connection error
        die("Database error: " . $e->getMessage());
    }
}
function get_all_categories()
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name ASC");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $categories;
}
// Get all orders for a specific user
function get_user_orders($userId)
{
    // Replace with your database credentials
    // $servername = "localhost";
    // $username = "root";
    // $password = "Salama@99";
    // $dbname = "cafeteria_db";

    try {
        // Create a new PDO instance
        $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

        // Set the PDO error mode to exception
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch all orders for the user from the database
        $stmt = $db->prepare('SELECT * FROM orders WHERE user_id = :user_id');
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $orders;
    } catch (PDOException $e) {
        // Handle database connection error
        die("Database error: " . $e->getMessage());
    }
}

function is_category_exists($category_id)
{
    global $pdo;
    $query = "SELECT id FROM categories WHERE id = :category_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}



// Add more functions for other functionality as needed

// Function to retrieve a user by email
function get_user_by_email($email)
{
    $pdo = DataBase::getPDO();

    $query = "SELECT * FROM users WHERE email = :email";
    $statement = $pdo->prepare($query);
    $statement->execute(['email' => $email]);

    return $statement->fetch();
}
function get_product_by_id($id)
{
    $pdo = DataBase::getPDO();

    $query = "SELECT * FROM products WHERE id = :id";
    $statement = $pdo->prepare($query);
    $statement->execute(['id' => $id]);

    return $statement->fetch();
}
function get_user_by_id($id)
{
    $pdo = DataBase::getPDO();

    $query = "SELECT * FROM users WHERE id = :id";
    $statement = $pdo->prepare($query);
    $statement->execute(['id' => $id]);

    return $statement->fetch();
}


// Function to get orders by user ID
function get_orders_by_user($user_id)
{
    $pdo = DataBase::getPDO();

    $query = "
        SELECT *
        FROM orders
        WHERE user_id = :user_id
        ORDER BY order_date DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function get_all_users()
{
    $pdo = DataBase::getPDO();

    $query = "
        SELECT *
        FROM users
    ";

    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Function to create the necessary tables in the database
function create_tables()
{
    $query = "
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        );

        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            reset_token VARCHAR(255) DEFAULT NULL
        );

        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            category_id INT NOT NULL,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            room_no VARCHAR(255) NOT NULL,
            total_price DECIMAL(10, 2) NOT NULL,
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            order_status ENUM('processing', 'out for delivery', 'done') NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );

        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            notes TEXT,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        );
    ";

    $pdo = DataBase::getPDO();
    $pdo->exec($query);
}



// Example function:
function cancel_order($orderId)
{
    // Replace with your database credentials
    $servername = "localhost";
    $username = "root";
    $password = "Salama@99";
    $dbname = "cafeteria_db";

    try {
        // Create a new PDO instance
        $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

        // Set the PDO error mode to exception
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Update the order status to "canceled" in the database
        $stmt = $db->prepare('UPDATE orders SET order_status = "canceled" WHERE id = :order_id');
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
    } catch (PDOException $e) {
        // Handle database connection error
        die("Database error: " . $e->getMessage());
    }
}
