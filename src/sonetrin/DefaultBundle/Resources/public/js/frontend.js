$(document).ready(function()
{ 
  $("img").error(function(){
    $(this).hide();     
    $(this).parent().append('<div id="error">No data available</div>');     
  });    
});