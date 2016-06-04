$(document).ready(function() {

var clear_url = window.location.protocol + "//" + window.location.hostname + "/",
    opthref   = clear_url + "e-shop/adm/options.php";

//-----
    function ui_sort($obj){
        
          //вытащим какой объект(class) нужно выбрать
          var table = $($obj).attr("data-js-sort");
          
          $($obj).sortable({
            cursor: 'crosshair',
            start: function(event, ui) {           
               /*
               var thisLi = ui.item;
               thisLi.addClass('sortNow');*/
            },                    
            stop: function(event, ui) {
              /*
              var thisLi = ui.item;
              thisLi.removeClass('sortNow'); */          
            },
            update: function(event, ui) {

              var forPost = {
                 "data": $(this).sortable("serialize"),
                 "method_name": "sort",
                 "table": table
              };

              $.post(opthref, forPost,  function(e){
                    var res = JSON.parse(e);
                    if(res["error"]){ alert(res["error"]); }
              });
              
                
             } //end of update
            
        
          }).disableSelection();
        
    }

//----


//Сортируем категории
    ui_sort($(".sort_cont"));

	

}); //Конец Ready