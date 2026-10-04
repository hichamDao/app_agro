
$(document).ready(function(){

/* Navigation principale et recherche : gerees par js/nav.js
   (identifiants #searchtoggl / #menu-toggle / #menu-derouler
    volontairement retires du header). Ces anciens handlers
   sont desactives pour eviter tout conflit. */

/*
   Ancien handler de recherche (desactive) :
$('#searchtoggl').click(function(e){
    e.preventDefault();
    if(!$searchbar.is(":visible")) {
        $searchlink.removeClass('fa-search').addClass('fa-search-minus');
    } else {
        $searchlink.removeClass('fa-search-minus').addClass('fa-search');
    }
    $searchbar.slideToggle(300);
});
*/

/*
   Ancien handler du sous-menu produits (desactive) :
$('#menu-toggle').click(function(e){
    e.preventDefault();
    if(!$('#menu-derouler').is(":visible")) {
        $('#menu-toggle i').removeClass('fa-angle-right').addClass('fa-angle-down');
    } else {
        $('#menu-toggle i').removeClass('fa-angle-down').addClass('fa-angle-right');
    }
    $('#menu-derouler').slideToggle(300);
});
*/

     $(window).scroll(function(){
    ( $(this).scrollTop() > 300 ) ? $("a#scroll-to-top").addClass('visible') : $("a#scroll-to-top").removeClass('visible');

  });

  $("a#scroll-to-top").click(function() {
    $("html, body").animate({ scrollTop: 0 }, "slow");
    return false;
  });

$('#menu-toggle').click(function(e){
    
    e.preventDefault();
   // alert("hello");
    if($(this).attr('id') == 'menu-toggle') {
      if(!$('#menu-derouler').is(":visible")) { 
             
              $('#menu-toggle i').removeClass('fa-angle-right').addClass('fa-angle-down');
            
            } else {
     
        $('#menu-toggle i').removeClass('fa-angle-down').addClass('fa-angle-right');
      }
      
      $('#menu-derouler').slideToggle(300);
       $('.section-post2').css('z-index','0');
    }
  });

$.extend({
  getUrlVars: function(){
    var vars = [], hash;
    var url = decodeURIComponent(window.location.href);
    var hashes = url.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  },
  getUrlVar: function(name){
    return $.getUrlVars()[name];
 }
});

$(document).ready(function() {
    // Unhide the main content area
    $('section.centered').fadeIn('slow');


    /*
    if($.getUrlVar('name')){
    var name = $.getUrlVar('name');
    var scrollElement = '#' + name;
}
else{
  
   var tag = $.getUrlVar('tags');
    var scrollElement = '#' + tag;
}
*/
  /* Ancien scroll automatique vers une ancre, desactive :
     il levait une erreur JS sur toutes les pages (offset() renvoie null
     quand l'ancre n'existe pas).

  // Create a var out of the URL param that we can scroll to
      var url = decodeURIComponent(window.location.href);

      var hashe = url.split('/')[5];

var scrollElement = '#' + hashe;
    // Scroll down to the newly specified anchor point
    var destination = $(scrollElement).offset().top;
    $("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination-75}, 800 );

    return false;
}); */
});


/* Fancybox n'est charge que par les pages qui en ont besoin (galerie,
   about-us, contact) et ce script s'execute dans le <head>, donc avant le
   plugin. On attend que jQuery.fancybox soit defini avant d'appeler, sinon on
   leve "$(...).fancybox is not a function" sur les pages qui ne l'incluent pas.
   L'initialisation est protegee par .fmFancyboxReady pour n'etre faite qu'une fois. */
(function ($) {
    var opts = {
        prevEffect : 'none',
        nextEffect : 'none',

        /* Croix de fermeture, fleches et navigation au clavier : sans elles
           la visionneuse s'ouvre sans aucun moyen visible d'en sortir. */
        closeBtn  : true,
        arrows    : true,
        nextClick : true,
        padding   : 14,

        helpers : {
          thumbs : {
            width  : 60,
            height : 60
          }
        }
    };

    function initFancybox() {
        if (!$ || !$.fn || !$.fn.fancybox) { return false; }  /* plugin pas encore la */
        if (!$('.fancybox-thumbs').length) { return true; }   /* rien a faire sur cette page */
        if (initFancybox.done) { return true; }
        initFancybox.done = true;
        $('.fancybox-thumbs').fancybox(opts);
        return true;
    }

    if (!initFancybox()) {
        /* DOMContentLoaded puis load : le second rattrape le cas ou le premier
           n'a pas declenche (ressource externe bloquante dans le head). */
        $(initFancybox);
        $(window).on('load', initFancybox);
    }
})(window.jQuery);

$('.background-d').each(function(){

$(this).removeClass('background-detail');

  var type = $(this).attr('data-type');
    var brand = $(this).attr('data-brand');
$(this).addClass('background-detail');
    $('.background-detail').css({'background-image' : 'url(css/img/' + type + '/' + brand + '.png)',
      'background-repeat': 'no-repeat'});

});
//
/*
$(".product-grid").hover(function () {
 if($(".cover-img").hasClass('desactive')){
   $(".cover-img").removeClass('desactive');
   $(".cover-img").addClass('active');
   $(".cover-img").fadeIn();
 }

   $(".cover-img").addClass('active').siblings().removeClass('active');

})

$(".product-grid").mouseleave(function() {      
    if($(".cover-img").hasClass('active')){

      $(".cover-img").removeClass('active');
      $(".cover-img").addClass('desactive');
      $(".cover-img").fadeOut();
    }
    
    

})
    */

     var getUrl = window.location;
if(getUrl.host=="www.foodmax-group.com"){
   document.location.href="https://foodmax-group.com"+getUrl.pathname.split('https://')[0];
}


});