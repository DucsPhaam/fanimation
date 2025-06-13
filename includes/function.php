<?php
function getProducts($conn, $records_per_page, $page, $search = '', $category = '', $min_price = '', $max_price = '', $color = '', $brand = '') {
    $offset = ($page - 1) * $records_per_page;

    $query = "SELECT DISTINCT p.*, p.id AS product_id, pi.image_url
              FROM products p
              LEFT JOIN product_variants pv ON p.id = pv.product_id
              LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.u_primary = 1
              WHERE 1=1";

    $count_query = "SELECT COUNT(DISTINCT p.id) as total 
                    FROM products p
                    LEFT JOIN product_variants pv ON p.id = pv.product_id
                    WHERE 1=1";

    $params = [];
    $types = '';

    if (!empty($search)) {
        $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $count_query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'ss';
    }

    if (!empty($category)) {
        $query .= " AND p.category_id = ?";
        $count_query .= " AND p.category_id = ?";
        $params[] = $category;
        $types .= 'i';
    }

    if ($min_price !== '') {
        $query .= " AND p.price >= ?";
        $count_query .= " AND p.price >= ?";
        $params[] = $min_price;
        $types .= 'd';
    }

    if ($max_price !== '') {
        $query .= " AND p.price <= ?";
        $count_query .= " AND p.price <= ?";
        $params[] = $max_price;
        $types .= 'd';
    }

    if (!empty($color)) {
        $query .= " AND pv.color_id = ?";
        $count_query .= " AND pv.color_id = ?";
        $params[] = $color;
        $types .= 'i';
    }

    if (!empty($brand)) {
        $query .= " AND p.brand_id = ?";
        $count_query .= " AND p.brand_id = ?";
        $params[] = $brand;
        $types .= 'i';
    }

    // Thêm phân trang
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $records_per_page;
    $params[] = $offset;
    $types .= 'ii';

    // Đếm tổng sản phẩm
    $count_stmt = mysqli_prepare($conn, $count_query);
    if (!empty($params)) {
        $count_params = array_slice($params, 0, count($params) - 2);
        $count_types = substr($types, 0, strlen($types) - 2);
        if (!empty($count_params)) {
            mysqli_stmt_bind_param($count_stmt, $count_types, ...$count_params);
        }
    }
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total_records = mysqli_fetch_assoc($count_result)['total'];
    mysqli_stmt_close($count_stmt);

    $total_pages = ceil($total_records / $records_per_page);

    // Truy vấn chính
    $stmt = mysqli_prepare($conn, $query);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    mysqli_stmt_close($stmt);

    return [
        'products' => $products,
        'total_pages' => $total_pages
    ];
}

// Your existing functions remain unchanged
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


if (!function_exists('getMonthlySales')) {
    function getMonthlySales() {
        global $conn;
        $query = "SELECT SUM(total_money) as sales 
                  FROM orders 
                  WHERE status = 'completed' 
                  AND YEAR(created_at) = YEAR(CURDATE()) 
                  AND MONTH(created_at) = MONTH(CURDATE())";
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
        $query = "SELECT SUM(total_money) as sales 
                  FROM orders 
                  WHERE status = 'completed' 
                  AND YEAR(created_at) = YEAR(CURDATE())";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            error_log('Annual Sales Query failed: ' . mysqli_error($conn));
            return '0.00';
        }
        $row = mysqli_fetch_assoc($result);
        return $row['sales'] ? number_format($row['sales'], 2) : '0.00';
    }
}

if (!function_exists('getCategoryName')) {
    function getCategoryName($category_id) {
        global $conn;

        $query = "SELECT name FROM categories WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $category_name = null;
        if ($row = $result->fetch_assoc()) {
            $category_name = $row['name'];
        }

        $stmt->close();
        return $category_name;
    }
}

