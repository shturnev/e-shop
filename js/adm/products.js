$(document).ready(function () {

    var clear_url = window.location.protocol + "//" + window.location.hostname + "/"
        , this_page = window.location.href
        , optHref = clear_url + "adm/options.php";



    //удалить продукт
    $(".js-delete").on("click", function () {

        var href  = $(this).attr("href")
            ,parr = $(this).parents("li");

        $.get(href, function (d) {
            var res = JSON.parse( d );
            if(res.error){ alert(res.error); return false; }

            $(parr).fadeOut("fast").remove();
        });

        return false;
    });



}); //Конец Ready