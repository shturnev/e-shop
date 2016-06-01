$(document).ready(function () {


//Открыть / закрыть admBar
    $("#admBar .tymbler").on("click", function () {

        var status = $(this).hasClass("active"),
            cont    = $("#admBar"),
            icon    = $(this).children("i"),
            contW   = -$(cont).outerWidth();

        $(this).toggleClass("active");

        if(!status){
            $(icon).html("&#xE23D;");
            $(cont).animate({"left": 0}, "fast");
        }
        else{
            $(icon).html("&#xE23E;");
            $(cont).animate({"left": contW + "px"}, "fast");
        }

        return false;
    });


}); //Конец Ready