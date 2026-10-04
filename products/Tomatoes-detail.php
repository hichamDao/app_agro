<?php
require_once((__DIR__ . "/../includes/_header.php")); 
?>
 <?php
$req="select * from works where namew='Tomatoes'";
 $rs=mysqli_query($conn, $req);
 $work=mysqli_fetch_array($rs);
  if($work['state']=="no") 
  echo "<script> document.location.href='https://foodmax-group.com/offers/'</script>";
?>
<!DOCTYPE html>
<html lang="en-US">



<head>

<meta charset="utf-8">
<title>Products : <?php echo($work['namew'])?></title>
  <!-- base href local : desactive pour developpement -->
<link rel="stylesheet" type="text/css" href="../css/style.css">

<link rel="stylesheet" type="text/css" href="../css/phlox.css">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,600i,700,700i">
	<link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/css?family=Indie+Flower" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/fancybox/jquery.fancybox.css">
	<link rel="stylesheet" type="text/css" href="../css/fancybox/helpers/jquery.fancybox-thumbs.css" />
  <link rel="stylesheet" href="../css/jquery-ui.css" />
	<link rel="apple-touch-icon" sizes="180x180" href="../img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon/favicon-16x16.png">
    <link rel="manifest" href="../img/favicon/site.webmanifest">
    <link rel="mask-icon" href="../img/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#b91d47">
    <meta name="theme-color" content="#ffffff">
	<script type="text/javascript" src="../js/jquery.min.js"></script>
	<script type="text/javascript" src="../js/setting.js"></script>
   
   <style type="text/css">
  .background-detail-foto{
    background-position: 56% 86%;
        margin-left: 1px;
    background-size: cover;
    display: block;
    width: 107%;
    height: 448px;
   }
   .detail-cat {
    width: 460px;
    margin-bottom: 44px;
    -moz-transition-duration: 1s;
    -o-transition-duration: 1s;
    -webkit-transition-duration: 1s;
    transition-duration: 1s;
}
.section-detail-right {
    float: right;
    margin-top: 314px;
    width: 404px;
}
.detail-wrap-img {
    margin-top: 40px;
    margin-bottom: 24px;
    position: relative;
    /* width: 675px;*/
    display:flex;
    }
    .breadcrumb-wrap2 h2 {
        font-size: 62px;
    font-family: 'Raleway';
    color: #fff;
    line-height: 2;
    text-transform: uppercase;
}

.table-s tr:nth-of-type(even) {
    background-color: #f3f3f3;
}
.table-s tr{

  background-color: #fff;
  } 
.table tr td{
width: 289px;
  *border-bottom: solid;

}
.table tr:nth-of-type(even) {
    background-color: #f6753721;
}

.table tr{
  background-color: #ef9f7ad1;
  } 
   </style>

</head>

<?php 
include((__DIR__ . "/../includes/header-inc.php")); 
?>
	<?php
$myphoto=explode(",",$work['Photo']);

    ?>

	<div class="background-detail-foto" data-foto="<?php echo $myphoto[1]; ?>" data-name="<?php echo $work['namew']; ?>" >
<div class="breadcrumb-wrap2">

	<h2>
<?php echo($work['titlew']) ?>
	 </h2>
		
	 <ol class="breadcrumb">
	 	<li><a href="home/">Home</a></li>
      <li><a href="offers/">Offers</a></li>
	 	<li><?php echo($work['namew']) ?>

	 	</li>
	 </ol>

   
	 </div>
</div>
<!--  <div class="bassline-wrap">
    </div> -->
</div>



</header>
<div class="container">

<section class="section-detail-left">
	<div class="detail-produits">
		<h1 style="">Best <?php echo($work['namew']) ?></h1>

<?php
$idcat=$work['idcatw'];
$query="select * from categories where Code_cat=$idcat";
$rs=mysqli_query($conn, $query) or die(mysqli_error($conn));
$rows=mysqli_fetch_array($rs);
?>

     <table class="table-s" >

<tr>
  <td><b>Posted by : </b></td><td><?php echo $work['postedw']; ?></td><td><b>Category :</b></td><td><?php echo $rows['Nom_cat']; ?></td>
</tr>

 <tr>
  <td><b>Publish Date : </b></td><td><?php echo $work['datew']; ?></td><td><b>Last updated </b></td><td><?php echo $work['updatew']; ?></td>
</tr>


