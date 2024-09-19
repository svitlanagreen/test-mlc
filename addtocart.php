<?php
session_start();
// connect to DB
require 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    // Checking if the user is authorized
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($user_id) {
        //If the user is authorized
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE product_id = ? AND user_id = ?");
        $stmt->execute([$product_id, $user_id]);
        $existingProduct = $stmt->fetch();

        if ($existingProduct) {
            // if product in cart - update quantity
            $newQuantity = $existingProduct['quantity'] + $quantity;
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE product_id = ? AND user_id = ?");
            $stmt->execute([$newQuantity, $product_id, $user_id]);
        } else {
            // add a new rpoduct to cart
            $stmt = $pdo->prepare("INSERT INTO cart (product_id, user_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$product_id, $user_id, $quantity]);
        }

        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        exit;
    } else {
        // if the user is a guest, save in the session   
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'product_id' => $product_id,
                'quantity' => $quantity
            ];
        }
        echo json_encode(['success' => true, 'message' => 'Product added to cart.']);
        exit;
    }
}
?>