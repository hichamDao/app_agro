<?php
session_start();
?>
<?php require_once((__DIR__ . "/../includes/connection.php")); ?>
<style type="text/css">
	
	@import url('../css/style.css');
</style>



<?php
if(isset($_POST['login']) && isset($_POST['pass'])){
$l=$_POST['login'];
$p=$_POST['pass'];
$ps=md5($p);
$req="select * from members where username='$l'";
$rs=mysqli_query($conn, $req)or die(mysqli_error($conn));


while($u=mysqli_fetch_assoc($rs)){
	if($ps == $u['password']){
$_SESSION['LOGIN']=$l;
$_SESSION['ID']=$u['id'];
//header("location:index.php");
}
else{
	echo "error";
}
}
}

 else if(isset($_POST['username']) && isset($_POST['password']))
{
?>

<?php

$username=addslashes($_POST['username']);
$pass=addslashes($_POST['password']);
$date=$_POST['date'];
$phone=$_POST['phone'];
$email=$_POST['email'];

if (strlen($pass) <= 3){
	?>

<i class="fal fa-frown"></i><span>Sorry you wrote a short password!</span>
	<?php
	}
	else{

$md5pass=md5($pass);
$req="insert into members(username,password,email,phone,date) values('".$username."','".$md5pass."','".$email."','".$phone."','".$date."')";
$rs=mysqli_query($conn, $req) or die(mysqli_error($conn));
$last_id = mysqli_insert_id($conn);
?>

<i class='fa fa-check'></i><span> ok!</span>

<?php

$_SESSION['LOGIN']=$username;
$_SESSION['ID']=$last_id;
?>


<?php

}

}


 ?>