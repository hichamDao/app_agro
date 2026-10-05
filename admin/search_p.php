<?php
/* Identifiants lus dans includes/connection.secret.php (jamais dans le code). */
require_once(__DIR__ . "/../includes/connection.php");

if(isset($_POST["Search"])){

	$connect = new PDO("mysql:host=" . $fm_config['host'] . "; dbname=" . $fm_config['db'], $fm_config['user'], $fm_config['pass']);

$search= $_POST["Search"];
$query = " 
SELECT * FROM produits where Designation like '$search'";

    $statement = $connect->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();
	foreach ($result as $row) {

		$id=$row["id_prod"];
	

      

	}

		
    
	echo"<script>window.location.href='https://foodmax-group.com/products_detail/$id/'</script>";
			
}
