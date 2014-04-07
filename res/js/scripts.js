jQuery(document).ready(function(){

	jQuery( "#string_auto_translate_predefined_templates" ).change(function() {
		jQuery( "#strings_auto_translate_template").val( jQuery( "#string_auto_translate_predefined_templates option:selected").text() );
	});

});