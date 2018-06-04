$(document).ready(function() {
    
    // Contextual buttons show when checkboxes are checked
    $('#main .content input[type=checkbox]').click(function() {
        if($(this).is(':checked')) {
            // Hide buttons that only apply for a single checked item
            if($('#main .content input[type=checkbox]:checked').length > 1) {
                $(".show-on-single-checkbox").css("background-color","#999999");
                $(".show-on-single-checkbox").prop('disabled', true);
                $(".show-on-single-checkbox").prop('title', 'The Test function is only available when a single Policy is selected.');
            }
            $("#context-buttons").show().css('display', 'flex');
        } else {
            // Show buttons that only apply for a single checked item
            if($('#main .content input[type=checkbox]:checked').length == 1) {
                $(".show-on-single-checkbox").css("background-color","#356695");
                $(".show-on-single-checkbox").prop('disabled', false);
                $(".show-on-single-checkbox").prop('title', '');
            }
            if($('#main .content input[type=checkbox]:checked').length == 0) {
                $("#context-buttons").hide();
            }
        }
    });

    $(".overlay-trigger-add-policy-entry").click(function() {
        $("#overlay-container #progress ul").empty();
        $.ajax({
            url: "forms/add-policy-entry.php",
            success: function(html){
                // Insert HTML from url into #content
                $("#overlay-container #content").html(html);
                // Generate progress titles from items with .section-title as their class
                $('.section-title').each(function() {
                    var $sectionTitle = $(this).text();
                    $('#overlay-container #box #progress ul').append('<li>'+$sectionTitle+'</li>');
                    $('#overlay-container #box #progress ul li').first().addClass('current-page-item');
                });
                // If the cancel button is pressed, hide the overlay container
                $('#overlay-container .cancel').click(function(e) {
                    e.preventDefault();
                    $('#overlay-container').hide();
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
                // Show the overlay now that all AJAX is completed.
                $('#overlay-container').show();
            }
        });
    });
    
    $(".overlay-trigger-add-network").click(function() {
        $("#overlay-container #progress ul").empty();
        $.ajax({
            url: "forms/add-network.php",
            success: function(html){
                // Insert HTML from url into #content
                $("#overlay-container #content").html(html);
                // Generate progress titles from items with .section-title as their class
                $('.section-title').each(function() {
                    var $sectionTitle = $(this).text();
                    $('#overlay-container #box #progress ul').append('<li>'+$sectionTitle+'</li>');
                    $('#overlay-container #box #progress ul li').first().addClass('current-page-item');
                });
                // If the cancel button is pressed, hide the overlay container
                $('#overlay-container .cancel').click(function(e) {
                    e.preventDefault();
                    $('#overlay-container').hide();
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
                // Show the overlay now that all AJAX is completed.
                $('#overlay-container').show();
            }
        });
    });
    
    $(".overlay-trigger-add-endpoint").click(function() {
        $("#overlay-container #progress ul").empty();;
        $.ajax({
            url: "forms/add-endpoint.php",
            success: function(html){
                // Insert HTML from url into #content
                $("#overlay-container #content").html(html);
                // Generate progress titles from items with .section-title as their class
                $('.section-title').each(function() {
                    var $sectionTitle = $(this).text();
                    $('#overlay-container #box #progress ul').append('<li>'+$sectionTitle+'</li>');
                    $('#overlay-container #box #progress ul li').first().addClass('current-page-item');
                });
                // If the cancel button is pressed, hide the overlay container
                $('#overlay-container .cancel').click(function(e) {
                    e.preventDefault();
                    $('#overlay-container').hide();
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
                // Show the overlay now that all AJAX is completed.
                $('#overlay-container').show();
            }
        });
    });
    
    $(".overlay-trigger-add-policy").click(function() {
        $("#overlay-container #progress ul").empty();
        $.ajax({
            url: "forms/add-policy.php",
            success: function(html){
                // Insert HTML from url into #content
                $("#overlay-container #content").html(html);
                // Generate progress titles from items with .section-title as their class
                $('.section-title').each(function() {
                    var $sectionTitle = $(this).text();
                    $('#overlay-container #box #progress ul').append('<li>'+$sectionTitle+'</li>');
                    $('#overlay-container #box #progress ul li').first().addClass('current-page-item');
                });
                // If the cancel button is pressed, hide the overlay container
                $('#overlay-container .cancel').click(function(e) {
                    e.preventDefault();
                    $('#overlay-container').hide();
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
                // Show the overlay now that all AJAX is completed.
                $('#overlay-container').show();
            }
        });
    });
    
    $(".overlay-trigger-test-policy").click(function() {
        $("#overlay-container #progress ul").empty();
        $.ajax({
            url: "forms/test-policy.php",
            success: function(html){
                // Insert HTML from url into #content
                $("#overlay-container #content").html(html);
                // Generate progress titles from items with .section-title as their class
                $('.section-title').each(function() {
                    var $sectionTitle = $(this).text();
                    $('#overlay-container #box #progress ul').append('<li>'+$sectionTitle+'</li>');
                    $('#overlay-container #box #progress ul li').first().addClass('current-page-item');
                });
                // If the cancel button is pressed, hide the overlay container
                $('#overlay-container .cancel').click(function(e) {
                    e.preventDefault();
                    $('#overlay-container').hide();
                });
                // Copy the selected checkbox value to a hidden input
                //$(".clone-value-dst").val($(".clone-value-src:checked"));
                var selectedPolicy = $(".clone-value-src:checked").val();
                $(".clone-value-dst").val(selectedPolicy);
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
                // Show the overlay now that all AJAX is completed.
                $('#overlay-container').show();
            }
        });
    });
    
    $(".overlay-trigger-export-policy").click(function() {
        $("#overlay-container #progress ul").empty();
        $.ajax({
            url: "forms/export-policy.php",
            success: function(html){
                // Insert HTML from url into #content
                $("#overlay-container #content").html(html);
                // Generate progress titles from items with .section-title as their class
                $('.section-title').each(function() {
                    var $sectionTitle = $(this).text();
                    $('#overlay-container #box #progress ul').append('<li>'+$sectionTitle+'</li>');
                    $('#overlay-container #box #progress ul li').first().addClass('current-page-item');
                });
                // If the cancel button is pressed, hide the overlay container
                $('#overlay-container .cancel').click(function(e) {
                    e.preventDefault();
                    $('#overlay-container').hide();
                });
                // Copy the selected checkbox value to a hidden input
                //$(".clone-value-dst").val($(".clone-value-src:checked"));
                var selectedPolicy = $(".clone-value-src:checked").val();
                $(".clone-value-dst").val(selectedPolicy);
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
                // Show the overlay now that all AJAX is completed.
                $('#overlay-container').show();
            }
        });
    });
    
    // Handle certain keystrokes
    $(document).keyup(function(e) {
        // If escape key is pressed, hide the overlay container
        if(e.keyCode === 27) {
            $('#overlay-container').hide();
        }
    });
    
    if($("#main .content table tr").hasClass("test-policy-denied")) {
        $("#context-info .policy-test-action").attr("src","img/policytest-denied.png");
    } else if($("#main .content table tr").hasClass("test-policy-permitted")) {
        $("#context-info .policy-test-action").attr("src","img/policytest-permitted.png");
    } else {
        $("#context-info .policy-test-action").attr("src","img/policytest-denied.png");
    }
 
});