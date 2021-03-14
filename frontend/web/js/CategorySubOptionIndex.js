// JQuery Ready Handler
jQuery(document).ready(function($)
{
    // Add Handle Event Handler
    $("#add-category-sub-option-form").submit(function(event)
    {
        // Stop form submission
        event.preventDefault();
        event.stopImmediatePropagation();

        // Submit via ajax
        var data = $(this).serializeArray();
        var url = $(this).attr('action');
        $.ajax(
        {
            url: url,
            type: 'post',
            dataType: 'json',
            data: data
        })
        .done(function(response)
        {
            if (response.data.success == true)
            {
                // Make request to pull the handle identifiers from the twitter API
              $.pjax.reload({container:'#category-sub-option-grid'});
            }
        })
        .fail(function()
        {
        });

        // Make sure form does not submit
        return false;
    });
});
