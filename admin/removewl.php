<?php
require_once((__DIR__ . "/../includes/_header.php"));
?>
<?php
if(isset($_GET["id"])){
$id=$_GET["id"];
$idu=$_SESSION['ID'];
$req="delete from wishlist where iduser='$idu' and idprod='$id'";
mysqli_query($conn, $req) or die(mysqli_error($conn));
//header("location:wishlist.php?id=$idu");
?>
<script> document.location.href="wishlist.php?id=<?php echo $idu; ?>"</script>

<?php
}
?>