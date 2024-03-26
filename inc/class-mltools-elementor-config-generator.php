<?php

class MLTools_Elementor_Config_Generator
{

    public function __construct() {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( class_exists( 'Sitepress' ) && is_plugin_active( 'elementor/elementor.php' ) ) {
            add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
        }
    }

    public function register_meta_box() {
        $screens = get_post_types();
        global $post;
        $elementor_data = get_post_meta( $post->ID, '_elementor_data', true );

        if ( ! empty( $elementor_data ) ) {
            foreach ( $screens as $screen ) {
                add_meta_box(
                    'wpmlpb_config_generator_box',
                    'WPML - Config Generator for Elementor',
                    array( $this, 'meta_box_html' ),
                    $screen
                );
            }
        }
    }

    public function get_widgets_blacklist() {
        $widgets_blacklist = array();

        if ( class_exists( 'WPML_Elementor_Translatable_Nodes' ) ) {
            $wpml_elementor_translatable_nodes = new WPML_Elementor_Translatable_Nodes();
            $default_widgets                   = $wpml_elementor_translatable_nodes->get_nodes_to_translate();
            $default_widgets                   = apply_filters( 'wpml_elementor_widgets_to_translate', $default_widgets );

            foreach ( $default_widgets as $key => $value ) {
                $widgets_blacklist[] = $key;
            }
        }

        return $widgets_blacklist;
    }

    public function get_settings_blacklist() {
        $blacklist = array();

        $blacklist[] = 'is_external';
        $blacklist[] = 'nofollow';
        $blacklist[] = 'custom_attributes';

        return $blacklist;
    }

    public function get_widgets_list( $elements ) {
        $widgets = array();

        foreach ( $elements as $element ) {
            if ( $element->elType === 'widget' && isset( $element->settings ) && is_object( $element->settings ) ) {
                $widgetType = $element->widgetType;

                $settings = $element->settings;

                if ( is_object( $settings ) ) {
                    $settings                 = (array) get_object_vars( $settings );
                }

                foreach ( $settings as $field_key => $field_value ) {
                    $settings[ $field_key ]   = $this->get_field_from_widget( $field_key, $field_value, $widgetType );
                }

                $widgets[ $widgetType ]               = array();
                $widgets[ $widgetType ]['widgetType'] = $widgetType;
                $widgets[ $widgetType ]['settings']   = $settings;
            }

            if ( ! empty( $element->elements ) ) {
                $widgets                      = array_merge( $widgets, $this->get_widgets_list( $element->elements ) );
            }
        }

        return $widgets;
    }

    public function get_field_from_widget( $field_key, $field_value, $parent = '' ) {
        // Repeater Fields
        if ( is_array( $field_value ) ) {
            $field['fieldKey']           = $field_key;
            $field['fieldType']          = 'repeater_field';
            $field['parent']             = $parent;
            $field['subFields']          = array();

            if ( is_array( $field_value ) && array_key_exists( '0', $field_value ) ) {
                $field_value             = $field_value['0'];
            }

            foreach ( $field_value as $subfield_key => $subfield_value ) {
                $field['subFields'][ $subfield_key ] = $this->get_field_from_widget( $subfield_key, $subfield_value, $field_key );
            }
        }

        // Composite Fields
        elseif ( is_object( $field_value ) ) {
            $field['fieldKey']           = $field_key;
            $field['fieldType']          = 'parent_field';
            $field['parent']             = $parent;
            $field['subFields']          = array();
            $field_value                 = (array) $field_value;

            foreach ( $field_value as $subfield_key => $subfield_value ) {
                $field['subFields'][ $subfield_key ] = $this->get_field_from_widget( $subfield_key, $subfield_value, $field_key );
            }
        }

        // Regular Fields
        else {
            $field['fieldKey']           = $field_key;
            $field['fieldType']          = 'simple_field';
            $field['parent']             = $parent;
            $field['subFields']          = '';
            $field['fieldContent']       = $field_value;
        }

        return $field;
    }

