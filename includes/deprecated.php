<?php
/**
* Includes deprecated functions
*/

/**
 * Upgrade plugin options
 * @deprecated since 1.3.5
 * @todo remove after 20.03.2018
 */
if ( ! function_exists( 'qtsndtps_update_old_options' ) ) {
	function qtsndtps_update_old_options() {
		global $qtsndtps_options;

		$qtsndtps_options['custom_background_image'] = $qtsndtps_options['background_image'];

		$qtsndtps_options['additional_options'] = ( $qtsndtps_options['additional_options'] == 1 ) ? 0 : 1;
		$qtsndtps_options['background_image'] = ( $qtsndtps_options['background_image_use'] == 0 ) ? 'default' : 'custom';
		unset( $qtsndtps_options['background_image_use'] );
	}
}