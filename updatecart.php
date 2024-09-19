<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
     if($quantity <= 0){
        echo json_encode(['success' => false, 'message' => 'Incorrect quantity: '.$quantity]);        
        exit;
    }

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if ($user_id) {
        // Update db
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE product_id = ? AND user_id = ?");
        $stmt->execute([$quantity, $product_id, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Quantity updated']);  
        exit;
    } else {
        // update session
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            echo json_encode(['success' => true, 'message' => 'Quantity updated.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
            exit;
        }
    }
} 

?>
   