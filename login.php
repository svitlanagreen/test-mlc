<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cheking if user is authorized
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // if user is authorized  
        $_SESSION['user_id'] = $user['id'];

        // Transferring products from a session to the database
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $product_id => $product) {
                $stmt = $pdo->prepare("SELECT * FROM cart WHERE product_id = ? AND user_id = ?");
                $stmt->execute([$product_id, $user['id']]);
                $existingProduct = $stmt->fetch();

                if ($existingProduct) {
                    // updating the quantity, since the product is already in cart
                    $newQuantity = $existingProduct['quantity'] + $product['quantity'];
                    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE product_id = ? AND user_id = ?");
                    $stmt->execute([$newQuantity, $product_id, $user['id']]);
                } else {
                    // adding product to database if product doesnt exist in db
                    $stmt = $pdo->prepare("INSERT INTO cart (product_id, user_id, quantity) VALUES (?, ?, ?)");
                    $stmt->execute([$product_id, $user['id'], $product['quantity']]);
                }
            }
            // delete session
            unset($_SESSION['cart']);
        }

        echo json_encode(['success' => true, 'message' => 'Login successful']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid login']);
        exit;
    }
}
?>
