<?php
require_once((__DIR__ . "/../includes/_header.php")); 
?>
 <?php
   if (isset($_GET['w'])){
$idwork=addslashes($_GET['w']);
$req="select * from works where idw=$idwork";
 $rs=mysqli_query($conn, $req);
 $work=mysqli_fetch_array($rs);
?>
<!DOCTYPE html>
<html lang="en-US">


<head>
<title>Products : <?php echo($work['namew'])?></title>
<meta charset="utf-8">

<title>FoodMax  detail</title>
  <!-- base href local : desactive pour developpement -->
<link rel="stylesheet" type="text/css" href="../css/style.css">

<link rel="stylesheet" type="text/css" href="../css/phlox.css">
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,600i,700,700i">
  <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css?family=Indie+Flower" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/fancybox/jquery.fancybox.css">
  <link rel="stylesheet" type="text/css" href="../css/fancybox/helpers/jquery.fancybox-thumbs.css" />
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
    margin-top: 314px;
    width: 567px;
}
.detail-wrap-img {
    margin-top: 40px;
    margin-bottom: 24px;
    position: relative;
    /* width: 675px;*/
    display:flex;
    }
    .breadcrumb-wrap2 h2 {
    font-size: 74px;
    font-family: 'Pacifico', cursive;
    color: #fff;
    line-height: 1;
}
        .table-sty {
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 0.9em;
    font-family: sans-serif;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    width: 487px;
}
.table-sty thead tr {
    background-color: #009879;
    color: #ffffff;
    text-align: left;
    font-size: 21px;
}
.table-sty th,
.table-sty td {
    padding: 12px 12px;
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
    *: ;
    zoom: 1;
    padding: -121px 0;
    padding: 25px 0;
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
   <?php }
?>
   
   </div>
</div>
<!--  <div class="bassline-wrap">
    </div> -->
</div>



</header>
<div class="container">

<section class="section-detail-left">
  <div class="detail-produits">
    <h1>Best <?php echo($work['namew']) ?></h1>
 <h2>
<?php echo($work['titlew']) ?>
     </h2>
  <?php

