<?php
/*
Plugin Name: Quotes and Tips by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/wordpress/plugins/quotes-and-tips/
Description: Add customizable quotes and tips blocks to WordPress posts, pages and widgets.
Author: BestWebSoft
Text Domain: quotes-and-tips
Domain Path: /languages
Version: 1.31
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*  © Copyright 2016  BestWebSoft  ( http://support.bestwebsoft.com )

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

if ( ! function_exists( 'add_qtsndtps_admin_menu' ) ) {
	function add_qtsndtps_admin_menu() {
		global $submenu;
		bws_general_menu();
		$settings = add_submenu_page( 'bws_panel', 'Quotes and Tips', 'Quotes and Tips', 'manage_options', "quotes-and-tips.php", 'qtsndtps_settings_page' );
		
		$url = admin_url( 'admin.php?page=quotes-and-tips.php' );
		if ( isset( $submenu['edit.php?post_type=quote'] ) )
			$submenu['edit.php?post_type=quote'][] = array( __( 'Settings', 'quotes-and-tips' ), 'manage_options', $url );
		if ( isset( $submenu['edit.php?post_type=tips'] ) )
			$submenu['edit.php?post_type=tips'][] = array( __( 'Settings', 'quotes-and-tips' ), 'manage_options', $url );

		add_action( 'load-' . $settings, 'qtsndtps_add_tabs' );
		add_action( 'load-post.php', 'qtsndtps_add_tabs' );
		add_action( 'load-edit.php', 'qtsndtps_add_tabs' );
		add_action( 'load-post-new.php', 'qtsndtps_add_tabs' );
	}
}

/**
 * Internationalization
 */
