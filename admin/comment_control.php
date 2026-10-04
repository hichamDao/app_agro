<style type="text/css">
	
	@import url('../css/style.css');
</style>

<?php require_once((__DIR__ . "/../includes/connection.php")); ?>



<?php 

if(isset($_POST['name']) && isset($_POST['message']))
{
?>

<?php

$name=addslashes($_POST['name']);
$email=addslashes($_POST['email']);
$date=$_POST['date'];
$phone=$_POST['phone'];
$idr=intval($_POST['idr']);
$message=$_POST['message'];

if (strlen($_POST['message']) <= 5){
	?>
	<div class="check-message-alert" id="message">
<a href="#" id="hide-message"><div class="close-message"><i class="fa fa-times" aria-hidden="true"></i></div></a>
<i class="fal fa-frown"></i><span>Sorry you wrote a short message!</span>
</div>
	<?php
	}
	else{

$req="insert into comments(name,email,phone,idr,message,date) values('".$name."','".$email."','".$phone."','".$idr."','".$message."','".$date."')";
$rs=mysqli_query($conn, $req) or die(mysqli_error($conn));
?>
<div class="check-message" id="message">
<a href="#" id="hide-message"><div class="close-message"><i class="fa fa-times" aria-hidden="true"></i></div></a>
<i class='fa fa-check'></i><span> Your comment has been sent successfully!</span>
</div>
<?php


}
}


 ?>