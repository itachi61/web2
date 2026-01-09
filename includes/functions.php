<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' VNĐ';
}

function getProductRating($conn, $product_id) {
    $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE product_id = $product_id";
    $result = $conn->query($sql)->fetch_assoc();
    return [
        'avg' => round($result['avg_rating'], 1) ?? 0,
        'count' => $result['count'] ?? 0
    ];
}

function renderStars($rating) {
    $html = '<div class="flex items-center gap-1">';
    for ($i = 1; $i <= 5; $i++) {
        $colorClass = $i <= $rating ? "fill-yellow-400 text-yellow-400" : "text-gray-300";
        $html .= '<i data-lucide="star" class="w-4 h-4 ' . $colorClass . '"></i>';
    }
    $html .= '</div>';
    return $html;
}
?>