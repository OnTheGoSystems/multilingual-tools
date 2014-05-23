<div class="wrap">
	<div id="icon-wpml" class="icon32"><br /></div>
	<h2><?php _e('WordPress Configuration', 'wpml-compatibility-test-tools'); ?></h2>



	<table class="widefat general_options_table" style="margin-top: 20px;">
		<thead>
		<tr>
			<th><?php _e('Custom objects', 'wpml-compatibility-test-tools') ?></th>
		</tr>
		</thead>

		<tbody>

		<tr>
			<td><h4><?php _e('Custom Post Types', 'wpml-compatibility-test-tools') ?></h4></td>
		</tr>


		<tr>
			<td>

				<?php

					$args = array(
						'_builtin' => false
					);

					$post_types = get_post_types( $args, 'names' );

					if ($post_types) {
						echo '<ul>';
						foreach ( $post_types as $post_type ) {

							echo '<li>' . $post_type . '</li>';
						}
						echo '</ul>';

					}
					else{
						_e('No custom post types found', 'wpml-compatibility-test-tools');
					}

				?>



			</td>
		</tr>


		<tr>
			<td><h4><?php _e('Custom Taxonomies', 'wpml-compatibility-test-tools') ?></h4></td>
		</tr>

		<tr>
			<td>


				<?php

				$args = array(
					'_builtin' => false
				);

				$taxonomies = get_taxonomies( $args );

				if ($taxonomies) {
					echo '<ul>';
					foreach ( $taxonomies as $taxonomy ) {

						echo '<li>' . $taxonomy . '</li>';
					}
					echo '</ul>';

				}
				else{
					_e('No custom taxonomies found', 'wpml-compatibility-test-tools');
				}

				?>



			</td>
		</tr>

		<tr>
			<td><h4><?php _e('Custom Fields', 'wpml-compatibility-test-tools') ?></h4></td>
		</tr>

		<tr>
			<td>


				<?php


				$custom_fields = wpml_get_custom_fields( );

				if ($custom_fields) {
					echo '<ul>';
					foreach ( $custom_fields as $custom_field ) {

						echo '<li>' . $custom_field->meta_key . '</li>';
					}
					echo '</ul>';

				}
				else{
					_e('No custom fields found', 'wpml-compatibility-test-tools');
				}

				?>




			</td>
		</tr>


		</tbody>
	</table>




</div>