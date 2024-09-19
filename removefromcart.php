<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    if ($user_id) {
        // remove from db
        $stmt = $pdo->prepare("DELETE FROM cart WHERE product_id = ? AND user_id = ?");
        $stmt->execute([$product_id, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Product deleted from cart']);
        exit;
    } else {
        // remove from session
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            echo json_encode(['success' => true, 'message' => 'Product deleted from car.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
    }
}
?>  