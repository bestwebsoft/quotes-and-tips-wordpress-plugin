<?php
/*
Plugin Name: Quotes and Tips
Plugin URI:  http://bestwebsoft.com/plugin/
Description: This plugin displays the Quotes and Tips in random order
Author: BestWebSoft
Version: 1.13
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*  © Copyright 2011  BestWebSoft  ( http://support.bestwebsoft.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );

if ( ! function_exists( 'add_qtsndtps_admin_menu' ) ) {
	function add_qtsndtps_admin_menu() {
		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', plugins_url("images/px.png", __FILE__), 1001 ); 
		add_submenu_page( 'bws_plugins', __( 'Quotes and Tips', 'quotes_and_tips' ), __( 'Quotes and Tips', 'quotes_and_tips' ), 'manage_options', "quotes-and-tips.php", 'qtsndtps_settings_page' );
	}
}

if ( ! function_exists( 'qtsndtps_register_tips_post_type' ) ) {
	function qtsndtps_register_tips_post_type() {
		$args = array(
			'label'					=> __( 'Tips', 'quotes_and_tips' ),
			'singular_label'		=> __( 'Tips', 'quotes_and_tips' ),
			'public'				=> true,
			'show_ui'				=> true,
			'capability_type' 		=> 'post',
			'hierarchical'			=> false,
			'rewrite'				=> true,
			'supports'				=> array( 'title', 'editor' ),
			'labels'				=> array(
				'add_new_item'			=> __( 'Add a new tip', 'quotes_and_tips' ),
				'edit_item'				=> __( 'Edit tips', 'quotes_and_tips' ),
				'new_item'				=> __( 'New tip', 'quotes_and_tips' ),
				'view_item'				=> __( 'View tips', 'quotes_and_tips' ),
				'search_items'			=> __( 'Search tips', 'quotes_and_tips' ),
				'not_found'				=> __( 'No tips found', 'quotes_and_tips' ),
				'not_found_in_trash'	=> __( 'No tips found in Trash', 'quotes_and_tips' )
			)
		);
		register_post_type( 'tips' , $args );
	}
}

if( ! function_exists( 'qtsndtps_register_quote_post_type' ) ) {
	function qtsndtps_register_quote_post_type() {
		$args = array(
			'label'					=> __( 'Quotes', 'quotes_and_tips' ),
			'singular_label'		=> __( 'Quotes', 'quotes_and_tips' ),
			'public'				=> true,
			'show_ui'				=> true,
			'capability_type'		=> 'post',
			'hierarchical'			=> false,
			'rewrite'				=> true,
			'supports'				=> array( 'title', 'editor' ),
			'labels'				=> array(
				'add_new_item'			=> __( 'Add a New quote', 'quotes_and_tips' ),
				'edit_item'				=> __( 'Edit quote', 'quotes_and_tips' ),
				'new_item'				=> __( 'New quote', 'quotes_and_tips' ),
				'view_item'				=> __( 'View quote', 'quotes_and_tips' ),
				'search_items'			=> __( 'Search quote', 'quotes_and_tips' ),
				'not_found'				=> __( 'No quote found', 'quotes_and_tips' ),
				'not_found_in_trash'	=> __( 'No quote found in Trash', 'quotes_and_tips' )
			),
			'public'				=> true,
			'supports'				=> array( 'title', 'editor', 'thumbnail', 'comments' ),
			'capability_type'		=> 'post',
			'rewrite'				=> array( "slug" => "quote" )
		);
		register_post_type( 'quote' , $args );
	}
}

if( ! function_exists( 'qtsndtps_get_random_tip_quote' ) ) {
	function qtsndtps_get_random_tip_quote() {
		global $post, $qtsndtps_options;
		$args = array(
			'post_type' => 'tips',
			'post_status' => 'publish',
			'orderby' => 'rand',
			'posts_per_page' => '0' == $qtsndtps_options['qtsndtps_page_load'] ? -1 : 1
		);
		query_posts( $args ); ?>
		<div id="quotes_box_and_tips">
			<div class="box_delimeter">
		<?php
			$count = 0;
		// The Loop
		while ( have_posts() ) : the_post(); ?>
				<div class="tips_box <?php if( $count > 0 ) echo "hidden"; else echo "visible"; ?>">
					<h3><?php if( '1' == $qtsndtps_options['qtsndtps_title_post'] )	the_title(); else echo $qtsndtps_options['qtsndtps_tip_label']; ?></h3>
					<p><?php echo strip_tags(get_the_content()); ?></p>
				</div>
		<?php $count ++; endwhile;

		// Reset Query
		wp_reset_query();

		$args = array(
			'post_type' => 'quote',
			'post_status' => 'publish',
			'orderby' => 'rand',
			'posts_per_page' => '0' == $qtsndtps_options['qtsndtps_page_load'] ? -1 : 1
		);
		query_posts( $args ); 
		$count = 0;
		// The Loop
		while ( have_posts() ) : the_post(); ?>
			<div class="quotes_box <?php if( $count > 0 ) echo "hidden"; else echo "visible"; ?>">
				<div class="testemonials_box" id="testemonials_1">
			<?php $name_field = get_post_meta( $post->ID, 'name_field') ;
			$off_cap = get_post_meta( $post->ID, 'off_cap'); ?>
					<h3><?php if( '1' == $qtsndtps_options['qtsndtps_title_post'] )	the_title(); else echo $qtsndtps_options['qtsndtps_quote_label']; ?></h3>
					<p><i>"<?php echo strip_tags(get_the_content()); ?>"</i></p>
					<p class="signature"><?php echo $name_field[0] ?><?php if( ! empty( $off_cap[0] ) && ! empty( $name_field[0] ) ) { ?> | <?php } ?><?php if( ! empty( $off_cap[0] ) ) { ?><span><?php echo $off_cap[0] ?></span><?php } ?></p>
					</div>
				</div>
		<?php $count ++; endwhile;

		// Reset Query
		wp_reset_query(); ?>
				<div class="clear"></div>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'qtsndtps_quote_custom_metabox' ) ) {
	function qtsndtps_quote_custom_metabox() {
		global $post;
		$name_field = get_post_meta( $post->ID, 'name_field') ;
		$off_cap = get_post_meta( $post->ID, 'off_cap');
		wp_nonce_field( plugin_basename(__FILE__), 'qtsndtps_nonce_name' );
		?>
		<p><label for="name_field"><?php _e( 'Name:', 'quotes_and_tips' ); ?><br />
			<input type="text" id="name_field" size="37" name="name_field" value="<?php echo $name_field[0]; ?>"/></label></p>
		<p><label for="off_cap"><?php _e( 'Official position:', 'quotes_and_tips' ); ?></label><br />
			<input type="text" id="off_cap" size="37" name="off_cap" value="<?php echo $off_cap[0]; ?>"/></p>
	<?php
	}
}

if ( ! function_exists( 'qtsndtps_save_custom_quote' ) ) {
	function qtsndtps_save_custom_quote( $post_id ) {
		global $post;	
		if( ( ( isset( $_POST['name_field'] ) && $_POST['name_field'] != '' ) || ( isset( $_POST['off_cap'] ) && $_POST['off_cap'] != '' ) ) && check_admin_referer( plugin_basename(__FILE__), 'qtsndtps_nonce_name' ) ) {
			update_post_meta( $post->ID, 'name_field', $_POST['name_field'] );
			update_post_meta( $post->ID, 'off_cap', $_POST['off_cap'] );
		}
	}
}

if ( ! function_exists( 'qtsndtps_add_custom_metabox' ) ) {
	function qtsndtps_add_custom_metabox() {
		add_meta_box( 'custom-metabox', __( 'Name and Official position', 'quotes_and_tips' ), 'qtsndtps_quote_custom_metabox', 'quote', 'normal', 'high' );
	}
}

// register settings function
if( ! function_exists( 'register_qtsndtps_settings' ) ) {
	function register_qtsndtps_settings() {
		global $wpmu, $qtsndtps_options;

		$qtsndtps_options_defaults = array(
			'qtsndtps_page_load'				=> '1',
			'qtsndtps_interval_load'		=> '10',
			'qtsndtps_tip_label'				=> __( 'Tips', 'quotes_and_tips' ),
			'qtsndtps_quote_label'			=> __( 'Quotes from our clients', 'quotes_and_tips' ),
			'qtsndtps_title_post'				=> '0',
			'qtsndtps_additional_options'	=> '0',
			'qtsndtps_background_color' => '#2484C6',
			'qtsndtps_text_color'				=> '#FFFFFF',
			'qtsndtps_background_image_use' => '0',
			'qtsndtps_background_image' => '',
			'qtsndtps_background_image_repeat_x' => '0',
			'qtsndtps_background_image_repeat_y' => '0',
			'qtsndtps_background_image_gposition' => 'left',
			'qtsndtps_background_image_vposition' => 'bottom'
		);

		// install the option defaults
		if ( 1 == $wpmu ) {
			if ( ! get_site_option( 'qtsndtps_options' ) ) {
				add_site_option( 'qtsndtps_options', $qtsndtps_options_defaults, '', 'yes' );
			}
		} else {
			if( ! get_option( 'qtsndtps_options' ) )
				add_option( 'qtsndtps_options', $qtsndtps_options_defaults, '', 'yes' );
		}

		// get options from the database
		if ( 1 == $wpmu )
			$qtsndtps_options = get_site_option( 'qtsndtps_options' ); // get options from the database
		else
			$qtsndtps_options = get_option( 'qtsndtps_options' );// get options from the database

		// array merge incase this version has added new options
		$qtsndtps_options = array_merge( $qtsndtps_options_defaults, $qtsndtps_options );
	}
}

if ( ! function_exists( 'qtsndtps_settings_page' ) ) {
	function qtsndtps_settings_page() {
		global $qtsndtps_options;
		$error = "";

		// Save data for settings page
		if ( isset( $_REQUEST['qtsndtps_form_submit'] ) && check_admin_referer( plugin_basename(__FILE__), 'qtsndtps_nonce_name' ) ) {
			$qtsndtps_request_options = array();
			$qtsndtps_request_options['qtsndtps_page_load']					= $_REQUEST['qtsndtps_page_load'];
			$qtsndtps_request_options['qtsndtps_interval_load']			= $_REQUEST['qtsndtps_interval_load'];
			$qtsndtps_request_options['qtsndtps_tip_label']					= $_REQUEST['qtsndtps_tip_label'];
			$qtsndtps_request_options['qtsndtps_quote_label']				= $_REQUEST['qtsndtps_quote_label'];
			$qtsndtps_request_options['qtsndtps_title_post']				= $_REQUEST['qtsndtps_title_post'];
			$qtsndtps_request_options['qtsndtps_additional_options']	= isset( $_REQUEST['qtsndtps_additional_options'] ) ? 1 : 0 ;
			$qtsndtps_request_options['qtsndtps_background_color']	= $_REQUEST['qtsndtps_background_color'];
			$qtsndtps_request_options['qtsndtps_text_color']				= $_REQUEST['qtsndtps_text_color'];
			$qtsndtps_request_options['qtsndtps_background_image_use']				= isset( $_REQUEST['qtsndtps_background_image_use'] ) ? 1 : 0 ;
			$qtsndtps_request_options['qtsndtps_background_image_gposition']	= $_REQUEST['qtsndtps_background_image_gposition'];
			$qtsndtps_request_options['qtsndtps_background_image_vposition']	= $_REQUEST['qtsndtps_background_image_vposition'];
			$qtsndtps_request_options['qtsndtps_background_image_repeat_x']	= isset( $_REQUEST['qtsndtps_background_image_repeat_x'] ) ? 1 : 0 ;
			$qtsndtps_request_options['qtsndtps_background_image_repeat_y']	= isset( $_REQUEST['qtsndtps_background_image_repeat_y'] ) ? 1 : 0 ;
			
			if ( isset( $_FILES["qtsndtps_background_image"]['name'] ) && ! empty( $_FILES["qtsndtps_background_image"]['name'] ) ) {
				$images = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_wp_attachment_qtsndtp_background_image', 'meta_value' => get_option('stylesheet'), 'orderby' => 'none', 'nopaging' => true ) );
				if( ! empty ( $images ) )
					wp_delete_attachment( $images[0]->ID );

				$uploads['path'] = TEMPLATEPATH . "/";
				$new_file = $uploads['path'] . $_FILES["qtsndtps_background_image"]['name'];
				if ( false === @ move_uploaded_file( $_FILES["qtsndtps_background_image"]['tmp_name'], $new_file ) )
					wp_die( sprintf( __('The uploaded file could not be moved to %s.' ), $uploads['path'] ), __( 'Image Processing Error' ) );
				$file['url'] = get_bloginfo('template_directory'). "/" .$_FILES["qtsndtps_background_image"]['name'];
				$file['type'] = $_FILES["qtsndtps_background_image"]["type"];
				$file['file'] = $new_file;

				if ( isset($file['error']) )
					wp_die( $file['error'],  __( 'Image Upload Error' ) );

				$url = $file['url'];
				$type = $file['type'];
				$file = $file['file'];
				$filename = basename($file);

				// Construct the object array
				$object = array(
					'post_title' => $filename,
					'post_content' => $url,
					'post_mime_type' => $type,
					'guid' => $url,
					'context' => 'qtsndtp_background_image'
				);

				// Save the data
				$id = wp_insert_attachment($object, $file);
				//wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
				update_post_meta( $id, '_wp_attachment_qtsndtp_background_image', get_option('stylesheet' ) );

				$qtsndtps_request_options['qtsndtps_background_image'] = $url;
		}

			// array merge incase this version has added new options
			$qtsndtps_options = array_merge( $qtsndtps_options, $qtsndtps_request_options );

			// Check select one point in the blocks Arithmetic actions and Difficulty on settings page
			update_option( 'qtsndtps_options', $qtsndtps_options, '', 'yes' );
			$message = __( "Settings saved", 'quotes_and_tips' );
		}
		// Display form on the setting page
	?>
	<div class="wrap">
		<div class="icon32 icon32-bws" id="icon-options-general"></div>
		<h2><?php _e('Quotes and Tips Settings', 'quotes_and_tips' ); ?></h2>
		<div class="updated fade" <?php if( ! isset( $_REQUEST['qtsndtps_form_submit'] ) || $error != "" ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
		<div class="error" <?php if( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
		<p><?php echo __( "If you would like to use this block, please paste the following strings into the template source code", 'quotes_and_tips' ); ?> <code>&#60;?php if( function_exists( 'qtsndtps_get_random_tip_quote' ) ) qtsndtps_get_random_tip_quote(); ?&#62;</code></p>	
		<form method="post" action="admin.php?page=quotes-and-tips.php" id='qtsndtps_form_image_size' enctype="multipart/form-data">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Upload settings:', 'quotes_and_tips' ); ?> </th>
					<td>
						<input type="radio" name="qtsndtps_page_load" value="1" <?php if( $qtsndtps_options['qtsndtps_page_load'] == '1') echo 'checked="checked"'; ?> /> <label for="qtsndtps_page_load"><?php _e( 'Random order with the page reload', 'quotes_and_tips' ); ?></label><br />
						<input type="radio" name="qtsndtps_page_load" value="0" <?php if( $qtsndtps_options['qtsndtps_page_load'] == '0') echo 'checked="checked"'; ?> /> <label for="qtsndtps_page_load"><?php _e( 'Random order without the page reload', 'quotes_and_tips' ); ?></label><br />
						<input type="text" name="qtsndtps_interval_load" value="<?php echo $qtsndtps_options['qtsndtps_interval_load']; ?>" style="width:30px" /> <label for="qtsndtps_interval_load"><?php _e( 'Reload time (in seconds)', 'quotes_and_tips' ); ?></label> 
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Title options:', 'quotes_and_tips' ); ?> </th>
					<td>
						<input type="radio" name="qtsndtps_title_post" value="1" class="qtsndtps_title_post" <?php if( $qtsndtps_options['qtsndtps_title_post'] == '1') echo 'checked="checked"'; ?> /> <label for="qtsndtps_title_post"><?php _e( 'Get title from post', 'quotes_and_tips' ); ?></label><br />
						<input type="radio" name="qtsndtps_title_post" value="0" class="qtsndtps_title_post" <?php if( $qtsndtps_options['qtsndtps_title_post'] == '0') echo 'checked="checked"'; ?> /> <label for="qtsndtps_title_post"><?php _e( 'Get label of the block', 'quotes_and_tips' ); ?></label>
					</td>
				</tr>
				<tr valign="top" class="qtsndtps_title_post_fields">
					<th scope="row"><?php _e( 'Tip label:', 'quotes_and_tips' ); ?> </th>
					<td>
						<input type="text" name="qtsndtps_tip_label" value="<?php echo $qtsndtps_options['qtsndtps_tip_label']; ?>" style="width:250px" />
					</td>
				</tr>
				<tr valign="top" class="qtsndtps_title_post_fields">
					<th scope="row"><?php _e( 'Quote label:', 'quotes_and_tips' ); ?> </th>
					<td>
						<input type="text" name="qtsndtps_quote_label" value="<?php echo $qtsndtps_options['qtsndtps_quote_label']; ?>" style="width:250px" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" colspan="2"><input type="checkbox" name="qtsndtps_additional_options" id="qtsndtps_additional_options" value="1" <?php if( $qtsndtps_options['qtsndtps_additional_options'] == '1') echo 'checked="checked"'; ?> /> <?php _e( 'Additional settings', 'quotes_and_tips' ); ?> </th>
				</tr>
				<tr valign="top" class="qtsndtps_additions_block <?php if( $qtsndtps_options['qtsndtps_additional_options'] == '0') echo 'qtsndtps_hidden'; ?>">
					<th scope="row"><?php _e( 'Background Color:', 'quotes_and_tips' ); ?></th>
					<td>
						<input type="text" name="qtsndtps_background_color" id="link-color" value="<?php echo esc_attr( $qtsndtps_options['qtsndtps_background_color'] ); ?>" />
						<a href="#" class="pickcolor hide-if-no-js" id="link-color-example"></a>
						<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
					</td>
				</tr>
				<tr valign="top" class="qtsndtps_additions_block <?php if( $qtsndtps_options['qtsndtps_additional_options'] == '0') echo 'qtsndtps_hidden'; ?>">
					<th scope="row"><?php _e( 'Text Color:', 'quotes_and_tips' ); ?></th>
					<td>
						<input type="text" name="qtsndtps_text_color" id="text-color" value="<?php echo esc_attr( $qtsndtps_options['qtsndtps_text_color'] ); ?>" />
						<a href="#" class="pickcolor1 hide-if-no-js" id="text-color-example"></a>
						<div id="colorPickerDiv1" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
					</td>
				</tr>
				<tr valign="top" class="qtsndtps_additions_block <?php if( $qtsndtps_options['qtsndtps_additional_options'] == '0') echo 'qtsndtps_hidden'; ?>">
					<th scope="row"><?php _e( 'Background image:', 'quotes_and_tips' ); ?></th>
					<td>
						<input type="checkbox" name="qtsndtps_background_image_use" value="1" <?php if( $qtsndtps_options['qtsndtps_background_image_use'] == '1') echo 'checked="checked"'; ?> /> <label for="qtsndtps_background_image_use"><?php _e( 'Use background image', 'quotes_and_tips' ); ?></label><br />
						<label for="qtsndtps_background_image"><?php _e( 'Choose an image from your computer:', 'quotes_and_tips' ); ?></label><br />
						<input type="file" name="qtsndtps_background_image" id="qtsndtps_background_image"><br />
						<?php if ( ! empty( $qtsndtps_options['qtsndtps_background_image'] ) ) { ?>
						<label for="qtsndtps_background_image"><?php _e( 'Current image:', 'quotes_and_tips' ); ?></label><br>
						<img src="<?php echo $qtsndtps_options['qtsndtps_background_image']; ?>" alt="" title="" style="border:1px solid red;background-color:<?php echo esc_attr( $qtsndtps_options['qtsndtps_background_color'] ); ?>;" />
						<?php } ?>
					</td>
				</tr>
				<tr valign="top" class="qtsndtps_additions_block <?php if( $qtsndtps_options['qtsndtps_additional_options'] == '0') echo 'qtsndtps_hidden'; ?>">
					<th scope="row"><?php _e( 'Background image repeat:', 'quotes_and_tips' ); ?> </th>
					<td>
						<input type="checkbox" name="qtsndtps_background_image_repeat_x" value="1" <?php if( $qtsndtps_options['qtsndtps_background_image_repeat_x'] == '1') echo 'checked="checked"'; ?> /> <label for="qtsndtps_background_image_repeat_x"><?php _e( 'Horizontal repeat (x)', 'quotes_and_tips' ); ?></label><br />
						<input type="checkbox" name="qtsndtps_background_image_repeat_y" value="1" <?php if( $qtsndtps_options['qtsndtps_background_image_repeat_y'] == '1') echo 'checked="checked"'; ?> /> <label for="qtsndtps_background_image_repeat_y"><?php _e( 'Vertical repeat (y)', 'quotes_and_tips' ); ?></label>
					</td>
				</tr>
				<tr valign="top" class="qtsndtps_additions_block <?php if( $qtsndtps_options['qtsndtps_additional_options'] == '0') echo 'qtsndtps_hidden'; ?>">
					<th scope="row"><?php _e( 'Background image horizontal alignment:', 'quotes_and_tips' ); ?> </th>
					<td>
						<input type="radio" name="qtsndtps_background_image_gposition" value="left" <?php if( $qtsndtps_options['qtsndtps_background_image_gposition'] == 'left') echo 'checked="checked"'; ?> /> <label for="qtsndtps_background_image_position"><?php _e( 'Left', 'quotes_and_tips' ); ?></label><br />
						<input type="radio" name="qtsndtps_background_image_gposition" value="center" <?php if( $qtsndtps_options['qtsndtps_background_image_gposition'] == 'center') echo 'checked="checked"'; ?> /> <label for="qtsndtps_background_image_position"><?php _e( 'Center', 'quotes_and_tips' ); ?></label><br />
						<input type="radio" name="qtsndtps_background_image_gposition" value="right" <?php if( $qtsndtps_options['qtsndtps_background_image_gposition'] == 'right') echo 'checked="checked"'; ?> /> <label for="qtsndtps_background_image_position"><?php _e( 'Right', 'quotes_and_tips' ); ?></label>
					</td>
				</tr>
				<tr valign="top" class="qtsndtps_additions_block <?php if( $qtsndtps_options['qtsndtps_additional_options'] == '0') echo 'qtsndtps_hidden'; ?>">
					<th scope="row"><?php _e( 'Background image vertical alignment:', 'quotes_and_tips' ); ?> </th>
					<td>
						<input type="radio" name="qtsndtps_background_image_vposition" value="top" <?php if( $qtsndtps_options['qtsndtps_background_image_vposition'] == 'top') echo 'checked="checked"'; ?> /> <label for="qtsndtps_background_image_position"><?php _e( 'Top', 'quotes_and_tips' ); ?></label><br />
						<input type="radio" name="qtsndtps_background_image_vposition" value="center" <?php if( $qtsndtps_options['qtsndtps_background_image_vposition'] == 'center') echo 'checked="checked"'; ?> /> <label for="qtsndtps_background_image_position"><?php _e( 'Center', 'quotes_and_tips' ); ?></label><br />
						<input type="radio" name="qtsndtps_background_image_vposition" value="bottom" <?php if( $qtsndtps_options['qtsndtps_background_image_vposition'] == 'bottom') echo 'checked="checked"'; ?> /> <label for="qtsndtps_background_image_position"><?php _e( 'Bottom', 'quotes_and_tips' ); ?></label>
					</td>
				</tr>
			</table>    
			<input type="hidden" name='qtsndtps_form_submit' value="submit" />
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
			<?php wp_nonce_field( plugin_basename(__FILE__), 'qtsndtps_nonce_name' ); ?>
		</form>
	</div>
	<?php } 
}

if ( ! function_exists( 'qtsndtps_register_plugin_links' ) ) {
	function qtsndtps_register_plugin_links( $links, $file ) {
		$base = plugin_basename(__FILE__);
		if ( $file == $base ) {
			$links[] = '<a href="admin.php?page=quotes-and-tips.php">' . __( 'Settings', 'quotes_and_tips' ) . '</a>';
			$links[] = '<a href="http://wordpress.org/extend/plugins/quotes-and-tips/faq/" target="_blank">' . __( 'FAQ', 'quotes_and_tips' ) . '</a>';
			$links[] = '<a href="http://support.bestwebsoft.com">' . __( 'Support', 'quotes_and_tips' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists( 'qtsndtps_plugin_action_links' ) ) {
	function qtsndtps_plugin_action_links( $links, $file ) {
		//Static so we don't call plugin_basename on every plugin row.
		static $this_plugin;
		if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

		if ( $file == $this_plugin ) {
			$settings_link = '<a href="admin.php?page=quotes-and-tips.php">' . __( 'Settings', 'quotes_and_tips' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	} // end function qtsndtps_plugin_action_links
}

if ( ! function_exists ( 'qtsndtps_plugin_init' ) ) {
	function qtsndtps_plugin_init() {
		load_plugin_textdomain( 'quotes_and_tips', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
		load_plugin_textdomain( 'bestwebsoft', false, dirname( plugin_basename( __FILE__ ) ) . '/bws_menu/languages/' ); 
	}
}

if ( ! function_exists ( 'qtsndtps_print_style_script' ) ) {
	function qtsndtps_print_style_script() {
		global $qtsndtps_options;

		$background_color				= $qtsndtps_options['qtsndtps_background_color'];
		$text_color						= $qtsndtps_options['qtsndtps_text_color'];
		$background_image_use			= $qtsndtps_options['qtsndtps_background_image_use'];
		$background_image				= $qtsndtps_options['qtsndtps_background_image'];
		$background_gposition			= $qtsndtps_options['qtsndtps_background_image_gposition'];
		$background_vposition			= $qtsndtps_options['qtsndtps_background_image_vposition'];
		$background_repeat_x			= $qtsndtps_options['qtsndtps_background_image_repeat_x'];
		$background_repeat_y			= $qtsndtps_options['qtsndtps_background_image_repeat_y'];
		$interval_load					= $qtsndtps_options['qtsndtps_interval_load'];
		$page_load						= $qtsndtps_options['qtsndtps_page_load'];

		if ( $background_color == '#2484C6' && $background_color == '#FFFFFF' && empty ( $background_image ) )
			return;
		?>
		<style type="text/css">
			/* Style for tips|quote block */
			#quotes_box_and_tips{
				background-color:<?php echo $background_color; ?> !important;
				color: <?php echo $text_color; ?> !important;
				<?php if ( 1 == $background_image_use && ! empty( $background_image ) ) { ?>
				background-image: url( <?php echo $background_image; ?> );
				<?php } ?>					
				background-position: <?php echo $background_gposition ." ". $background_vposition; ?>;
				<?php if ( 1 == $background_repeat_x && 1 == $background_repeat_y ) { ?>
				background-repeat: repeat;
				<?php } else if ( 1 == $background_repeat_x ) { ?>
				background-repeat: repeat-x;
				<?php } else if ( 1 == $background_repeat_y ) { ?>
				background-repeat: repeat-y;
				<?php } else { ?>
				background-repeat: no-repeat;
				<?php } ?>
			}
			#quotes_box_and_tips h3 {
				color: <?php echo $text_color; ?> !important;
			}
			#quotes_box_and_tips .signature {
				color: <?php echo $text_color; ?> !important;
			}
			#quotes_box_and_tips .signature span {
				color: <?php echo $text_color; ?> !important;
			}
		</style>
		<?php if( '0' == $page_load ) { ?>
		<script type="text/javascript">
		if ( window.jQuery ) {
			(function($){
				$(document).ready( function() {
					var interval = <?php echo $interval_load; ?>;
					setInterval( change_tip_quote, interval * 1000 );
				});

				function change_tip_quote() {
					var flag = false;
					$('#quotes_box_and_tips').find('.tips_box').each(function(){
						if($(this).hasClass("visible") === true && !flag) {
							if($(this).next().hasClass("tips_box")){
								$(this).animate({opacity:0}, 500, function(){ 
									$(this).addClass("hidden"); 
									$(this).removeClass("visible");							
									$(this).next().animate({opacity:0}, 1);
									$(this).next().removeClass("hidden");
									$(this).next().addClass("visible");
									$(this).next().animate({opacity:1}, 500);
								});
								
							}
							else{
								$(this).animate({opacity:0}, 500, function(){ 
									$(this).addClass("hidden"); 
									$(this).removeClass("visible");
									$('#quotes_box_and_tips').find('.tips_box:first').animate({opacity:0}, 1);
									$('#quotes_box_and_tips').find('.tips_box:first').removeClass("hidden");
									$('#quotes_box_and_tips').find('.tips_box:first').addClass("visible");
									$('#quotes_box_and_tips').find('.tips_box:first').animate({opacity:1}, 500);
								});
							}
							flag = true;
						}
					});
					flag = false;
					$('#quotes_box_and_tips').find('.quotes_box').each(function(){
						if($(this).hasClass("visible") === true && !flag) {
							if($(this).next().hasClass("quotes_box")){
								$(this).animate({opacity:0}, 500, function(){ 
									$(this).addClass("hidden"); 
									$(this).removeClass("visible");
									$(this).next().animate({opacity:0}, 10);
									$(this).next().removeClass("hidden");
									$(this).next().addClass("visible");
									$(this).next().animate({opacity:1}, 500);
								});
							}
							else{
								$(this).animate({opacity:0}, 500, function(){ 
									$(this).addClass("hidden"); 
									$(this).removeClass("visible");
									$('#quotes_box_and_tips').find('.quotes_box:first').animate({opacity:0}, 1);
									$('#quotes_box_and_tips').find('.quotes_box:first').removeClass("hidden");
									$('#quotes_box_and_tips').find('.quotes_box:first').addClass("visible");
									$('#quotes_box_and_tips').find('.quotes_box:first').animate({opacity:1}, 500);
								});
							}
							flag = true;
						}
					});
				}
			})(jQuery);
		}
		</script>
		<?php } 
	}
}

