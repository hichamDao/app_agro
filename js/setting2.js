  $(document).ready(function(){
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

    // Create a var out of the URL param that we can scroll to
    
      var cle = $.getUrlVar('motCle');
    var scrollElement = '#' + cle ;

    // Scroll down to the newly specified anchor point
    var destination = $(scrollElement).offset().top;
    $("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination-75}, 800 );
    return false;
});
  });