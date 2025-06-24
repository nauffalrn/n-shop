$(document).ready(function () {
    console.log("Product detail page JS loaded");

    // QUANTITY CONTROLS
    function updateQuantityButtons() {
        const input = $("#quantityInput");
        const value = parseInt(input.val()) || 1;
        const min = parseInt(input.attr("min")) || 1;
        const max = parseInt(input.attr("max")) || 999;

        $(".qty-decrease").prop("disabled", value <= min);
        $(".qty-increase").prop("disabled", value >= max);
    }

    // Initialize
    updateQuantityButtons();

    // Handle clicks
    $(".qty-decrease").on("click", function () {
        const input = $("#quantityInput");
        const value = parseInt(input.val()) || 1;
        const min = parseInt(input.attr("min")) || 1;

        if (value > min) {
            input.val(value - 1);
            updateQuantityButtons();
        }
    });

    $(".qty-increase").on("click", function () {
        const input = $("#quantityInput");
        const value = parseInt(input.val()) || 1;
        const max = parseInt(input.attr("max")) || 999;

        if (value < max) {
            input.val(value + 1);
            updateQuantityButtons();
        }
    });

    // Direct input changes
    $("#quantityInput").on("input", function () {
        const value = parseInt($(this).val()) || 1;
        const min = parseInt($(this).attr("min")) || 1;
        const max = parseInt($(this).attr("max")) || 999;

        if (value < min) $(this).val(min);
        if (value > max) $(this).val(max);

        updateQuantityButtons();
    });
});