    public function generate_wpml_config_xml( $widgets ) {
        if ( empty( $widgets ) ) {
            return __( 'All widgets on this page are already registered.', 'wpml-compatibility-test-tools' );
        }

        $settings_blacklist           = $this->get_settings_blacklist();
        $xml                          = new SimpleXMLElement( '<wpml-config></wpml-config>' );
        $elementor_widgets            = $xml->addChild( 'elementor-widgets' );
        $registered_widgets           = array();

        foreach ( $widgets as $widget ) {
            if ( in_array( $widget['widgetType'], $registered_widgets ) ) {
                continue;
            }

            $registered_widgets[]        = $widget['widgetType'];
            $widget_xml                  = $elementor_widgets->addChild( 'widget' );
            $widget_xml->addAttribute( 'name', $widget['widgetType'] );
            $fields                      = $widget_xml->addChild( 'fields' );

            foreach ( $widget['settings'] as $key => $value ) {
                if ( in_array( $key, $settings_blacklist ) ) {
                    continue;
                }

                if ( $value['fieldType'] == 'simple_field' ) {
                    $field = $fields->addChild( 'field', $key );
                }
                // Repeater Fields
                elseif ( $value['fieldType'] == 'repeater_field' ) {
                    $field                     = $fields->addChild( 'field', $key );
                    $fields_in_item            = $widget_xml->addChild( 'fields-in-item' );
                    $fields_in_item->addAttribute( 'items_of', $key );

                    $repeater_registered_keys = array();

                    foreach ( $value['subFields'] as $sub_key => $sub_value ) {
                        // Parent fields inside repeater fields
                        if ( $sub_value['fieldType'] == 'parent_field' ) {
                            $repeater_subfield_registered_keys = array();

                            foreach ( $sub_value['subFields'] as $sub_key_child => $sub_value_child ) {
                                if ( in_array( $sub_key_child, $settings_blacklist ) || in_array( $sub_key_child, $repeater_subfield_registered_keys ) ) {
                                    continue;
                                }

                                $field_in_item                       = $fields_in_item->addChild( 'field', $sub_value_child['parent'] . '>' . $sub_key_child );
                                $repeater_subfield_registered_keys[] =  $sub_key_child;
                            }
                        }
                        // Simple fields inside repeater fields
                        else {
                            if ( in_array( $sub_key, $settings_blacklist ) || in_array( $sub_key, $repeater_registered_keys ) ) {
                                continue;
                            }
                            $field_in_item              = $fields_in_item->addChild( 'field', $sub_key );
                            $repeater_registered_keys[] =  $sub_key;
                        }
                    }
                }
                // Parent Field
                elseif ( $value['fieldType'] == 'parent_field' ) {
                    foreach ( $value['subFields'] as $sub_key => $sub_value ) {
                        if ( in_array( $sub_key, $settings_blacklist ) ) {
                            continue;
                        }
                        $field = $fields->addChild( 'field', $sub_value['parent'] . '>' . $sub_key );
                    }
                }
            }
        }

        $dom               = dom_import_simplexml( $xml )->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML( $dom->documentElement, LIBXML_NOXMLDECL );
    }

    public function generate_xml_for_all( $widgets ) {
        return $this->generate_wpml_config_xml( $widgets );
    }

    public function generate_xml_for_missing_widgets( $widgets ) {
        $widgets_blacklist = $this->get_widgets_blacklist();

        foreach ( $widgets as $key => $widget ) {
            if ( in_array( $widget['widgetType'], $widgets_blacklist ) ) {
                unset( $widgets[ $key ] );
            }
        }

        return $this->generate_wpml_config_xml( $widgets );
    }

    public function meta_box_html( $post ) {
        // Variables
        $elementor_data       = get_post_meta( $post->ID, '_elementor_data', true );
        $elementor_data_array = json_decode( $elementor_data );
        $widgets              = $this->get_widgets_list( $elementor_data_array );

        // XML Config only for missing widgets
        echo '<h3>' . __( 'WPML: Elementor Widgets', 'wpml-compatibility-test-tools' ) . '</h3>';
        echo '<p>' . __( 'XML generated for widgets from this page that does not have translation settings.', 'wpml-compatibility-test-tools' ) . '</p>';
        echo '<textarea style="width:100%; min-height: 100px;">';
        echo htmlspecialchars_decode( $this->generate_xml_for_missing_widgets( $widgets ) );
        echo '</textarea>';

        // Debug Information
        $sections = array(
            array(
                'title'       => __( 'WPML Config XML (generated for all widgets in the page)', 'wpml-compatibility-test-tools' ),
                'description' => __( 'WARNING: Using this may overwrite existing settings (including default elementor widgets). Please check it before and use with caution.', 'wpml-compatibility-test-tools' ),
                'content'     => htmlspecialchars_decode( $this->generate_xml_for_all( $widgets ) ),
            ),
            array(
                'title'       => __( 'RAW value from _elementor_data (JSON)', 'wpml-compatibility-test-tools' ),
                'description' => __( 'This is the raw value stored in the _elementor_data meta field.', 'wpml-compatibility-test-tools' ),
                'content'     => print_r( $elementor_data, true ),
            ),
            array(
                'title'       => __( 'Array generated from _elementor_data', 'wpml-compatibility-test-tools' ),
                'description' => __( 'This is the _elementor_data converted into a PHP array.', 'wpml-compatibility-test-tools' ),
                'content'     => print_r( $elementor_data_array, true ),
            ),
            array(
                'title'       => __( 'Extracted Widgets from _elementor_data', 'wpml-compatibility-test-tools' ),
                'description' => __( 'These are the widgets that have been extracted from the _elementor_data array.', 'wpml-compatibility-test-tools' ),
                'content'     => print_r( $widgets, true ),
            ),
        );

        echo '<h5>' . __( 'WPML: Elementor Debug Information', 'wpml-compatibility-test-tools' ) . '</h5>';

        foreach ( $sections as $section ) {
            echo '<details style="border: 1px solid #ddd; padding: 20px; margin-bottom: 10px;">';
            echo '<summary style="font-weight: bold; font-size: 14px; cursor: pointer;">' . __( $section['title'], 'wpml-compatibility-test-tools' ) . '</summary>';
            echo '<p>' . __( $section['description'], 'wpml-compatibility-test-tools' ) . '</p>';
            echo '<textarea style="width:100%; min-height: 100px; margin-top: 10px;">';
            echo $section['content'];
            echo '</textarea>';
            echo '</details>';
        }
    }
}

new MLTools_Elementor_Config_Generator();
