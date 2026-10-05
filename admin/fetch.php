<?php
/* Identifiants lus dans includes/connection.secret.php (jamais dans le code). */
require_once(__DIR__ . "/../includes/connection.php");



if(isset($_GET["term"]))
{
	$connect = new PDO("mysql:host=" . $fm_config['host'] . "; dbname=" . $fm_config['db'], $fm_config['user'], $fm_config['pass']);

	$query = "
	SELECT * FROM works
	WHERE namew LIKE '%".$_GET["term"]."%' 
	and state='selected' ORDER BY namew ASC
	";

	$statement = $connect->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	$total_row = $statement->rowCount();

	$output = array();
	if($total_row > 0)
	{
		foreach($result as $row)
		{
 $n= str_replace(" ","_", $row['namew']); 
         $myphoto=explode(",",$row['Photo']);

			$temp_array = array();
			$temp_array['value'] = $n;
			$temp_array['label'] = '<img src="../imagesw/'.$n.'/'.$myphoto[0].'" width="100"  ><span>'.$row['namew'].'</span>';
			$output[] = $temp_array;
		}
	}
	else
	{
		$output['value'] = '';
		$output['label'] = 'No Record Found';
	}

	echo json_encode($output);
}

?>