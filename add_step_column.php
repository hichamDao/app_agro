<?php
require 'includes/connection.php';
$sql = "ALTER TABLE chat_conversations ADD COLUMN step VARCHAR(32) DEFAULT 'welcome' AFTER message_count;";
if ($conn->query($sql)) {
    echo "Column 'step' added successfully\n";
} else {
    echo "Error: " . $conn->error . "\n";
}