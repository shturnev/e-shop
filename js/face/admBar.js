$(document).ready(function () {

    $("#admBar .tymbler").on("click", function () {

        var status = $(this).hasClass("active");

        if(!status){
            $(this).addClass("active");
            $("body").addClass("noScroll");
            $("#admBar").css({"min-width": "100%", "overflow-y": "auto"}).removeClass("noScroll");
            $(".barCont").addClass("active");
        }
        else{
            $(".barCont").removeClass("active");
            $("#admBar").css({"min-width": "100px", "overflow-y": "hidden"});
            $("body").removeClass("noScroll");
            $(this).removeClass("active");

        }

        return false;
    });


}); //Конец Ready