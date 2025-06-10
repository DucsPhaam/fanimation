<?php
if (!function_exists('getAllProducts')) {
    function getAllProducts() {
        global $conn;
        $query = "SELECT * FROM products";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die('Query failed: ' . mysqli_error($conn));
        }
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        return $products;
    }
}

if (!function_exists('getCountUsers')) {
    function getCountUsers() {
        global $conn;
        $query = "SELECT COUNT(*) as count FROM users";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die('Query failed: ' . mysqli_error($conn));
        }
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }
}

if (!function_exists('getCountProducts')) {
    function getCountProducts() {
        global $conn;
        $query = "SELECT COUNT(*) as count FROM products";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die('Query failed: ' . mysqli_error($conn));
        }
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }
}

if (!function_exists('getCountOrders')) {
    function getCountOrders() {
        global $conn;
        $query = "SELECT COUNT(*) as count FROM orders";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die('Query failed: ' . mysqli_error($conn));
        }
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }
}

if (!function_exists('getAllOrders')) {
    function getAllOrders() {
        global $conn;
        $query = "SELECT o.order_id, u.username, o.order_date, o.amount, o.status 
                  FROM orders o 
                  JOIN users u ON o.user_id = u.user_id 
                  ORDER BY o.order_date DESC";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die('Query failed: ' . mysqli_error($conn));
        }
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        return $orders;
    }
}

if (!function_exists('getMonthlySales')) {
    function getMonthlySales() {
        global $conn;
        $query = "SELECT SUM(amount) as sales 
                  FROM orders 
                  WHERE status = 'Delivered' 
                  AND YEAR(order_date) = YEAR(CURDATE()) 
                  AND MONTH(order_date) = MONTH(CURDATE())";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            error_log('Monthly Sales Query failed: ' . mysqli_error($conn));
            return '0.00';
        }
        $row = mysqli_fetch_assoc($result);
        return $row['sales'] ? number_format($row['sales'], 2) : '0.00';
    }
}

if (!function_exists('getAnnualSales')) {
    function getAnnualSales() {
        global $conn;
        $query = "SELECT SUM(amount) as sales 
                  FROM orders 
                  WHERE status = 'Delivered' 
                  AND YEAR(order_date) = YEAR(CURDATE())";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            error_log('Annual Sales Query failed: ' . mysqli_error($conn));
            return '0.00';
        }
        $row = mysqli_fetch_assoc($result);
        return $row['sales'] ? number_format($row['sales'], 2) : '0.00';
    }
}
?>