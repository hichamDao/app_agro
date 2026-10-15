<?php
require 'includes/connection.php';

header('Content-Type: text/plain; charset=utf-8');

echo "Adding city and country columns to chat_conversations...\n";

$conn->query("ALTER TABLE chat_conversations ADD COLUMN city VARCHAR(100) DEFAULT NULL AFTER ip_address");
echo "city column: " . ($conn->error ?: "OK") . "\n";

$conn->query("ALTER TABLE chat_conversations ADD COLUMN country VARCHAR(100) DEFAULT NULL AFTER city");
echo "country column: " . ($conn->error ?: "OK") . "\n";

$conn->query("ALTER TABLE chat_conversations ADD COLUMN step VARCHAR(32) DEFAULT 'welcome' AFTER message_count");
echo "step column: " . ($conn->error ?: "OK") . "\n";

echo "\nVerifying...\n";
$result = $conn->query("SHOW COLUMNS FROM chat_conversations");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}