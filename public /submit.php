<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\AmoCrmClient;
use App\Validation\LeadRequestValidator;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] !== "POST"){
    http_response_code(405);
    exit('Method not allowed');
}

$validator = new LeadRequestValidator();
$result = $validator->validate($_POST);

if ($result['errors'] !== []) {
    http_response_code(422);

    echo json_encode([
        'status' => 'error',
        'message' => 'Ошибка валидации',
        'errors' => $result['errors'],
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

$data = $result['data'];

$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$price = $data['price'];
$timeOnSite = $data['time_on_site'];

try {
    $client = new AmoCrmClient();

    $contactId = $client->createContact($name, $email, $phone);
    $leadId = $client->createLead($contactId, $price, (int)$timeOnSite);

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'ok',
        'name' => $name,
        'price' => $price,
        'phone' => $phone,
        'email' => $email,
        'time_on_site' => $timeOnSite,
    ]);
} catch (\Exception $e) {
    error_log('Error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
    exit;
}
