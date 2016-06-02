$(document).ready(function () {

    var clear_url = "http://"+window.location.host,
        optHref   = clear_url + "e-shop/adm/option.php";



  /*------------------------------
  EVENTS
  -------------------------------*/
    $(".addPage").on("click", function () {

        $(this).next().slideToggle("fast");
        return false;
    });


    $(".delete").on("click", function () {

        if (!confirm("Удалить?")) {
            return false;
        }

    });


    $(".js-delItem").on("click", function () {

        if(!confirm("Точно?")){return false;}


        var href   = $(this).attr("href"),
            parenT = $(this).parent("li");
        $.get(href, function (d) {
            if(d){
                $(parenT).fadeOut("fast").remove();
            }

        });

        return false;
    });

}); //Конец Ready