<tr>
  <td><b>Contact :</b></td><td>info@foodmax-group.com</td>
</tr>


</table>
 <h2>
<?php echo($work['titlew']) ?>
     </h2>
  

        


</div>

	
<br>
  <table class="table-sty" >

<tr>
<td><img src="../img/Tomatoes/plant-5784306.jpg" width="324" height="291"/> </td>

<td style="padding-left: 29px;
    width: 332px;
    display: block;"><span style="text-align: justify;">We make sure that all exported products are always of the highest quality and have all necessary certificates. Having the best interests of our clients at heart, we control the quality of our products to make sure that it complies with international standards.</span>
 
 <p>Modern farming technology is used to improve wide range of production.</p>

 <p>The most common types of equipment and machinery used on farms .</p></td>
</tr>

</table>
<br>
<br>
<table class="table" >

<tr>
  <td><b>Variety : </b></td><td>Babyplum Angelle, Cherry Tomatoes, Colored Tomatoes: Bamano and Kumato</td>
</tr>
 <tr>
  <td><b>Origin : </b></td><td>Morocco</td>
</tr>
<tr>
  <td><b>Packaging: </b></td><td>Loose 6kg, Loose 4kg, Triangle 250g, Rectangle 250g, Shaker 12x250g, Buckets 12x250g, Buckets 14x500g, Buckets 15x500g, Buckets 06x500g, Punnet 12x250g, Punnet 09x250g</td>
</tr>
<tr>
  <td><b>Quantity / PALLETS : </b></td><td>According to customers request</td>
</tr>
 <tr>
  <td><b>Trucks : </b></td><td>According to customers request</td>
</tr>
<tr>
  <td><b>PRODUCTION DATE :</b></td><td> OCTOBER -  MAY</td>
</tr>


</table>
<br>
<br>
<h3>Babyplum Angelle :</h3>
<table class="table-sty" >
<tr>
<td></td><td><img src="../img/Tomatoes/babyangelleshaker250g.jpg" width="220" height="240"/><br><span style="font-weight:bold;text-align:right;">Shaker 250g</span> </td><td> </td><td></td><td><img src="../img/Tomatoes/bucket500gangello.jpg" width="220" height="240"/><br><b>Bucket 500g</b> </td>
</tr>
</table>

<h3>Cherry Tomatoes :</h3>
<table class="table-sty" >
<tr>
<td> </td><td><img src="../img/Tomatoes/cherryshaker250g.jpg" width="220" height="240"/> <br><b>Shaker 250g </b></td><td></td><td><img src="../img/Tomatoes/bucket500gcherry.jpg" width="220" height="240"/><br><b>Bucket 500g </b> </td>
</tr>
</table>


<h3>Colored Tomatoes : Bamano and Kumato : </h3>
<table class="table-sty" >
<tr>
<td> </td><td><img src="../img/Tomatoes/bamanoandkumatoshaker250.jpg" width="220" height="240"/> <br><b>Shaker 250g </b></td><td></td><td><img src="../img/Tomatoes/bamanoandkumatobucket500g.jpg" width="220" height="240"/><br><b>Bucket 500g </b> </td>
</tr>
</table>

</div>


	
</div>
<section class="section-detail-right">
	   <div class="detail-search">
    <h1>Search</h1>
<form method="post" action="#" class="form-search" id="search__form">

  <input type="text" placeholder="Search offers..."  id="search_data"  name="Search" class="input-search" autocomplete="off">

</form>
<div id="reslut_search"></div>
  </div>
<div class="detail-cat">
	<h1>We are?</h1>

<p>
	<?php
$aboutw=explode("|",$work['aboutw']);

function str_replace_first($from, $to, $subject)
{
    $from = '/'.preg_quote($from, '/').'/';

    return preg_replace($from, $to, $subject, 1);
}
    ?>
<?php 
//$ab1=str_replace("/"," ",$aboutw['0']);
 
foreach($aboutw as $i =>$key) {
	?>
    <p><?php echo(empty($aboutw[$i])?"":$aboutw[$i]); ?></p><br>
    <?php
}

?>


</p>
<h3><b>Our Mission</b></h3>
To provide healthy and high quality products that meet the requirements of local and international customers.

<p>
  <h3>Social And Environmental Commitment</h3>
<ul>
  <li>
  Our commitment not to affect the environment and to provide sustainable resources.
</li>
<li>
  Our social commitment is reflected in the improvement of the well-being of our staff and in our support for local associations.