if ( ! function_exists( 'qtsndtps_plugins_loaded' ) ) {
	function qtsndtps_plugins_loaded() {
		load_plugin_textdomain( 'quotes-and-tips', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists ( 'qtsndtps_plugin_init' ) ) {
	function qtsndtps_plugin_init() {
		global $qtsndtps_plugin_info;	

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		
		if ( empty( $qtsndtps_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$qtsndtps_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version  */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $qtsndtps_plugin_info, '3.8' );
		
		/* Call register settings function */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && "quotes-and-tips.php" == $_GET['page'] ) )
			register_qtsndtps_settings();

		qtsndtps_register_tips_post_type();
		qtsndtps_register_quote_post_type();
	}
}

if ( ! function_exists ( 'qtsndtps_plugin_admin_init' ) ) {
	function qtsndtps_plugin_admin_init() {
		global $bws_plugin_info, $qtsndtps_plugin_info, $bws_shortcode_list;

		if ( empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '82', 'version' => $qtsndtps_plugin_info["Version"] );

		qtsndtps_add_custom_metabox();
		/* add Quotes and Tips to global $bws_shortcode_list  */
		$bws_shortcode_list['qtsndtps'] = array( 'name' => 'Quotes and Tips' );
	}
}

/* Register settings function */
if ( ! function_exists( 'register_qtsndtps_settings' ) ) {
	function register_qtsndtps_settings() {
		global $qtsndtps_options, $qtsndtps_plugin_info, $qtsndtps_options_defaults;

		$qtsndtps_options_defaults = array(
			'plugin_option_version'			=> $qtsndtps_plugin_info["Version"],
			'page_load'						=> '1',
			'interval_load'					=> '10',
			'tip_label'						=> __( 'Tips', 'quotes-and-tips' ),
			'quote_label'					=> __( 'Quotes from our clients', 'quotes-and-tips' ),
			'title_post'					=> '0',
			'additional_options'			=> '1',
			'background_color' 				=> '#2484C6',
			'text_color'					=> '#FFFFFF',
			'background_image_use' 			=> '0',
			'background_image'				=> '',
			'background_image_repeat_x'		=> '0',
			'background_image_repeat_y'		=> '0',
			'background_image_gposition'	=> 'left',
			'background_image_vposition'	=> 'bottom',
			'display_settings_notice'		=> 1,
			'suggest_feature_banner'		=> 1
		);

		/* Install the option defaults */
		if ( ! get_option( 'qtsndtps_options' ) )
			add_option( 'qtsndtps_options', $qtsndtps_options_defaults );

		/* Get options from the database */
		$qtsndtps_options = get_option( 'qtsndtps_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $qtsndtps_options['plugin_option_version'] ) || $qtsndtps_options['plugin_option_version'] != $qtsndtps_plugin_info["Version"] ) {

			/**
			 * @since  since 1.30
			 * @todo remove after 11.02.2017
			 */
			$qtsndtps_options_defaults['display_settings_notice'] = 0;
			foreach ( $qtsndtps_options_defaults as $key => $value ) {
				$old_key = 'qtsndtps_' . $key;
				if ( isset( $qtsndtps_options[ $old_key ] ) ) {
					$qtsndtps_options[ $key ] = $qtsndtps_options[ $old_key ];
					unset( $qtsndtps_options[ $old_key ] );
				}
			}/* end @todo */

			$qtsndtps_options = array_merge( $qtsndtps_options_defaults, $qtsndtps_options );
			$qtsndtps_options['plugin_option_version'] = $qtsndtps_plugin_info["Version"];
			update_option( 'qtsndtps_options', $qtsndtps_options );
		}
	}
}

if ( ! function_exists( 'qtsndtps_register_tips_post_type' ) ) {
	function qtsndtps_register_tips_post_type() {
		$args = array(
			'label'				=>	__( 'Tips', 'quotes-and-tips' ),
			'singular_label'	=>	__( 'Tips', 'quotes-and-tips' ),
			'public'			=>	true,
			'show_ui'			=>	true,
			'capability_type' 	=>	'post',
			'hierarchical'		=>	false,
			'rewrite'			=>	true,
			'supports'			=>	array( 'title', 'editor' ),
			'labels'			=>	array(
				'add_new_item'			=>	__( 'Add a new tip', 'quotes-and-tips' ),
				'edit_item'				=>	__( 'Edit tips', 'quotes-and-tips' ),
				'new_item'				=>	__( 'New tip', 'quotes-and-tips' ),
				'view_item'				=>	__( 'View tips', 'quotes-and-tips' ),
				'search_items'			=>	__( 'Search tips', 'quotes-and-tips' ),
				'not_found'				=>	__( 'No tips found', 'quotes-and-tips' ),
				'not_found_in_trash'	=>	__( 'No tips found in Trash', 'quotes-and-tips' ),
				'filter_items_list'     => __( 'Filter tips list', 'quotes-and-tips' ),
				'items_list_navigation' => __( 'Tips list navigation', 'quotes-and-tips' ),
				'items_list'            => __( 'Tips list', 'quotes-and-tips' )
			)
		);
		register_post_type( 'tips' , $args );
	}
}

if ( ! function_exists( 'qtsndtps_register_quote_post_type' ) ) {
	function qtsndtps_register_quote_post_type() {
		$args = array(
			'label'				=>	__( 'Quotes', 'quotes-and-tips' ),
			'singular_label'	=>	__( 'Quotes', 'quotes-and-tips' ),
			'public'			=>	true,
			'show_ui'			=>	true,
			'capability_type'	=>	'post',
			'hierarchical'		=>	false,
			'rewrite'			=>	true,
			'supports'			=>	array( 'title', 'editor' ),
			'labels'			=>	array(
				'add_new_item'			=>	__( 'Add a New quote', 'quotes-and-tips' ),
				'edit_item'				=>	__( 'Edit quote', 'quotes-and-tips' ),
				'new_item'				=>	__( 'New quote', 'quotes-and-tips' ),
				'view_item'				=>	__( 'View quote', 'quotes-and-tips' ),
				'search_items'			=>	__( 'Search quote', 'quotes-and-tips' ),
				'not_found'				=>	__( 'No quote found', 'quotes-and-tips' ),
				'not_found_in_trash'	=>	__( 'No quote found in Trash', 'quotes-and-tips' ),
				'filter_items_list'     => __( 'Filter quotes list', 'quotes-and-tips' ),
				'items_list_navigation' => __( 'Quotes list navigation', 'quotes-and-tips' ),
				'items_list'            => __( 'Quotes list', 'quotes-and-tips' )
			),
			'public'			=>	true,
			'supports'			=>	array( 'title', 'editor', 'thumbnail', 'comments' ),
			'capability_type'	=>	'post',
			'rewrite'			=>	array( "slug" => "quote" )
		);
		register_post_type( 'quote' , $args );
	}
}

if ( ! function_exists( 'qtsndtps_get_random_tip_quote' ) ) {
	function qtsndtps_get_random_tip_quote() {
		echo qtsndtps_create_tip_quote_block();
	}
}

if ( ! function_exists( 'qtsndtps_create_tip_quote_block' ) ) {
	function qtsndtps_create_tip_quote_block() {
		global $post, $qtsndtps_options;
		$random_tip_quote_block = "";
		$args = array(
			'post_type'			=>	'tips',
			'post_status'		=>	'publish',
			'orderby'			=>	'rand',
			'posts_per_page'	=>	'0' == $qtsndtps_options['page_load'] ? -1 : 1
		);
		query_posts( $args );
		$random_tip_quote_block .= '<div id="quotes_box_and_tips">
			<div class="box_delimeter">';
				$count = 0;
				/* The Loop */
				while ( have_posts() ) {
					the_post();
					$random_tip_quote_block .= '<div class="tips_box ';
					$random_tip_quote_block .= ( 0 < $count ) ? 'hidden' : 'visible';
					$random_tip_quote_block .= '">
						<h3>';
					$random_tip_quote_block .= ( '1' == $qtsndtps_options['title_post'] ) ? get_the_title() : $qtsndtps_options['tip_label'];
					$random_tip_quote_block .= '</h3>
						<p>' . strip_tags( get_the_content() ) . '</p>
					</div>';
					$count ++;
				}
				/* Reset Query */
				wp_reset_query();

				$args = array(
					'post_type'			=>	'quote',
					'post_status'		=>	'publish',
					'orderby'			=>	'rand',
					'posts_per_page'	=>	'0' == $qtsndtps_options['page_load'] ? -1 : 1
				);
				query_posts( $args );
				$count = 0;
				/* The Loop */
				while ( have_posts() ) {
					the_post();
					$name_field = get_post_meta( $post->ID, 'name_field' );
					$off_cap = get_post_meta( $post->ID, 'off_cap' );

					$random_tip_quote_block .= '<div class="quotes_box ';
					$random_tip_quote_block .= ( 0 < $count ) ? 'hidden' : 'visible';
					$random_tip_quote_block .= '">
						<div class="testemonials_box" id="testemonials_1">
						<h3>';
					$random_tip_quote_block .= ( '1' == $qtsndtps_options['title_post'] ) ? get_the_title() : $qtsndtps_options['quote_label'];
					$random_tip_quote_block .= '</h3>
							<p><i>"' . strip_tags( get_the_content() ) . '"</i></p>
							<p class="signature">';
							if ( ! empty( $name_field[0] ) )
								$random_tip_quote_block .= $name_field[0];
							if ( ! empty( $off_cap[0] ) && ! empty( $name_field[0] ) )
								$random_tip_quote_block .= ' | '; 
							if ( ! empty( $off_cap[0] ) )
								$random_tip_quote_block .= '<span>' . $off_cap[0] . '</span>';
						$random_tip_quote_block .= '</p>
						</div>
					</div>';
					$count ++;
				}
				/* Reset Query */
				wp_reset_query();
				$random_tip_quote_block .= '<div class="clear"></div>
			</div>
		</div>';
		return $random_tip_quote_block;
	}
}

if ( ! function_exists( 'qtsndtps_quote_custom_metabox' ) ) {
	function qtsndtps_quote_custom_metabox() {
		global $post;
		$name_field = get_post_meta( $post->ID, 'name_field' ) ;
		$off_cap = get_post_meta( $post->ID, 'off_cap' );
		wp_nonce_field( plugin_basename( __FILE__ ), 'qtsndtps_nonce_name' ); ?>
		<p><label for="name_field"><?php _e( 'Name:', 'quotes-and-tips' ); ?><br />
			<input type="text" id="name_field" size="37" name="name_field" value="<?php if ( ! empty( $name_field ) ) echo $name_field[0]; ?>"/></label></p>
		<p><label for="off_cap"><?php _e( 'Official position:', 'quotes-and-tips' ); ?></label><br />
			<input type="text" id="off_cap" size="37" name="off_cap" value="<?php if ( ! empty( $off_cap ) ) echo $off_cap[0]; ?>"/></p>
	<?php }
}

if ( ! function_exists( 'qtsndtps_add_custom_metabox' ) ) {
	function qtsndtps_add_custom_metabox() {
		add_meta_box( 'custom-metabox', __( 'Name and Official position', 'quotes-and-tips' ), 'qtsndtps_quote_custom_metabox', 'quote', 'normal', 'high' );
	}
}

if ( ! function_exists( 'qtsndtps_settings_page' ) ) {
	function qtsndtps_settings_page() {
		global $qtsndtps_options, $qtsndtps_plugin_info, $qtsndtps_options_defaults;
		$error = $message = $cstmsrch_options_name = "";

		if ( false !== get_option( 'cstmsrchpr_options' ) )
			$cstmsrch_options_name = "cstmsrchpr_options";
		elseif ( false !== get_option( 'cstmsrch_options' ) )
			$cstmsrch_options_name = "cstmsrch_options";
		elseif ( false !== get_option( 'bws_custom_search' ) )
			$cstmsrch_options_name = "bws_custom_search";		
		
		$cstmsrch_options = get_option( $cstmsrch_options_name );

		$all_plugins = get_plugins();

		/* Save data for settings page */
		if ( isset( $_REQUEST['qtsndtps_form_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'qtsndtps_nonce_name' ) ) {
			$qtsndtps_request_options = array();
			$qtsndtps_request_options['page_load']					=	$_REQUEST['qtsndtps_page_load'];
			$qtsndtps_request_options['interval_load']				=	intval( $_REQUEST['qtsndtps_interval_load'] );
			$qtsndtps_request_options['tip_label']					=	stripslashes( esc_html( $_REQUEST['qtsndtps_tip_label'] ) );
			$qtsndtps_request_options['quote_label']				=	stripslashes( esc_html( $_REQUEST['qtsndtps_quote_label'] ) );
			$qtsndtps_request_options['title_post']					=	$_REQUEST['qtsndtps_title_post'];
			$qtsndtps_request_options['additional_options']			=	isset( $_REQUEST['qtsndtps_additional_options'] ) ? 1 : 0 ;
			$qtsndtps_request_options['background_color']			=	stripslashes( esc_html( $_REQUEST['qtsndtps_background_color'] ) );
			$qtsndtps_request_options['text_color']					=	stripslashes( esc_html( $_REQUEST['qtsndtps_text_color'] ) );
			$qtsndtps_request_options['background_image_use']		=	isset( $_REQUEST['qtsndtps_background_image_use'] ) ? 1 : 0 ;
			$qtsndtps_request_options['background_image_gposition']	=	$_REQUEST['qtsndtps_background_image_gposition'];
			$qtsndtps_request_options['background_image_vposition']	=	$_REQUEST['qtsndtps_background_image_vposition'];
			$qtsndtps_request_options['background_image_repeat_x']	=	isset( $_REQUEST['qtsndtps_background_image_repeat_x'] ) ? 1 : 0 ;
			$qtsndtps_request_options['background_image_repeat_y']	=	isset( $_REQUEST['qtsndtps_background_image_repeat_y'] ) ? 1 : 0 ;

			if ( isset( $_FILES["qtsndtps_background_image"]['name'] ) && ! empty( $_FILES["qtsndtps_background_image"]['name'] ) ) {
				$images = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_wp_attachment_qtsndtp_background_image', 'meta_value' => get_option( 'stylesheet' ), 'orderby' => 'none', 'nopaging' => true ) );
				if ( ! empty ( $images ) )
					wp_delete_attachment( $images[0]->ID );
				
				$upload_dir = wp_upload_dir();
				$upload_dir_full = $upload_dir['basedir'] . '/quotes-and-tips-image/';
				if ( ! is_dir( $upload_dir_full ) ) {
					wp_mkdir_p( $upload_dir_full, 0755 );
				}
				$new_file = $upload_dir_full . sanitize_file_name( $_FILES["qtsndtps_background_image"]['name'] );
				if ( false === @ move_uploaded_file( $_FILES["qtsndtps_background_image"]['tmp_name'], $new_file ) )
					wp_die( sprintf( __( 'The uploaded file could not be moved to %s.', 'quotes-and-tips' ), $upload_dir_full ), __( 'Image Processing Error', 'quotes-and-tips' ) );
				
				$file['url']	=	$upload_dir['baseurl'] . "/quotes-and-tips-image/" . sanitize_file_name( $_FILES["qtsndtps_background_image"]['name'] );
				$file['type']	=	$_FILES["qtsndtps_background_image"]["type"];
				$file['file']	=	$new_file;

				if ( isset( $file['error'] ) )
					wp_die( $file['error'],  __( 'Image Upload Error', 'quotes-and-tips' ) );

				$filename	=	basename( $file['file'] );

				/* Construct the object array */
				$object = array(
					'post_title'		=>	$filename,
					'post_content'		=>	$file['url'],
					'post_mime_type'	=>	$file['type'],
					'guid'				=>	$file['url'],
					'context'			=>	'qtsndtp_background_image'
				);

				/* Save the data */
				$id = wp_insert_attachment( $object, $file['file'] );
				update_post_meta( $id, '_wp_attachment_qtsndtp_background_image', get_option( 'stylesheet' ) );

				$qtsndtps_request_options['background_image'] = $file['url'];
			}

			if ( isset( $_REQUEST['qtsndtps_add_to_search'] ) && 2 == count( $_REQUEST['qtsndtps_add_to_search'] ) ) {
				foreach ( $_REQUEST['qtsndtps_add_to_search'] as $key => $value ) {
					if ( ! in_array( $key, $cstmsrch_options ) ) {
						array_push( $cstmsrch_options, $key );
					}
				}
				update_option( $cstmsrch_options_name, $cstmsrch_options );
			} elseif ( isset( $_REQUEST['qtsndtps_add_to_search'] ) && 1 == count( $_REQUEST['qtsndtps_add_to_search'] ) ) {
				$qtsndtps_push = array_keys( $_REQUEST['qtsndtps_add_to_search'] );
				$qtsndtps_push = $qtsndtps_push[0];
				if ( 'quote' == $qtsndtps_push ) {
					if ( in_array( 'tips', $cstmsrch_options ) ) {
						$key = array_search( 'tips', $cstmsrch_options );
						unset( $cstmsrch_options[ $key ] );
					}
				} else {
					if ( in_array( 'quote', $cstmsrch_options ) ) {
						$key = array_search( 'quote', $cstmsrch_options );
						unset( $cstmsrch_options[ $key ] );
					}
				}
				if ( ! in_array( $qtsndtps_push, $cstmsrch_options ) )
					array_push( $cstmsrch_options, $qtsndtps_push );
				update_option( $cstmsrch_options_name, $cstmsrch_options );
			} elseif ( $cstmsrch_options ) {
				$qtsndtps_push = array( 'quote', 'tips' );
				foreach ( $qtsndtps_push as $value ) {
					if ( in_array( $value, $cstmsrch_options ) ) {
						$key = array_search( $value, $cstmsrch_options );
						unset( $cstmsrch_options[ $key ] );
					}
					update_option( $cstmsrch_options_name, $cstmsrch_options );
				}
			}

			/* Array merge incase this version has added new options */
			$qtsndtps_options = array_merge( $qtsndtps_options, $qtsndtps_request_options );
			/* Check select one point in the blocks Arithmetic actions and Difficulty on settings page */
			update_option( 'qtsndtps_options', $qtsndtps_options );
			$message = __( "Settings saved", 'quotes-and-tips' );
		} /* Display form on the setting page */ 

		/* Add restore function */
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' ) ) {
			$qtsndtps_options = $qtsndtps_options_defaults;
			update_option( 'qtsndtps_options', $qtsndtps_options );
			$message = __( 'All plugin settings were restored.', 'quotes-and-tips' );
		} /* end */ ?>
		<div class="wrap">
			<h1><?php _e( 'Quotes and Tips Settings', 'quotes-and-tips' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( ! isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>"  href="admin.php?page=quotes-and-tips.php"><?php _e( 'Settings', 'quotes-and-tips' ); ?></a>
				<a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=quotes-and-tips.php&amp;action=custom_code"><?php _e( 'Custom code', 'quotes-and-tips' ); ?></a>
			</h2>
			<div class="updated fade below-h2" <?php if ( $message == "" || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<?php bws_show_settings_notice(); ?>
			<div class="error below-h2" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php if ( ! isset( $_GET['action'] ) ) { 
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( plugin_basename( __FILE__ ) );
				} else { ?>
					<br/>
					<div><?php printf( 
						__( "If you would like to add Quotes and Tips block to your page or post, please use %s button", 'quotes-and-tips' ), 
						'<span class="bws_code"><img style="vertical-align: sub;" src="' . plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) . '" alt=""/></span>' ); ?> 
						<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 180px;">
								<?php printf( 
									__( "You can add Quotes and Tips block to your page or post by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s", 'quotes-and-tips' ), 
									'<code><img style="vertical-align: sub;" src="' . plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) . '" alt="" /></code>',
									'<code>[quotes_and_tips]</code>'
								); ?>
							</div>
						</div>
					</div>
					<p>
						<?php _e( "Or add the following strings into the template source code", 'quotes-and-tips' ); ?> <code>&#60;?php if ( function_exists( 'qtsndtps_get_random_tip_quote' ) ) qtsndtps_get_random_tip_quote(); ?&#62;</code>
					</p>
					<form method="post" action="admin.php?page=quotes-and-tips.php" class="bws_form" enctype="multipart/form-data">
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php _e( 'Upload settings:', 'quotes-and-tips' ); ?> </th>
								<td><fieldset>
									<label><input type="radio" name="qtsndtps_page_load" value="1" <?php if ( '1' == $qtsndtps_options['page_load'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Random order with the page reload', 'quotes-and-tips' ); ?></label><br />
									<label><input type="radio" name="qtsndtps_page_load" value="0" <?php if ( '0' == $qtsndtps_options['page_load'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Random order without the page reload', 'quotes-and-tips' ); ?></label><br />
									<input type="number" name="qtsndtps_interval_load" min="1" max="999" step="1" value="<?php echo $qtsndtps_options['interval_load']; ?>" style="width:55px" /> <?php _e( 'Reload time (in seconds)', 'quotes-and-tips' ); ?></label>
								</fieldset></td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e( 'Title options:', 'quotes-and-tips' ); ?> </th>
								<td><fieldset>
									<label><input type="radio" name="qtsndtps_title_post" value="1" class="qtsndtps_title_post" <?php if ( '1' == $qtsndtps_options['title_post'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Get title from post', 'quotes-and-tips' ); ?></label><br />
									<label><input type="radio" name="qtsndtps_title_post" value="0" class="qtsndtps_title_post" <?php if ( '0' == $qtsndtps_options['title_post'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Get label of the block', 'quotes-and-tips' ); ?></label>
								</fieldset></td>
							</tr>
							<tr valign="top" class="qtsndtps_title_post_fields">
								<th scope="row"><?php _e( 'Tip label:', 'quotes-and-tips' ); ?> </th>
								<td>
									<input type="text" name="qtsndtps_tip_label" maxlength="250" value="<?php echo $qtsndtps_options['tip_label']; ?>" />
								</td>
							</tr>
							<tr valign="top" class="qtsndtps_title_post_fields">
								<th scope="row"><?php _e( 'Quote label:', 'quotes-and-tips' ); ?> </th>
								<td>
									<input type="text" name="qtsndtps_quote_label" maxlength="250" value="<?php echo $qtsndtps_options['quote_label']; ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row" colspan="2"><label><input type="checkbox" name="qtsndtps_additional_options" id="qtsndtps_additional_options" value="1" <?php if ( '1' == $qtsndtps_options['additional_options'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Additional settings', 'quotes-and-tips' ); ?> </label></th>
							</tr>
							<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['additional_options'] ) echo 'qtsndtps_hidden'; ?>">
								<th scope="row"><?php _e( 'Background Color:', 'quotes-and-tips' ); ?></th>
								<td>
									<input type="text" name="qtsndtps_background_color" id="qtsndtps-link-color" maxlength="7" value="<?php echo esc_attr( $qtsndtps_options['background_color'] ); ?>" />
									<a href="#" class="pickcolor hide-if-no-js" id="qtsndtps-link-color-example"></a>
									<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
								</td>
							</tr>
							<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['additional_options'] ) echo 'qtsndtps_hidden'; ?>">
								<th scope="row"><?php _e( 'Text Color:', 'quotes-and-tips' ); ?></th>
								<td>
									<input type="text" name="qtsndtps_text_color" id="qtsndtps-text-color" maxlength="7" value="<?php echo esc_attr( $qtsndtps_options['text_color'] ); ?>" />
									<a href="#" class="pickcolor1 hide-if-no-js" id="qtsndtps-text-color-example"></a>
									<div id="colorPickerDiv1" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
								</td>
							</tr>
							<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['additional_options'] ) echo 'qtsndtps_hidden'; ?>">
								<th scope="row"><?php _e( 'Background image:', 'quotes-and-tips' ); ?></th>
								<td>
									<label><input type="checkbox" name="qtsndtps_background_image_use" value="1" <?php if ( '1' == $qtsndtps_options['background_image_use'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Use background image', 'quotes-and-tips' ); ?></label><br />
									<label for="qtsndtps_background_image"><?php _e( 'Choose an image from your computer:', 'quotes-and-tips' ); ?></label><br />
									<input type="file" name="qtsndtps_background_image" id="qtsndtps_background_image"><br />
									<?php if ( ! empty( $qtsndtps_options['background_image'] ) ) { ?>
									<label for="qtsndtps_background_image"><?php _e( 'Current image:', 'quotes-and-tips' ); ?></label><br>
									<img src="<?php echo $qtsndtps_options['background_image']; ?>" alt="" title="" style="border:1px solid red;background-color:<?php echo esc_attr( $qtsndtps_options['background_color'] ); ?>;" />
									<?php } ?>
								</td>
							</tr>
							<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['additional_options'] ) echo 'qtsndtps_hidden'; ?>">
								<th scope="row"><?php _e( 'Background image repeat:', 'quotes-and-tips' ); ?> </th>
								<td><fieldset>
									<label><input type="checkbox" name="qtsndtps_background_image_repeat_x" value="1" <?php if ( '1' == $qtsndtps_options['background_image_repeat_x'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Horizontal repeat (x)', 'quotes-and-tips' ); ?></label><br />
									<label><input type="checkbox" name="qtsndtps_background_image_repeat_y" value="1" <?php if ( '1' == $qtsndtps_options['background_image_repeat_y'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Vertical repeat (y)', 'quotes-and-tips' ); ?></label>
								</fieldset></td>
							</tr>
							<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['additional_options'] ) echo 'qtsndtps_hidden'; ?>">
								<th scope="row"><?php _e( 'Background image horizontal alignment:', 'quotes-and-tips' ); ?> </th>
								<td><fieldset>
									<label><input type="radio" name="qtsndtps_background_image_gposition" value="left" <?php if ( 'left' == $qtsndtps_options['background_image_gposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Left', 'quotes-and-tips' ); ?></label><br />
									<label><input type="radio" name="qtsndtps_background_image_gposition" value="center" <?php if ( 'center' == $qtsndtps_options['background_image_gposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Center', 'quotes-and-tips' ); ?></label><br />
									<label><input type="radio" name="qtsndtps_background_image_gposition" value="right" <?php if ( 'right' == $qtsndtps_options['background_image_gposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Right', 'quotes-and-tips' ); ?></label>
								</fieldset></td>
							</tr>
							<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['additional_options'] ) echo 'qtsndtps_hidden'; ?>">
								<th scope="row"><?php _e( 'Background image vertical alignment:', 'quotes-and-tips' ); ?> </th>
								<td><fieldset>
									<label><input type="radio" name="qtsndtps_background_image_vposition" value="top" <?php if ( 'top' == $qtsndtps_options['background_image_vposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Top', 'quotes-and-tips' ); ?></label><br />
									<label><input type="radio" name="qtsndtps_background_image_vposition" value="center" <?php if ( 'center' == $qtsndtps_options['background_image_vposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Center', 'quotes-and-tips' ); ?></label><br />
									<label><input type="radio" name="qtsndtps_background_image_vposition" value="bottom" <?php if ( 'bottom' == $qtsndtps_options['background_image_vposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Bottom', 'quotes-and-tips' ); ?></label>
								</fieldset></td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e( 'Add Quotes and Tips to the search', 'quotes-and-tips' ); ?></th>
								<td>
									<?php if ( array_key_exists( 'custom-search-plugin/custom-search-plugin.php', $all_plugins ) || array_key_exists( 'custom-search-pro/custom-search-pro.php', $all_plugins ) ) { ?>
										<fieldset>
											<?php if ( is_plugin_active( 'custom-search-plugin/custom-search-plugin.php' ) || is_plugin_active( 'custom-search-pro/custom-search-pro.php' ) ) { ?>
												<label><input type="checkbox" name="qtsndtps_add_to_search[quote]" value="1" <?php if ( false !== $cstmsrch_options && in_array( 'quote', $cstmsrch_options ) ) echo "checked=\"checked\"";  elseif ( ! $cstmsrch_options ) echo "disabled=\"disabled\""; ?> />Quote</label>
												<span class="bws_info"> (<?php _e( 'Using', 'quotes-and-tips' ); ?> <a href="admin.php?page=custom_search.php">Custom Search</a> <?php _e( 'powered by', 'quotes-and-tips' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>)</span><br />
												<label><input type="checkbox" name="qtsndtps_add_to_search[tips]" value="1" <?php if ( false !== $cstmsrch_options && in_array( 'tips', $cstmsrch_options ) ) echo "checked=\"checked\""; elseif ( ! $cstmsrch_options ) echo "disabled=\"disabled\"";  ?> /> Tips</label>
											<?php } else { ?>
												<label><input disabled="disabled" type="checkbox" name="qtsndtps_add_to_search[quote]" value="1" <?php if ( false !== $cstmsrch_options && in_array( 'quote', $cstmsrch_options ) ) echo "checked=\"checked\""; ?> />Quote</label>
												<span class="bws_info">(<?php _e( 'Using Custom Search powered by', 'quotes-and-tips' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>) <a href="<?php echo bloginfo("url"); ?>/wp-admin/plugins.php"><?php _e( 'Activate Custom Search', 'quotes-and-tips' ); ?></a></span><br />
												<label><input disabled="disabled" type="checkbox" name="qtsndtps_add_to_search[tips]" value="1" <?php if ( false !== $cstmsrch_options && in_array( 'tips', $cstmsrch_options ) ) echo "checked=\"checked\""; ?> /> Tips</label>
											<?php } ?>
										</fieldset>
									<?php } else { ?>
										<input disabled="disabled" type="checkbox" name="qtsndtps_add_to_search[]" value="1" />
										<span class="bws_info">(<?php _e( 'Using Custom Search powered by', 'quotes-and-tips' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>) <a href="http://bestwebsoft.com/products/wordpress/plugins/custom-search/"><?php _e( 'Download Custom Search', 'quotes-and-tips' ); ?></a></span><br />
									<?php } ?>
								</td>
							</tr>
						</table>						
						<p class="submit">
							<input type="hidden" name='qtsndtps_form_submit' value="submit" />
							<input id="bws-submit-button" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'quotes-and-tips' ); ?>" />
							<?php wp_nonce_field( plugin_basename( __FILE__ ), 'qtsndtps_nonce_name' ); ?>
						</p>
					</form>
					<?php bws_form_restore_default_settings( plugin_basename( __FILE__ ) );
				}
			} else {
				bws_custom_code_tab();
			}
			bws_plugin_reviews_block( $qtsndtps_plugin_info['Name'], 'quotes-and-tips' ); ?>
		</div>
	<?php }
}

if ( ! function_exists( 'qtsndtps_register_plugin_links' ) ) {
	function qtsndtps_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[]	=	'<a href="admin.php?page=quotes-and-tips.php">' . __( 'Settings', 'quotes-and-tips' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/quotes-and-tips/faq/" target="_blank">' . __( 'FAQ', 'quotes-and-tips' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'quotes-and-tips' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists( 'qtsndtps_plugin_action_links' ) ) {
	function qtsndtps_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = plugin_basename( __FILE__ );

			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=quotes-and-tips.php">' . __( 'Settings', 'quotes-and-tips' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	} /* End function qtsndtps_plugin_action_links */
}

if ( ! function_exists ( 'qtsndtps_print_style_script' ) ) {
	function qtsndtps_print_style_script() {
		global $qtsndtps_options;
		
		$background_image_use	=	$qtsndtps_options['background_image_use'];
		$background_image		=	$qtsndtps_options['background_image'];
		$background_gposition	=	$qtsndtps_options['background_image_gposition'];
		$background_vposition	=	$qtsndtps_options['background_image_vposition'];
		$background_repeat_x	=	$qtsndtps_options['background_image_repeat_x'];
		$background_repeat_y	=	$qtsndtps_options['background_image_repeat_y'];
		$interval_load			=	( $qtsndtps_options['interval_load'] == '0' ) ? '10' : $qtsndtps_options['interval_load'];
		$page_load				=	$qtsndtps_options['page_load'];
		$additional_options		=	$qtsndtps_options['additional_options'];

		if ( '0' == $additional_options ) {
			/* If additional settings is turned off */
			$background_color = $text_color = 'inherit';
		} else {
			$background_color	= $qtsndtps_options['background_color'];
			$text_color			= $qtsndtps_options['text_color'];
		} ?>
		<style type="text/css">
			/* Style for tips|quote block */
			#quotes_box_and_tips {
				background-color: <?php echo $background_color; ?> !important;
				color: <?php echo $text_color; ?> !important;
				<?php if ( 1 == $background_image_use && ! empty( $background_image ) ) { ?>
				background-image: url( <?php echo $background_image; ?> );
				<?php } elseif ( '0' == $additional_options ) { ?>
					background-image: none;
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
		<?php if ( '0' == $page_load ) { ?>
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
								if ( $(this).hasClass("visible") === true && !flag ) {
									if ( $(this).next().hasClass("tips_box") ){
										$(this).animate({opacity:0}, 500, function(){
											$(this).addClass("hidden");
											$(this).removeClass("visible");
											$(this).next().animate({opacity:0}, 1);
											$(this).next().removeClass("hidden");
											$(this).next().addClass("visible");
											$(this).next().animate({opacity:1}, 500);
										});

									} else {
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
								if ( $(this).hasClass("visible") === true && !flag ) {
									if ( $(this).next().hasClass("quotes_box") ){
										$(this).animate({opacity:0}, 500, function(){
											$(this).addClass("hidden");
											$(this).removeClass("visible");
											$(this).next().animate({opacity:0}, 10);
											$(this).next().removeClass("hidden");
											$(this).next().addClass("visible");
											$(this).next().animate({opacity:1}, 500);
										});
									} else {
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

if ( ! function_exists ( 'qtsndtps_wp_head' ) ) {
	function qtsndtps_wp_head() {
		wp_enqueue_style( 'qtsndtps_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );

		if ( is_admin() && isset( $_GET['page'] ) && "quotes-and-tips.php" == $_GET['page'] ) {
			if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) {
				bws_plugins_include_codemirror();
			} else {
				wp_enqueue_style( 'farbtastic' );
				wp_enqueue_script( 'farbtastic' );
				wp_enqueue_script( 'qtsndtps_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
			}
		}
	}
}

/* add admin notices */
if ( ! function_exists ( 'qtsndtps_admin_notices' ) ) {
	function qtsndtps_admin_notices() {
		global $hook_suffix, $qtsndtps_plugin_info;
		if ( 'plugins.php' == $hook_suffix && ! is_network_admin() ) {
			bws_plugin_banner_to_settings( $qtsndtps_plugin_info, 'qtsndtps_options', 'quotes-and-tips', 'admin.php?page=quotes-and-tips.php', 'post-new.php?post_type=quote' );
		}
		if ( isset( $_GET['page'] ) && "quotes-and-tips.php" == $_GET['page'] ) {
			bws_plugin_suggest_feature_banner( $qtsndtps_plugin_info, 'qtsndtps_options', 'quotes-and-tips' );
		}
	}
}

if ( ! function_exists( 'qtsndtps_save_custom_quote' ) ) {
	function qtsndtps_save_custom_quote( $post_id ) {
		global $post;
		if ( ( ( isset( $_POST['name_field'] ) && '' != $_POST['name_field'] ) || ( isset( $_POST['off_cap'] ) && '' != $_POST['off_cap'] ) ) && check_admin_referer( plugin_basename( __FILE__ ), 'qtsndtps_nonce_name' ) ) {
			update_post_meta( $post->ID, 'name_field', stripslashes( esc_html( $_POST['name_field'] ) ) );
			update_post_meta( $post->ID, 'off_cap', stripslashes( esc_html( $_POST['off_cap'] ) ) );
		}
	}
}

/* add shortcode content  */
if ( ! function_exists( 'qtsndtps_shortcode_button_content' ) ) {
	function qtsndtps_shortcode_button_content( $content ) { ?>
		<div id="qtsndtps" style="display:none;">
			<fieldset>				
				<?php _e( 'Add Quotes and Tips block to your page or post', 'quotes-and-tips' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[quotes_and_tips]" />
			<div class="clear"></div>
		</div>
	<?php }
}

/* add help tab  */
if ( ! function_exists( 'qtsndtps_add_tabs' ) ) {
	function qtsndtps_add_tabs() {
		$screen = get_current_screen();
		if ( ( ! empty( $screen->post_type ) && 'quote' == $screen->post_type ) ||
			( ! empty( $screen->post_type ) && 'tips' == $screen->post_type ) ||
			( isset( $_GET['page'] ) && $_GET['page'] == 'quotes-and-tips.php' ) ) {
			$args = array(
				'id' 			=> 'qtsndtps',
				'section' 		=> '200538959'
			);
			bws_help_tab( $screen, $args );
		}
	}
}

/* Function for delete options */
if ( ! function_exists ( 'qtsndtps_delete_options' ) ) {
	function qtsndtps_delete_options() {
		global $wpdb;
		/* Delete options */
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'qtsndtps_options' );
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'qtsndtps_options' );
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

add_action( 'admin_menu', 'add_qtsndtps_admin_menu' );

add_action( 'init', 'qtsndtps_plugin_init' );
add_action( 'admin_init', 'qtsndtps_plugin_admin_init' );
add_action( 'plugins_loaded', 'qtsndtps_plugins_loaded' );

add_action( 'wp_head', 'qtsndtps_print_style_script' );
add_action( 'admin_enqueue_scripts', 'qtsndtps_wp_head' );
add_action( 'wp_enqueue_scripts', 'qtsndtps_wp_head' );

add_action( 'save_post', 'qtsndtps_save_custom_quote' );

add_shortcode( 'quotes_and_tips', 'qtsndtps_create_tip_quote_block' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'qtsndtps_shortcode_button_content' );
/* Additional links on the plugin page */
add_filter( 'plugin_row_meta', 'qtsndtps_register_plugin_links', 10, 2 );
/* Adds "Settings" link to the plugin action page */
add_filter( 'plugin_action_links', 'qtsndtps_plugin_action_links', 10, 2 );
/* add admin notices */
add_action( 'admin_notices', 'qtsndtps_admin_notices' );

register_uninstall_hook( __FILE__, 'qtsndtps_delete_options' );