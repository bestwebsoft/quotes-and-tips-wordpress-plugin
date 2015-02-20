<?php
/*
Plugin Name: Quotes and Tips
Plugin URI: http://bestwebsoft.com/products/
Description: This plugin displays the Quotes and Tips in random order
Author: BestWebSoft
Version: 1.23
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*  © Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

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
		global $bstwbsftwppdtplgns_options, $bstwbsftwppdtplgns_added_menu;
		$bws_menu_info = get_plugin_data( plugin_dir_path( __FILE__ ) . "bws_menu/bws_menu.php" );
		$bws_menu_version = $bws_menu_info["Version"];
		$base = plugin_basename( __FILE__ );

		if ( ! isset( $bstwbsftwppdtplgns_options ) ) {
			if ( is_multisite() ) {
				if ( ! get_site_option( 'bstwbsftwppdtplgns_options' ) )
					add_site_option( 'bstwbsftwppdtplgns_options', array() );
				$bstwbsftwppdtplgns_options = get_site_option( 'bstwbsftwppdtplgns_options' );
			} else {
				if ( ! get_option( 'bstwbsftwppdtplgns_options' ) )
					add_option( 'bstwbsftwppdtplgns_options', array() );
				$bstwbsftwppdtplgns_options = get_option( 'bstwbsftwppdtplgns_options' );
			}
		}

		if ( isset( $bstwbsftwppdtplgns_options['bws_menu_version'] ) ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			unset( $bstwbsftwppdtplgns_options['bws_menu_version'] );
			if ( is_multisite() )
				update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			else
				update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] ) || $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] < $bws_menu_version ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			if ( is_multisite() )
				update_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			else
				update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options );
			require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_added_menu ) ) {
			$plugin_with_newer_menu = $base;
			foreach ( $bstwbsftwppdtplgns_options['bws_menu']['version'] as $key => $value ) {
				if ( $bws_menu_version < $value && is_plugin_active( $base ) ) {
					$plugin_with_newer_menu = $key;
				}
			}
			$plugin_with_newer_menu = explode( '/', $plugin_with_newer_menu );
			$wp_content_dir = defined( 'WP_CONTENT_DIR' ) ? basename( WP_CONTENT_DIR ) : 'wp-content';
			if ( file_exists( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' ) )
				require_once( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' );
			else
				require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
			$bstwbsftwppdtplgns_added_menu = true;
		}

		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', plugins_url( "images/px.png", __FILE__ ), 1001 );
		add_submenu_page( 'bws_plugins', __( 'Quotes and Tips', 'quotes_and_tips' ), __( 'Quotes and Tips', 'quotes_and_tips' ), 'manage_options', "quotes-and-tips.php", 'qtsndtps_settings_page' );
	}
}

if ( ! function_exists( 'qtsndtps_register_tips_post_type' ) ) {
	function qtsndtps_register_tips_post_type() {
		$args = array(
			'label'				=>	__( 'Tips', 'quotes_and_tips' ),
			'singular_label'	=>	__( 'Tips', 'quotes_and_tips' ),
			'public'			=>	true,
			'show_ui'			=>	true,
			'capability_type' 	=>	'post',
			'hierarchical'		=>	false,
			'rewrite'			=>	true,
			'supports'			=>	array( 'title', 'editor' ),
			'labels'			=>	array(
				'add_new_item'			=>	__( 'Add a new tip', 'quotes_and_tips' ),
				'edit_item'				=>	__( 'Edit tips', 'quotes_and_tips' ),
				'new_item'				=>	__( 'New tip', 'quotes_and_tips' ),
				'view_item'				=>	__( 'View tips', 'quotes_and_tips' ),
				'search_items'			=>	__( 'Search tips', 'quotes_and_tips' ),
				'not_found'				=>	__( 'No tips found', 'quotes_and_tips' ),
				'not_found_in_trash'	=>	__( 'No tips found in Trash', 'quotes_and_tips' )
			)
		);
		register_post_type( 'tips' , $args );
	}
}

if( ! function_exists( 'qtsndtps_register_quote_post_type' ) ) {
	function qtsndtps_register_quote_post_type() {
		$args = array(
			'label'				=>	__( 'Quotes', 'quotes_and_tips' ),
			'singular_label'	=>	__( 'Quotes', 'quotes_and_tips' ),
			'public'			=>	true,
			'show_ui'			=>	true,
			'capability_type'	=>	'post',
			'hierarchical'		=>	false,
			'rewrite'			=>	true,
			'supports'			=>	array( 'title', 'editor' ),
			'labels'			=>	array(
				'add_new_item'			=>	__( 'Add a New quote', 'quotes_and_tips' ),
				'edit_item'				=>	__( 'Edit quote', 'quotes_and_tips' ),
				'new_item'				=>	__( 'New quote', 'quotes_and_tips' ),
				'view_item'				=>	__( 'View quote', 'quotes_and_tips' ),
				'search_items'			=>	__( 'Search quote', 'quotes_and_tips' ),
				'not_found'				=>	__( 'No quote found', 'quotes_and_tips' ),
				'not_found_in_trash'	=>	__( 'No quote found in Trash', 'quotes_and_tips' )
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
			'posts_per_page'	=>	'0' == $qtsndtps_options['qtsndtps_page_load'] ? -1 : 1
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
					$random_tip_quote_block .= ( '1' == $qtsndtps_options['qtsndtps_title_post'] ) ? the_title() : $qtsndtps_options['qtsndtps_tip_label'];
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
					'posts_per_page'	=>	'0' == $qtsndtps_options['qtsndtps_page_load'] ? -1 : 1
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
					$random_tip_quote_block .= ( '1' == $qtsndtps_options['qtsndtps_title_post'] ) ? the_title() : $qtsndtps_options['qtsndtps_quote_label'];
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
		<p><label for="name_field"><?php _e( 'Name:', 'quotes_and_tips' ); ?><br />
			<input type="text" id="name_field" size="37" name="name_field" value="<?php if ( ! empty( $name_field ) ) echo $name_field[0]; ?>"/></label></p>
		<p><label for="off_cap"><?php _e( 'Official position:', 'quotes_and_tips' ); ?></label><br />
			<input type="text" id="off_cap" size="37" name="off_cap" value="<?php if ( ! empty( $off_cap ) ) echo $off_cap[0]; ?>"/></p>
	<?php }
}

if ( ! function_exists ( 'qtsndtps_plugin_init' ) ) {
	function qtsndtps_plugin_init() {
		global $qtsndtps_plugin_info;
		load_plugin_textdomain( 'quotes_and_tips', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		qtsndtps_version_check();

		/* Call register settings function */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && "quotes-and-tips.php" == $_GET['page'] ) )
			register_qtsndtps_settings();

		qtsndtps_register_tips_post_type();

		qtsndtps_register_quote_post_type();
	}
}

