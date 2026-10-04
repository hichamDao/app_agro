$(document).ready(function(){
    
$('#twitter-share').sharrre({
        share: {
            twitter: true
        },template: '<i class="fa fa-twitter"></i>',
        enableHover: false,
        enableTracking: true,
        click: function(api, options){
            api.simulateClick();
            api.openPopup('twitter');
        }
    });
    $('#facebook-share').sharrre({
        share: {
            facebook: true
    },template: '<i class="fa fa-facebook"></i>',
        enableHover: false,
        enableTracking: true,
        click: function(api, options){
            api.simulateClick();
            api.openPopup('facebook');
        }
    });
	
	 $('#googlePlusshare').sharrre({
        share: {
             googlePlus: true
    },template: '<i class="fa fa-google-plus"></i>',
        enableHover: false,
        enableTracking: true,
        click: function(api, options){
            api.simulateClick() ;
			api.openPopup('plusgoogle');
        }
    });

     });