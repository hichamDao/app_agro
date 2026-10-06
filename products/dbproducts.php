<?php
require_once((__DIR__ . "/../includes/_header.php")); 

/* ---------------------------------------------------------------- securite
   Aucune valeur venant de l'URL n'est collee dans le SQL : les requetes sont
   preparees (fm_requete) et les valeurs affichees sont encodees. */
if (!function_exists('fm_requete')) {
    /* Execute une requete preparee et renvoie un mysqli_result (ou false). */
    function fm_requete($conn, $sql, $types = '', $params = array())
    {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) { return false; }
        if ($types !== '') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
        if (!mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return false; }
        $res = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        return $res;
    }
}
if (!function_exists('fm_h')) {
    function fm_h($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
}

/* Entrees normalisees une seule fois : nombres forces, textes seulement. */
if (isset($_GET['idCat'])) { $_GET['idCat'] = (int) $_GET['idCat']; }
if (isset($_GET['page']))  { $_GET['page']  = max(1, (int) $_GET['page']); }
foreach (array('motCle', 'tags', 'name') as $fm_k) {
    if (isset($_GET[$fm_k]) && !is_string($_GET[$fm_k])) { unset($_GET[$fm_k]); }
}
?>
<?php
function str_replace_first($from, $to, $subject)
{
    $from = '/'.preg_quote($from, '/').'/';

    return preg_replace($from, $to, $subject, 1);
}
    ?>


<!DOCTYPE html>
<html>
<meta charset="utf-8">
<head>
  <!-- base href local : desactive pour developpement -->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,600i,700,700i">
  <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css?family=Indie+Flower" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../css/fancybox/jquery.fancybox.css">
  <link rel="stylesheet" type="text/css" href="../css/fancybox/helpers/jquery.fancybox-thumbs.css" />
<style type="text/css">
.hidden{
  display:none;
}
.loading{
background: url(img/loading.gif) no-repeat;
    width: 181px;
    height: 181px;
    position: absolute;
    top: 571px;
    left: 584px;
    z-index: 9999;
    background-size: cover;
}
.loading:before{
  content:'';

}
.pagination {
    margin: 169px 293px 127px;
    }
.pagination-lg > li > a, .pagination-lg > li > span {
    padding: 17px 28px;
    font-size: 18px;
    line-height: 1.33333;
    }

</style>
</head>
<body id="">

<div class="cb"></div>    
 <div class="BodyLayout-sidebar Sidebar" >
  
<ul class="sidebar-list">
 <?php
 if(!isset($_GET['idCat'])){
?>
<li><a href="dbproducts/" class="prodinfo is-active">All products</a></li>
<?php
 }
 else{
 ?>
    <li><a href="dbproducts/"  class="prodinfo">All products</a></li>
    <?php
}

    $req="select * from categories";
    $rs=mysqli_query($conn, $req);
    while($cat=mysqli_fetch_assoc($rs)){

      if($cat['Code_cat']==@$_GET['idCat']){

    ?>
    <li><a href="dbproducts/<?php echo($cat['Code_cat'])?>/<?php echo(str_replace('_','',$cat['Nom_cat']))?>/" class="prodinfo is-active"><?php echo(str_replace('_',' ',$cat['Nom_cat']))?></a>
    </li>
    <?php
}
else{
?>
<li><a href="dbproducts/<?php echo($cat['Code_cat'])?>/<?php echo(str_replace('_','',$cat['Nom_cat']))?>/" class="prodinfo" ><?php echo(str_replace('_',' ',$cat['Nom_cat']))?></a>
    </li>
<?php
}
}
?>
 
      </ul>

   </div>

<?php

if (isset($_GET['motCle'])){
$motCle=trim($_GET['motCle']);
$reqSql    = "select * from produits where Designation LIKE ?";
$reqTypes  = 's';
$reqParams = array('%' . addcslashes($motCle, '%_\\') . '%');
 $rs=fm_requete($conn, $reqSql, $reqTypes, $reqParams);
  $nombreTotal=$rs ? mysqli_num_rows($rs) : 0;
  $nombreParPage=12;
      
    $nombreDePages = ceil($nombreTotal / $nombreParPage );  
      $page = (isset($_GET['page']))?intval($_GET['page']):'1';


 
$premierMessageAafficher = ($page - 1) * $nombreParPage;
$reqSql   .= " order by id_prod ASC limit ?,?";
$reqTypes .= 'ii';
$reqParams[] = $premierMessageAafficher;
$reqParams[] = $nombreParPage;
?>
<?php
}
elseif(isset($_GET['tags'])){



  $tag=trim($_GET['tags']);

  $reqSql    = "select * from produits where description like ?";
  $reqTypes  = 's';
  $reqParams = array('%' . addcslashes($tag, '%_\\') . '%');

$rs=fm_requete($conn, $reqSql, $reqTypes, $reqParams);
 $nombreTotal=$rs ? mysqli_num_rows($rs) : 0;
  $nombreParPage=12;
      
    $nombreDePages = ceil($nombreTotal / $nombreParPage );  
      $page = (isset($_GET['page']))?intval($_GET['page']):'1';
      $premierMessageAafficher = ($page - 1) * $nombreParPage;
      $reqSql   .= " order by id_prod ASC limit ?,?";
      $reqTypes .= 'ii';
      $reqParams[] = $premierMessageAafficher;
      $reqParams[] = $nombreParPage;
?>
<div class="fullpage">
<div class="search-result" id="section2">
  <?php
if($nombreTotal!='0'){

  echo '<b>'.$nombreTotal.' Results </b>';
}
  else{
    echo '<b>'.$nombreTotal.' Result </b>';
  }
?>
</div>
</div>
<?php

}
elseif (isset($_GET['idCat'])){
$idCat=(int) $_GET['idCat'];
$Namecat=rawurlencode(isset($_GET['name']) ? $_GET['name'] : '');

$reqSql    = "select * from produits where Code_cat=?";
$reqTypes  = 'i';
$reqParams = array($idCat);
$rs=fm_requete($conn, $reqSql, $reqTypes, $reqParams);
  $nombreTotal=$rs ? mysqli_num_rows($rs) : 0;
  $nombreParPage=12;
      
    $nombreDePages = ceil($nombreTotal / $nombreParPage );  
      $page = (isset($_GET['page']))?intval($_GET['page']):'1';


 
$premierMessageAafficher = ($page - 1) * $nombreParPage;
$reqSql   .= " order by id_prod ASC limit ?,?";
$reqTypes .= 'ii';
$reqParams[] = $premierMessageAafficher;
$reqParams[] = $nombreParPage;
}
else{
  $rs=fm_requete($conn, "select * from produits");
  $nombreTotal=$rs ? mysqli_num_rows($rs) : 0;
  $nombreParPage=12;
      
    $nombreDePages = ceil($nombreTotal / $nombreParPage );  
      $page = (isset($_GET['page']))?intval($_GET['page']):'1';


 
$premierMessageAafficher = ($page - 1) * $nombreParPage;
$reqSql    = "select * from produits where selectionne=1 order by id_prod ASC limit ?,?";
$reqTypes  = 'ii';
$reqParams = array($premierMessageAafficher, $nombreParPage);

}
$rsProd=fm_requete($conn, $reqSql, $reqTypes, $reqParams);
if (!$rsProd) {
  http_response_code(500);
  die('Unable to load the products right now. Please try again later.');
}
?>


        <?php 
        /*
if($cat['Code_cat']=='13'){
  echo"<p><h2>7 dry fruits you should include in your diet to stay healthy</h2></p>";
  echo"<h1>Dry fruits are a great source of proteins, vitamins, minerals, dietary fibre, and an ideal substitute for high-calorie snacks.</h1>";

}
*/
?>
<?php
if(isset($_GET['idCat'])){
    $req="select * from categories where Code_cat='13'";
    $rs=mysqli_query($conn, $req);
    $dd=mysqli_fetch_array($rs);
  echo $dd['Description'];
  //echo"<h1>Dry fruits are a great source of proteins, vitamins, minerals, dietary fibre, and an ideal substitute for high-calorie snacks.</h1>";

}

?>
 <div class="posts">
  
    <?php while($prod=mysqli_fetch_assoc($rsProd)){?>
  <?php
$myphoto=explode(",",$prod['Photo']);
    ?>
<div class="post">

   <a id="effect-zone" href="../images/<?php echo rawurlencode($prod['Ref_prod'])."/".rawurlencode($myphoto[0]); ?>" title="<?php echo fm_h($prod['Designation'])?>" class="post-thumb">
    <img src="../images/<?php echo rawurlencode($prod['Ref_prod'])."/".rawurlencode($myphoto[0]); ?>" width="240"
  height="170">
  </a>
  <a id="effect-zone" href="../images/<?php echo rawurlencode($prod['Ref_prod'])."/".rawurlencode($myphoto[0]); ?>" title="<?php echo fm_h($prod['Designation'])?>">
  <div class="post-icon">
  
  <i class="fa fa-picture-o"></i>
  </div>
  </a>
  <div class="post-content">
  <h2 class="post-title"><a href="products_detail/<?php echo (int) $prod['id_prod']?>/"><?php echo fm_h($prod['Designation'])?></a></h2>
 
  <p>
    
  </p>
  </div>
  </div>

<?php
}

?>
<div id="loader12" class="loading hidden">&nbsp;</div>
<?php
if(isset($_GET['idCat'])){
$idCat=(int) $_GET['idCat'];
$Namecat=rawurlencode(isset($_GET['name']) ? $_GET['name'] : '');

?>
<div class="cb"></div>
<?php if($nombreDePages!="1"){

?>
<ul class="pagination  pagination-lg" >
   <?php
    $page = (isset($_GET['page']))?intval($_GET['page']):'1';
 if($page == 1){
      ?>
      <li>
<a  href="#" title="Previous Page" style="pointer-events: none;background-color:#ffdfcf;"><span>&#10094; Previous</span></a></li>
   <?php
    }
elseif ($page > 1) {
    ?>
   <li> <a class="prodinfo" id="prev-page"
                    href="dbproducts/<?php echo $idCat.'/'.$Namecat.'/'.(($page-1));?>"
                    title="Previous Page" ><span>&#10094; Previous</span></a></li>
            <?php }

for ($i = 1; $i <= $nombreDePages ; $i++)
{
    if ($i == $page) //On affiche pas la page actuelle en lien
    {
    echo'<li class="active"><span>'.$i.'</span></li>';
    }
    else
    {
  echo'<li><a href="dbproducts/'.$idCat.'/'.$Namecat.'/'.$i.'" class="prodinfo"><span>'.$i.'</span></a></li>';
  }
  }
if ($page >= $nombreDePages){
  ?>
  <li > <a href="#" title="Previous Page" style="pointer-events: none;background-color:#ffdfcf;"><span>Next &#10095;</span></a></li>

<?php
}
  
    if ($page < $nombreDePages) {
        ?>
       <li> <a class="prodinfo" id="next-page"
                    href="dbproducts/<?php echo $idCat.'/'.$Namecat.'/'.(($page+1)).'/';?>"
                    title="Next Page" ><span>Next &#10095;</span></a> </li>
        <?php
    }
?>
  </ul>
  <?php }
  else{
    ?>
<ul class="pagination  pagination-lg" >
</ul>
    <?php
  }
  ?>
  </div>
  </div>
<?php
}
elseif(isset($_GET['tags'])){
$tag=trim($_GET['tags']);


?>
<div class="cb"></div>  
  <?php if($nombreDePages!="1"){

?>
<ul class="pagination  pagination-lg" >
   <?php
    $page = (isset($_GET['page']))?intval($_GET['page']):'1';
    if($page == 1){
      ?>
      <li>
<a  href="#" title="Previous Page" style="pointer-events: none;background-color:#ffdfcf;"><span>&#10094; Previous</span></a></li>
   <?php
    }
elseif ($page > 1) {

    ?>
   <li> <a class="prodinfo" id="prev-page"
                    href="dbproducts/tags/<?php echo rawurlencode($_GET['tags']).'/'.(($page-1));?>"
                    title="Previous Page" ><span>&#10094; Previous</span></a></li>
            <?php }
for ($i = 1; $i <= $nombreDePages ; $i++)
{
    if ($i == $page) //On affiche pas la page actuelle en lien
    {
    echo'<li class="active"><span>'.$i.'</span></li>';
    }
    else
    {
  echo'<li><a href="dbproducts/tags/'.rawurlencode($_GET['tags']).'/'.$i.'" class="prodinfo"><span>'.$i.'</span></a></li>';
  }
  }
if ($page >= $nombreDePages){
  ?>
  <li > <a href="#" title="Previous Page" style="pointer-events: none;background-color:#ffdfcf;"><span>Next &#10095;</span></a></li>

<?php
}
    if ($page < $nombreDePages) {
        ?>
       <li> <a class="prodinfo" id="next-page"
                    href="dbproducts/tags/<?php echo rawurlencode($_GET['tags']).'/'.(($page+1)).'/';?>"
                    title="Next Page" ><span>Next &#10095;</span></a> </li>
        <?php
    }
?>
  </ul>
  <?php }
  else{
    ?>
<ul class="pagination  pagination-lg" >
</ul>
    <?php
  }
  ?>
  </div>

<?php

}

elseif(isset($_GET['motCle'])){
?>
<div class="cb"></div>    
<?php if($nombreDePages!="1"){

?>
<ul class="pagination  pagination-lg" >

   <?php
    $page = (isset($_GET['page']))?intval($_GET['page']):'1';
    if($page == 1){
      ?>
      <li>
<a  href="#" title="Previous Page" style="pointer-events: none;background-color:#ffdfcf;"><span>&#10094; Previous</span></a></li>
   <?php
    }
elseif ($page > 1) {

    ?>
   <li> <a class="prodinfo" id="prev-page"
                    href="dbproducts/search/<?php echo rawurlencode($_GET['motCle']).'/'.(($page-1));?>"
                    title="Previous Page" ><span>&#10094; Previous</span></a></li>
            <?php }

for ($i = 1; $i <= $nombreDePages ; $i++)
{
    if ($i == $page) //On affiche pas la page actuelle en lien
    {
    echo'<li class="active"><span>'.$i.'</span></li>';
    }
    else
    {
  echo'<li><a href="dbproducts/search/'.rawurlencode($_GET['motCle']).'/'.$i.'" class="prodinfo"><span>'.$i.'</span></a></li>';
  }
  }
if ($page >= $nombreDePages){
  ?>
  <li > <a href="#" title="Previous Page" style="pointer-events: none;background-color:#ffdfcf;"><span>Next &#10095;</span></a></li>

<?php
}
    if ($page < $nombreDePages) {
        ?>
       <li> <a class="prodinfo" id="next-page"
                    href="dbproducts/search/<?php echo rawurlencode($_GET['motCle']).'/'.(($page+1));?>"
                    title="Next Page" ><span>Next &#10095;</span></a> </li>
        <?php
    }
?>
  </ul>
   <?php }
  else{
    ?>
<ul class="pagination  pagination-lg" >
</ul>
    <?php
  }
  ?>
<?php
}
else{
?>

<div class="cb"></div>    
<?php if($nombreDePages!="1"){

?>
 <ul class="pagination  pagination-lg" >

   <?php
    $page = (isset($_GET['page']))?intval($_GET['page']):'1';
    if($page == 1){
      ?>
      <li>
<a  href="#" title="Previous Page" style="pointer-events: none;background-color:#ffdfcf;"><span>&#10094; Previous</span></a></li>
   <?php
    }
elseif ($page > 1) {
    ?>
  
   <li> <a class="prodinfo" id="prev-page"
                    href="dbproducts/<?php echo (($page-1));?>"
                    title="Previous Page" ><span>&#10094; Previous</span></a></li>

            <?php }


for ($i = 1; $i <= $nombreDePages ; $i++)
{
    if ($i == $page) //On affiche pas la page actuelle en lien
    {
    echo'<li class="active"><span>'.$i.'</span></li>';
    }
    else
    {
  echo'<li><a href="dbproducts/'.$i.'" class="prodinfo"><span>'.$i.'</span></a></li>';
  }
  }
if ($page >= $nombreDePages){
  ?>
  <li > <a href="#" title="Previous Page" style="pointer-events: none;background-color:#ffdfcf;"><span>Next &#10095;</span></a></li>

<?php
}
    if ($page < $nombreDePages) {
        ?>
         
        
       <li> <a class="prodinfo" id="next-page"
                    href="dbproducts/<?php echo (($page+1));?>"
                    title="Next Page" ><span>Next &#10095;</span></a> </li>
        <?php
    }
?>
  </ul>
    <?php }
  else{
    ?>
<ul class="pagination  pagination-lg" >
</ul>
    <?php
  }
  ?>
  <?php
  }
    ?>
  </div>
  
    </div>
    </div>
    </div>

<?php
mysqli_free_result($rsProd);
?>
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../css/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="../css/fancybox/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="../css/fancybox/helpers/jquery.fancybox-thumbs.js"></script>
<script type="text/javascript">
$(".prodinfo").click(function(e){
  e.preventDefault();
  var href = $(this).attr('href');
  var page=href.split('/')[3];
  var id = href.split('/')[1];
  var name= href.split('/')[2];
  var cle=href.split('motCle=')[1];

  var tag=href.split('/')[1];


if(href=='dbproducts/'){
window.history.pushState("", "", 'products/');
  }
  else if(href=='dbproducts/'+page+'/'){
//
window.history.pushState("", "", 'products/'+page+'/');
 }
 else if(href=='dbproducts/'+tag){
    //
window.history.pushState("", "", 'products/'+tag);
 }
else if(href=='dbproducts/motCle/'+cle){
    //
window.history.pushState("", "", 'products/motCle/'+cle);
 }
else if(name!=0 && page!=0){
    window.history.pushState("", "", 'products/'+id+'/'+name+'/'+page+'/');
}
else{


window.history.pushState("", "", 'products/'+id+'/'+name+'/');

}

$.get($(this).attr('href'),{},function(data){

    if(data.error){
alert(data.message);
    }

$("#zone_de_rechargement").empty().hide();
$("html, body").animate({ scrollTop: 777 }, "slow");
$("#zone_de_rechargement").append(data);

$('#zone_de_rechargement').fadeIn(4000);
 
    // return false;
});
});

</script>

<script type="text/javascript">
</body>
</html>