if(isset($_GET['name']) and $_GET['name']==="Olives"){

?>

<table class="table-sty" >
    <thead>
<tr>
<th></th><th>Green olives in brine </th><th></th><th></th>
</tr>
</thead>
<tbody>
  <tr><td></td><td><img src="../img/olives/Green-olives.jpg" width="190" height="210">
  
    
  </td>
    </tr>
  <tr>
<td><b>INGREDIENTS </b></td><td>green olives, water, salt, Citric acid. </td><td></td><td></td>
  </tr>
<tr><td><b>STORAGE</b> </td><td>max 10° C</td>
</tr>
<tr>
  <thead>
<tr><th></th><th>Bucket 13 L</th><th></th><th></th> </tr>
  </thead>
<td><b>PNE/NDW</b></td><td>7 Kg</td><td> </td><td></td>
</tr>
<tr>
<td><b>Paletization </b></td><td>66 buckets / Pal </td><td> </td>
<td></td>
</tr>
   <thead>
<tr>
<th></th><th>Bucket 3.8 L</th><th></th><th></th>
</tr>
</thead>
<tbody>
<tr>
<td><b>PNE/NDW</b></td><td>2.5 Kg</td><td> </td>
</tr>
<tr>
<td><b>Paletization </b></td><td>216/pal whether 54 cartons/Pal  </td><td> </td>
<td></td>
</tr>
</tbody>

</table>

<!--     end t1> -->
<table class="table-sty" style="">
    <thead>
<tr>
<th></th><th>Black olives in brine </th><th></th><th></th>
</tr>
</thead>
<tbody>
  <tr><td></td><td><img src="../img/olives/Black-olives.jpg" width="190" height="210">
    
  </td>
    <br></tr>
  <tr>
<td><b>INGREDIENTS </b></td><td>black olives, salt, Vegetable oil. </td><td></td><td></td>
  </tr>
<tr><td><b>STORAGE</b> </td><td>max 10° C</td>
</tr>
<tr>
  <thead>
<tr><th></th><th>Bucket 13 L</th><th></th><th></th> </tr>
  </thead>
<td><b>PNE/NDW</b></td><td>10 Kg</td><td> </td><td></td>
</tr>
<tr>
<td><b>Paletization </b></td><td>66 buckets / Pal </td><td> </td>
<td></td>
</tr>
   <thead>
<tr>
<th></th><th>Bucket 3.8 L</th><th></th><th></th>
</tr>
</thead>
<tbody>
<tr>
<td><b>PNE/NDW</b></td><td>2.75 Kg </td><td> </td>
</tr>
<tr>
<td><b>Paletization </b></td><td>216/pal whether 54 cartons/Pal  </td><td> </td>
<td></td>
</tr>
</tbody>

</table>
<!--     end t2> -->
<table class="table-sty" style="">
    <thead>
<tr>
<th></th><th>Violet olives in brine </th><th></th><th></th>
</tr>
</thead>
<tbody>
  <tr><td></td><td><img src="../img/olives/Violet-olives.jpg" width="190" height="210">
    
  </td>
    <br></tr>
  <tr>
<td><b>INGREDIENTS </b></td><td>violet olives, water, salt, Citric acid. </td><td></td><td></td>
  </tr>
<tr><td><b>STORAGE</b> </td><td>max 10° C</td>
</tr>
<tr>
  <thead>
<tr><th></th><th>Bucket 13 L</th><th></th><th></th> </tr>
  </thead>
<td><b>PNE/NDW</b></td><td>9 Kg</td><td> </td><td></td>
</tr>
<tr>
<td><b>Paletization </b></td><td>66 buckets / Pal </td><td> </td>
<td></td>
</tr>
   <thead>
<tr>
<th></th><th>Bucket 3.8 L</th><th></th><th></th>
</tr>
</thead>
<tbody>
<tr>
<td><b>PNE/NDW</b></td><td>2.5 Kg </td><td> </td>
</tr>
<tr>
<td><b>Paletization </b></td><td>216/pal whether 54 cartons/Pal  </td><td> </td>
<td></td>
</tr>
</tbody>

</table>
<?php


}

?>


        <?php

