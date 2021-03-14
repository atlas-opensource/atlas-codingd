// JQuery Ready Handler
jQuery(document).ready(function($)
{
    // Add Handle Event Handler
    $("#add-handle-form").submit(function(event)
    {
        // Stop form submission
        event.preventDefault();
        event.stopImmediatePropagation();

        // Submit via ajax
        var data = $(this).serializeArray();

        // console.log($(this));
        var url = $(this).attr('action');

        // Set spinner
        $("#addHandleBtnNoSpinner").addClass("hidden");
        $("#addHandleBtnWithSpinner").removeClass("hidden");

        $.ajax(
        {
            url: url,
            type: 'post',
            dataType: 'json',
            data: data
        })
        .done(function(response)
        {
            // Set spinner
            $("#addHandleBtnWithSpinner").addClass("hidden");
            $("#addHandleBtnNoSpinner").removeClass("hidden");

            if (response.data.success == true)
            {
                // Make request to pull the handle identifiers from the twitter API
                $.pjax.reload({container:'#handle-grid'});
            }
        })
        .fail(function()
        {
            // console.log("error");
        });

        // Make sure form does not submit
        return false;
    });
});
