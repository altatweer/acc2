<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (!isset($_GET['purchase_code'])) {
    echo json_encode(['success' => false, 'message' => 'Purchase code required']);
    exit;
}

$purchase_code = $_GET['purchase_code'];
$personal_token = 'angl9NbNVfI0kcsmkV5KZ7YrNRXNbWYt'; // ضع التوكن هنا فقط في هذا السكربت

$url = "https://api.envato.com/v3/market/author/sale?code={$purchase_code}";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$personal_token}"
]);
$result = curl_exec($ch);
$data = json_decode($result, true);
curl_close($ch);

if (isset($data['item']['id'])) {
    echo json_encode(['success' => true, 'buyer' => $data['buyer'], 'item' => $data['item']['name']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid purchase code']);
} 