if(isset($_GET['name']) and $_GET['name']==="Fish"){

?>
   
<div class="detail-wrap-imgs">
<?php for($i=2;$i<4;$i++){ 

    ?>

    <div class="m-<?php echo $i-1; ?>">
     <a class="fancybox-thumbs" data-fancybox-group="thumbs" href="../imagesw/<?php echo $work['namew'].'/'.$myphoto[$i]; ?>">

        <img src="../imagesw/<?php echo $work['namew'].'/'.$myphoto[$i]; ?>" width="246" height="190">
        </a>
        </div>
        
<?php
}

?>


</div>
<table class="table-sty">
    <thead>
<tr>
<th></th><th>Frozen sardines whole Round or H/G+T</th><th></th><th></th>
</tr>
</thead>
<tbody>
<tr>
<td><b>Specification</b></td><td>WR : Whole Round</td><td>HGT : Headed, Gutted & Tail-off  </td>
</tr>
<tr>
<td><b>Size</b></td><td>S-9+cm(10-14 pcs / kg) <br>M-14+cm(8-10 pcs / kg)<br> L-18+cm(6-8 pcs / kg) </td><td>10,5+cm(10-12 pcs / kg) </td>
<td></td>
</tr>
<tr>
<td><b>Net  Weight </b> </td><td>Cartons 20 kgs(21 kgs Net) </td>
</tr>
</tbody>

</table>
<div class="detail-wrap-imgs">
<?php for($i=3;$i<5;$i++){ 

    ?>

    <div class="m-<?php echo $i-1; ?>">
     <a class="fancybox-thumbs" data-fancybox-group="thumbs" href="../imagesw/<?php echo $work['namew'].'/'.$myphoto[$i]; ?>">

        <img src="../imagesw/<?php echo $work['namew'].'/'.$myphoto[$i]; ?>" width="246" height="190">
        </a>
        </div>
        
<?php
}

?>


</div>

<table class="table-sty" >
    <thead >
<tr style="background-color:#980042">
<th></th><th>Frozen Mackerel whole Round or H/G+T </th><th></th><th></th>
</tr>
</thead>
<tbody>
<tr>
<td><b>Specification</b> </td><td>WR : Whole Round</td><td>HGT : Headed, Gutted & Tail-off  </td>
</tr>
<tr>
<td><b>Size</b></td><td>S-15+cm(7-9 pcs / kg) <br> M-25+cm(4-6 pcs / kg) <br>L-30+cm(1-3 pcs / kg)  </td><td>S-10+cm(8-10 pcs / kg) <br> M-14+cm(5-7 pcs / kg) <br>L-16+cm(3-5 pcs / kg)  </td><td></td>

</tr>
<tr>
<td><b>Net  Weight</b> </td><td>Cartons 20 kgs(21 kgs Net) </td>
</tr >
</tbody style= "border-bottom: 1px solid #980042;">

</table>
<?php
$descw=explode("|",$work['descriptionw']);

    ?>
<?php 
//$ab1=str_replace("/"," ",$aboutw['0']);
 
foreach($descw as $i =>$key) {
    ?>
   <p><b><?php echo(empty($descw[$i])?"":$descw[$i]); ?></b></p><br>
    <?php
}

?>
<?php
}
else{
?>  

    <div class="detail-img">

  
    <a class="fancybox-thumbs" data-fancybox-group="thumbs" href="../imagesw/<?php echo $work['namew'].'/'.$myphoto[2]; ?>">
  
     <img src="../imagesw/<?php echo $work['namew'].'/'.$myphoto[2]; ?>" width="570" height="340">

     </a>
    </div>
    <?php
$descw=explode("|",$work['descriptionw']);

    ?>
<?php 
//$ab1=str_replace("/"," ",$aboutw['0']);
 
foreach($descw as $i =>$key) {
  ?>
    <p><?php echo(empty($descw[$i])?"":$descw[$i]); ?></p><br>
    <?php
}

?>

    <?php
}


        ?>
        <?php
