<?php
require 'includes/connection.php';
$result = $conn->query("DESCRIBE chat_conversations");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}