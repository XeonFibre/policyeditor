$(document).ready(function() {
    
    // Contextual buttons show when checkboxes are checked
    $('#main .content input').click(function() {
        if($(this).is(':checked')) {
            $("#context-buttons").show().css('display', 'flex');
        } else {
            $("#context-buttons").hide();
        }
    });

    // Generate progress titles from items with .section-title as their class
    $('.section-title').each(function() {
        var $sectionTitle = $(this).text();
        $('#overlay-container #box #progress ul').append('<li>'+$sectionTitle+'</li>');
        $('#overlay-container #box #progress ul li').first().addClass('current-page-item');
    });
    
    // Multi-step form
    // From: https://codepunk.io/validating-a-single-page-multi-step-html-form/ (with edits)
    $("section[data-step]").hide();
    $("button.button-outline").hide();
    $("section[data-step=1]").show();
    $("input[type='submit']").click(function(e) {
        e.preventDefault();
        var step = $("form").data("step");
        var isValid = true;
        $("section[data-step='" + step + "'] input[required='required']").each(function(idx, elem) {
            $(elem).removeClass("error");
            if($(elem).val().trim() === "") {
                isValid = false;
                $(elem).addClass("error");
            }
        });
        if(isValid) {
            step += 1;
            if(step > $("section[data-step]").length) {
                $(this).closest("form").submit(); //Submit the form to the URL in the action attribute, or you could always do something else.
            }
            $("form").data("step", step);
            $("section[data-step]").hide();
            $("section[data-step='" + step + "']").show();
            $("button.button-outline").show();
            // Update progress bar when the form is progressed
            $('#overlay-container #box #progress ul li').removeClass('current-page-item');
            var sectionTitle = $("section[data-step='" + step + "']").find('.section-title').text();
            $('#overlay-container #box #progress ul li').each(function(index) {
                if($(this).text().match(sectionTitle)) {
                    $(this).addClass('current-page-item');
                }
            });
        }
    });
    $("button.button-outline").click(function(e) {
        e.preventDefault();
        var step = $("form").data("step");
        step -= 1;
        $("form").data("step", step);
        $("section[data-step]").hide();
        $("section[data-step='" + step + "']").show();
        // Update progress bar when the form is progressed
        $('#overlay-container #box #progress ul li').removeClass('current-page-item');
        var sectionTitle = $("section[data-step='" + step + "']").find('.section-title').text();
        $('#overlay-container #box #progress ul li').each(function(index) {
            if($(this).text().match(sectionTitle)) {
                $(this).addClass('current-page-item');
            }
        });
        if(step === 1) {
            $("button.button-outline").hide();
        }
    });

    // When the radio button is selected, remove any entered
    // text from input[text]
    $('.radio-or-input input[type=radio]').change(function() {
        $(this).parent().parent().find('input[type=text]').val('');
    });
    
    // When the text input is selected, uncheck the radio button
    $('.radio-or-input input[type=text]').focusin(function() {
        $(this).parent().parent().find('input[type=radio]').prop("checked", false);
    });
    
    // Open overlay when triggered
    $("#overlay-trigger-add-policy").click(function() {
        $('#overlay-container').show();
    });
    
});