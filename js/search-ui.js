$( document ).ready(function() {
     

$('#search_data').autocomplete({
      
      source: "fetch.php",
      minLength: 1,
      select: function(event, ui)
      {
        $('#search_data').val(ui.item.value);
        window.document.location="offers/"+ui.item.value+"/";
      }
    }).data('ui-autocomplete')._renderItem = function(ul, item){
      return $("<li class='ui-autocomplete-row'></li>")
        
        .append(item.label)

        .appendTo(ul);

     
       
    };

  


})