if ( ! function_exists ( 'qtsndtps_plugin_admin_init' ) ) {
	function qtsndtps_plugin_admin_init() {
		global $bws_plugin_info, $qtsndtps_plugin_info;

		if ( ! $qtsndtps_plugin_info )
			$qtsndtps_plugin_info = get_plugin_data( __FILE__ );

		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '82', 'version' => $qtsndtps_plugin_info["Version"] );

		qtsndtps_add_custom_metabox();
	}
}

/* Register settings function */
if( ! function_exists( 'register_qtsndtps_settings' ) ) {
	function register_qtsndtps_settings() {
		global $qtsndtps_options, $bws_plugin_info, $qtsndtps_plugin_info;

		if ( ! $qtsndtps_plugin_info ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$qtsndtps_plugin_info = get_plugin_data( __FILE__ );
		}

		$qtsndtps_options_defaults = array(
			'plugin_option_version'					=>	$qtsndtps_plugin_info["Version"],
			'qtsndtps_page_load'					=>	'1',
			'qtsndtps_interval_load'				=>	'10',
			'qtsndtps_tip_label'					=>	__( 'Tips', 'quotes_and_tips' ),
			'qtsndtps_quote_label'					=>	__( 'Quotes from our clients', 'quotes_and_tips' ),
			'qtsndtps_title_post'					=>	'0',
			'qtsndtps_additional_options'			=>	'1',
			'qtsndtps_background_color' 			=>	'#2484C6',
			'qtsndtps_text_color'					=>	'#FFFFFF',
			'qtsndtps_background_image_use' 		=>	'0',
			'qtsndtps_background_image'				=>	'',
			'qtsndtps_background_image_repeat_x'	=>	'0',
			'qtsndtps_background_image_repeat_y'	=>	'0',
			'qtsndtps_background_image_gposition'	=>	'left',
			'qtsndtps_background_image_vposition'	=>	'bottom'
		);

		/* Install the option defaults */
		if ( ! get_option( 'qtsndtps_options' ) )
			add_option( 'qtsndtps_options', $qtsndtps_options_defaults );

		/* Get options from the database */
		$qtsndtps_options = get_option( 'qtsndtps_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $qtsndtps_options['plugin_option_version'] ) || $qtsndtps_options['plugin_option_version'] != $qtsndtps_plugin_info["Version"] ) {
			$qtsndtps_options = array_merge( $qtsndtps_options_defaults, $qtsndtps_options );
			$qtsndtps_options['plugin_option_version'] = $qtsndtps_plugin_info["Version"];
			update_option( 'qtsndtps_options', $qtsndtps_options );
		}
	}
}

