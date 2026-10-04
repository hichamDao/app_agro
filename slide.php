<?php
include((__DIR__ . "/includes/connection.php"));
?>

<html>
<head>
	<title>
	</title>
	<link rel="stylesheet" type="text/css" href="css/prettyPhoto.css">

	 <script type="text/javascript" src="js/jquery.min.js"></script>
	 <script type="text/javascript" src="js/jquery.prettyPhoto.js"></script>
	 <script src="js/owl.carousel.js"></script>
      <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
     <STYLE TYPE="text/css">
ol, ul ,li{
  list-style: none; }

.wrapper{
  width: calc(100% + 80px);
}
.carousel{
 max-width: 2500px;
  margin: auto;
  padding: 0 10px;
}
.gallery-pic{

   width: auto;
  height: 240px;
  transition: all 12.3s ease-in-out;
}
.owl-dot{
  width: 10px;
  height: 10px;
  margin: 0 5px;
  outline: none;
  text-align: center;
  color;
  -webkit-backface-visibility: visible;
    transition: opacity .2s ease;
    border-radius: 30px;
  border: 2px solid #4dc7a0!important;
  box-shadow: 0px 4px 15px rgba(0,0,0,0.2);

}

.owl-dot.active,
.owl-dot:hover{
  background: #0072bc!important;
}
button.owl-next, button.owl-prev {
   display:none;
     }
.owl-dots{
      top: 215px;
    left: 619px;
    position: absolute;
     }
     .owl-dot.active, .owl-dot:hover {
    background: #0072bc!important;
}
     .bond-l {
/*   position: absolute;
    top: 243px;
    left: 591px;
    height: 49px;
    width: 100px;
    border-radius: 0 0 100px 100px;*/
}

     </STYLE>

</head>
<body>

<div class="wrapper">

         <div class="carousel owl-carousel">
         	<?php
         	$req="select * from gallery order by id_Gal DESC limit 15";
$rs2=mysqli_query($conn, $req) or die(mysqli_error($conn));
while ($data=mysqli_fetch_assoc($rs2)){
//echo '<li><a href="'.$data['Photo2'].'" rel="prettyPhoto"><img src="'.$data['Photo'].'" class="gallery-pic"  alt="" /></a></li>';
				}

				?>

           
         </div>

      </div>
      <script>
      $('.carousel').owlCarousel({
    loop:true,
    margin:20,
    autoplay: true,
    autoplayTimeout: 2000,
    responsiveClass:true,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items:1,
            nav:false
        },
        600:{
            items:3,
            nav:false
        },
        1000:{
            items:5,
            loop:true,
            nav: true,
            margin: 20
        }
    }
});
        /* $(".carousel").owlCarousel({
           margin: 20,
           loop: true,
           autoplay: true,
           autoplayTimeout: 2000,
           autoplayHoverPause: true,
           responsive: {
             0:{
               items:1,
               nav: false
             },
             600:{
               items:4,
               nav: false
             },
             1000:{
               items:3,
               nav: false
             }
           }
         });*/
      </script>
<script type="text/javascript" charset="utf-8">
  $(document).ready(function(){
       $("a[rel^='prettyPhoto']").prettyPhoto({
         social_tools: false,
         allow_resize: true,
         overlay_gallery: true,
         horizontal_padding: 20
       });

     });
  </script>