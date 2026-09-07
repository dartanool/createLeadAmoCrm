<?php


require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\AmoCrmClient;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] !== "POST"){
    http_response_code(405);
    exit('Method not allowed');
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$price = $_POST['price'] ?? '';
$timeOnSite = $_POST['time_on_site'] ?? '0';

if (!$name || !$email || !$phone || $price <= 0){
    http_response_code(400);
    exit('Invalid data');
}
if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    http_response_code(400);
    exit('Invalid email');
}


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

