<?php
require 'includes/connection.php';
$r = $conn->query('DESCRIBE contact');
while($row = $r->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}