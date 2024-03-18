// JQuery Ready Handler
jQuery(document).ready(function($)
{
    // Populate the add category option value select box
    populateEmailDropdownList();

    // Add CategoryOptionValue Event Handler
    $("#add-group-member-form").submit(function(event)
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
                $.pjax.reload({container:'#group-member-grid'});
            }
        })
        .fail(function()
        {
        });

        // Make sure form does not submit
        return false;
    });
});

function populateEmailDropdownList()
{
    let dropdown = $('#user-id');

    dropdown.empty();

    dropdown.append('<option disabled="disabled">Choose User to Add</option>');
    dropdown.prop('selectedIndex', 0);

    const url = 'index.php?r=groupmember/getemailjson';

    // Populate dropdown with list of provinces
    $.getJSON(url, function (data)
    {
        // Fix offset
        data = data[0];
        if (data.success)
        {
            $.each(data.payload, function (key, entry)
            {
                dropdown.append($('<option></option>').attr('value', entry.id).text(entry.email));
            })
        }
        else {
            // console.log(data.message);
        }
    });
}
