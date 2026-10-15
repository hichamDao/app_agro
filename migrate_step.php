<?php
require 'includes/connection.php';

header('Content-Type: text/plain; charset=utf-8');

echo "Checking chat_conversations table...\n";

$result = $conn->query("SHOW COLUMNS FROM chat_conversations LIKE 'step'");
if ($result->num_rows > 0) {
    echo "Column 'step' already exists.\n";
    exit;
}

echo "Adding 'step' column...\n";
$sql = "ALTER TABLE chat_conversations ADD COLUMN step VARCHAR(32) DEFAULT 'welcome' AFTER message_count;";
if ($conn->query($sql)) {
    echo "Success: Column 'step' added.\n";
} else {
    echo "Error: " . $conn->error . "\n";
}

// Verify
$result = $conn->query("SHOW COLUMNS FROM chat_conversations LIKE 'step'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Verified: " . $row['Field'] . " - " . $row['Type'] . " - Default: " . $row['Default'] . "\n";
}