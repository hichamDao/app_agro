
// URL de base du site, calculee pendant le chargement synchrone du script.
var FM_BASE = (function () {
    var s = document.currentScript;
    return (s && s.src) ? s.src.replace(/\/js\/[^\/]*$/, '/') : '/';
})();

$(document).ready(function() {

$("#contactform").submit(function(e){ 
    e.preventDefault(); 

    var donnees = $(this).serialize(); 
   
            $.ajax({

       url : FM_BASE + 'admin/contact_control.php',
       type : 'POST', 
       data : donnees,
       dataType : 'html',
      
                success: function (result) {
                    $('#resultContactForm').html(result);
             var action = $("#contactform").attr('action');
             if(action=="home/"){
              
                 $("html:not(:animated),body:not(:animated)").animate({ scrollTop:3000}, 800 );
               }

                    $('#hide-message').click(function(e){
                    e.preventDefault();
                  
                      $("#message").fadeOut(200);
                  });
                }
            });
        
        return false;

    });


 $('#loadMobile').click(function(e){
e.preventDefault();

$.get($(this).attr('href'),{},function(data){

    if(data.error){
    alert(data.message);
    }
$("#zone_de_rechargement").empty();
$("#zone_de_rechargement").append(data);
$('#hide-message').click(function(e){
e.preventDefault();
                  
$("#message").fadeOut(200);
                  });
});

});


  //$boxbar.hide();
  $('#loadPanier').click(function(e){
e.preventDefault();

var $boxbar  = $('#box-p');

 if(!$boxbar.is(":visible")) { 
    
        $boxbar.show();
        $(".ic-txt").show();
      } else {
      
         $boxbar.hide();
         $(".ic-txt").hide();
      }
      
      //$boxbar.slideToggle(300);
});

 $('#Menu2').click(function(e){
var $menulogin =$('.menulogin');
e.preventDefault();
//var elements = $("[aria-labelledby='Menu2']");
//console.log(elements);
 if(!$menulogin.is(":visible")) { 
  $menulogin.removeClass('close').addClass('open');
  } else {
  $menulogin.removeClass('open').addClass('close');
      }
 
});

