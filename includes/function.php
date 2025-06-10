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
?>
