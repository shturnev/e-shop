$(document).ready(function() {


//1ый слайдер
    var goBtns = $("#slider-nav a");

    $('#slider-holder').jCarouselLite({
        auto: 3000
        ,speed: 1000
        ,btnGo: goBtns
    });

    $(goBtns).on("click", function () {
        $(goBtns).not(this).removeClass("active");
        $(this).addClass("active");
        return false;
    });

//2й слайдер
    $(".more-products-holder").jCarouselLite({
         auto: 5000
        ,speed: 1000
        ,btnNext: ".more-nav .next"
        ,btnPrev: ".more-nav .prev"
        ,scroll: 2
        ,visible: 7
    });



});
