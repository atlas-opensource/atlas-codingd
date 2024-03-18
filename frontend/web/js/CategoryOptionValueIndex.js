// JQuery Ready Handler
jQuery(document).ready(function($)
{
    // Populate the add category option value select box
    populateCategoryDropdownList();

    // Add change category event handler
    $("#category-display_name").change(function(event)
    {
        populateCodeDropdownList();
    });

    // Add change category event handler
    $(".optionSelect").change(function(event)
    {
        // TODO: move this into the above function
        checkAllowMultiple($(this).attr("id"));
    });

    // Add click handler
    // $(".optionSelect").each(function(event))
    // {
    //     // Find the
    //     $(this).find("option[value='notSelected']").attr("disabled","disabled");
    // });

    // Add change category event handler
    $("#categoryoption-id").change(function(event)
    {
        populateSubOptionDropdownList();
    });

    // Add CategoryOptionValue Event Handler
    $("#add-category-option-value-form").submit(function(event)
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
              $.pjax.reload({container:'#category-option-value-grid'});
            }
        })
        .fail(function()
        {
        });

        // Make sure form does not submit
        return false;
    });

    // Add CategoryOptionValue Event Handler
    $("#add-multiple-category-option-value-form").submit(function(event)
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
              $.pjax.reload({container:'#category-option-value-grid'});
            }
        })
        .fail(function()
        {
        });

        // Make sure form does not submit
        return false;
    });
});

function checkAllowMultiple(categoryOptionSelect)
{
    // Remove the allow multiple class
    $("#"+categoryOptionSelect).removeClass("allowMultiple");

    // Check if multiple are allowed
    // Get category id
    categoryId = $("#"+categoryOptionSelect).attr("data-category");

    // Find the count of the selector that just changed and the new count
    var oldCount = parseInt($("#"+categoryOptionSelect).attr("data-count"), 10);

    // Find new count
    var count = oldCount + 1;

    if (oldCount == 0)
    {
        // Get selector corresponding to input where allow multiple value is stored
        allowMultipleSelector = "#category-"+categoryId+"-allowMultiple";
    }
    else
    {
        // Get selector corresponding to input where allow multiple value is stored
        allowMultipleSelector = "#category-"+categoryId+"-allowMultiple-"+oldCount;
    }

    // Get allow multiple value
    allowMultiple = $(allowMultipleSelector).val();

    // Check to see if allow multiple is enabled
    if (allowMultiple == 1)
    {
        // Get the label selector
        labelSelector = "#category-"+categoryId+"-label-"+oldCount;

        // Get the label text
        labelText = $(labelSelector).text();

        // Create the row
        //var row = $("<div class='row'>").insertBefore("#tweet-id");
        var row = $("<div class='row'>").insertAfter($("#"+categoryOptionSelect).parent().parent());

        // Append the label container
        var labelCol = $("<div class='col-sm-3 category-label-container'>").appendTo(row);

        // Add column div for category selector
        var categoryOptionSelectCol = $("<div class='col-sm-3' category-select-container'>").appendTo(row);

        // Define category select name
        var categoryOptionSelectName = "category_options[category-"+categoryId+"-"+count+"]";

        // Define cateogry select id
        var categoryOptionSelectId = "category-"+categoryId+"-options-"+count;

        // Add select to the category select div
        var newCategoryOptionSelect = $("<select name='"+categoryOptionSelectName+"' id='"+categoryOptionSelectId+"' class='form-control optionSelect' data-count='"+count+"' data-category='"+categoryId+"' >").appendTo(categoryOptionSelectCol);

        // Add the label
        var label = $("<label id='category-"+categoryId+"-label-"+count+"' data-count='"+count+"' for='"+categoryOptionSelectId+"'>").appendTo(labelCol);

        // Put label into label container
        $(label).html(labelText);

        // Add hidden inputs
        var newCategoryIdHidden = $("<input type='hidden' name='category_ids[category-"+categoryId+"-"+count+"]' value='"+categoryId+"' >").appendTo(categoryOptionSelectCol);
        var newCategoryAllowMultipleHidden = $("<input type='hidden' name='category_allow_multiple[category-"+categoryId+"-"+count+"]' id='category-"+categoryId+"-allowMultiple-"+count+"' value=1 >").appendTo(categoryOptionSelectCol);

        // Add sub option column
        var categorySubOptionSelectCol = $("<div class='col-sm-3' category-suboption-container'>").appendTo(row);

        // Define category select name
        var categorySubOptionSelectName = "category_suboptions[category-"+categoryId+"]";

        // Define cateogry select id
        var categorySubOptionSelectId = "category-"+categoryId+"-subOptions-"+count;

        // Add select to the category select div
        var newCategorySubOptionSelect = $("<select name='"+categorySubOptionSelectName+"' id='"+categorySubOptionSelectId+"' class='form-control hidden' data-count='"+count+"'>").appendTo(categorySubOptionSelectCol);

        // Remove the option select event handler until we are done
        //$("#"+categoryOptionSelect).unbind( event );

        // Set multicoding to false
        $(allowMultipleSelector).val(0);

        // Populate category option select
        populateCodeDropdownList(categoryId, categoryOptionSelectId);

        // Add event handler to new category option dropdown
        $(newCategoryOptionSelect).change(function(event)
        {
            checkAllowMultiple($(this).attr("id"));
        });

        // Populate and show / hide the suboption dropdown
        //populateSubOptionDropdownList(categoryOptionSelect);

        // Reattach the event handler when both of the above finish

    }
    else
    {
        // Populate the subcoding dropdown
        populateSubOptionDropdownList(categoryOptionSelect);
    }
}

