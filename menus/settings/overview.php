<div class="wrap">
    <div id="icon-wpml" class="icon32"><br/></div>
    <h2><?php _e( 'Multilingual Tools Overview', 'wpml-compatibility-test-tools' ); ?></h2>
    <table id="wctt-generator" class="widefat general_options_table">
        <tbody>
        <tr>
            <td>
                <table class="widefat">
                    <thead>
                    <tr>
                        <th colspan="3"><h3><?php _e( 'Configuration files loaded', 'wpml-compatibility-test-tools' ); ?></h3>
                    </tr>
                    </thead>
                    <tbody class="wctt">
                    <tr>
                        <td><?php WPML_Config::load_config_run(); ?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>

        <?php global $wpml_config_debug; ?>

        <?php foreach ( $wpml_config_debug as $type => $config ) : ?>
           <?php if ( ! empty( $config ) ) : ?>
                <tr>
                    <td>
                        <table class="widefat">
                            <thead>
                            <tr>
                                <th colspan="3"><h3><?php _e( $type, 'sitepress' ) ?></h3>
                            </tr>
                            </thead>
                            <tbody class="wctt">
                            <?php foreach ( $config as $entry ) : ?>
                                <tr>
                                    <td><?php wpml_ctt_parse_entry( $entry ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
           <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