if ( ! function_exists( 'qtsndtps_add_custom_metabox' ) ) {
	function qtsndtps_add_custom_metabox() {
		add_meta_box( 'custom-metabox', __( 'Name and Official position', 'quotes_and_tips' ), 'qtsndtps_quote_custom_metabox', 'quote', 'normal', 'high' );
	}
}

/* Function check if plugin is compatible with current WP version  */
if ( ! function_exists ( 'qtsndtps_version_check' ) ) {
	function qtsndtps_version_check() {
		global $wp_version, $qtsndtps_plugin_info;
		$plugin			=	plugin_basename( __FILE__ );
		$require_wp		=	"3.0"; /* Wordpress at least requires version */
	 	if ( version_compare( $wp_version, $require_wp, "<" ) ) {
	 		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
				$admin_url = ( function_exists( 'get_admin_url' ) ) ? get_admin_url( null, 'plugins.php' ) : esc_url( '/wp-admin/plugins.php' );
				if ( ! $qtsndtps_plugin_info )
					$qtsndtps_plugin_info = get_plugin_data( __FILE__, false );
				wp_die( "<strong>" . $qtsndtps_plugin_info['Name'] . "</strong> " . __( 'requires', 'quotes_and_tips' ) . " <strong>WordPress " . $require_wp . "</strong> " . __( 'or higher, that is why it has been deactivated! Please upgrade WordPress and try again.', 'quotes_and_tips') . "<br /><br />" . __( 'Back to the WordPress', 'quotes_and_tips') . " <a href='" . $admin_url . "'>" . __( 'Plugins page', 'quotes_and_tips') . "</a>." );
			}
		}
	}
}

