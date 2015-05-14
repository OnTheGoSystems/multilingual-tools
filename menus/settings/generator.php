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
                                    <input type="checkbox" name="cpt[<?php echo $post_type; ?>]" value="1" <?php echo isset($_POST['cpt'][$post_type]) ? 'checked' : '' ?>>
                                    <label><?php echo $post_type; ?></label>
                                </td>
	                            <td width="100px" align="right">
                                    <input id="cpt0" type="radio" name="radio_cpt[<?php echo $post_type; ?>]" value="0" <?php echo !isset($_POST[$post_type]) || $_POST[$post_type] == '0' ? 'checked' : '' ?>/>
									<label>Do nothing</label>
	                            </td>
	                            <td width="100px">
                                    <input id="cpt1" type="radio" name="radio_cpt[<?php echo $post_type; ?>]" value="1" <?php echo isset($_POST[$post_type]) && $_POST[$post_type] == '1' ? 'checked' : '' ?>/>
	                                <label>Translate</label>
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
                                <input type="checkbox" name="tax[<?php echo $taxonomy; ?>]" value="1" <?php echo isset($_POST['tax'][$taxonomy]) ? 'checked' : '' ?>>
                                <label><?php echo $taxonomy; ?></label>
                            </td>
	                        <td width="100px" align="right">
                                <input id="tax0" type="radio" name="radio_tax[<?php echo $taxonomy; ?>]" value="0" <?php echo !isset($_POST[$taxonomy]) || $_POST[$taxonomy] == '0' ? 'checked' : '' ?>/>
								<label>Do nothing</label>
	                        </td>
	                        <td width="100px">
                                <input id="tax1" type="radio" name="radio_tax[<?php echo $taxonomy; ?>]" value="1" <?php echo isset($_POST[$taxonomy]) && $_POST[$taxonomy] == '1' ? 'checked' : '' ?>/>
	                            <label>Translate</label>
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
                                <input type="checkbox" name="cf[<?php echo $custom_field->meta_key; ?>]" value="1" <?php echo isset($_POST['cf'][$custom_field->meta_key]) ? 'checked' : '' ?>>
                                <label><?php echo $custom_field->meta_key ?></label>
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

                $all_options = array_slice( wp_load_alloptions(), 30);
				$tmp_options = array();
				foreach( $all_options as $name => $value ) {
					if(!stristr($name, '_transient')) $tmp_options[$name] = $value;
				}
                $all_options = $tmp_options;

				?>
                    </tbody>
                </table>
			</td>
		</tr>
		<tr>
			<td>
				<table class="widefat">
                        <tr>
                            <th><?php _e('Admin Texts', 'wpml-compatibility-test-tools') ?></th>
                            <th style="float:right">
                            	<label style="text-align:right;width: 235px; margin-top: 3px;"><?php _e('Select options name:', 'wpml-compatibility-test-tools'); ?>
                                    <select name="option_name"><?php
                                        foreach ($all_options as $option => $value) {
                                            echo "<option namevalue='$option'" . ($option == isset($_POST['option_name']) ? ' selected="selected"' : '') .">$option</option>";
                                        } ?>
                                    </select>
                                </label>
	                        </th>
                        </tr>
                </table>
			</td>
		</tr>
        <tr>
            <td>
                <table style="float:right">
                    <tr>
                        <th>
                            <input style="margin: 2px 2px" type="radio" name="save" value="dir" <?php echo !isset($_POST['save']) || $_POST['save'] == 'dir' ? 'checked' : '' ?>/>
                            <label style="margin-right: 20px">Save to theme directory</label>
                            <input style="margin: 2px 2px" type="radio" name="save" value="file" <?php echo isset($_POST['save']) && $_POST['save'] == 'file' ? 'checked' : '' ?>/>
                            <label>Save to file</label>
                        </th>
                    </tr>
                </table>
            </td>
        </tr>
		</tbody>
		<th><input name="submit" style="float:right;margin-bottom:5px" type="submit" class="button-primary" value="<?php _e('Generate', 'wpml-compatibility-test-tools') ?>" /></th>
	</table>
</form>
</div>