if(isset($_GET['name']) and $_GET['name']==="Citrus"){
  ?>
  <h2>Orange, Lemon , Clementine</h2>
<table class="table-sty-1" >
    <thead>
<tr style="font-size: 16px;">
<th>Product Name</th><th>Origin</th><th>Calibration Code </th><th>Diameter</th><th>Average Weight fruit / G </th><th>Average Number of fruits / KG </th>
</tr>
</thead>
<tbody>
<tr>
  <td><b>Orange</b></td><td>Morocco</td><td>1 </td><td>87 -100 mm </td><td>400 - 450 g </td><td>2 -3 </td>
</tr>
<tr>
  <td><b>Orange</b></td><td>Morocco</td><td>2 </td><td>84 -96 mm  </td><td>350 -400 g  </td><td>3 </td>
</tr>
<tr>
  <td><b>Orange</b></td><td>Morocco</td><td>3 </td><td>81 -92 mm  </td><td>250 -350 g  </td><td>3 -4 </td>
</tr>
<tr>
  <td><b>Orange</b></td><td>Morocco</td><td>4 </td><td>77 -88 mm  </td><td>200 -250 g  </td><td>4- 5  </td>
</tr>
<tr>
  <td><b>Orange</b></td><td>Morocco</td><td>5 </td><td>73 -84 mm  </td><td>200 g  </td><td>5- 6  </td>
</tr>
<tr>
  <td><b>Orange</b></td><td>Morocco</td><td>6 </td><td>70 -80 mm  </td><td>150 -200 g  </td><td>6- 7  </td>
</tr>
<tr>
  <td><b>Orange</b></td><td>Morocco</td><td>7 </td><td>67 -76 mm  </td><td>125 -150 g  </td><td>7- 8  </td>
</tr>
<!-- Lemons tr -->
<tr style="background-color:#f7ff00">
  <td ><b> Lemon</b></td><td>Morocco</td><td>1</td><td>72-83 mm   </td><td>  230 - 250 g   </td><td>4  </td>
</tr>
<tr>
  <td><b>Lemon</b></td><td>Morocco</td><td>2 </td><td>68-78 mm   </td><td>  190 - 210 g   </td><td>5 </td>
</tr>
<tr>
  <td><b>Lemon</b></td><td>Morocco</td><td>3 </td><td>63-72 mm   </td><td>  170 - 200 g   </td><td>6  </td>
</tr>
<tr>
  <td><b>Lemon</b></td><td>Morocco</td><td>4 </td><td>58-67 mm   </td><td>  125 - 170 g   </td><td>6- 8  </td>
</tr>
<tr>
  <td><b>Lemon</b></td><td>Morocco</td><td>5 </td><td>53-62 mm   </td><td>  100 - 120 g  </td><td>9- 10  </td>
</tr>
<tr>
  <td><b>Lemon</b></td><td>Morocco</td><td>6 </td><td>48-57 mm   </td><td> 70 - 95 g  </td><td> 11- 14  </td>
</tr>
<tr>
  <td><b>Lemon</b></td><td>Morocco</td><td>7 </td><td>45-52 mm   </td><td>  < 70 g  </td><td>  > 14   </td>
</tr>
<!-- Clementine tr -->
<tr style="background-color:#f67537;">
  <td><b>Clementine</b></td><td>Morocco</td><td>10 </td><td>35 - 42 mm    </td><td>  28 - 29 g   </td><td>  34 - 36    </td>
</tr>
<tr>
  <td><b>Clementine</b></td><td>Morocco</td><td>9 </td><td>37 - 44 mm    </td><td>  30 -32 g   </td><td>  31 - 33    </td>
</tr>
<tr>
  <td><b>Clementine</b></td><td>Morocco</td><td>8 </td><td>39 - 46 mm    </td><td>  33 -36 g   </td><td>  28 - 30    </td>
</tr>
<tr>
  <td><b>Clementine</b></td><td>Morocco</td><td>7 </td><td>41 - 48 mm    </td><td>  37 - 40 g   </td><td>  25 - 27    </td>
</tr>
<tr>
  <td><b>Clementine</b></td><td>Morocco</td><td>6 </td><td>43 - 52 mm    </td><td> 41 -46 g   </td><td>  22 - 24    </td>
</tr>
<tr>
  <td><b>Clementine</b></td><td>Morocco</td><td>5 </td><td>46 - 56 mm   </td><td>  47 - 53 g   </td><td>  19 - 21    </td>
</tr>
<tr>
  <td><b>Clementine</b></td><td>Morocco</td><td>4 </td><td>50 - 60 mm   </td><td>  54 - 63 g   </td><td>  16 - 18    </td>
</tr>
<tr>
  <td><b>Clementine</b></td><td>Morocco</td><td>3 </td><td>54 - 64 mm    </td><td>  64 - 72 g   </td><td>  14 - 15    </td>
</tr>
<tr>
  <td><b>Clementine</b></td><td>Morocco</td><td>2 </td><td>58 - 69 mm    </td><td>  73 - 83 g   </td><td>  12 - 13    </td>
</tr>
<tr>
  <td><b>Clementine</b></td><td>Morocco</td><td>1 </td><td> > 63 mm    </td><td>  > 83 g   </td><td>  < 12    </td>
</tr>

</tbody>

</table>
<?php

}
?>
  </div>
  
