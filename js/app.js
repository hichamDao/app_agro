// URL de base du site, calculee pendant le chargement synchrone du script.
var FM_BASE = (function () {
    var s = document.currentScript;
    return (s && s.src) ? s.src.replace(/\/js\/[^\/]*$/, '/') : '/';
})();

$( document ).ready(function() {

/*
jQuery('#grid-container').cubeportfolio({
    filters: '#filters-container',
        layoutMode: 'grid',
        defaultFilter: '*',
        animationType: 'flipOutDelay',
        gapHorizontal: 20,
        gapVertical: 20,
        gridAdjustment: 'responsive',
        mediaQueries: [{
            width: 1100,
            cols: 4
        }, {
            width: 800,
            cols: 3
        }, {
            width: 500,
            cols: 2
        }, {
            width: 320,
            cols: 1
        }],
        caption: 'overlayBottomAlong',
        displayType: 'bottomToTop',
        displayTypeSpeed: 100,

        // lightbox
        lightboxDelegate: '.cbp-lightbox',
        lightboxGallery: true,
        lightboxTitleSrc: 'data-title',
        lightboxCounter: '<div class="cbp-popup-lightbox-counter">{{current}} of {{total}}</div>',
    });


$("#searchform").submit(function(){
  var url = decodeURIComponent(window.location.href);

 var motcle=$(this).serialize(); 
 var cle=motcle.split('motCle=')[1];

   window.history.pushState("", "", 'products/motCle/'+cle+'/'); 
}
});

   */




 
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
else if(href=='dbproducts/search/'+cle){
    //
window.history.pushState("", "", 'products/search/'+cle);
 }
 else if(href=='products/?motCle='+cle){
//
window.history.pushState("", "", 'products/search/'+cle+'/1/');
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


$("#effect-zone").fancybox({
          padding: 0,

        openEffect : 'elastic',
        openSpeed  : 150,

        closeEffect : 'elastic',
        closeSpeed  : 150,

        closeClick : true,

        helpers : {
          overlay : null
        }
      });


   /* $("a[rel^='prettyPhoto']").prettyPhoto();

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
*/
    var name=$('.background-detail-foto').attr('data-name');
   var foto = $('.background-detail-foto').attr('data-foto');

    $('.background-detail-foto').css({'background-image' : 'url(imagesw/'+ name +'/'+ foto +')',
      'background-repeat': 'no-repeat','background-position-x':'30%','background-position-y':'12%'});

//---> subscribe

    $('#subscribe').submit(function(){

       $.ajax({type:"POST", data: $(this).serialize(), url: FM_BASE + 'admin/subscribe_controle.php',
      success: function(data){
     
       $("#subscribe-post").html(data);
        
      },
                        error: function(){
              $("#subscribe-post").html('An error has occurred.');
      }
      
     
    });
    return false;
  });



});

  
