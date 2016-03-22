jQuery( document ).ready(function() {

    var option       = [],
        result       = jQuery( '#result' ),
        submitButton = jQuery( '[type="submit"][id="wctt_generate"]' );

    optionCount();

    // Toggle drop-down on mouse gesture.
    jQuery( '#dropdown' )
        .mouseenter(function() {
            jQuery( '#dropdown dd ul' ).slideToggle( 'fast', function() {
                jQuery( '#ctt_settings #dropdown .toggle' ).show()
            });
        })
        .mouseleave(function() {
            jQuery('#dropdown dd ul').slideToggle({
                duration: 'fast',
                start: function () {
                    jQuery( '#ctt_settings #dropdown .toggle' ).hide();
                }
            });
        })

    // Update option count on checkbox selection.
    jQuery( '#dropdown input[type="checkbox"]' ).change(function() {
        optionCount()
    });

    // Enable submit button if any checkbox is selected.
    jQuery( document ).on( 'click', '[type="checkbox"]', function() {
        buttonToggle();
    });

    jQuery( '#multiSelect [type="checkbox"]' ).on( 'click', function () {

        // Collecting options from Multi-Select.
        option = jQuery( '#multiSelect [type="checkbox"]:checked' ).map(function( _, i ) {
            return jQuery( i ).val();
        }).get();

        if ( option.length != 0 ) {

            var data = {
                'options'            : option,
                'action'             : "wpml_ctt_action",
                '_wctt_mighty_nonce' : jQuery( '#_wctt_mighty_nonce' ).val()
            };

            jQuery.post( ajax_object.ajax_url, data, function ( response ) {

                var output, data = jQuery.parseJSON( response );

                output = '<ul id="tree">';
                jQuery.each( data, generateList );

                // Generating checkboxes option tree.
                function generateList( key, value ) {
                    key = jQuery( '<div />' ).text( key ).html();

                    if ( jQuery.isPlainObject( value ) ) {
                        output += '<li><input id="at" type="checkbox" name="at[' + key + ']" value="0"> [ ' + key + ' ] => ' + '</><ul>';
                        jQuery.each( value, generateList );
                        output += '</ul></li>';
                    } else {
                        value   = jQuery('<div />').text( value ).html();
                        output += '<li><input id="at" type="checkbox" name="at[' + key + ']" value="0"> [ ' + key + ' ] => <strong>' + value + '</strong></li>';
                    }
                }
                output += '</ul>';

                jQuery( '#tree' ).remove();

                var content = jQuery( output ).hide();
                result.append( content );
                content.fadeIn();

                jQuery( '#at-notice' ).hide();
            });
        } else {
            jQuery( '#tree' ).remove();
            jQuery( '#at-notice' ).fadeIn();
        }
    });

    // Multi-check options tree.
    result.on( 'click', '#tree [type="checkbox"]', function() {
        var current = jQuery( this );

        if ( this.checked ) {
            current.parentsUntil('ul#tree').children( '[type="checkbox"]' ).prop( 'checked', true );
        } else {
            current.parent().find( '[type="checkbox"]' ).prop( 'checked', false );
        }
    });

	jQuery( "#string_auto_translate_predefined_templates" ).change(function() {
		jQuery( "#strings_auto_translate_template" ).val( jQuery( "#string_auto_translate_predefined_templates" ).find( "option:selected" ).text() );
	});

	jQuery( "#duplicate_strings_predefined_templates" ).change(function() {
		jQuery( "#duplicate_strings_template" ).val( jQuery( "#duplicate_strings_predefined_templates" ).find( "option:selected" ).text() );
	});

	jQuery( "#strings_auto_translate_action_translate" ).click(function() {
		var question = "All existing strings translations will be replaced with new values.\nAre you sure you want to do this?";
		return confirm( question );
	});

    // Provides toggle all functionality.
    jQuery( '.toggle' ).click( function ( event ) {
        event.preventDefault();

        var group = jQuery( 'input[id=' + this.id + ']' );

        if ( group.attr( 'type' ) == 'radio' ) {
            group.prop( 'checked', true );
            jQuery( 'input[type="checkbox"][id="' + this.id.slice( 0, -2 ) + '"]' ).prop( 'checked', true );
            buttonToggle();
        } else {
            group.prop( 'checked', ! group.prop( 'checked' ) );
            buttonToggle();
            optionCount();
        }
    });

    // Automatically check checkbox if radio is changed.
    jQuery( 'input[type=radio]' ).change( function() {
        jQuery( 'input[type="checkbox"][name="_' + this.name + '"]' ).prop( 'checked', true );
        buttonToggle();
    });

    // Button toggle disable.
    function buttonToggle(){
        submitButton.attr( 'disabled', !jQuery( '[type="checkbox"]' ).not( '[class="option"]' ).is( ':checked' ));
    }

    // Count selected options from dropdown.
    function optionCount() {
        var selectedCount   = jQuery( '#dropdown input[type="checkbox"]:checked' ).length

        if ( selectedCount > 0 ) {
            jQuery('.placeholder').text('- Select options (' + selectedCount + ') -')
        } else {
            jQuery('.placeholder').text('- Select options -')
        }
    }
});