if ( ! function_exists ( 'qtsndtps_admin_head' ) ) {
	function qtsndtps_admin_head() {
		wp_enqueue_style( 'qtsndtpsStylesheet', plugins_url( 'css/style.css', __FILE__ ) );

		if( strpos( $_SERVER["REQUEST_URI"], "quotes-and-tips.php" ) !== false ) {			
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_script( 'qtsndtpsrColorJs', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
		}
		
		if ( isset( $_GET['page'] ) && $_GET['page'] == "bws_plugins" )
			wp_enqueue_script( 'bws_menu_script', plugins_url( 'js/bws_menu.js' , __FILE__ ) );
	}
}

if ( ! function_exists ( 'qtsndtps_wp_head' ) ) {
	function qtsndtps_wp_head() {
		wp_enqueue_style( 'qtsndtpsStylesheet', plugins_url( 'css/style.css', __FILE__ ) );
		wp_enqueue_script( 'jquery' );
	}
}

// Function for delete options
if ( ! function_exists ( 'qtsndtps_delete_options' ) ) {
	function qtsndtps_delete_options() {
		global $wpdb;
		delete_option( 'qtsndtps_options' );
	}
}

// adds "Settings" link to the plugin action page
add_filter( 'plugin_action_links', 'qtsndtps_plugin_action_links', 10, 2 );
//Additional links on the plugin page
add_filter( 'plugin_row_meta', 'qtsndtps_register_plugin_links', 10, 2 );

add_action( 'admin_menu', 'add_qtsndtps_admin_menu' );
add_action( 'init', 'qtsndtps_plugin_init' );

add_action( 'admin_init', 'qtsndtps_add_custom_metabox' );
add_action( 'save_post', 'qtsndtps_save_custom_quote' );
add_action( 'init', 'qtsndtps_register_tips_post_type' );
add_action( 'init', 'qtsndtps_register_quote_post_type' );

add_action( 'wp_head', 'qtsndtps_print_style_script' );
add_action( 'init', 'register_qtsndtps_settings' );

add_action( 'admin_enqueue_scripts', 'qtsndtps_admin_head' );
add_action( 'wp_enqueue_scripts', 'qtsndtps_wp_head' );

register_uninstall_hook( __FILE__, 'qtsndtps_delete_options' );
?>