$(document).ready(function () {


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

}); //Конец Ready