</li>
</ul>

</p>
</div>
<h1>CERTIFICATES :</h1>
<table class="table-sty" >
<tr>
<td><img src="../img/Tomatoes/cert.png" /> </td>
</tr>

</table>
<table class="table-sty" >

  <tr>

    <?php for($i=2;$i<3;$i++){ 

    ?>
<td>
    
     <a class="fancybox-thumbs" data-fancybox-group="thumbs" href="../imagesw/<?php echo $work['namew'].'/'.$myphoto[$i]; ?>">

        <img src="../imagesw/<?php echo $work['namew'].'/'.$myphoto[$i]; ?>" width="314" height="234">
        </a>
    
        </td>
<?php
}

?>


  </tr>
</table>






	</div>

	</div>


	
</div>

<?php
///if(isset($_GET['pict'])){
$req="select * from gallery order by id_Gal limit 4 ";
$rs=mysqli_query($conn, $req) or die(mysqli_error($conn));


        ?>
     
</section>
<div class="cb"></div>
</div>
<section>
<div class="bg-subscribe">
  
<div class="section-subscribe">

<div class="text-subscribe">

  <h1>Subscribe to us!</h1>
  <p>Enter Your email address for our mailing list to keep yourself update.</p>

  </div>
<div class="form-subscribe" >
  <form metod='POST' action="#" id="subscribe" >
    <input type="Email" placeholder="Email address" name="email"  class="input-subscribe">
    <input type="hidden" name="date" value="<?php echo date("j-n-Y H:i:s"); ?>">
    <button class="btn-subscribe" type="submit">Submit</button>
  </form>
 
</div>
</div>
</div>
 <div id="subscribe-post"></div>
</section>
<div class="row row-padded">

<div class="row-content">
     <div class="row-icon">
	 <i class="fa fa-leaf fa-3x icon-color"></i>
     </div>
     <div class="row-text">
	 <h2>Fresh</h2>
     <p>Always fresh.</p>
     </div>
     

     </div>

<div class="row-content">
    <div class="row-icon">
	<i class="fa fa-heartbeat fa-3x icon-color"></i>
    </div>
    <div class="row-text">
	<h2>Healthy</h2>
    <p>Keeps your family healthy.</p>
    </div>
    </div>

<div class="row-content">
    <div class="row-icon">
	<i class="fa fa-pagelines fa-3x icon-color"></i>
    </div>
    <div class="row-text">
	<h2>Eco</h2>
    <p>Carefully selected and checked before packing.</p>
    </div>
    </div>

<div class="row-content">
    <div class="row-icon">
	<i class="fa fa-heart fa-3x icon-color"></i>
    </div>
    <div class="row-text">
	<h2>Yammy</h2>
    <p>Easy to make yammy dishes.</p>
    </div> 
    </div>


</div>
<section>
<a id="scroll-to-top">
      <i class="fa fa-angle-up"></i>
    </a>
<footer class="footer background4">
  <div class="footer-content">
    ALL right reserves    |  © 2022 |    FoodMax website
  </div>
</footer>
</div>


<script type="text/javascript" src="../js/share.js"></script>
<script type="text/javascript" src="../js/sharemy.js"></script>
<script type="text/javascript" src="../css/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="../css/fancybox/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="../css/fancybox/helpers/jquery.fancybox-thumbs.js"></script>
<script type="text/javascript" src="../css/fancybox/jquery.mousewheel.pack.js"></script>
<script type="text/javascript" src="../js/jquery.malihu.PageScroll2id.js"></script>
<script type="text/javascript" src="../css/fullpages/jquery.fullpage.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script type="text/javascript" src="../js/search-ui.js"></script>
<script type="text/javascript" src="../js/app.js"></script>

<?php
if($_SERVER['REMOTE_ADDR'] != "160.177.3.200"){
?>
<?php require_once((__DIR__ . "/../includes/analyticstracking.php")); ?>
<?php 
}
?>
<script> 
var $buoop = {vs:{i:10,f:-4,o:-4,s:8,c:-4},api:4}; 
function $buo_f(){ 
 var e = document.createElement("script"); 
 e.src = "//browser-update.org/update.min.js"; 
 document.body.appendChild(e);
};
try {document.addEventListener("DOMContentLoaded", $buo_f,false)}
catch(e){window.attachEvent("onload", $buo_f)}
</script>
</body>
</html>
