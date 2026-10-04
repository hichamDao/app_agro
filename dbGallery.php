<?php
require_once((__DIR__ . "/includes/connection.php"));

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/prettyPhoto.css">
</head>
<body>
<div class="cb"></div>   
<section> 
<div class="section-gallery">
	<h2></h2>
			<ul class="gallery clearfix">
				<?php
				$select="select * from gallery";
				$rs=mysqli_query($conn, $select) or die(mysqli_error($conn));
			   $nombreTotal=mysqli_num_rows($rs);
			   $nombreParPage=16;
      
               $nombreDePages = ceil($nombreTotal / $nombreParPage );  
                 $page = (isset($_GET['page']))?intval($_GET['page']):'1';


 
$premierPhotoAafficher = ($page - 1) * $nombreParPage;
$req="select * from gallery order by id_Gal DESC limit $premierPhotoAafficher,$nombreParPage";
$rs2=mysqli_query($conn, $req) or die(mysqli_error($conn));
while ($data=mysqli_fetch_assoc($rs2)){
echo '<li><a href="'.$data['Photo2'].'" rel="prettyPhoto"><img src="'.$data['Photo'].'" class="gallery-pic-s"  alt="" /></a></li>';
				}

				?>
			
				
			</ul>
</div>
</section>
  <div class="cb"></div>    
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
   <li> <a class="clickpage" id="prev-page"
                    href="dbGallery/<?php echo (($page-1));?>"
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
  echo'<li><a href="dbGallery/'.$i.'" class="clickpage"><span>'.$i.'</span></a></li>';
  }
  }
if ($page >= $nombreDePages){
  ?>
  <li > <a href="#" title="Previous Page" style="pointer-events: none;background-color:#ffdfcf;"><span>Next &#10095;</span></a></li>

<?php
}
  
    if ($page < $nombreDePages) {
        ?>
       <li> <a class="clickpage" id="next-page"
                    href="dbGallery/<?php echo (($page+1)).'/';?>"
                    title="Next Page" ><span>Next &#10095;</span></a> </li>
        <?php
    }
?>
  </ul>
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/jquery.prettyPhoto.js"></script>
  <script type="text/javascript" charset="utf-8">
  $(document).ready(function(){
       $("a[rel^='prettyPhoto']").prettyPhoto({
        social_tools: false
       });
 
 

       $(".clickpage").click(function(e){
  e.preventDefault();
  var href = $(this).attr('href');
  var page=href.split('/')[1];

if(href=='dbGallery/'+page){
//
  window.history.pushState("", "", 'gallery/'+page+'/');
  }


  $.get($(this).attr('href'),{},function(data){

    if(data.error){
    alert(data.message);
    }

$("#zone_de_rechargement").empty().hide();
$("html:not(:animated),body:not(:animated)").animate({ scrollTop: 334}, 700 );
$("#zone_de_rechargement").append(data);

$('#zone_de_rechargement').slideToggle(200);
 
    // return false;
});
});
  });
</script>

<script type="text/javascript" src="css/fullpages/jquery.fullpage.min.js"></script>
<script type="text/javascript" src="js/jquery.malihu.PageScroll2id.js"></script>
<script type="text/javascript" src="js/app.js"></script>
  </body>
  </html>
