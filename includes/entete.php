<?php require_once((__DIR__ . "/../includes/connection.php")); ?>
<style type="text/css">

.menu{
	top: 163px;
    position: absolute;
    font-size:16px;
    background-color:#204e41;
    height: 55px;
    padding: 16px 30px;
    border-radius: 13px;
   
	}
	.menu-item{
		text-decoration-style: none;
		 color:rgba(255, 255, 255, 0.68);
padding: 0 11px;
-moz-transition-duration: 1s;
  -o-transition-duration: 1s;
  -webkit-transition-duration: 1s;
  transition-duration: 1s;
}
.menu-item:hover,.menu-item:active,.menu-item:visited{
background-color:#328f31;
padding-top: 18px;
    padding-bottom: 20px;
    text-decoration: none;
    color:#fff;

}
a.menu_item{
	text-decoration: none;
}
     
	.l-container{
	width: 1030px;
	
	z-index:5;
	position:relative;
}
	.background{
		
		height:242px;
		background: url("../images/b.png") 50% 30% repeat;

		-moz-background-size: cover;
  -o-background-size: cover;
  -webkit-background-size: cover;
  background-size: cover;
  position: relative;
		z-index:5px;
		position:relative;
 width:1200px;

	}
	.background:before{
		content:" ";
		background-color:rgba(60, 118, 61, 0.72);
		width:100%;
		height:242px;
		position:absolute;
	}
	.header-content{
		position:absolute;
		    top: -5px;
         left: 15px; 
          z-index: 5;
          width: 100%;
	}
	.header-img{
		position:absolute;
		right:0;
		top:67px;
	}
/*
.menu-item:before {
    content: '|';
    display: inline;
    padding: 0 11px;
     }

    .menu-item:first-child:before{
    	display:none;
    }
    */
.container{
	}
.row_{
	    margin-top: 40px;
}
.color{
	color:#7A9C4C;
}
.wrap-panier{
height:30px;
background-color: rgba(169, 68, 66, 0.78);
border-radius:8px;
width:200px;
}
.meta-panier{
	padding: 5px 22px;
	color:#fff;
	display:inline;
}
.bell-message{
	display:inline-block;
	position:relative;

}
.bell-message .fa-bell{
	color:white;
}
.bell-message .num-message{
	position:absolute;
	top: -8px;
    right: -10px;
    width: 17px;
    height: 17px;
    border-radius: 17px;
    color: white;
    background-color: #d40e0e;
    text-align: center;
}
</style>

<div class="background">

</div>
<div class="container">

<div class="header-content">
<div class="row">

<div class="col-md-3">

<div id="logo">
	<img src="../img/logo.png" />

</div>
</div>
<div class="row_">
<div class="col-md-6" >

</div>
<div class="col-md-2 wrap-panier" >
<?php
if(isset($_SESSION['ROLE_USER']) && $_SESSION['ROLE_USER']=='0'){
	?>
<div class="bell-message">
<div class="num-message">
	
<?php
 
$sql="select * from contact  ";
$res=mysqli_query($conn, $sql) or die(mysqli_error($conn));
$total_contact=mysqli_num_rows($res);
echo $total_contact;
?>
</div>

<a href="../contact.php"><i class="fa fa-bell" aria-hidden="true"></i></a></div>
<?php
}
?>
<div class="meta-panier">
<?php
/*
if(!(isset($_SESSION['panier']))){
$panier=array();*/

$req="select * from orders";
$rs=mysqli_query($conn, $req);
  $nombreTotal=mysqli_num_rows($rs);

echo '<a href="commander.php" style="color:white;"><i class="fa fa-shopping-basket color" aria-hidden="true"></i> '.$nombreTotal.' orders</a>';
/*}
else{
$panier=$_SESSION['panier'];
 echo '<i class="fa fa-shopping-basket color" aria-hidden="true"></i> '.@$_SESSION['quantite'].' KS /'.@$_SESSION['total'].' KC';
}
*/

?>
</div>
</div>
</div>
<div class="header-img">
<img src="../images/agrumes.png" width="370" height="320">
</div>
</div>
</div>
<nav class="menu">

<a href="home.php" class="menu-item is-active">Home</a>
<a href="wrapsite.php" class="menu-item">Wrap Images</a>
<a href="home.php?panier=1" class="menu-item">Panier</a>

<?php
 if(isset($_SESSION['ROLE_USER']) && $_SESSION['ROLE_USER']=='0'){ ?>
  <a href="GestionWorks.php" class="menu-item">Gestion Works</a>
  <a href="GestionGaleries.php" class="menu-item">Gestion Galeries</a>
<a href="GestionCategories.php" class="menu-item">Gestion Catégories</a>
<a href="GestionProduits.php" class="menu-item">Gestion Produits</a>
</nav>
<?php } ?>
</div>
</div>

</div>