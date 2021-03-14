// JQuery Ready Handler
jQuery(document).ready(function($)
{
    // Delete Handle Event Handler
    $('.pjax-delete-link').on('click', function(event)
    {
        // Stop link click default behavior
        event.preventDefault();
        event.stopImmediatePropagation();

        // Define some useful variables
        var deleteUrl = $(this).attr('delete-url');
        var pjaxContainer = $(this).attr('pjax-container');
        var result = confirm('Are you sure you want to delete this?');
        var data = new Object();
        data.id = $(this).parent().parent().attr("data-key");

        // If the user said yes
        if(result)
        {
            $.ajax(
            {
                url: deleteUrl,
                dataType: 'json',
                data: data,
                type: 'post',
                error: function(xhr, status, error)
                {
                    alert('There was an error with your request.' + xhr.responseText);
                },
            }).done(function(data)
            {
                $.pjax.reload('#' + $.trim(pjaxContainer), {timeout: 3000});
            });
        }

        // Make sure to stop execution in caller
        return false;
    });
});

jQuery(document).on('pjax:success', function()
{
    // Delete Handle Event Handler
    $('.pjax-delete-link').on('click', function(event)
    {
        // Stop link click default behavior
        event.preventDefault();
        event.stopImmediatePropagation();

        // Define some useful variables
        var deleteUrl = $(this).attr('delete-url');
        var pjaxContainer = $(this).attr('pjax-container');
        var result = confirm('Are you sure you want to delete this?');
        var data = new Object();
        data.id = $(this).parent().parent().attr("data-key");

        // If the user said yes
        if(result)
        {
            $.ajax(
            {
                url: deleteUrl,
                dataType: 'json',
                data: data,
                type: 'post',
                error: function(xhr, status, error)
                {
                    alert('There was an error with your request.' + xhr.responseText);
                },
            }).done(function(data)
            {
                $.pjax.reload('#' + $.trim(pjaxContainer), {timeout: 3000});
            });
        }

        // Make sure to stop execution in caller
        return false;
    });
});
