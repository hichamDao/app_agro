<?php



if(isset($_GET["term"]))
{
	$connect = new PDO("mysql:host=wm133.wedos.net; dbname=d150242_foodmax", "a150242_foodmax", "48HsUqeq");

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