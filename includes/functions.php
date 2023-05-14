<?php

require_once 'config.php';
require_once 'DB_class.php';

session_start();

function save_reset_token($user_id, $token)
{
    $db = new DataBase();
    $db->connect();
    $stmt = $db->pdo->prepare("UPDATE users SET reset_token = ? WHERE id = ?");
    $stmt->execute([$token, $user_id]);
    return $stmt->rowCount() > 0;
}

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

// Check session and redirect if user is not logged in
function check_session()
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
    // Additional session checks or validations can be added here
}

// Check if the user is an admin
function is_admin()
{
    return is_logged_in() && $_SESSION['role'] === 'admin';
}

// Validate the category form
function validate_category_form($name)
{
    $errors = [];
    // Perform validation on the category name
    if (empty($name)) {
        $errors[] = 'Category name is required';
    } elseif (strlen($name) > 50) {
        $errors[] = 'Category name should not exceed 50 characters';
    }
    return $errors;
}

function get_checks($pdo)
{
    // Prepare the query
    $query = "SELECT c.id, c.check_date, c.total_price, u.name AS user_name
              FROM checks c
              INNER JOIN users u ON c.user_id = u.id";
    
    // Prepare the statement
    $stmt = $pdo->prepare($query);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch all the results
    $checks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the checks
    return $checks;
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
    $pdo = DataBase::getPDO();
    $stmt = $pdo->query('SELECT * FROM products');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all categories
function get_all_categories()
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->query('SELECT * FROM categories ORDER BY name ASC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_all_orders()
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->query('SELECT * FROM orders ORDER BY order_date ASC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get orders for a specific user
function get_user_orders($userId)
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = :user_id');
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check if a category exists
function is_category_exists($categoryId)
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE id = :category_id');
    $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

// Add more functions for other functionality as needed

// Function to retrieve a user by email
function get_user_by_email($email)
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get a product by ID
function get_product_by_id($id)
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
// function get_user_ext_by_orderId($id)
// {
//     $pdo = DataBase::getPDO();
//     $stmt = $pdo->prepare('SELECT * FROM orders WHERE orders.id=:id and users.id = :id');
//     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
//     $stmt->execute();
//     return $stmt->fetch(PDO::FETCH_ASSOC);
// }

// Get a user by ID
function get_user_by_id($id)
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get orders by user ID
function get_orders_by_user($userId)
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC');
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all users
function get_all_users()
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->query('SELECT * FROM users');
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

// ...

// Save the order in the database
function save_order($selectedProduct, $quantity, $roomNo, $notes, $totalAmount)
{
    $pdo = DataBase::getPDO();

    // Insert the order into the orders table
    $stmt = $pdo->prepare('INSERT INTO orders (user_id, room_no, total_price, order_status) VALUES (:user_id, :room_no, :total_price, :order_status)');
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':room_no', $roomNo);
    $stmt->bindParam(':total_price', $totalAmount);
    $stmt->bindValue(':order_status', 'processing');
    $stmt->execute();

    // Get the ID of the inserted order
    $orderId = $pdo->lastInsertId();

    // Insert the order item into the order_items table
    $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, notes) VALUES (:order_id, :product_id, :quantity, :notes)');
    $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $selectedProduct, PDO::PARAM_INT);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':notes', $notes);
    $stmt->execute();
}

// ...

function get_product_details($productId)
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :product_id');
    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Example function:
function cancel_order($orderId)
{
    $pdo = DataBase::getPDO();
    $stmt = $pdo->prepare('UPDATE orders SET order_status = "canceled" WHERE id = :order_id');
    $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
    $stmt->execute();
}
