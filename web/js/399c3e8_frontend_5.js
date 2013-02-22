$(document).ready(function()
    {
               
        $( "#datepicker_start" ).datepicker({
            dateFormat: "yy-mm-dd"
        });
        $( "#datepicker_end" ).datepicker({
            dateFormat: "yy-mm-dd"
        });
           
              
        $("img").error(function(){
            $(this).hide();     
            $(this).parent().append('<div id="error">No data available</div>');     
        });    
    });