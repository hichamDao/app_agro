<?php require_once((__DIR__ . "/../includes/connection.php")); ?>
<?php
/* Page heritee : la creation et la modification des produits sont desormais
   traitees par GestionProduits.php, qui ecrit les photos dans images/{Ref}/
   a la racine du site — seul emplacement lu par products/products.php.
   Celle-ci les depositait dans admin/images/{Ref}/, hors du site public.
   Elle reste atteignable, mais uniquement pour un utilisateur identifie. */
require_once((__DIR__ . "/../includes/admin-auth.php"));
fm_admin_exiger_login();
/* Ne traite rien d'autre qu'un envoi de formulaire : sans ce test, une simple
   visite en GET déclenchait un warning sur chaque champ $_POST manquant. */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: GestionProduits.php?action=nouveau');
    exit;
}
session_start();
?>
<?php

$ref=$_POST["refProduit"]; 

$des=$_POST["designation"];
$desc=$_POST['description'];
$idCat=$_POST["idCat"]; 
$prix=$_POST["prix"]; 
$quantite=$_POST["quantite"];
$settag = $_POST['tag'];

$setcomposant=$_POST['composants'];
$setpercent=$_POST['percents'];
$j = 0;

if (!file_exists("images/$ref/")){
mkdir("images/$ref/", 0777);
chmod("images/$ref/", 0777);
}


chmod("images/$ref/", 0777);
   
if(isset($_FILES['photo'])){

for ($i = 0; $i < count($_FILES['photo']['name']); $i++) {
$nomPhoto=$_FILES['photo']['name'][$i];
$fichierTemporaire=$_FILES['photo']['tmp_name'][$i];
 $extension_upload = strtolower(substr(strrchr($nomPhoto, '.')  ,1));
 $name = substr(time(),0,6);
$des=strtr($_POST["designation"],array("&nbsp;"=>"-"," "=>"-"));
$path = "images/$ref/$des-$name$i.$extension_upload";
$photos=$des.-$name.$i.'.'.$extension_upload;
if(move_uploaded_file($fichierTemporaire, $path)){
   $images[] = $photos;
}
}
}
if(isset($_POST['id_prod'])){
$id=intval($_POST['id_prod']);
$sql="select * from produits where id_prod=$id";
  $rs=mysqli_query($conn, $sql);
  $prod=mysqli_fetch_assoc($rs);
}
@$all_images = implode(",",$images);
$all=(empty($all_images)?$prod['Photo']:$all_images);
if(isset($_POST['promotion'])) $promo=1; else $promo=0;
if(isset($_POST['selectionne'])) $sel=1; else $sel=0;
if(isset($_POST['disponible'])) $dispo=1; else $dispo=0;
$des=strtr($_POST["designation"],array("-"=>"&nbsp;","-"=>" "));
if(isset($_POST['update'])){
  $id=intval($_POST['id_prod']);

$req="update produits set Ref_prod='$ref',Designation='$des',description='$desc',
Photo='$all',Disponible='$dispo',promotion='$promo',selectionne='$sel',Code_cat='$idCat',tags='$settag',composant='$setcomposant',
percent='$setpercent' where id_prod='$id' ";

}

  else
  {



$req="insert into produits(Ref_prod,Designation,description,Quantite,Prix,Photo,
Disponible,promotion,selectionne,Code_cat,tags,composant,percent)
values
('$ref','$des','$desc','0','0','$all','$dispo','$promo','$sel','$idCat','$settag','$setcomposant','$setpercent')";
}
mysqli_query($conn, $req) or die(mysqli_error($conn));
?>
<!DOCTYPE html>
<html> <head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../css/style.css">
<link rel="stylesheet" type="text/css" href="bootstrap-3.3.6-dist/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<div class="container">
<div class="row" >
<?php require_once((__DIR__ . "/../includes/entete.php")); ?>


<div class="col-md-4">
<div class="search">
<div class="search-title">
  <h1>Rechercher</h1>
</div>
<form method="post" action="../index.php" id="form">
<input type="text" name="motCle"/>
<input type="submit" value="Chercher"/>

</form>
</div>
<div id="categories">
  <div class="cat-title">
    <h1>Categories</h1>
  </div>
  <?php require_once((__DIR__ . "/../includes/categories.php")); ?>
  </div>
  </div>
  <div class="col-md-7">
  <ol class="breadcrumb">
  <li><a href="../index.php">Home</a></li>
  <li><a href="GestionProduits.php">Gestion produits</a></li>
  <li class="active">Ajouter produits</li>
</ol>

<h3> Données Enregistrées avec succés</h3>
<table border="1" class="table">
<tr> <td><label>REF </label></td><td><?php echo $ref; ?> </td> </tr>
<tr> <td><label>Designation </label></td><td><?php echo $des; ?> </td></tr>
<tr><td><label>Description </label> </td><td><?php echo $desc; ?></td></tr>
<tr> <td><label>Photo </label></td><td>

<?php
for ($i = 0; $i < count($_FILES['photo']['name']); $i++) {
$nomPhoto=$_FILES['photo']['name'][$i];
$path = "images/$ref/$nomPhoto";
?>
    <img src="<?php echo $path; ?>" width="100" height="50" />
 <?php
}
?>


</td></tr>

<tr> <td><label>Prix </label></td><td><?php echo $prix; ?> </td> </tr>
<tr> <td><label>Quantité </label></td><td><?php echo $quantite; ?> </td> </tr>
<tr> <td><label>Code Catégorie </label></td><td><?php echo $idCat; ?> </td> </tr>
<tr> <td><label>Disponible </label></td><td><?php echo $dispo; ?> </td> </tr>
<tr> <td><label>Promotion </label></td><td><?php echo $promo; ?> </td> </tr>
<tr> <td><label>Sélectionné </label></td><td><?php echo $sel; ?> </td> </tr>
<tr><td><label>Tags</label></td><td><?php echo($settag)?></td></tr>
<tr><td><label>Composant</label></td><td><?php echo($setcomposant)?></td></tr>
<tr><td><label>Percent</label></td><td><?php echo($setpercent)?></td></tr>
</table>
<a href="../index.php">Home</a>
</div>
</div>
</div>
</div>
</body></html>
<?php
mysqli_close($conn);
?>
