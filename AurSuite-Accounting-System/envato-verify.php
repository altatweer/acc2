<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$purchase_code = $_GET['purchase_code'] ?? '';
$domain = $_GET['domain'] ?? '';
$db_file = __DIR__ . '/purchase_domains.json';
$db = file_exists($db_file) ? json_decode(file_get_contents($db_file), true) : [];

// Developer/test code
$dev_code = 'AURSUITE-DEV-ONLY';
if ($purchase_code === $dev_code) {
    echo json_encode(['success' => true, 'message' => 'Development/Test License', 'dev' => true]);
    exit;
}

if (!$purchase_code || !$domain) {
    echo json_encode(['success' => false, 'message' => 'Purchase code and domain required']);
    exit;
}

// Envato verification
$personal_token = 'angl9NbNVfI0kcsmkV5KZ7YrNRXNbWYt';
$url = "https://api.envato.com/v3/market/author/sale?code={$purchase_code}";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$personal_token}"
]);
$result = curl_exec($ch);
$data = json_decode($result, true);
curl_close($ch);

if (!isset($data['item']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid purchase code']);
    exit;
}

// Domain binding
if (!isset($db[$purchase_code])) {
    $db[$purchase_code] = $domain;
    file_put_contents($db_file, json_encode($db));
    echo json_encode(['success' => true, 'message' => 'Activated for this domain']);
    exit;
} elseif ($db[$purchase_code] === $domain) {
    echo json_encode(['success' => true, 'message' => 'Already activated for this domain']);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'This purchase code is already used on another domain.']);
    exit;
} 