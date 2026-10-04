<?php

if(isset($_POST["Search"])){

	$connect = new PDO("mysql:host=wm133.wedos.net; dbname=d150242_foodmax", "a150242_foodmax", "48HsUqeq");

$search= $_POST["Search"];
$query = " 
SELECT * FROM works where namew like '$search'";

    $statement = $connect->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();
	foreach ($result as $row) {

		$title=$row["namew"];
	
      $n= str_replace(" ","_", $title); 
      

	}

		
    
	echo"<script>window.location.href='https://foodmax-group.com/offers/$n/'</script>";
			
}
