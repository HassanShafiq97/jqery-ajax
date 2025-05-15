<?php

$conn = new mysqli('localhost', 'root', '', 'auth_api');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>