<div class="wrap">
    <div id="icon-wpml" class="icon32"><br /></div>
    <h2 style="margin-bottom: 20px;"><?php _e('WPML Configuration Generator', 'wpml-compatibility-test-tools'); ?></h2>
    <form method="post">
        <table class="widefat general_options_table" style="margin-top: 20px;">
            <tbody>
            <tr>
                <td>
                    <table class="widefat">
                        <thead>
                        <tr>
                            <th colspan="3"><?php _e('Custom Post Types', 'wpml-compatibility-test-tools') ?></th>
                        </tr>
                        </thead>
                        <tbody class="ctt">
                        <?php

                        $args = array(
                            '_builtin' => false
                        );

                        $post_types = get_post_types( $args, 'names' );
                        if ($post_types) {

                            foreach ( $post_types as $post_type ):

                                ?><tr>
                                <td>
                                    <label><input class="cb" type="checkbox" name="cpt[<?php echo $post_type; ?>]" value="1" <?php echo isset($_POST['cpt'][$post_type]) ? 'checked' : '' ?>><?php echo $post_type; ?></label>
                                </td>
                                <td width="100px" align="right">
                                    <label><input id="cpt0" type="radio" name="radio_cpt[<?php echo $post_type; ?>]" value="0" <?php echo !isset($_POST[$post_type]) || $_POST[$post_type] == '0' ? 'checked' : '' ?>/>Do nothing</label>
                                </td>
                                <td width="100px">
                                    <label><input id="cpt1" type="radio" name="radio_cpt[<?php echo $post_type; ?>]" value="1" <?php echo isset($_POST[$post_type]) && $_POST[$post_type] == '1' ? 'checked' : '' ?>/>Translate</label>
                                </td>
                                </tr><?php

                            endforeach;

                        }
                        else{
                            _e('<tr><td>No custom post types found</td></tr>', 'wpml-compatibility-test-tools');
                        }

                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th style="font-size:11px;line-height: 5px"><a id="cpt_toggle_all" href="#">Toggle all</a></th>
                            <th style="font-size:11px;line-height: 5px;padding-left: 22px"><a id="cpt0_toggle_all" href="#">Toggle all</a></th>
                            <th style="font-size:11px;line-height: 5px"><a id="cpt1_toggle_all" href="#">Toggle all</a></th>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="widefat">
                        <thead>
                        <tr>
                            <th colspan="3"><?php _e('Custom Taxonomies', 'wpml-compatibility-test-tools') ?></th>
                        </tr>
                        </thead>
                        <tbody class="ctt">
                        <?php



                        $args = array(
                            '_builtin' => false
                        );

                        $taxonomies = get_taxonomies( $args );
                        if ($taxonomies) {

                            foreach ( $taxonomies as $taxonomy ) :

                                ?><tr>
                                <td>
                                    <label><input class="cb" type="checkbox" name="tax[<?php echo $taxonomy; ?>]" value="1" <?php echo isset($_POST['tax'][$taxonomy]) ? 'checked' : '' ?>><?php echo $taxonomy; ?></label>
                                </td>
                                <td width="100px" align="right">
                                    <label><input id="tax0" type="radio" name="radio_tax[<?php echo $taxonomy; ?>]" value="0" <?php echo !isset($_POST[$taxonomy]) || $_POST[$taxonomy] == '0' ? 'checked' : '' ?>/>Do nothing</label>
                                </td>
                                <td width="100px">
                                    <label><input id="tax1" type="radio" name="radio_tax[<?php echo $taxonomy; ?>]" value="1" <?php echo isset($_POST[$taxonomy]) && $_POST[$taxonomy] == '1' ? 'checked' : '' ?>/>Translate</label>
                                </td>
                                </tr><?php

                            endforeach;
                        }
                        else{
                            _e('<tr><td>No custom taxonomies found</td></tr>', 'wpml-compatibility-test-tools');
                        }

                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th style="font-size:11px;line-height: 5px"><a id="tax_toggle_all" href="#">Toggle all</a></th>
                            <th style="font-size:11px;line-height: 5px;padding-left: 22px"><a id="tax0_toggle_all" href="#">Toggle all</a></th>
                            <th style="font-size:11px;line-height: 5px"><a id="tax1_toggle_all" href="#">Toggle all</a></th>
                        </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="widefat">
                        <thead>
                        <tr>
                            <th>
                                <?php _e('Custom fields', 'wpml-compatibility-test-tools');?>
                            </th>
                            <th>
                                <?php _e("Don't translate", 'wpml-compatibility-test-tools')?>
                            </th>
                            <th>
                                <?php _e("Copy from original to translation", 'wpml-compatibility-test-tools')?>
                            </th>
                            <th>
                                <?php _e("Translate", 'wpml-compatibility-test-tools')?>
                            </th>
                        </tr>
                        <tr>
                            <th style="font-size:11px;line-height: 5px"><a class="cf_toggle_all" href="#">Toggle all</a></th>
                            <th style="font-size:11px;line-height: 5px"><a class="cf0_toggle_all" href="#">Toggle all</a></th>
                            <th style="font-size:11px;line-height: 5px"><a class="cf1_toggle_all" href="#">Toggle all</a></th>
                            <th style="font-size:11px;line-height: 5px"><a class="cf2_toggle_all" href="#">Toggle all</a></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th style="font-size:11px;line-height: 5px"><a class="cf_toggle_all" href="#">Toggle all</a></th>
                            <th style="font-size:11px;line-height: 5px"><a class="cf0_toggle_all" href="#">Toggle all</a></th>
                            <th style="font-size:11px;line-height: 5px"><a class="cf1_toggle_all" href="#">Toggle all</a></th>
                            <th style="font-size:11px;line-height: 5px"><a class="cf2_toggle_all" href="#">Toggle all</a></th>
                        </tr>
                        <tr>
                            <th>
                                <?php _e('Custom fields', 'wpml-compatibility-test-tools');?>
                            </th>
                            <th>
                                <?php _e("Don't translate", 'wpml-compatibility-test-tools')?>
                            </th>
                            <th>
                                <?php _e("Copy from original to translation", 'wpml-compatibility-test-tools')?>
                            </th>
                            <th>
                                <?php _e("Translate", 'wpml-compatibility-test-tools')?>
                            </th>
                        </tr>
                        </tfoot>
                        <tbody class="ctt">
                        <?php

                        $custom_fields = wpml_get_custom_fields( );
                        if ($custom_fields) {

                            foreach ( $custom_fields as $custom_field ):

                                ?><tr>
                                <td>
                                    <label><input class="cb" type="checkbox" name="cf[<?php echo $custom_field->meta_key; ?>]" value="1" <?php echo isset($_POST['cf'][$custom_field->meta_key]) ? 'checked' : '' ?>><?php echo $custom_field->meta_key ?></label>
                                </td>
                                <td width="100px" title="<?php _e("Don't translate", 'wpml-compatibility-test-tools')?>">
                                    <input id="cf0" type="radio" name="radio_cf[<?php echo $custom_field->meta_key ?>]" value="ignore" <?php echo !isset($_POST[$custom_field->meta_key]) || $_POST[$custom_field->meta_key] == 'ignore' ? 'checked' : '' ?>/>
                                </td>
                                <td width="100px" title="<?php _e("Copy from original to translation", 'wpml-compatibility-test-tools')?>">
                                    <input id="cf1" type="radio" name="radio_cf[<?php echo $custom_field->meta_key ?>]" value="copy" <?php echo isset($_POST[$custom_field->meta_key]) && $_POST[$custom_field->meta_key] == 'copy' ? 'checked' : '' ?>/>
                                </td>
                                <td width="100px" title="<?php _e("Translate", 'wpml-compatibility-test-tools')?>">
                                    <input id="cf2" type="radio" name="radio_cf[<?php echo $custom_field->meta_key ?>]" value="translate" <?php echo isset($_POST[$custom_field->meta_key]) && $_POST[$custom_field->meta_key] == 'translate' ? 'checked' : '' ?>/>
                                </td>
                                </tr><?php

                            endforeach;

                        }
                        else{
                            _e('<tr><td>No custom fields found</td></tr>', 'wpml-compatibility-test-tools');
                        }

                        ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="widefat">
                        <thead>
                        <tr>
                            <th>
                                <?php _e('Admin Texts', 'wpml-compatibility-test-tools') ?>
                            </th>

                            <th style="width: 200px;font-size: 13px"><?php _e('Options from <strong>wp_options</strong> table:', 'wpml-compatibility-test-tools'); ?></th>
                            <th width="300px" >
                                <dl class="dropdown">
                                    <dt>
                                        <span class="placeholder"><?php _e('- Select options -', 'wpml-compatibility-test-tools'); ?></span>
                                        <span class="selection"></span>
                                    </dt>
                                    <dd>
                                        <div id="multiSelect">
                                            <ul>
                                                <?php

                                                $options = wpml_ctt_options_list();

                                                foreach ($options as $name => $value) {
                                                    echo "<li><input type='checkbox' value='". $name . "' />{$name}</li>";
                                                }

                                                ?>
                                            </ul>
                                        </div>
                                    </dd>
                                </dl>
                            </th>
                        </tr>
                        </thead>
                        <tbody id="result"></tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="float:right">
                        <tr>
                            <th>
                                <label style="margin-right: 20px"><input style="margin: 2px 2px" type="radio" name="save" value="dir" <?php echo !isset($_POST['save']) || $_POST['save'] == 'dir' ? 'checked' : '' ?>/>Save to theme directory</label>
                                <label><input style="margin: 2px 2px" type="radio" name="save" value="file" <?php echo isset($_POST['save']) && $_POST['save'] == 'file' ? 'checked' : '' ?>/>Save to file</label>
                            </th>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr><th><input id="wctt_generate" name="submit" style="float:right;margin-bottom:5px" type="submit" class="button-primary" value="<?php _e('Generate', 'wpml-compatibility-test-tools') ?>" disabled /><?php wp_nonce_field("wctt-generate","_wctt_mighty_nonce"); ?></th></tr>
        </table>
    </form>
</div>