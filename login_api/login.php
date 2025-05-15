<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'], $data['password'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$email = $data['email'];
$password = $data['password'];

include 'conn.php';

$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION["username"] = $user['username'];
        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "username" => $user['username']
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Incorrect password"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Email not found"]);
}

$stmt->close();
$conn->close();
?>