if ( ! function_exists( 'qtsndtps_settings_page' ) ) {
	function qtsndtps_settings_page() {
		global $qtsndtps_options;
		$error = $message = $cstmsrch_options_name = "";

		if ( false !== get_option( 'cstmsrchpr_options' ) )
			$cstmsrch_options_name = "cstmsrchpr_options";
		elseif ( false !== get_option( 'cstmsrch_options' ) )
			$cstmsrch_options_name = "cstmsrch_options";
		elseif ( false !== get_option( 'bws_custom_search' ) )
			$cstmsrch_options_name = "bws_custom_search";		
		
		$cstmsrch_options = get_option( $cstmsrch_options_name );

		$all_plugins	=	get_plugins();

		/* Save data for settings page */
		if ( isset( $_REQUEST['qtsndtps_form_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'qtsndtps_nonce_name' ) ) {
			$qtsndtps_request_options = array();
			$qtsndtps_request_options['qtsndtps_page_load']						=	$_REQUEST['qtsndtps_page_load'];
			$qtsndtps_request_options['qtsndtps_interval_load']					=	intval( $_REQUEST['qtsndtps_interval_load'] );
			$qtsndtps_request_options['qtsndtps_tip_label']						=	stripslashes( esc_html( $_REQUEST['qtsndtps_tip_label'] ) );
			$qtsndtps_request_options['qtsndtps_quote_label']					=	stripslashes( esc_html( $_REQUEST['qtsndtps_quote_label'] ) );
			$qtsndtps_request_options['qtsndtps_title_post']					=	$_REQUEST['qtsndtps_title_post'];
			$qtsndtps_request_options['qtsndtps_additional_options']			=	isset( $_REQUEST['qtsndtps_additional_options'] ) ? 1 : 0 ;
			$qtsndtps_request_options['qtsndtps_background_color']				=	stripslashes( esc_html( $_REQUEST['qtsndtps_background_color'] ) );
			$qtsndtps_request_options['qtsndtps_text_color']					=	stripslashes( esc_html( $_REQUEST['qtsndtps_text_color'] ) );
			$qtsndtps_request_options['qtsndtps_background_image_use']			=	isset( $_REQUEST['qtsndtps_background_image_use'] ) ? 1 : 0 ;
			$qtsndtps_request_options['qtsndtps_background_image_gposition']	=	$_REQUEST['qtsndtps_background_image_gposition'];
			$qtsndtps_request_options['qtsndtps_background_image_vposition']	=	$_REQUEST['qtsndtps_background_image_vposition'];
			$qtsndtps_request_options['qtsndtps_background_image_repeat_x']		=	isset( $_REQUEST['qtsndtps_background_image_repeat_x'] ) ? 1 : 0 ;
			$qtsndtps_request_options['qtsndtps_background_image_repeat_y']		=	isset( $_REQUEST['qtsndtps_background_image_repeat_y'] ) ? 1 : 0 ;

			if ( isset( $_FILES["qtsndtps_background_image"]['name'] ) && ! empty( $_FILES["qtsndtps_background_image"]['name'] ) ) {
				$images = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_wp_attachment_qtsndtp_background_image', 'meta_value' => get_option( 'stylesheet' ), 'orderby' => 'none', 'nopaging' => true ) );
				if ( ! empty ( $images ) )
					wp_delete_attachment( $images[0]->ID );

				$uploads['path'] = TEMPLATEPATH . "/";
				$new_file = $uploads['path'] . $_FILES["qtsndtps_background_image"]['name'];
				if ( false === @ move_uploaded_file( $_FILES["qtsndtps_background_image"]['tmp_name'], $new_file ) )
					wp_die( sprintf( __( 'The uploaded file could not be moved to %s.' ), $uploads['path'] ), __( 'Image Processing Error' ) );
				$file['url']	=	get_bloginfo('template_directory') . "/" . $_FILES["qtsndtps_background_image"]['name'];
				$file['type']	=	$_FILES["qtsndtps_background_image"]["type"];
				$file['file']	=	$new_file;

				if ( isset( $file['error'] ) )
					wp_die( $file['error'],  __( 'Image Upload Error' ) );

				$url		=	$file['url'];
				$type		=	$file['type'];
				$file		=	$file['file'];
				$filename	=	basename( $file );

				/* Construct the object array */
				$object = array(
					'post_title'		=>	$filename,
					'post_content'		=>	$url,
					'post_mime_type'	=>	$type,
					'guid'				=>	$url,
					'context'			=>	'qtsndtp_background_image'
				);

				/* Save the data */
				$id = wp_insert_attachment( $object, $file );
				/* wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) ); */
				update_post_meta( $id, '_wp_attachment_qtsndtp_background_image', get_option('stylesheet' ) );

				$qtsndtps_request_options['qtsndtps_background_image'] = $url;
			}

			if ( isset( $_REQUEST['qtsndtps_add_to_search'] ) && 2 == count( $_REQUEST['qtsndtps_add_to_search'] ) ) {
				foreach ( $_REQUEST['qtsndtps_add_to_search'] as $key => $value) {
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
						unset( $cstmsrch_options[$key] );
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
						unset( $cstmsrch_options[$key] );
					}
					update_option( $cstmsrch_options_name, $cstmsrch_options );
				}
			}

			/* Array merge incase this version has added new options */
			$qtsndtps_options = array_merge( $qtsndtps_options, $qtsndtps_request_options );
			/* Check select one point in the blocks Arithmetic actions and Difficulty on settings page */
			update_option( 'qtsndtps_options', $qtsndtps_options );
			$message = __( "Settings saved", 'quotes_and_tips' );
		} /* Display form on the setting page */ ?>
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php _e('Quotes and Tips Settings', 'quotes_and_tips' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active"  href="admin.php?page=quotes-and-tips.php"><?php _e( 'Settings', 'quotes_and_tips' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/products/quotes-and-tips/faq/" target="_blank"><?php _e( 'FAQ', 'quotes_and_tips' ); ?></a>
			</h2>
			<div class="updated fade" <?php if ( ! isset( $_REQUEST['qtsndtps_form_submit'] ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div id="qtsndtps_settings_notice" class="updated fade" style="display:none"><p><strong><?php _e( "Notice:", 'quotes_and_tips' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'quotes_and_tips' ); ?></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<p>
				<?php _e( "If you would like to use this block, just copy and paste this shortcode to your post or page", 'quotes_and_tips' ); ?> - <code>[quotes_and_tips]</code>, 
				<?php _e( "or add the following strings into the template source code", 'quotes_and_tips' ); ?> <code>&#60;?php if ( function_exists( 'qtsndtps_get_random_tip_quote' ) ) qtsndtps_get_random_tip_quote(); ?&#62;</code>
			</p>
			<form method="post" action="admin.php?page=quotes-and-tips.php" id="qtsndtps_form_image_size" enctype="multipart/form-data">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Upload settings:', 'quotes_and_tips' ); ?> </th>
						<td>
							<label><input type="radio" name="qtsndtps_page_load" value="1" <?php if ( '1' == $qtsndtps_options['qtsndtps_page_load'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Random order with the page reload', 'quotes_and_tips' ); ?></label><br />
							<label><input type="radio" name="qtsndtps_page_load" value="0" <?php if ( '0' == $qtsndtps_options['qtsndtps_page_load'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Random order without the page reload', 'quotes_and_tips' ); ?></label><br />
							<input type="text" name="qtsndtps_interval_load" value="<?php echo $qtsndtps_options['qtsndtps_interval_load']; ?>" style="width:30px" /> <?php _e( 'Reload time (in seconds)', 'quotes_and_tips' ); ?></label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Title options:', 'quotes_and_tips' ); ?> </th>
						<td>
							<label><input type="radio" name="qtsndtps_title_post" value="1" class="qtsndtps_title_post" <?php if ( '1' == $qtsndtps_options['qtsndtps_title_post'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Get title from post', 'quotes_and_tips' ); ?></label><br />
							<label><input type="radio" name="qtsndtps_title_post" value="0" class="qtsndtps_title_post" <?php if ( '0' == $qtsndtps_options['qtsndtps_title_post'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Get label of the block', 'quotes_and_tips' ); ?></label>
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
						<th scope="row" colspan="2"><label><input type="checkbox" name="qtsndtps_additional_options" id="qtsndtps_additional_options" value="1" <?php if ( '1' == $qtsndtps_options['qtsndtps_additional_options'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Additional settings', 'quotes_and_tips' ); ?> </label></th>
					</tr>
					<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['qtsndtps_additional_options'] ) echo 'qtsndtps_hidden'; ?>">
						<th scope="row"><?php _e( 'Background Color:', 'quotes_and_tips' ); ?></th>
						<td>
							<input type="text" name="qtsndtps_background_color" id="link-color" value="<?php echo esc_attr( $qtsndtps_options['qtsndtps_background_color'] ); ?>" />
							<a href="#" class="pickcolor hide-if-no-js" id="link-color-example"></a>
							<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
						</td>
					</tr>
					<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['qtsndtps_additional_options'] ) echo 'qtsndtps_hidden'; ?>">
						<th scope="row"><?php _e( 'Text Color:', 'quotes_and_tips' ); ?></th>
						<td>
							<input type="text" name="qtsndtps_text_color" id="text-color" value="<?php echo esc_attr( $qtsndtps_options['qtsndtps_text_color'] ); ?>" />
							<a href="#" class="pickcolor1 hide-if-no-js" id="text-color-example"></a>
							<div id="colorPickerDiv1" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
						</td>
					</tr>
					<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['qtsndtps_additional_options'] ) echo 'qtsndtps_hidden'; ?>">
						<th scope="row"><?php _e( 'Background image:', 'quotes_and_tips' ); ?></th>
						<td>
							<label><input type="checkbox" name="qtsndtps_background_image_use" value="1" <?php if ( '1' == $qtsndtps_options['qtsndtps_background_image_use'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Use background image', 'quotes_and_tips' ); ?></label><br />
							<label for="qtsndtps_background_image"><?php _e( 'Choose an image from your computer:', 'quotes_and_tips' ); ?></label><br />
							<input type="file" name="qtsndtps_background_image" id="qtsndtps_background_image"><br />
							<?php if ( ! empty( $qtsndtps_options['qtsndtps_background_image'] ) ) { ?>
							<label for="qtsndtps_background_image"><?php _e( 'Current image:', 'quotes_and_tips' ); ?></label><br>
							<img src="<?php echo $qtsndtps_options['qtsndtps_background_image']; ?>" alt="" title="" style="border:1px solid red;background-color:<?php echo esc_attr( $qtsndtps_options['qtsndtps_background_color'] ); ?>;" />
							<?php } ?>
						</td>
					</tr>
					<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['qtsndtps_additional_options'] ) echo 'qtsndtps_hidden'; ?>">
						<th scope="row"><?php _e( 'Background image repeat:', 'quotes_and_tips' ); ?> </th>
						<td>
							<label><input type="checkbox" name="qtsndtps_background_image_repeat_x" value="1" <?php if ( '1' == $qtsndtps_options['qtsndtps_background_image_repeat_x'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Horizontal repeat (x)', 'quotes_and_tips' ); ?></label><br />
							<label><input type="checkbox" name="qtsndtps_background_image_repeat_y" value="1" <?php if ( '1' == $qtsndtps_options['qtsndtps_background_image_repeat_y'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Vertical repeat (y)', 'quotes_and_tips' ); ?></label>
						</td>
					</tr>
					<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['qtsndtps_additional_options'] ) echo 'qtsndtps_hidden'; ?>">
						<th scope="row"><?php _e( 'Background image horizontal alignment:', 'quotes_and_tips' ); ?> </th>
						<td>
							<label><input type="radio" name="qtsndtps_background_image_gposition" value="left" <?php if ( 'left' == $qtsndtps_options['qtsndtps_background_image_gposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Left', 'quotes_and_tips' ); ?></label><br />
							<label><input type="radio" name="qtsndtps_background_image_gposition" value="center" <?php if ( 'center' == $qtsndtps_options['qtsndtps_background_image_gposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Center', 'quotes_and_tips' ); ?></label><br />
							<label><input type="radio" name="qtsndtps_background_image_gposition" value="right" <?php if ( 'right' == $qtsndtps_options['qtsndtps_background_image_gposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Right', 'quotes_and_tips' ); ?></label>
						</td>
					</tr>
					<tr valign="top" class="qtsndtps_additions_block <?php if ( '0' == $qtsndtps_options['qtsndtps_additional_options'] ) echo 'qtsndtps_hidden'; ?>">
						<th scope="row"><?php _e( 'Background image vertical alignment:', 'quotes_and_tips' ); ?> </th>
						<td>
							<label><input type="radio" name="qtsndtps_background_image_vposition" value="top" <?php if ( 'top' == $qtsndtps_options['qtsndtps_background_image_vposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Top', 'quotes_and_tips' ); ?></label><br />
							<label><input type="radio" name="qtsndtps_background_image_vposition" value="center" <?php if ( 'center' == $qtsndtps_options['qtsndtps_background_image_vposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Center', 'quotes_and_tips' ); ?></label><br />
							<label><input type="radio" name="qtsndtps_background_image_vposition" value="bottom" <?php if ( 'bottom' == $qtsndtps_options['qtsndtps_background_image_vposition'] ) echo 'checked="checked"'; ?> /> <?php _e( 'Bottom', 'quotes_and_tips' ); ?></label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Add Quotes and Tips to the search', 'quotes_and_tips' ); ?></th>
						<td>
							<?php if ( array_key_exists( 'custom-search-plugin/custom-search-plugin.php', $all_plugins ) || array_key_exists( 'custom-search-pro/custom-search-pro.php', $all_plugins ) ) {
								if ( is_plugin_active( 'custom-search-plugin/custom-search-plugin.php' ) || is_plugin_active( 'custom-search-pro/custom-search-pro.php' ) ) { ?>
									<label><input type="checkbox" name="qtsndtps_add_to_search[quote]" value="1" <?php if ( false !== $cstmsrch_options && in_array( 'quote', $cstmsrch_options ) ) echo "checked=\"checked\"";  elseif ( ! $cstmsrch_options ) echo "disabled=\"disabled\""; ?> />Quote</label>
									<span style="color: #888888;font-size: 10px;"> (<?php _e( 'Using', 'quotes_and_tips' ); ?> <a href="admin.php?page=custom_search.php">Custom Search</a> <?php _e( 'powered by', 'quotes_and_tips' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>)</span><br />
									<label><input type="checkbox" name="qtsndtps_add_to_search[tips]" value="1" <?php if ( false !== $cstmsrch_options && in_array( 'tips', $cstmsrch_options ) ) echo "checked=\"checked\""; elseif ( ! $cstmsrch_options ) echo "disabled=\"disabled\"";  ?> /> Tips</label>
								<?php } else { ?>
									<label><input disabled="disabled" type="checkbox" name="qtsndtps_add_to_search[quote]" value="1" <?php if ( false !== $cstmsrch_options && in_array( 'quote', $cstmsrch_options ) ) echo "checked=\"checked\""; ?> />Quote</label>
									<span style="color: #888888;font-size: 10px;">(<?php _e( 'Using Custom Search powered by', 'quotes_and_tips' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>) <a href="<?php echo bloginfo("url"); ?>/wp-admin/plugins.php"><?php _e( 'Activate Custom Search', 'quotes_and_tips' ); ?></a></span><br />
									<label><input disabled="disabled" type="checkbox" name="qtsndtps_add_to_search[tips]" value="1" <?php if ( false !== $cstmsrch_options && in_array( 'tips', $cstmsrch_options ) ) echo "checked=\"checked\""; ?> /> Tips</label>
								<?php }
							} else { ?>
								<input disabled="disabled" type="checkbox" name="qtsndtps_add_to_search[]" value="1" />
								<span style="color: #888888;font-size: 10px;">(<?php _e( 'Using Custom Search powered by', 'quotes_and_tips' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>) <a href="http://bestwebsoft.com/products/custom-search/"><?php _e( 'Download Custom Search', 'quotes_and_tips' ); ?></a></span><br />
							<?php } ?>
						</td>
					</tr>
				</table>
				<input type="hidden" name='qtsndtps_form_submit' value="submit" />
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'quotes_and_tips' ) ?>" />
				</p>
				<?php wp_nonce_field( plugin_basename( __FILE__ ), 'qtsndtps_nonce_name' ); ?>
			</form>
			<div class="bws-plugin-reviews">
				<div class="bws-plugin-reviews-rate">
					<?php _e( 'If you enjoy our plugin, please give it 5 stars on WordPress', 'quotes_and_tips' ); ?>:
					<a href="http://wordpress.org/support/view/plugin-reviews/quotes-and-tips" target="_blank" title="Quotes and Tips reviews"><?php _e( 'Rate the plugin', 'quotes_and_tips' ); ?></a><br/>
				</div>
				<div class="bws-plugin-reviews-support">
					<?php _e( 'If there is something wrong about it, please contact us', 'quotes_and_tips' ); ?>:
					<a href="http://support.bestwebsoft.com">http://support.bestwebsoft.com</a>
				</div>
			</div>
		</div>
	<?php }
}

if ( ! function_exists( 'qtsndtps_register_plugin_links' ) ) {
	function qtsndtps_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[]	=	'<a href="admin.php?page=quotes-and-tips.php">' . __( 'Settings', 'quotes_and_tips' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/quotes-and-tips/faq/" target="_blank">' . __( 'FAQ', 'quotes_and_tips' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'quotes_and_tips' ) . '</a>';
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
				$settings_link = '<a href="admin.php?page=quotes-and-tips.php">' . __( 'Settings', 'quotes_and_tips' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	} /* End function qtsndtps_plugin_action_links */
}

if ( ! function_exists ( 'qtsndtps_print_style_script' ) ) {
	function qtsndtps_print_style_script() {
		global $qtsndtps_options;

		$background_color		=	$qtsndtps_options['qtsndtps_background_color'];
		$text_color				=	$qtsndtps_options['qtsndtps_text_color'];
		$background_image_use	=	$qtsndtps_options['qtsndtps_background_image_use'];
		$background_image		=	$qtsndtps_options['qtsndtps_background_image'];
		$background_gposition	=	$qtsndtps_options['qtsndtps_background_image_gposition'];
		$background_vposition	=	$qtsndtps_options['qtsndtps_background_image_vposition'];
		$background_repeat_x	=	$qtsndtps_options['qtsndtps_background_image_repeat_x'];
		$background_repeat_y	=	$qtsndtps_options['qtsndtps_background_image_repeat_y'];
		$interval_load			=	( $qtsndtps_options['qtsndtps_interval_load'] == '0' ) ? '10' : $qtsndtps_options['qtsndtps_interval_load'];
		$page_load				=	$qtsndtps_options['qtsndtps_page_load'];
		$additional_options		=	$qtsndtps_options['qtsndtps_additional_options'];

		if ( '0' == $additional_options ) {
			/* If additional settings is turned off */
			$background_color = 'inherit';
			$text_color = 'inherit';
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
								if( $(this).hasClass("visible") === true && !flag ) {
									if( $(this).next().hasClass("tips_box") ){
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
								if( $(this).hasClass("visible") === true && !flag ) {
									if( $(this).next().hasClass("quotes_box") ){
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
		global $wp_version;
		if ( $wp_version < 3.8 )
			wp_enqueue_style( 'qtsndtps_stylesheet', plugins_url( 'css/style_wp_before_3.8.css', __FILE__ ) );
		else
			wp_enqueue_style( 'qtsndtps_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );

		if (  is_admin() && isset( $_GET['page'] ) && "quotes-and-tips.php" == $_GET['page'] ) {
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_script( 'qtsndtps_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
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

/* Function for delete options */
if ( ! function_exists ( 'qtsndtps_delete_options' ) ) {
	function qtsndtps_delete_options() {
		delete_option( 'qtsndtps_options' );
	}
}

add_action( 'admin_menu', 'add_qtsndtps_admin_menu' );
add_action( 'init', 'qtsndtps_plugin_init' );
add_action( 'admin_init', 'qtsndtps_plugin_admin_init' );

add_action( 'wp_head', 'qtsndtps_print_style_script' );
add_action( 'admin_enqueue_scripts', 'qtsndtps_wp_head' );
add_action( 'wp_enqueue_scripts', 'qtsndtps_wp_head' );
add_action( 'save_post', 'qtsndtps_save_custom_quote' );

add_shortcode( 'quotes_and_tips', 'qtsndtps_create_tip_quote_block' );

/* Additional links on the plugin page */
add_filter( 'plugin_row_meta', 'qtsndtps_register_plugin_links', 10, 2 );
/* Adds "Settings" link to the plugin action page */
add_filter( 'plugin_action_links', 'qtsndtps_plugin_action_links', 10, 2 );

register_uninstall_hook( __FILE__, 'qtsndtps_delete_options' );
?>