if (!function_exists('getAllOrders')) {
    function getAllOrders($conn, $records_per_page = 10, $page = 1, $search = '', $status = '') {
        $offset = ($page - 1) * $records_per_page;

        $query = "SELECT o.id, u.name, o.created_at, o.total_money, o.status 
                  FROM orders o 
                  JOIN users u ON o.user_id = u.id 
                  WHERE 1=1";

        $count_query = "SELECT COUNT(*) as total 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        WHERE 1=1";

        $params = [];
        $types = '';

        if (!empty($search)) {
            $query .= " AND (u.name LIKE ? OR o.id LIKE ?)";
            $count_query .= " AND (u.name LIKE ? OR o.id LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= 'ss';
        }

        if (!empty($status)) {
            $query .= " AND o.status = ?";
            $count_query .= " AND o.status = ?";
            $params[] = $status;
            $types .= 's';
        }

        $query .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $records_per_page;
        $params[] = $offset;
        $types .= 'ii';

        // Count total orders
        $count_stmt = mysqli_prepare($conn, $count_query);
        if (!empty($params)) {
            $count_params = array_slice($params, 0, count($params) - 2);
            $count_types = substr($types, 0, strlen($types) - 2);
            if (!empty($count_params)) {
                mysqli_stmt_bind_param($count_stmt, $count_types, ...$count_params);
            }
        }
        mysqli_stmt_execute($count_stmt);
        $count_result = mysqli_stmt_get_result($count_stmt);
        $total_records = mysqli_fetch_assoc($count_result)['total'];
        mysqli_stmt_close($count_stmt);

        $total_pages = ceil($total_records / $records_per_page);

        // Main query
        $stmt = mysqli_prepare($conn, $query);
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        mysqli_stmt_close($stmt);

        return [
            'orders' => $orders,
            'total_pages' => $total_pages,
            'total_records' => $total_records
        ];
    }
}

