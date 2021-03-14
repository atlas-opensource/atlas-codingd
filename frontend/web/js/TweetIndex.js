// JQuery Ready Handler
jQuery(document).ready(function()
{
    // Register event handler for refresh tweets button click
    $("#refreshTweetBtn").click(function(event)
    {
        // Define request url to update tweets
        var url = $(this).attr("data-refreshurl");

        // Make ajax request to update tweets
        $.ajax(
        {
            url: url,
            type: 'post',
            dataType: 'json',
        })
        .done(function(response)
        {
            if (response.data.success == true)
            {
                // Update the grid
                $.pjax.reload({container:'#tweet-grid'});
            }
        })
        .fail(function()
        {
        });
    });
});
