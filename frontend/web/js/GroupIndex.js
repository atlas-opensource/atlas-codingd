// JQuery Ready Handler
jQuery(document).ready(function($)
{
    // Add Handle Event Handler
    $("#add-group-form").submit(function(event)
    {
        // Stop form submission
        event.preventDefault();
        event.stopImmediatePropagation();

        // Submit via ajax
        var data = $(this).serializeArray();

        // console.log($(this));
        var url = $(this).attr('action');

        // Set spinner
        $("#addGroupBtnNoSpinner").addClass("hidden");
        $("#addGroupBtnWithSpinner").removeClass("hidden");

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
            $("#addGroupBtnWithSpinner").addClass("hidden");
            $("#addGroupBtnNoSpinner").removeClass("hidden");

            if (response.data.success == true)
            {
                // Make request to pull the handle identifiers from the twitter API
                $.pjax.reload({container:'#group-grid'});
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