</div>
<section class="section-detail-right">
  
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
</div>
<?php if($_GET['name']!="Fish"){ ?>
<div class="detail-wrap-img">
<?php for($i=3;$i<count($myphoto);$i++){ 

  ?>

  <div class="m-<?php echo $i-1; ?>">
   <a class="fancybox-thumbs" data-fancybox-group="thumbs" href="../imagesw/<?php echo $work['namew'].'/'.$myphoto[$i]; ?>">

    <img src="../imagesw/<?php echo $work['namew'].'/'.$myphoto[$i]; ?>" width="309" height="190">
    </a>
    </div>
    
<?php
}

?>


</div>
<?php
}
else{



}
?>
<?php
if(isset($_GET['name']) and $_GET['name']==="Olives"){
  ?>
<h1 style="font-family: 'Pacifico', cursive;
    font-size: 37px;">Favored Olives </h1>
<table  class="table-sty">
<tr>
  <tr><td><img src="../img/olives/Andalusion.jpg" width="150" height="180"></td><td><b>Andalusion olives sauce in brine.</b> </td></tr>
    <tr><td><img src="../img/olives/kemia.jpg" width="150" height="160"></td><td><b>Olives kemia in brine </b> </td></tr>
    <tr><td><img src="../img/olives/lemon.jpg" width="150" height="160"></td><td><b>Olives with Lemon</b>  </td></tr>
    <tr><td><img src="../img/olives/Tunisian.jpg" width="150" height="160"></td><td><b>Olives Tunisian</b>  </td></tr>
    <tr><td><img src="../img/olives/Tahitian.jpg" width="150" height="160"></td><td><b>Tahitian Olives in brine</b>  </td></tr>
    <tr><td><img src="../img/olives/Provencal.jpg" width="150" height="160"></td><td><b>Provencal Olives in brine</b>  </td></tr>
    <tr><td><img src="../img/olives/Spiced-Cocktail.jpg" width="150" height="160"></td><td><b>Spiced Cocktail Olives in brine</b> </td></tr>
    <tr><td><img src="../img/olives/Sigalou.jpg" width="150" height="160"></td><td><b>Sigalou Olives in brine</b> </td></tr>
    <tr><td><img src="../img/olives/garlic.jpg" width="150" height="160"></td><td><b>Olives with garlic in brine </b> </td></tr>
     <tr><td><img src="../img/olives/Moroccan.jpg" width="150" height="160"></td><td><b>Moroccan Olives Light in brine  </b> </td></tr>
     <tr><td><img src="../img/olives/Barbecue.jpg" width="150" height="180"></td><td><b>Olives Barbecue in brine </b>  </td></tr>
    <tr><td><img src="../img/olives/Spiced-Olives.jpg" width="150" height="160"></td><td><b>Spiced Olives in brine</b>  </td></tr>

</tr>




  </table>

  <?php

}
  ?>


  </div>

  </div>


  

</div>

<?php
///if(isset($_GET['pict'])){
$req="select * from gallery order by id_Gal limit 4 ";
$rs=mysqli_query($conn, $req) or die(mysqli_error($conn));


        ?>
      <?php if($_GET['name']=="Fish" ){ ?>

  <div class="detail-picture">
  <h1>Pictures</h1> <span><a href="gallery"></a></span>

<div class="cb"></div>
<div class="pict">

      <a  id="effect-zone" href="../img/fish/OMeM0_7.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/fish/OMeM0_7.jpg" width="109" height="109">
  </a>
</div>
<div class="pict">

      <a  id="effect-zone" href="../img/fish/OMeM0_8.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/fish/OMeM0_8.jpg" width="109" height="109">
  </a>
</div>
<div class="pict">

      <a  id="effect-zone" href="../img/fish/OMeM0_6.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/fish/OMeM0_6.jpg" width="109" height="109">
  </a>
</div>
<div class="pict">

      <a  id="effect-zone" href="../img/fish/OMeM0_5.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/fish/OMeM0_5.jpg" width="109" height="109">
  </a>
</div>
<div class="pict">

      <a  id="effect-zone" href="../img/fish/OMeM0_2.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/fish/OMeM0_2.jpg" width="109" height="109">
  </a>
</div>
<div class="pict">

      <a  id="effect-zone" href="../img/fish/OMeM0_4.jpg">
  <i class="fa fa-search-plus fa-3x" aria-hidden="true"></i>
  <img src="../img/fish/OMeM0_4.jpg" width="109" height="109">
  </a>
</div>
  <?php
}
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
<div class="form-subscribe">
  <form metod='post' action="#" >
    <input type="Email" placeholder="Email address" name="email" class="input-subscribe">
    <button type="submit" class="btn btn-subscribe">Submit</button>
  </form>
</div>
</div>
</div>

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
    ALL right reserves    |  © 2021  |    FoodMax website
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
<script type="text/javascript" src="../js/app.js"></script>

<?php require_once((__DIR__ . "/../includes/analyticstracking.php")); ?>
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
