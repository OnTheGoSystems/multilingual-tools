jQuery(document).ready(function(){

	jQuery( "#string_auto_translate_predefined_templates" ).change(function() {
		jQuery( "#strings_auto_translate_template").val( jQuery( "#string_auto_translate_predefined_templates option:selected").text() );
	});

	jQuery( "#duplicate_strings_predefined_templates" ).change(function() {
		jQuery( "#duplicate_strings_template").val( jQuery( "#duplicate_strings_predefined_templates option:selected").text() );
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