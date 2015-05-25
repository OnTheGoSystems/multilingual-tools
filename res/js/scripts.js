jQuery(document).ready(function(){

    var checkboxes = jQuery("input[type='checkbox']"),
        submitButton = jQuery("input[type='submit']"),
        result = jQuery('#result');

    // Enable submit button if any checkbox is selected.
    checkboxes.click(function() {
        submitButton.attr("disabled", !checkboxes.is(":checked"));
    });

    // Generating checkboxes option tree.
    jQuery('select').change(function() {
        var option = jQuery('select option:selected').val();

        if (option !== 'none') {

            var data = {
                'action': 'wpml_ctt_action',
                'option': option
            };

            jQuery.post(ajax_object.ajax_url, data, function (response) {

                var data = jQuery.parseJSON(response),
                    output;

                output = '<ul id="tree">';
                jQuery.each(data, test);

                function test(key, value) {
                    if (jQuery.isPlainObject(value)) {
                        output += '<li><input type="checkbox" name="at[' + key + ']" value="0"><strong> [ ' + key + ' ] => ' + '</strong><ul>';
                        jQuery.each(value, test);
                        output += '</ul></li>';
                    } else {
                        output += '<li><input type="checkbox" name="at[' + key + ']" value="0"> [ ' + key + ' ] => <strong>' + value + '</strong></li>';
                    }
                }

                output += '</ul>';

                result.hide('fast', function () {
                    jQuery('#result').html(output);
                    submitButton.attr("disabled",true);
                });
                result.show('fast');

            });
        } else {
            result.hide('slow', function(){
                submitButton.attr("disabled",true);
            });
        }
    });

    // Multi-check options tree.
    result.on("click", "#tree input[type=checkbox]", function() {
        submitButton.attr("disabled", !jQuery(this).is(":checked"));
        var cur = jQuery(this);
        cur.next().next().find('input[type=checkbox]').prop('checked', this.checked);
        if (this.checked) {
            cur.parents('li').children('input[type=checkbox]').prop('checked', true);
        } else while (cur.attr('id') != 'tree' && !(cur = cur.parent().parent()).find('input:checked').length) {
            cur.prev().prev().prop('checked', false);
        }
    });

	jQuery( "#string_auto_translate_predefined_templates" ).change(function() {
		jQuery( "#strings_auto_translate_template").val( jQuery( "#string_auto_translate_predefined_templates").find("option:selected").text() );
	});

	jQuery( "#duplicate_strings_predefined_templates" ).change(function() {
		jQuery( "#duplicate_strings_template").val( jQuery( "#duplicate_strings_predefined_templates").find("option:selected").text() );
	});

	jQuery( "#strings_auto_translate_action_translate" ).click(function() {
		var question = "All existing strings translations will be replaced with new values.\nAre you sure you want to do this?";
		return confirm(question);
	});

	jQuery('#duplicate_strings_to_translate_toggle_all').click(function(event) {
			event.preventDefault();
			var checkBoxes = jQuery("input[type='checkbox'][name^='duplicate_strings_to_translate']");
			checkBoxes.prop("checked", !checkBoxes.prop("checked"));
	});

	jQuery('#translate_strings_active_languages_toggle_all').click(function(event) {
		event.preventDefault();
		var checkBoxes = jQuery("input[type='checkbox'][name^='active_languages']");
		checkBoxes.prop("checked", !checkBoxes.prop("checked"));
	});

    jQuery('#cpt_toggle_all').click(function(event) {
        event.preventDefault();
        var checkBoxes = jQuery("input[type='checkbox'][name^='cpt']");
        checkBoxes.prop("checked", !checkBoxes.prop("checked"));
    });

    jQuery('#cpt0_toggle_all').click(function(event) {
        event.preventDefault();
        var radioGroup = jQuery("input[type='radio'][id^='cpt0']");
        radioGroup.prop("checked", true);
    });

    jQuery('#cpt1_toggle_all').click(function(event) {
        event.preventDefault();
        var radioGroup = jQuery("input[type='radio'][id^='cpt1']");
        radioGroup.prop("checked", true);
    });

    jQuery('#tax_toggle_all').click(function(event) {
        event.preventDefault();
        var checkBoxes = jQuery("input[type='checkbox'][name^='tax']");
        checkBoxes.prop("checked", !checkBoxes.prop("checked"));
    });

    jQuery('#tax0_toggle_all').click(function(event) {
        event.preventDefault();
        var radioGroup = jQuery("input[type='radio'][id^='tax0']");
        radioGroup.prop("checked", true);
    });

    jQuery('#tax1_toggle_all').click(function(event) {
        event.preventDefault();
        var radioGroup = jQuery("input[type='radio'][id^='tax1']");
        radioGroup.prop("checked", true);
    });

    jQuery('.cf_toggle_all').click(function(event) {
        event.preventDefault();
        var checkBoxes = jQuery("input[type='checkbox'][name^='cf']");
        checkBoxes.prop("checked", !checkBoxes.prop("checked"));
    });

    jQuery('.cf0_toggle_all').click(function(event) {
        event.preventDefault();
        var radioGroup = jQuery("input[type='radio'][id^='cf0']");
        radioGroup.prop("checked", true);
    });

    jQuery('.cf1_toggle_all').click(function(event) {
        event.preventDefault();
        var radioGroup = jQuery("input[type='radio'][id^='cf1']");
        radioGroup.prop("checked", true);
    });

    jQuery('.cf2_toggle_all').click(function(event) {
        event.preventDefault();
        var radioGroup = jQuery("input[type='radio'][id^='cf2']");
        radioGroup.prop("checked", true);
    });

});