function populateCategoryDropdownList()
{
    let dropdown = $('#category-display_name');

    dropdown.empty();

    dropdown.append('<option disabled="disabled">Choose Category to Code</option>');
    dropdown.prop('selectedIndex', 0);

    const url = 'index.php?r=categoryoptionvalue/getcategoryjson';

    // Populate dropdown with list of provinces
    $.getJSON(url, function (data)
    {
        // Fix offset
        //data = data[0];
        if (data.success)
        {
            $.each(data.payload, function (key, entry)
            {
                dropdown.append($('<option></option>').attr('value', entry.id).text(entry.name));
            });
        }
    });
}

function populateCodeDropdownList(categoryId, categoryOptionId)
{
    if (typeof(categoryId)==='undefined')
    {
        dropdown = $('#categoryoption-id');
        categoryId = $('#category-display_name').val();
    }
    else
    {
        dropdown = $('#'+categoryOptionId);
        // categoryId = $('#category-display_name').val();
    }

    dropdown.empty();
    dropdown.append('<option disabled="disabled">Choose Code</option>');
    dropdown.prop('selectedIndex', 0);

    url = 'index.php?r=categoryoptionvalue/getcodejson&categoryId='+categoryId;

    // Populate dropdown with list of provinces
    $.getJSON(url, function (data)
    {
        // Fix offset
        data = data[0];

        if (data.success)
        {
            $.each(data.payload, function (key, entry)
            {
                dropdown.append($('<option></option>').attr('value', entry.id).text(entry.name));
            })
        }
    });
}

function populateSubOptionDropdownList(optionSelect)
{
    // If we did not get passed a event listener target object.
    if (typeof(optionSelect)==='undefined')
    {
        // Set the option select box to the default (deprecating)
        optionSelect = $('#categoryoption-id');

        // Set argument to old selector
        dropdown = $('#categorysuboptionvalue-category_sub_option_id');

        // Define suboption container to old style
        subOptionContainer = $("#category_sub_option_container");
    }
    else
    {
        // Set the option select box to the default (deprecating)
        optionSelect = $('#'+optionSelect);

        // Get the count (in the case that there are multiple of this category they are numbered)
        count = $(optionSelect).attr("data-count");
        count = parseInt(count, 10);

        // Dynamically build category select selector
        dropdownSelector = "category-" + $(optionSelect).attr("data-category") + "-subOptions-" + count;
        dropdown = $("#"+dropdownSelector);

        // Dynamically build category sub option select selector
        subOptionContainer = dropdown;
    }

    // Empty dropdown
    dropdown.empty();

    // Add default label
    dropdown.append('<option disabled="disabled">Choose Code</option>');

    // Select default label
    dropdown.prop('selectedIndex', 0);

    // Get the option we want sub options for
    category_option_id = $(optionSelect).val();

    // Define URL to request suboptions from
    url = 'index.php?r=categoryoptionvalue/getsuboptionjson&category_option_id='+category_option_id;

    // Populate dropdown with list of provinces
    $.getJSON(url, function (data)
    {
        // Fix offset
        data = data[0];

        if (data.success)
        {
            // Check to make sure we have some options
            if (typeof(data.payload) !== undefined && data.payload !== undefined && data.payload.length > 0)
            {
                // Loop through options and populate box
                $.each(data.payload, function (key, entry)
                {
                    dropdown.append($('<option></option>').attr('value', entry.id).text(entry.name));
                })

                // Unhide the sub category select box
                // $("#category_sub_option_container").removeClass("hidden");
                $(dropdown).removeClass("hidden");
            }
            else
            {
                // Hide the select container
                // $("#category_sub_option_container").addClass("hidden");
                $(dropdown).addClass("hidden");
            }
        }
        else
        {
            // Hide the select container
            // $("#category_sub_option_container").addClass("hidden");
            $(dropdown).addClass("hidden");
        }
    });
}

// Tweet Code
window.onload = (function()
{
    var tweet = document.getElementById("tweet");
    var id = tweet.getAttribute("tweetID");

    twttr.widgets.createTweet(
      id, tweet,
      {
        conversation : 'none',    // or all
        cards        : 'visible',  // or visible
        linkColor    : '#cc0000', // default is blue
        theme        : 'light'    // or dark
      })
    .then (function (el) {
      //el.contentDocument.querySelector(".footer").style.display = "none";
    });
});
