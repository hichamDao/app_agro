<?php require_once(__DIR__ . "/../includes/_header.php");
?>
<style type="text/css">
@media (max-width:991px){
	.header-log{
		width: 769px;
	}
	.heartwish {
    position: absolute;
    top: 19px;
    left: 385px;
}
	}
.heartwish {
    position: absolute;
    top: 22px;
    left: 398px;

}

    .header-log {
    
    top: 172px;
    left: 31px;
    position: absolute;
    width: 1237px;
}

   a.logout{
  margin-left:25px;
}
.rat-recp{
  position: absolute;
    left: 567px;
    z-index: 980;
    top: 22px;
}

.rat-recp i{
  color:yellow;
}
</style>
<body id='url_div'>
<header class="header">
<div class="header-log ">
	<?php if(isset($_SESSION['LOGIN']) && isset($_SESSION['ID'])){ 
$idu=$_SESSION['ID'];

$req="select * from wishlist where iduser='$idu' ";

$rsRecipe=mysqli_query($conn, $req) or die (mysqli_error($conn));

$NumRows=mysqli_num_rows($rsRecipe);

if($NumRows!=0){
  ?>
  <div class="heartwish"><a href="wishlist.php?id=<?php echo $idu; ?>"><i class="fa fa-heart" aria-hidden="true" style="color:red;"></i> (<?php echo $NumRows; ?>) Wish list</a></div>
  <?php
}else{
?>
  <div class="heartwish"><a href="../wishlist.php"><i class="fa fa-heart-o" aria-hidden="true" style="color:red;"></i> Wish list</a></div>
  <?php

}
}
if(!isset($_SESSION['LOGIN']) && !isset($_SESSION['ID'])){
?>
<div class="heartwish"><a href="../wishlist.php"><i class="fa fa-heart-o" aria-hidden="true" style="color:red;"></i> Wish list</a></div>
<?php
}
?>
<?php
if(isset($_SESSION['LOGIN'])){
$idu=$_SESSION['ID'];
$sql ="SELECT * FROM scoreusers where iduser=$idu";
$result = mysqli_query($conn, $sql);
$NumRows=mysqli_num_rows($result);
?>

<div class="rat-recp"><i class="fa fa-star" aria-hidden="true"></i> <a href="#"> <?php echo $NumRows; ?> Rated Recipes</a></div>
   <?php
   }
   else{
    ?>
    <div class="rat-recp"><i class="fa fa-star" aria-hidden="true"></i> <a href="#"> Rated Recipes</a></div>
  
  <?php
   }
   ?> 
        <?php if(isset($_SESSION['LOGIN'])){ ?>
<i class="fa fa-user-circle-o" aria-hidden="true"></i> <?php echo($_SESSION['LOGIN']);
?>
    <a href="../logout.php" class="logout">Logout <i class="fa fa-sign-out" aria-hidden="true"></i></a>
<?php }
else{ ?>
	<?php include((__DIR__ . "/../includes/login.php")); ?>
	<?php
    }
     ?>
     
<i class="fa fa-envelope color" aria-hidden="true" style="padding-top: 27px;padding-left: 31px;"></i> <a href="../contact.php" class="menu-item disable">Contact</a> </div>
</div>
<header class="header">
<div class="header-social">
	<a href="https://fr.pinterest.com/freshka_group/" target="_blank"><i class="fa fa-pinterest-p fa-lg icon-pinterest"></i></a>
   <a href="https://www.instagram.com/healthy_food__tips/" target="_blank"> <i class="fa fa-instagram fa-lg icon-instagram"></i></a>
   <a href="https://twitter.com/freshka_group/" target="_blank"> <i class="fa fa-twitter fa-lg icon-twitter"></i></a>
   <a href="https://www.facebook.com/Freshka-1909763699259192/" target="_blank"> <i class="fa fa-facebook fa-lg icon-facebook"></i></a>
   <a href="https://dribbble.com/freshka_group/" target="_blank"> <i class="fa fa-dribbble fa-lg icon-dribbble"></i></a>
   

</div>

<div class="header-logo">
	<img src="../img/logof.png" width="230">
</div>
<div class="header-logo1" style="display:;">
	<img src="../img/logo.png">
</div>

<div class="cb"></div>

<div class="menu" >
	<a href="home/" class="menu-item disable">Home</a>
	<a href="about-us/" class="menu-item disable">About us</a>
	<a href="#" class="menu-item disable" id="menu-toggle">Products  <span> <i class="fa fa-angle-right"></i></span> </a>
	<ul id="menu-derouler" class="clearfix">
		<span class="h-line"></span>
		<a href="https://foodmax-group.com/products/7/Tomatoes/" class="menu-d-item ">Tomatoes</a>
		<span class="h-line"></span>
		<span class="h-line"></span>
	<a href="https://foodmax-group.com/products/3/Citrus/" class="menu-d-item"> Citrus</a>
		<span class="h-line"></span>
			<a href="https://foodmax-group.com/products/8/Peppers/" class="menu-d-item"> Peppers</a>
		<span class="h-line"></span>
		<a href="products/14/Dryfruits/" class="menu-d-item ">Dry Fruits</a>
		<span class="h-line"></span>
	 <a href="products/" class="menu-d-item ">ALL</a>
		
	</ul>
	<a href="../recipes/recipes.php" class="menu-item">Recipes</a>
	<a href="../gallery.php" class="menu-item">Gallery</a>
	<?php /* panier desactive */ ?>
	<a href="../contact.php" class="menu-item disable">Contact</a>
<a href="#" id="searchtoggl"><i class="fa fa-search fa-lg"></i></a>
<div id="searchbar" class="clearfix">
  <form id="searchform" method="get" action="products/" >
    <button type="submit" id="searchsubmit" class="fa fa-search fa-4x"></button>
    <input type="search" name="motCle" id="s" placeholder="Keywords..." autocomplete="off">
    
  </form>
</div>
</div>

<div class="cb"></div>
