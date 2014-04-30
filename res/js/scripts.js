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



});