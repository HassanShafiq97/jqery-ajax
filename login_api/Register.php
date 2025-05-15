<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username'], $data['email'], $data['password'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$username = trim($data['username']);
$email = trim($data['email']);
$password_raw = $data['password'];

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "error" => "Invalid email format."]);
    exit;
}
if (strlen($username) < 3 || strlen($password_raw) < 6) {
    echo json_encode(["success" => false, "error" => "Username must be at least 3 characters and password at least 6 characters long."]);
    exit;
}

$password = password_hash($password_raw, PASSWORD_DEFAULT);

include('conn.php');

$check = $conn->prepare('SELECT id FROM users WHERE email = ?');
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Email already exists."]);
    exit;
}

$stmt = $conn->prepare('INSERT INTO users(username, email, password) VALUES(?,?,?)');
$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>