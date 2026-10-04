<?php
require_once((__DIR__ . "/../includes/_header.php")); 
?>
 <?php

$req="select * from works where namew='canned fish'";
 $rs=mysqli_query($conn, $req);
 $work=mysqli_fetch_array($rs);
  if($work['state']=="no") 
  echo "<script> document.location.href='https://foodmax-group.com/offers/'</script>";
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
<title>Products : Canned Fish</title>
<meta charset="utf-8">
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
.detail-produits p {
    font-size: 16px;
    margin-bottom: -21px;
}
.section-detail-right {
   float: right;
   *margin-top: 569px;
   width: 551px;
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
        .table-sty {
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 0.9em;
    font-family: sans-serif;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        width: 511px;
}
.p-left-sty {
  position: relative;
    left: 211px;
}
.sty-sty{
  position:relative;
}
.sty-sty .title{
  color: #ffffff;
    text-align: left;
    font-size: 31px;
    position: absolute;
    top: 109px;
    left: 19px;
    background-color: #f67537;
    /* padding-left: 19px; */
    padding: 38px 15px;
}
.title-sty{
    *background-color: #009879;
    color: #009879;
    text-align: left;
    font-size: 21px;
    font-weight: bold;
}

.table-sty td {
    padding: 12px 12px;
}


.table-sty th{
  display: block;
    width: 321px;
}
.table-sty tbody tr {
    border-bottom: 1px solid #dddddd;
}

.table-sty tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.table-sty tbody tr:last-of-type {
    border-bottom: 2px solid #009879;
}


 .table-sty-1 {
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 14px;
    font-family: sans-serif;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    width: 1095px;
}
.table-sty-1 thead tr {
    background-color: #980000;
    color: #ffffff;
    text-align: left;
    font-size: 21px;
}
.table-sty-1 th,
.table-sty-1 td {
    padding: 12px 12px;
}
.table-sty-1 tbody tr {
    border-bottom: 1px solid #dddddd;
}

.table-sty-1 tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.table-sty-1 tbody tr:last-of-type {
    border-bottom: 2px solid #980000;
}
@media (max-width:991px){
.table-sty-1 {
    margin: 120px 0;
    }
  }
  .row.row-padded {
    margin: 305px 110px 0;
}
.footer {
    overflow: hidden;
    zoom: 1;
    padding: -121px 0;
    padding: 25px 0;
}
.table-s tr:nth-of-type(even) {
 background-color: #f3f3f3;
}
.table-s tr{

  background-color: #fff;
  } 

.detail-picture {
width: 379px;
}

   </style>


</head>

<?php 
include((__DIR__ . "/../includes/header-inc.php")); 
?>
  <?php
$myphoto=explode(",",$work['Photo']);
  $n= str_replace(" ","_", $work['namew']); 
    ?>

  <div class="background-detail-foto" data-foto="<?php echo $myphoto[1]; ?>" data-name="<?php echo $n; ?>" >
<div class="breadcrumb-wrap2">

  <h2>
<?php
echo $work['titlew'];
?>
   </h2>
    
   <ol class="breadcrumb">
    <li><a href="home/">Home</a></li>
      <li><a href="offers/">Offers</a></li>
    <li><?php echo $work['namew']; ?>

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

    <h1 style=""><?php echo $work['namew']; ?></h1>

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
CANNED SARDINES, MACKEREL AND TUNA
     </h2>
  


</div>
<div class="sty-sty">
  <img src="../img/fish_sardine.png" width="512" />
  <p class="title">SARDINES IN VEGETABLE OIL </p>
  </div> 
<table class="table-sty" >

  <tr><td><b>DESCRIPTION </b> </td><td>



    Sardines in vegetable oil, tinplate ¼ club can with easy open lid, NW: 125 g.
  
    
  </td>
    </tr>
  <tr>
<td><b>INGREDIENTS </b></td><td>Sardines, vegetable oil, salt. </td><td></td><td></td>
  </tr>

</table>
<p class="title-sty" >Nutritional values for 100 g of product Drained</p>
 <table class="table-sty" >
<tr>
<td><b>Calories</b></td><td>217.8 Kcal/ 910,4 KJ</td><td> </td><td></td>
</tr>
<tr>
<td><b>Protein </b></td><td>  24,1 g  </td><td> </td>
<td></td>
</tr>
<td><b>Carbohydrates</b></td><td>  0,2 g   </td><td> </td>
<td></td>
</tr>
<td><b>Fat </b></td><td>  13,4 g  </td><td> </td>
<td></td>
</tr>
   </table>
 
<p class="title-sty">Specification</p>
   <table class="table-sty" >
<tr>
<td><b>NET WEIGHT
</b></td><td>125g/ AE </td><td>Lithographié<br> 125g 
</td>
</tr>
<tr>
<td><b>DRAINED WEIGHT </b></td><td>90g  </td><td> 90g </td>
<td></td>
</tr>

</table>
  <p class="title-sty">Related products</p>
   <table class="table-sty" >
<tr>
<td>Sardines in vegetable oil, Sardines in tomato sauce, Sardines in hot oil, Sardine in olive oil, Sardines balls in vegetable oil, Sardines balls in tomato sauce, Tajine sardines with vegetables.
</td><td></td><td>
</td>

</tr>

</table>

<div class="cb"></div>
 <div class="sty-sty">
  <img src="../img/mackerel_fish.png" width="512" />
  <p class="title">MACKEREL FILLETS IN VEGETABLE OIL</p>
</div>
<table class="table-sty" >

  <tr><td><b>DESCRIPTION </b> </td><td>Mackerel fillets in vegetable oil, tinplate ¼ club can with easy open lid, NW : 125 g.
    
  </td>
    </tr>
  <tr>
<td><b>INGREDIENTS </b></td><td>Mackerel, vegetable oil, salt </td><td></td><td></td>
  </tr>
</table>


<p class="title-sty">Nutritional values for 100 g of product Drained</p>
<table class="table-sty" >
<tr>
<td><b>Calories</b></td><td>216.7 Kcal/ 905.8 KJ  </td><td> </td><td></td>
</tr>
<tr>
<td><b>Protein </b></td><td>  26.9 g     </td><td> </td>
<td></td>
</tr>
<td><b>Carbohydrates</b></td><td>  0,5 g   </td><td> </td>
<td></td>
</tr>
<td><b>Fat </b></td><td>  11,9 g  </td><td> </td>
<td></td>
</tr>
  </table>
 <p class="title-sty">Specification</p>
<table class="table-sty" > 
<tr>
<td><b>NET WEIGHT
</b></td><td>RO 2000  </td>
</tr>
<tr>
<td><b>DRAINED WEIGHT </b></td><td>1 500g   </td>
<td></td>
</tr>

</table>
 <p class="title-sty">Related products</p>
   <table class="table-sty" >
<tr>
<td>Mackerel fillets in vegetable oil, Mackerel fillets in tomato sauce, Mackerel fillets in olive oil, Whole mackerel natural, Mackerel fillets in vegetable oil.
</td><td></td><td>
</td>

</tr>

</table>
<div class="">
<div class="sty-sty">
<img src="../img/tuna_fish.png" width="512" />
<p class="title">WHOLE TUNA IN VEGETABLE OIL<p>
</div>
<table class="table-sty" >

  <tr><td><b>DESCRIPTION </b> </td><td>Whole tuna in vegetable oil, tinplate can with easy open lid, NW : 80g/ 3x80g/ tinplate 1,7 Kg with normal lid.
  
    
  </td>
    </tr>
  <tr>
<td><b>INGREDIENTS </b></td><td>Tuna, vegetable oil, salt. </td><td></td><td></td>
  </tr>

<tr>
  </table>
  <p class="title-sty">Nutritional values for 100 g of product Drained</p>
<table class="table-sty" >
  <tr>
<td><b>Calories</b></td><td>257Kcal/ 1074KJ    </td><td> </td><td></td>
</tr>
<tr>
<td><b>Protein </b></td><td>  26.9 g     </td><td> </td>
<td></td>
</tr>
<td><b>Carbohydrates</b></td><td>  0.1 g       </td><td> </td>
<td></td>
</tr>
<td><b>Fat </b></td><td>  18.2 g  </td><td> </td>
<td></td>
</tr>
  </table>
<p class="title-sty">Specification</p>
  <table class="table-sty" >
<tr>
<td><b>NET WEIGHT
</b></td><td>80g   </td><td>3x80g    </td><td>1700g   </td>
</tr>
<tr>
<td><b>DRAINED WEIGHT </b></td><td>52g    </td><td>156g    </td><td>1400g    </td>
<td></td>
</tr>

</table>
 <p class="title-sty">Related products</p>
   <table class="table-sty" >
<tr>
<td>Whole tuna in vegetable oil, Tuna with tomato sauce, Whole tuna in olive oil,Whole tuna natural, Tuna in hot sauce, Tuna fillets vegetable oil, Tuna fillets of vegetable oil and lemon.
</td><td></td><td>
</td>

</tr>
</table>

  
</section>

<div class="section-detail-right">
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

<div class="detail-picture">
  <h1>Pictures</h1> <span><a href="gallery"></a></span>

<div class="cb"></div>
<div class="pict">

      <a  id="effect-zone" href="../img/canned_fish/canned-food-fish.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/canned_fish/canned-food-fish.jpg" width="116" height="141">
  </a>
</div>

<div class="pict">

      <a  id="effect-zone" href="../img/canned_fish/tuna-in-oil.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/canned_fish/tuna-in-oil.jpg" width="116" height="141">
  </a>
</div>
<div class="pict">

      <a  id="effect-zone" href="../img/canned_fish/canned-makerel.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/canned_fish/canned-makerel.jpg" width="116" height="141">
  </a>
</div>

<div class="pict">

      <a  id="effect-zone" href="../img/canned_fish/canned-sardines.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/canned_fish/canned-sardines.jpg" width="116" height="141">
  </a>
</div>
<div class="pict">

      <a  id="effect-zone" href="../img/canned_fish/tuna-chunk-in-brine.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/canned_fish/tuna-chunk-in-brine.jpg" width="116" height="141">
  </a>
</div>
<div class="pict">

      <a  id="effect-zone" href="../img/canned_fish/tuna.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/canned_fish/tuna.jpg" width="116" height="141">
  </a>
</div>


     </div>

</div>


</div>

  
<div class="cb"></div>

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
    ALL right reserves    |  © 2022  |    FoodMax website
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
