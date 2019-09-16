<div class="wrap">
    <div id="icon-wpml" class="icon32"><br /></div>
    <h2><?php _e('WPML Configuration Generator', 'wpml-compatibility-test-tools'); ?></h2>
    <form method="post">
        <table id="wctt-generator" class="widefat general_options_table" style="margin-top: 20px;">
            <tbody>
            <tr>
                <td>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th>
                                    <h3><?php _e('Custom Post Types', 'wpml-compatibility-test-tools') ?></h3>
                                </th>
                                <th>
		                            <?php _e( "Do nothing", 'wpml-compatibility-test-tools' )?>
                                </th>
                                <th>
		                            <?php _e( "Translatable - only show translated items", 'wpml-compatibility-test-tools' )?>
                                </th>
                                <th>
		                            <?php _e( "Translatable - use translation if available or fallback to default language", 'wpml-compatibility-test-tools' )?>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="wctt">
                        <?php $args = array( '_builtin' => false );

                        $post_types = get_post_types( $args, 'names' );

                        if ( $post_types ) :
                            foreach ( $post_types as $post_type ):
                                $post_type = esc_attr( $post_type ); ?>
                                <tr>
                                    <td>
                                        <label><input id="cpt" type="checkbox" name="_cpt[<?php echo $post_type; ?>]" value="1" <?php checked( isset( $_POST['_cpt'][$post_type] ) ) ?>><?php echo $post_type; ?></label>
                                    </td>
                                    <td>
                                        <input id="cpt_0" type="radio" name="cpt[<?php echo $post_type; ?>]" value="0" <?php checked( ! isset( $_POST['_cpt'][$post_type] ) || $_POST['cpt'][$post_type] === '0' ) ?>/>
                                    </td>
                                    <td>
                                        <input id="cpt_1" type="radio" name="cpt[<?php echo $post_type; ?>]" value="1" <?php checked( isset( $_POST['_cpt'][$post_type] ) && $_POST['cpt'][$post_type] && $_POST['cpt'][$post_type] === '1' ) ?>/>
                                    </td>
                                    <td>
                                        <input id="cpt_2" type="radio" name="cpt[<?php echo $post_type; ?>]" value="2" <?php checked( isset( $_POST['_cpt'][$post_type] ) && $_POST['cpt'][$post_type] && $_POST['cpt'][$post_type] === '2' ) ?>/>
                                    </td>
                                </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th><a id="cpt" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cpt_0" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cpt_1" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cpt_2" class="toggle" href="#">Toggle all</a></th>
                            </tr>
                        </tfoot>
                        <?php else : ?>
                        <tr>
                            <td><?php _e( 'No custom post types found', 'wpml-compatibility-test-tools' ); ?></td>
                        </tr>
                        </tbody>
                        <?php endif; ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th>
                                    <h3><?php _e('Custom Taxonomies', 'wpml-compatibility-test-tools') ?></h3>
                                </th>
                                <th>
		                            <?php _e( "Do nothing", 'wpml-compatibility-test-tools' )?>
                                </th>
                                <th>
		                            <?php _e( "Translatable - only show translated items", 'wpml-compatibility-test-tools' )?>
                                </th>
                                <th>
		                            <?php _e( "Translatable - use translation if available or fallback to default language", 'wpml-compatibility-test-tools' )?>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="wctt">
                        <?php $taxonomies = get_taxonomies( $args );

                        if ( $taxonomies ) :
                            foreach ( $taxonomies as $taxonomy ) :
                                $taxonomy = esc_attr( $taxonomy ); ?>
                                <tr>
                                    <td>
                                        <label><input id="tax" type="checkbox" name="_tax[<?php echo $taxonomy; ?>]" value="1" <?php checked( isset( $_POST['_tax'][$taxonomy] ) ) ?>><?php echo $taxonomy; ?></label>
                                    </td>
                                    <td>
                                        <input id="tax_0" type="radio" name="tax[<?php echo $taxonomy; ?>]" value="0" <?php checked( ! isset( $_POST['_tax'][$taxonomy] ) || $_POST['tax'][$taxonomy] === '0' ) ?>/>
                                    </td>
                                    <td>
                                        <input id="tax_1" type="radio" name="tax[<?php echo $taxonomy; ?>]" value="1" <?php checked( isset( $_POST['_tax'][$taxonomy] ) && $_POST['tax'][$taxonomy] && $_POST['tax'][$taxonomy] === '1') ?>/>
                                    </td>
                                    <td>
                                        <input id="tax_2" type="radio" name="tax[<?php echo $taxonomy; ?>]" value="2" <?php checked( isset( $_POST['_tax'][$taxonomy] ) && $_POST['tax'][$taxonomy] && $_POST['tax'][$taxonomy] === '2') ?>/>
                                    </td>
                                </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th><a id="tax" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="tax_0" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="tax_1" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="tax_2" class="toggle" href="#">Toggle all</a></th>
                            </tr>
                        </tfoot>
                        <?php else : ?>
                        <tr>
                            <td><?php _e( 'No custom taxonomies found', 'wpml-compatibility-test-tools' ); ?></td>
                        </tr>
                        </tbody>
                        <?php endif; ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="widefat">
                        <?php $custom_fields = wpml_get_custom_fields( );

                        if ( $custom_fields ) : ?>
                        <thead>
                            <tr>
                                <th>
                                    <h3><?php _e( 'Custom fields', 'wpml-compatibility-test-tools' );?></h3>
                                </th>
                                <th>
                                    <?php _e( "Don't translate", 'wpml-compatibility-test-tools' )?>
                                </th>
                                <th>
                                    <?php _e( "Copy", 'wpml-compatibility-test-tools' )?>
                                </th>
                                <th>
		                            <?php _e( "Copy once", 'wpml-compatibility-test-tools' )?>
                                </th>
                                <th>
                                    <?php _e( "Translate", 'wpml-compatibility-test-tools' )?>
                                </th>
                            </tr>
                            <tr>
                                <th><a id="cf" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cf_0" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cf_1" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cf_2" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cf_3" class="toggle" href="#">Toggle all</a></th>
                            </tr>
                        </thead>
                        <tbody class="wctt">
                        <?php
                            foreach ( $custom_fields as $custom_field ):
                                $custom_field->meta_key = esc_attr( $custom_field->meta_key ); ?>
                                <tr>
                                    <td>
                                        <label><input id="cf" type="checkbox" name="_cf[<?php echo $custom_field->meta_key; ?>]" value="1" <?php checked( isset( $_POST['_cf'][$custom_field->meta_key] ) ) ?>><?php echo $custom_field->meta_key ?></label>
                                    </td>
                                    <td title="<?php _e("Don't translate", 'wpml-compatibility-test-tools')?>">
                                        <input id="cf_0" type="radio" name="cf[<?php echo $custom_field->meta_key ?>]" value="ignore" <?php checked( ! isset( $_POST['_cf'][$custom_field->meta_key] ) || $_POST['cf'][$custom_field->meta_key] == 'ignore' ) ?>/>
                                    </td>
                                    <td title="<?php _e("Copy", 'wpml-compatibility-test-tools')?>">
                                        <input id="cf_1" type="radio" name="cf[<?php echo $custom_field->meta_key ?>]" value="copy" <?php checked( isset( $_POST['_cf'][$custom_field->meta_key] ) && $_POST['cf'][$custom_field->meta_key] == 'copy' ) ?>/>
                                    </td>
                                    <td title="<?php _e("Copy once", 'wpml-compatibility-test-tools')?>">
                                        <input id="cf_2" type="radio" name="cf[<?php echo $custom_field->meta_key ?>]" value="copy-once" <?php checked( isset( $_POST['_cf'][$custom_field->meta_key] ) && $_POST['cf'][$custom_field->meta_key] == 'copy-once' ) ?>/>
                                    </td>
                                    <td title="<?php _e("Translate", 'wpml-compatibility-test-tools')?>">
                                        <input id="cf_3" type="radio" name="cf[<?php echo $custom_field->meta_key ?>]" value="translate" <?php checked( isset( $_POST['_cf'][$custom_field->meta_key] ) && $_POST['cf'][$custom_field->meta_key] == 'translate' ) ?>/>
                                    </td>
                                </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th><a id="cf" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cf_0" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cf_1" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cf_2" class="toggle" href="#">Toggle all</a></th>
                                <th><a id="cf_3" class="toggle" href="#">Toggle all</a></th>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e( 'Custom fields', 'wpml-compatibility-test-tools' );?>
                                </th>
                                <th>
                                    <?php _e( "Don't translate", 'wpml-compatibility-test-tools' )?>
                                </th>
                                <th>
                                    <?php _e( "Copy", 'wpml-compatibility-test-tools' )?>
                                </th>
                                <th>
		                            <?php _e( "Copy once", 'wpml-compatibility-test-tools' )?>
                                </th>
                                <th>
                                    <?php _e( "Translate", 'wpml-compatibility-test-tools' )?>
                                </th>
                            </tr>
                        </tfoot>
                        <?php else : ?>
                        <thead>
                            <tr>
                                <th colspan="5"><?php _e( 'Custom fields', 'wpml-compatibility-test-tools' ); ?></th>
                            </tr>
                        </thead>
                        <tbody class="wctt">
                            <tr>
                                <td><?php _e( 'No custom fields found', 'wpml-compatibility-test-tools' ); ?></td>
                            </tr>
                        </tbody>
                        <?php endif; ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th>
                                    <h3><?php _e( 'Admin Texts', 'wpml-compatibility-test-tools' ) ?></h3>
                                </th>
                                <th class="column-desc"><?php _e( 'Options from <strong>wp_options</strong> table:', 'wpml-compatibility-test-tools' ); ?></th>
                                <th id="dropdown-column">
                                    <dl id="dropdown">
                                        <dt>
                                            <span class="placeholder"><?php _e( '- Select options -', 'wpml-compatibility-test-tools' ); ?></span>
                                            <span class="selection"></span>
                                        </dt>
                                        <dd>
                                            <div id="multiSelect">
                                                <ul><?php $options = wpml_ctt_options_list();

                                                    foreach ( $options as $name => $value ) :

                                                        $name = esc_attr( $name );

                                                        echo "<li><input class='option' type='checkbox' value='". $name . "' />{$name}</li>";

                                                    endforeach; ?>
                                                </ul>
                                            </div>
                                        </dd>
                                    </dl>
                                </th>
                            </tr>
                            <tr id="at-toggle">
                                <th colspan="3"><a id="at" class="toggle" href="#">Toggle all</a></th>
                            </tr>
                        </thead>
                        <tbody id="result">
                            <tr id="at-notice">
                                <td><?php _e( 'No option(s) selected', 'wpml-compatibility-test-tools' ); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table id="mt-shortcodes" class="widefat">
                        <thead>
                        <tr>
                            <th><h3><?php _e( 'Shortcodes', 'wpml-compatibility-test-tools' ) ?></h3></th>
                            <th>
                                <a href="#" id="add-shortcode-button" class="button-secondary">
					                <?php _e( 'Add shortcode', 'wpml-compatibility-test-tools' ) ?>
                                </a>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="wctt">
                        <tr id="shortcode-notice">
                            <td><?php _e( 'No shortcode(s) added', 'wpml-compatibility-test-tools' ); ?></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="2"><a id="remove-all" href="#">Remove all</a></th>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr>
                <th id="save">
                    <label><input type="radio" name="save" value="dir" checked /> Save to theme directory</label>
                    <label><input type="radio" name="save" value="file" /> Save to file</label>
                </th>
            </tr>
            <tr>
                <th><input name="wctt-generator-submit" type="submit" class="button-primary" value="<?php _e( 'Generate', 'wpml-compatibility-test-tools' ) ?>" disabled /><?php wp_nonce_field( "wctt-generate","_wctt_mighty_nonce" ); ?></th>
            </tr>
        </table>
    </form>
</div>