if (!function_exists('updateOrderStatus')) {
    function updateOrderStatus($conn, $order_id, $new_status, $new_payment_status = null) {
        // Validate order ID
        if (!is_numeric($order_id) || $order_id <= 0) {
            error_log("Invalid order ID: $order_id");
            return [
                'success' => false,
                'message' => 'Invalid order ID'
            ];
        }

        // Validate new status against allowed ENUM values
        $valid_statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        if (!in_array($new_status, $valid_statuses)) {
            error_log("Invalid order status: $new_status");
            return [
                'success' => false,
                'message' => 'Invalid order status'
            ];
        }

        // Disable autocommit and start transaction
        if (!mysqli_autocommit($conn, false)) {
            error_log("Failed to disable autocommit: " . mysqli_error($conn));
            return [
                'success' => false,
                'message' => 'Database error: Failed to configure transaction'
            ];
        }

        if (!mysqli_begin_transaction($conn)) {
            error_log("Failed to start transaction: " . mysqli_error($conn));
            mysqli_autocommit($conn, true); // Restore autocommit
            return [
                'success' => false,
                'message' => 'Failed to start transaction'
            ];
        }

        try {
            // Check if order exists and get current status
            $check_query = "SELECT status, payment_status FROM orders WHERE id = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            if (!$check_stmt) {
                mysqli_rollback($conn);
                mysqli_autocommit($conn, true);
                error_log("Check prepare failed: " . mysqli_error($conn));
                return [
                    'success' => false,
                    'message' => 'Database error: Failed to prepare order check'
                ];
            }
            mysqli_stmt_bind_param($check_stmt, 'i', $order_id);
            if (!mysqli_stmt_execute($check_stmt)) {
                mysqli_stmt_close($check_stmt);
                mysqli_rollback($conn);
                mysqli_autocommit($conn, true);
                error_log("Check execute failed: " . mysqli_stmt_error($check_stmt));
                return [
                    'success' => false,
                    'message' => 'Database error: Failed to check order'
                ];
            }
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) === 0) {
                mysqli_stmt_close($check_stmt);
                mysqli_rollback($conn);
                mysqli_autocommit($conn, true);
                error_log("Order not found: $order_id");
                return [
                    'success' => false,
                    'message' => 'Order not found'
                ];
            }
            $current_order = mysqli_fetch_assoc($check_result);
            mysqli_stmt_close($check_stmt);

            // Prevent updating to the same status
            if ($current_order['status'] === $new_status && ($new_payment_status === null || $current_order['payment_status'] === $new_payment_status)) {
                mysqli_rollback($conn);
                mysqli_autocommit($conn, true);
                error_log("No changes to update for order_id: $order_id");
                return [
                    'success' => false,
                    'message' => 'No changes to update'
                ];
            }

            // Business rule: Prevent cancelling a paid order
            if ($new_status === 'cancelled' && $current_order['payment_status'] === 'completed') {
                mysqli_rollback($conn);
                mysqli_autocommit($conn, true);
                error_log("Cannot cancel paid order: $order_id");
                return [
                    'success' => false,
                    'message' => 'Cannot cancel an order with completed payment'
                ];
            }

            // Handle stock restoration for cancelled orders
            if ($new_status === 'cancelled' && $current_order['status'] !== 'cancelled') {
                $stock_query = "SELECT oi.product_variant_id, oi.quantity 
                               FROM order_items oi 
                               WHERE oi.order_id = ?";
                $stock_stmt = mysqli_prepare($conn, $stock_query);
                if (!$stock_stmt) {
                    mysqli_rollback($conn);
                    mysqli_autocommit($conn, true);
                    error_log("Stock prepare failed: " . mysqli_error($conn));
                    return [
                        'success' => false,
                        'message' => 'Database error: Failed to prepare stock query'
                    ];
                }
                mysqli_stmt_bind_param($stock_stmt, 'i', $order_id);
                if (!mysqli_stmt_execute($stock_stmt)) {
                    mysqli_stmt_close($stock_stmt);
                    mysqli_rollback($conn);
                    mysqli_autocommit($conn, true);
                    error_log("Stock execute failed: " . mysqli_stmt_error($stock_stmt));
                    return [
                        'success' => false,
                        'message' => 'Database error: Failed to fetch order items'
                    ];
                }
                $stock_result = mysqli_stmt_get_result($stock_stmt);
                $item_count = mysqli_num_rows($stock_result);
                error_log("Found $item_count order items for order_id: $order_id");

                if ($item_count > 0) {
                    while ($item = mysqli_fetch_assoc($stock_result)) {
                        error_log("Restoring stock: product_variant_id={$item['product_variant_id']}, quantity={$item['quantity']}");
                        $update_stock_query = "UPDATE product_variants 
                                              SET stock = stock + ? 
                                              WHERE id = ?";
                        $update_stock_stmt = mysqli_prepare($conn, $update_stock_query);
                        if (!$update_stock_stmt) {
                            mysqli_rollback($conn);
                            mysqli_autocommit($conn, true);
                            error_log("Update stock prepare failed: " . mysqli_error($conn));
                            return [
                                'success' => false,
                                'message' => 'Database error: Failed to prepare stock update'
                            ];
                        }
                        mysqli_stmt_bind_param($update_stock_stmt, 'ii', $item['quantity'], $item['product_variant_id']);
                        if (!mysqli_stmt_execute($update_stock_stmt)) {
                            mysqli_stmt_close($update_stock_stmt);
                            mysqli_rollback($conn);
                            mysqli_autocommit($conn, true);
                            error_log("Update stock execute failed: " . mysqli_stmt_error($update_stock_stmt));
                            return [
                                'success' => false,
                                'message' => 'Failed to restore stock'
                            ];
                        }
                        $stock_affected_rows = mysqli_stmt_affected_rows($update_stock_stmt);
                        if ($stock_affected_rows === 0) {
                            mysqli_stmt_close($update_stock_stmt);
                            mysqli_rollback($conn);
                            mysqli_autocommit($conn, true);
                            error_log("No stock updated for product_variant_id: {$item['product_variant_id']}");
                            return [
                                'success' => false,
                                'message' => 'Failed to restore stock: No matching variant found'
                            ];
                        }
                        mysqli_stmt_close($update_stock_stmt);
                    }
                } else {
                    error_log("No order items found for order_id: $order_id, skipping stock restoration");
                }
                mysqli_stmt_close($stock_stmt);
            }

            // Prepare the update query for orders
            $query = "UPDATE orders SET status = ?";
            $params = [$new_status];
            $types = 's';

            // Handle payment status update
            $effective_payment_status = $new_payment_status;
            if ($new_status === 'completed' && $current_order['payment_status'] !== 'completed') {
                $effective_payment_status = 'completed'; // Auto-set payment_status to completed
            }

            if ($effective_payment_status !== null) {
                $valid_payment_statuses = ['pending', 'completed'];
                if (!in_array($effective_payment_status, $valid_payment_statuses)) {
                    mysqli_rollback($conn);
                    mysqli_autocommit($conn, true);
                    error_log("Invalid payment status: $effective_payment_status");
                    return [
                        'success' => false,
                        'message' => 'Invalid payment status'
                    ];
                }
                $query .= ", payment_status = ?";
                $params[] = $effective_payment_status;
                $types .= 's';
            }

            $query .= " WHERE id = ?";
            $params[] = $order_id;
            $types .= 'i';

            // Execute the order update
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                mysqli_rollback($conn);
                mysqli_autocommit($conn, true);
                error_log("Order prepare failed: " . mysqli_error($conn));
                return [
                    'success' => false,
                    'message' => 'Database error: Failed to prepare order update'
                ];
            }

            mysqli_stmt_bind_param($stmt, $types, ...$params);
            if (!mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_rollback($conn);
                mysqli_autocommit($conn, true);
                error_log("Order execute failed: " . mysqli_stmt_error($stmt));
                return [
                    'success' => false,
                    'message' => 'Failed to update order status'
                ];
            }

            $affected_rows = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);

            if ($affected_rows === 0) {
                mysqli_rollback($conn);
                mysqli_autocommit($conn, true);
                error_log("No rows affected for order_id: $order_id");
                return [
                    'success' => false,
                    'message' => 'No rows updated: Order may not exist or status unchanged'
                ];
            }

            // Update payments table if payment_status changed
            if ($effective_payment_status !== null && $effective_payment_status !== $current_order['payment_status']) {
                $payment_query = "UPDATE payments SET status = ? WHERE order_id = ?";
                $payment_stmt = mysqli_prepare($conn, $payment_query);
                if (!$payment_stmt) {
                    mysqli_rollback($conn);
                    mysqli_autocommit($conn, true);
                    error_log("Payment prepare failed: " . mysqli_error($conn));
                    return [
                        'success' => false,
                        'message' => 'Database error: Failed to prepare payment update'
                    ];
                }
                mysqli_stmt_bind_param($payment_stmt, 'si', $effective_payment_status, $order_id);
                if (!mysqli_stmt_execute($payment_stmt)) {
                    mysqli_stmt_close($payment_stmt);
                    mysqli_rollback($conn);
                    mysqli_autocommit($conn, true);
                    error_log("Payment execute failed: " . mysqli_stmt_error($payment_stmt));
                    return [
                        'success' => false,
                        'message' => 'Failed to update payment status'
                    ];
                }
                $payment_affected_rows = mysqli_stmt_affected_rows($payment_stmt);
                if ($payment_affected_rows === 0) {
                    error_log("No payment rows affected for order_id: $order_id");
                    // Log but continue, as payment record might not exist
                }
                mysqli_stmt_close($payment_stmt);
            }

            // Commit transaction
            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                mysqli_autocommit($conn, true);
                error_log("Commit failed: " . mysqli_error($conn));
                return [
                    'success' => false,
                    'message' => 'Failed to commit transaction'
                ];
            }

            // Restore autocommit
            mysqli_autocommit($conn, true);

            error_log("Successfully updated order_id: $order_id to status: $new_status");
            return [
                'success' => true,
                'message' => 'Order status updated successfully'
            ];
        } catch (Exception $e) {
            mysqli_rollback($conn);
            mysqli_autocommit($conn, true);
            error_log("Transaction failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ];
        }
    }
}

