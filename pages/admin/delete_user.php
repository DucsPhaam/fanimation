<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Fanimation/includes/config.php';
require_once $db_connect_url;

// Start session for message handling
session_start();

// Get user ID from URL
$user_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];
$success = false;

// Validate user ID
if ($user_id <= 0) {
    $errors[] = 'Invalid user ID.';
} else {
    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Check if user exists and get role
        $stmt = mysqli_prepare($conn, "SELECT role FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($user = mysqli_fetch_assoc($result)) {
            // Check for related orders
            $order_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
            mysqli_stmt_bind_param($order_stmt, 'i', $user_id);
            mysqli_stmt_execute($order_stmt);
            $order_result = mysqli_stmt_get_result($order_stmt);
            $order_count = mysqli_fetch_assoc($order_result)['count'];
            mysqli_stmt_close($order_stmt);
            if ($order_count > 0) {
                $errors[] = 'Cannot delete user because they have ' . $order_count . ' associated order(s).';
            }

            // Check for related carts
            $cart_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM carts WHERE user_id = ?");
            mysqli_stmt_bind_param($cart_stmt, 'i', $user_id);
            mysqli_stmt_execute($cart_stmt);
            $cart_result = mysqli_stmt_get_result($cart_stmt);
            $cart_count = mysqli_fetch_assoc($cart_result)['count'];
            mysqli_stmt_close($cart_stmt);
            if ($cart_count > 0) {
                $errors[] = 'Cannot delete user because they have ' . $cart_count . ' item(s) in cart.';
            }

            // Check for related feedbacks
            $feedback_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM feedbacks WHERE user_id = ?");
            mysqli_stmt_bind_param($feedback_stmt, 'i', $user_id);
            mysqli_stmt_execute($feedback_stmt);
            $feedback_result = mysqli_stmt_get_result($feedback_stmt);
            $feedback_count = mysqli_fetch_assoc($feedback_result)['count'];
            mysqli_stmt_close($feedback_stmt);
            if ($feedback_count > 0) {
                $errors[] = 'Cannot delete user because they have ' . $feedback_count . ' feedback(s).';
            }

            // Check for related contacts
            $contact_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM contacts WHERE user_id = ?");
            mysqli_stmt_bind_param($contact_stmt, 'i', $user_id);
            mysqli_stmt_execute($contact_stmt);
            $contact_result = mysqli_stmt_get_result($contact_stmt);
            $contact_count = mysqli_fetch_assoc($contact_result)['count'];
            mysqli_stmt_close($contact_stmt);
            if ($contact_count > 0) {
                $errors[] = 'Cannot delete user because they have ' . $contact_count . ' contact record(s).';
            }

            // Check if deleting the last admin
            if ($user['role'] === 'admin' && empty($errors)) {
                $admin_count_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND id != ?");
                mysqli_stmt_bind_param($admin_count_stmt, 'i', $user_id);
                mysqli_stmt_execute($admin_count_stmt);
                $admin_count_result = mysqli_stmt_get_result($admin_count_stmt);
                $admin_count = mysqli_fetch_assoc($admin_count_result)['count'];
                mysqli_stmt_close($admin_count_stmt);
                if ($admin_count < 1) {
                    $errors[] = 'Cannot delete the last admin user.';
                }
            }
        } else {
            $errors[] = 'User not found.';
        }
        mysqli_stmt_close($stmt);

        // If no errors, delete user
        if (empty($errors)) {
            $delete_stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
            mysqli_stmt_bind_param($delete_stmt, 'i', $user_id);
            if (mysqli_stmt_execute($delete_stmt)) {
                $success = true;
            } else {
                $errors[] = 'Failed to delete user: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($delete_stmt);
        }

        // Commit transaction if successful
        if ($success) {
            mysqli_commit($conn);
        } else {
            mysqli_rollback($conn);
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $errors[] = 'Transaction failed: ' . $e->getMessage();
    }
}

// Store messages in session and redirect
if ($success) {
    $_SESSION['success'] = 'User deleted successfully!';
} elseif (!empty($errors)) {
    $_SESSION['errors'] = $errors;
}
header("Location: $base_url/pages/admin/users.php");
exit;
?>