if (!function_exists('getAllUsers')) {
    function getAllUsers($conn, $records_per_page = 10, $page = 1, $search = '', $role = '') {
        $offset = ($page - 1) * $records_per_page;

        $query = "SELECT id, name, email, phone, address, city, role, created_at 
                  FROM users 
                  WHERE 1=1";

        $count_query = "SELECT COUNT(*) as total 
                        FROM users 
                        WHERE 1=1";

        $params = [];
        $types = '';

        if (!empty($search)) {
            $query .= " AND (name LIKE ? OR email LIKE ? OR id LIKE ?)";
            $count_query .= " AND (name LIKE ? OR email LIKE ? OR id LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= 'sss';
        }

        if (!empty($role)) {
            $query .= " AND role = ?";
            $count_query .= " AND role = ?";
            $params[] = $role;
            $types .= 's';
        }

        $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $records_per_page;
        $params[] = $offset;
        $types .= 'ii';

        // Count total users
        $count_stmt = mysqli_prepare($conn, $count_query);
        if (!empty($params)) {
            $count_params = array_slice($params, 0, count($params) - 2);
            $count_types = substr($types, 0, strlen($types) - 2);
            if (!empty($count_params)) {
                mysqli_stmt_bind_param($count_stmt, $count_types, ...$count_params);
            }
        }
        mysqli_stmt_execute($count_stmt);
        $count_result = mysqli_stmt_get_result($count_stmt);
        $total_records = mysqli_fetch_assoc($count_result)['total'];
        mysqli_stmt_close($count_stmt);

        $total_pages = ceil($total_records / $records_per_page);

        // Main query
        $stmt = mysqli_prepare($conn, $query);
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        mysqli_stmt_close($stmt);

        return [
            'users' => $users,
            'total_pages' => $total_pages,
            'total_records' => $total_records
        ];
    }
}
?>
