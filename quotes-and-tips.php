<?php
/*
Plugin Name: Quotes and Tips by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/quotes-and-tips/
Description: Add customizable quotes and tips blocks to WordPress posts, pages and widgets.
Author: BestWebSoft
Text Domain: quotes-and-tips
Domain Path: /languages
Version: 1.36
Author URI: https://bestwebsoft.com/
License: GPLv2 or later
*/

/*  © Copyright 2019  BestWebSoft  ( https://support.bestwebsoft.com )

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

require_once( dirname( __FILE__ ) . '/includes/deprecated.php' );

/* Function are using to add on admin-panel Wordpress page 'bws_panel' and sub-page of this plugin */
if ( ! function_exists( 'add_qtsndtps_admin_menu' ) ) {
	function add_qtsndtps_admin_menu() {
		global $submenu;

		if ( isset( $submenu['edit.php?post_type=quote'] ) ) {
			$settings = add_submenu_page( 'edit.php?post_type=quote', __( 'Quotes and Tips Settings', 'quotes-and-tips' ), __( 'Settings', 'quotes-and-tips' ), 'manage_options', 'quotes-and-tips.php', 'qtsndtps_settings_page' );
			add_submenu_page( 'edit.php?post_type=quote', 'BWS Panel', 'BWS Panel', 'manage_options', 'qtsndtps-bws-panel', 'bws_add_menu_render' );
		}
		if ( isset( $submenu['edit.php?post_type=tips'] ) ) {
			$settings = add_submenu_page( 'edit.php?post_type=tips', __( 'Quotes and Tips Settings', 'quotes-and-tips' ), __( 'Settings', 'quotes-and-tips' ), 'manage_options', 'quotes-and-tips.php', 'qtsndtps_settings_page' );
			add_submenu_page( 'edit.php?post_type=tips', 'BWS Panel', 'BWS Panel', 'manage_options', 'qtsndtps-bws-panel', 'bws_add_menu_render' );
		}

		add_action( 'load-' . $settings, 'qtsndtps_add_tabs' );
		add_action( 'load-post.php', 'qtsndtps_add_tabs' );
		add_action( 'load-edit.php', 'qtsndtps_add_tabs' );
		add_action( 'load-post-new.php', 'qtsndtps_add_tabs' );
	}
}

/* Localization */
if ( ! function_exists( 'qtsndtps_plugins_loaded' ) ) {
	function qtsndtps_plugins_loaded() {
		load_plugin_textdomain( 'quotes-and-tips', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Init plugin */
if ( ! function_exists ( 'qtsndtps_plugin_init' ) ) {
	function qtsndtps_plugin_init() {
		global $qtsndtps_plugin_info;

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( empty( $qtsndtps_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$qtsndtps_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $qtsndtps_plugin_info, '4.0' );

		/* Call register settings function */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && "quotes-and-tips.php" == $_GET['page'] ) ) {
			register_qtsndtps_settings();
		}

		qtsndtps_register_tips_post_type();
		qtsndtps_register_quote_post_type();
	}
}

/* Admin init */
if ( ! function_exists ( 'qtsndtps_plugin_admin_init' ) ) {
	function qtsndtps_plugin_admin_init() {
		global $bws_plugin_info, $qtsndtps_plugin_info, $bws_shortcode_list;

		if ( empty( $bws_plugin_info ) ) {
			$bws_plugin_info = array( 'id' => '82', 'version' => $qtsndtps_plugin_info["Version"] );
		}

		qtsndtps_add_custom_metabox();
		/* add Quotes and Tips to global $bws_shortcode_list */
		$bws_shortcode_list['qtsndtps'] = array( 'name' => 'Quotes and Tips', 'js_function' => 'qtsndtps_shortcode_init' );
	}
}

/* Function for activation */
if ( ! function_exists( 'qtsndtps_plugin_activate' ) ) {
	function qtsndtps_plugin_activate() {
		if ( is_multisite() ) {
			switch_to_blog( 1 );
			register_uninstall_hook( __FILE__, 'qtsndtps_delete_options' );
			restore_current_blog();
		} else {
			register_uninstall_hook( __FILE__, 'qtsndtps_delete_options' );
		}
	}
}

/* Default options */
if ( ! function_exists( 'qtsndtps_get_options_default' ) ) {
	function qtsndtps_get_options_default() {
		global $qtsndtps_plugin_info;

		$qtsndtps_options_defaults = array(
			'plugin_option_version'			=> $qtsndtps_plugin_info["Version"],
			'page_load'						=> '1',
			'interval_load'					=> '10',
			'tip_label'						=> __( 'Tips', 'quotes-and-tips' ),
			'quote_label'					=> __( 'The quotes from our clients', 'quotes-and-tips' ),
			'title_post'					=> '0',
			'additional_options'			=> '1',
			'background_color'				=> '#2484C6',
			'text_color'					=> '#FFFFFF',
			'background_image'				=> 'default', /* none | default | custom */
			'custom_background_image'		=> '',
			'background_image_repeat_x'		=> '0',
			'background_image_repeat_y'		=> '0',
			'background_image_gposition'	=> 'left',
			'background_image_vposition'	=> 'bottom',
			'display_settings_notice'		=> 1,
			'suggest_feature_banner'		=> 1
		);

		return $qtsndtps_options_defaults;
	}
}

/* Register settings function */
if ( ! function_exists( 'register_qtsndtps_settings' ) ) {
	function register_qtsndtps_settings() {
		global $qtsndtps_options, $qtsndtps_plugin_info;
		$qtsndtps_options_defaults = qtsndtps_get_options_default();

		/* Install the option defaults */
		if ( ! get_option( 'qtsndtps_options' ) ) {
			add_option( 'qtsndtps_options', $qtsndtps_options_defaults );
		}
		/* Get options from the database */
		$qtsndtps_options = get_option( 'qtsndtps_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $qtsndtps_options['plugin_option_version'] ) || $qtsndtps_options['plugin_option_version'] != $qtsndtps_plugin_info["Version"] ) {
			/**
			 * @deprecated since 1.35
			 * @todo remove after 20.03.2018
			 */
			if (
				isset( $qtsndtps_options['plugin_option_version'] ) &&
				version_compare( $qtsndtps_options['plugin_option_version'] , '1.34', '<' ) &&
				function_exists( 'qtsndtps_update_old_options' )
			) {
				qtsndtps_update_old_options();
			}
			/* end deprecated */

			$qtsndtps_options = array_merge( $qtsndtps_options_defaults, $qtsndtps_options );
			$qtsndtps_options['plugin_option_version'] = $qtsndtps_plugin_info["Version"];
			update_option( 'qtsndtps_options', $qtsndtps_options );
			qtsndtps_plugin_activate();
		}
	}
}

if ( ! function_exists( 'qtsndtps_register_tips_post_type' ) ) {
	function qtsndtps_register_tips_post_type() {
		$args = array(
			'label'				=> __( 'Tips', 'quotes-and-tips' ),
			'singular_label'	=> __( 'Tips', 'quotes-and-tips' ),
			'public'			=> true,
			'show_ui'			=> true,
			'capability_type'	=> 'post',
			'hierarchical'		=> false,
			'rewrite'			=> true,
			'supports'			=> array( 'title', 'editor' ),
			'menu_icon'			=> 'dashicons-testimonial',
			'labels'			=> array(
				'add_new_item'			=> __( 'Add a New Tip', 'quotes-and-tips' ),
				'edit_item'				=> __( 'Edit Tip', 'quotes-and-tips' ),
				'new_item'				=> __( 'New Tip', 'quotes-and-tips' ),
				'view_item'				=> __( 'View Tip', 'quotes-and-tips' ),
				'search_items'			=> __( 'Search Tip', 'quotes-and-tips' ),
				'not_found'				=> __( 'No tips found', 'quotes-and-tips' ),
				'not_found_in_trash'	=> __( 'No tips found in Trash', 'quotes-and-tips' ),
				'filter_items_list'		=> __( 'Filter tips list', 'quotes-and-tips' ),
				'items_list_navigation'	=> __( 'Tips list navigation', 'quotes-and-tips' ),
				'items_list'			=> __( 'Tips list', 'quotes-and-tips' )
			)
		);
		register_post_type( 'tips' , $args );
	}
}

if ( ! function_exists( 'qtsndtps_register_quote_post_type' ) ) {
	function qtsndtps_register_quote_post_type() {
		$args = array(
			'label'				=> __( 'Quotes', 'quotes-and-tips' ),
			'singular_label'	=> __( 'Quotes', 'quotes-and-tips' ),
			'public'			=> true,
			'show_ui'			=> true,
			'capability_type'	=> 'post',
			'hierarchical'		=> false,
			'rewrite'			=> true,
			'supports'			=> array( 'title', 'editor' ),
			'menu_icon'			=> 'dashicons-format-quote',
			'labels'			=> array(
				'add_new_item'			=> __( 'Add a New Quote', 'quotes-and-tips' ),
				'edit_item'				=> __( 'Edit Quote', 'quotes-and-tips' ),
				'new_item'				=> __( 'New Quote', 'quotes-and-tips' ),
				'view_item'				=> __( 'View Quote', 'quotes-and-tips' ),
				'search_items'			=> __( 'Search Quote', 'quotes-and-tips' ),
				'not_found'				=> __( 'No quote found', 'quotes-and-tips' ),
				'not_found_in_trash'	=> __( 'No quote found in Trash', 'quotes-and-tips' ),
				'filter_items_list'		=> __( 'Filter quotes list', 'quotes-and-tips' ),
				'items_list_navigation'	=> __( 'Quotes list navigation', 'quotes-and-tips' ),
				'items_list'			=> __( 'Quotes list', 'quotes-and-tips' )
			),
			'public'			=> true,
			'supports'			=> array( 'title', 'editor', 'thumbnail', 'comments' ),
			'capability_type'	=> 'post',
			'rewrite'			=> array( "slug" => "quote" )
		);
		register_post_type( 'quote' , $args );
	}
}

if ( ! function_exists( 'qtsndtps_get_random_tip_quote' ) ) {
	function qtsndtps_get_random_tip_quote() {
		echo qtsndtps_create_tip_quote_block();
	}
}

/* Create Quotes and Tips Block */
if ( ! function_exists( 'qtsndtps_create_tip_quote_block' ) ) {
	function qtsndtps_create_tip_quote_block( $atts = '' ) {
		global $post, $qtsndtps_options;
		$atts = shortcode_atts( array(
			'type' => 'quotes_and_tips',
		), $atts );
		$atts['type']   = explode( '_and_', $atts['type'] );
		$display_quotes = in_array( 'quotes', $atts['type'] );
		$display_tips   = in_array( 'tips', $atts['type'] );
		$html           = '<div class="quotes_box_and_tips">';
		if ( $display_quotes ) {
			$quotes_args = array(
				'post_type'			=> 'quote',
				'post_status'		=> 'publish',
				'orderby'			=> 'rand',
				'posts_per_page'	=> '0' == $qtsndtps_options['page_load'] ? -1 : 1
			);
			$quotes = get_posts( $quotes_args );
		}
		if ( $display_tips ) {
			$tips_args = array(
				'post_type'			=> 'tips',
				'post_status'		=> 'publish',
				'orderby'			=> 'rand',
				'posts_per_page'	=> '0' == $qtsndtps_options['page_load'] ? -1 : 1
			);
			$tips = get_posts( $tips_args );
		}

		/* Display Quotes and Tips */
		if ( $display_quotes && $display_tips ) {
			$quotes_class = empty( $tips ) ? 'single_quotes_box' : 'quotes_box';
			$tips_class   = empty( $quotes ) ? 'single_tips_box' : 'tips_box';
			$html_quotes  = qtsndtps_get_quotes_html( $quotes_class, $quotes, $atts );
			$html_tips    = qtsndtps_get_tips_html( $tips_class, $tips, $atts );
			if ( ! empty( $tips ) && ! empty( $quotes ) ) {
				$html .= '<div class="box_delimeter">' . $html_quotes . $html_tips .'<div class="clear"></div></div>';
			} elseif( empty( $tips ) && empty( $quotes ) ) {
				return '';
			} else {
				$html .= $html_quotes . $html_tips . '<div class="clear"></div>';
			}
		} /* Display Quotes only */
		elseif ( $display_quotes ) {
			if ( ! empty( $quotes ) ) {
				$quotes_class = empty( $tips ) ? 'single_quotes_box' : 'quotes_box';
				$html .= qtsndtps_get_quotes_html( $quotes_class, $quotes, $atts ) . '<div class="clear"></div>';
			} else {
				return '';
			}
		} /* Display Tips only */
		elseif ( in_array( array( 'tips' ), $atts ) && ! in_array( array( 'quotes' ), $atts ) ) {
			if ( ! empty( $tips ) ) {
				$tips_class = empty( $quotes ) ? 'single_tips_box' : 'tips_box';
				$html .= qtsndtps_get_tips_html( $tips_class, $tips, $atts ) . '<div class="clear"></div>';
			} else {
				return '';
			}
		} else {
			return '';
		}
		$html .= '</div>';
		return $html;
	}
}

/* Display Quotes Block */
if ( ! function_exists( 'qtsndtps_get_quotes_html' ) ) {
	function qtsndtps_get_quotes_html( $quotes_class, $posts, $atts ) {
		global $qtsndtps_options, $post;
		$buffer = $post;
		if ( empty( $posts ) ) {
			return '';
		}
		$html = '';
		/* The Loop */
		$count = 0;
		foreach ( $posts as $quote ) {
			$post       = $quote;
			$name_field = get_post_meta( $post->ID, 'name_field' );
			$off_cap    = get_post_meta( $post->ID, 'off_cap' );
			setup_postdata( $quote );
			$html .= '<div class="' . esc_attr( $quotes_class ) .
				 ( 0 < $count ? ' hidden ' : ' visible ' ) .
				 '">
				<div class="testemonials_box" id="testemonials_1">
					<h3>' .
						 ( '1' == $qtsndtps_options['title_post'] ? get_the_title() : $qtsndtps_options['quote_label'] ) .
					 '</h3>
					<p>
						<i>"' . strip_tags( get_the_content() ) . '"</i>
					</p>
					<p class="signature">';
						if ( ! empty( $name_field[0] ) )
							$html .= $name_field[0];
						if ( ! empty( $off_cap[0] ) && ! empty( $name_field[0] ) )
							$html .= ' | ';
						if ( ! empty( $off_cap[0] ) )
							$html .= '<span>' . $off_cap[0] . '</span>';
			$html .= '</p>
				</div>
			</div>';
			$count ++;
		}
		$post = $buffer;
		wp_reset_postdata();
		/* Reset Query */
		//return $html;
		$html = apply_filters( 'get_quotes_html', $html );
		return $html;
	}
}

/* Display Tips Block */
if ( ! function_exists( 'qtsndtps_get_tips_html' ) ) {
	function qtsndtps_get_tips_html( $tips_class , $posts, $atts ) {
		global $qtsndtps_options, $post;
		$buffer = $post;
		$html   = '';
		$count  = 0;
			/* The Loop */
			foreach ( $posts as $tip ) {
				$post = $tip;
				setup_postdata( $tip );
				$html .= '<div class="' . esc_attr( $tips_class ) .
					 ( 0 < $count ? ' hidden ' : ' visible ' ) .
					 '">
					<h3>' .
					 ( '1' == $qtsndtps_options['title_post'] ? get_the_title() : $qtsndtps_options['tip_label'] ) .
					 '</h3>
					<p>' . strip_tags( get_the_content() ) . '</p>
					</div>';
				$count ++;
			}
			$post = $buffer;
			wp_reset_postdata();
		/* Reset Query */
		//return $html;
		$html = apply_filters( 'get_tips_html', $html );
		return $html;
	}
}

if ( ! function_exists( 'qtsndtps_quote_custom_metabox' ) ) {
	function qtsndtps_quote_custom_metabox() {
		global $post;
		$name_field = get_post_meta( $post->ID, 'name_field' );
		$off_cap = get_post_meta( $post->ID, 'off_cap' );
		wp_nonce_field( plugin_basename( __FILE__ ), 'qtsndtps_nonce_name' ); ?>
		<p><label for="name_field"><?php _e( 'Name:', 'quotes-and-tips' ); ?><br />
				<input type="text" id="name_field" size="37" name="name_field" value="<?php if ( ! empty( $name_field ) ) echo $name_field[0]; ?>"/>
			</label>
		</p>
		<p><label for="off_cap"><?php _e( 'Official Position:', 'quotes-and-tips' ); ?><br />
				<input type="text" id="off_cap" size="37" name="off_cap" value="<?php if ( ! empty( $off_cap ) ) echo $off_cap[0]; ?>"/>
			</label>
		</p>
	<?php }
}

if ( ! function_exists( 'qtsndtps_add_custom_metabox' ) ) {
	function qtsndtps_add_custom_metabox() {
		add_meta_box( 'custom-metabox', __( 'Name and Official Position', 'quotes-and-tips' ), 'qtsndtps_quote_custom_metabox', 'quote', 'normal', 'high' );
	}
}

/* Settings page */
if ( ! function_exists( 'qtsndtps_settings_page' ) ) {
	function qtsndtps_settings_page() {
		require_once( dirname( __FILE__ ) . '/includes/class-qtsndtps-settings.php' );
		$page = new Qtsndtps_Settings_Tabs( plugin_basename( __FILE__ ) ); ?>
		<div class="wrap">
			<h1 class="qtsndtps-title"><?php _e( 'Quotes and Tips Settings', 'quotes-and-tips' ); ?></h1>
			<?php $page->display_content(); ?>
		</div>
	<?php }
}

if ( ! function_exists( 'qtsndtps_register_plugin_links' ) ) {
	function qtsndtps_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() ) {
				$links[] = '<a href="admin.php?page=quotes-and-tips.php">' . __( 'Settings', 'quotes-and-tips' ) . '</a>';
			}
			$links[] = '<a href="https://support.bestwebsoft.com/hc/en-us/sections/200538959" target="_blank">' . __( 'FAQ', 'quotes-and-tips' ) . '</a>';
			$links[] = '<a href="https://support.bestwebsoft.com">' . __( 'Support', 'quotes-and-tips' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists( 'qtsndtps_plugin_action_links' ) ) {
	function qtsndtps_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin ) {
				$this_plugin = plugin_basename( __FILE__ );
			}
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
		global $qtsndtps_options, $qtsndtps_options_defaults;
		$qtsndtps_options_defaults = qtsndtps_get_options_default();

		$background_image     = $qtsndtps_options['background_image'];
		$custom_bg_image      = $qtsndtps_options['custom_background_image'];
		$background_gposition = $qtsndtps_options['background_image_gposition'];
		$background_vposition = $qtsndtps_options['background_image_vposition'];
		$background_repeat_x  = $qtsndtps_options['background_image_repeat_x'];
		$background_repeat_y  = $qtsndtps_options['background_image_repeat_y'];
		$interval_load        = ( $qtsndtps_options['interval_load'] == '0' ) ? '10' : $qtsndtps_options['interval_load'];
		$page_load            = $qtsndtps_options['page_load'];
		$additional_options   = $qtsndtps_options['additional_options'];

		if ( '1' == $additional_options ) {
			/* If additional settings is turned off */
			$background_color     = $qtsndtps_options_defaults['background_color'];
			$text_color           = $qtsndtps_options_defaults['text_color'];
			$background_image     = $qtsndtps_options['background_image'];
			$background_gposition = $qtsndtps_options_defaults['background_image_gposition'];
			$background_vposition = $qtsndtps_options_defaults['background_image_vposition'];
			$background_repeat_x  = $qtsndtps_options_defaults['background_image_repeat_x'];
			$background_repeat_y  = $qtsndtps_options_defaults['background_image_repeat_y'];
		} else {
			$background_color = $qtsndtps_options['background_color'];
			$text_color       = $qtsndtps_options['text_color'];
		}

		$mask = $background_repeat_x . $background_repeat_y;
		switch( $mask ) {
			case '11':
				$repeat_rule = 'repeat';
				break;
			case '10';
				$repeat_rule = 'repeat-x';
				break;
			case '01':
				$repeat_rule = 'repeat-y';
				break;
			default:
				$repeat_rule = 'no-repeat';
				break;
		} ?>
		<style type="text/css">
			/* Style for tips|quote block */
			.quotes_box_and_tips {
				background-repeat: <?php echo $repeat_rule; ?>;
				background-color: <?php echo $background_color; ?> !important;
				color: <?php echo $text_color; ?> !important;
				<?php if ( '1' != $additional_options && 'custom' == $background_image && ! empty( $custom_bg_image ) ) { ?>
				background-image: url( <?php echo $custom_bg_image; ?> );
				<?php } elseif ( '1' != $additional_options && 'none' == $background_image ) { ?>
					background-image: none;
				<?php } elseif ( '1' == $additional_options ) { ?>
				background-image: <?php echo plugins_url( '/images/quotes_box_and_tips_bg.png', __FILE__ )?>;
				<?php } ?>
				background-position: <?php echo $background_gposition ." ". $background_vposition; ?>;
			}
			.quotes_box_and_tips h3,
			.quotes_box_and_tips .signature,
			.quotes_box_and_tips .signature span {
				color: <?php echo $text_color; ?> !important;
			}
		</style>
		<?php if ( '0' == $page_load ) { ?>
			<script type="text/javascript">
				if ( window.jQuery ) {
					( function($){
						$(document).ready( function() {
							var interval = <?php echo $interval_load; ?>;
							setInterval( change_tip_quote, interval * 1000 );
						});
						function change_tip_quote() {
							var flag = false;
							var quotes = $( '.quotes_box_and_tips' ).find( '.quotes_box, .single_quotes_box' );
							var tips = $( '.quotes_box_and_tips' ).find( '.tips_box, .single_tips_box' );
							tips.each( function() {
								var $this = $( this ),
									next = $this.next( '.tips_box, .single_tips_box' );
									if ( ! next.length ) {
										next = tips.filter( ':first');
									}
								if ( $this.hasClass( "visible" ) === true && !flag ) {
									$this.animate( { opacity:0 }, 500, function() {
										$this.addClass( "hidden" ).removeClass( "visible" );
									});
									next.animate( { opacity:1 }, 500, function() {
										next.removeClass( "hidden" ).addClass( "visible" );
									});
									flag = true;
								}
							});
							flag = false;
							quotes.each( function() {
								var $this = $( this ),
									next = $this.next( '.quotes_box, .single_quotes_box' );
									if ( ! next.length ) {
										next = quotes.filter( ':first');
									}
								if( $this.hasClass( "visible" ) === true && !flag ) {
									$this.animate( { opacity:0 }, 500, function() {
										$this.addClass( "hidden" ).removeClass( "visible" );

									});
									next.animate( { opacity:1 }, 500, function() {
										next.removeClass( "hidden" ).addClass( "visible" );
									});
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
			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_style( 'qtsndtps_admin_stylesheet', plugins_url( 'css/admin_style.css', __FILE__ ) );
			wp_enqueue_script( 'qtsndtps_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
		}
	}
}

/* Add admin notices */
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

if ( ! function_exists( 'get_human_readable_file_size' ) ) {
	function get_human_readable_file_size( $max_size ) {
		if ( 104857 <= $max_size ) {
			/* if file size more then 100KB */
			return round( $max_size / 1048576, 2 ) . "&nbsp;" . __( "MB", 'quotes-and-tips' );
		}
		if ( 1024 <= $bytes && 104857 >= $max_size ) {
			/* if file size more then 1KB but less then 100KB */
			return round( $max_size / 1024, 2 ) . "&nbsp;" . __( "KB", 'quotes-and-tips' );
		}
		/* if file size under 1KB */
		return $max_size . "&nbsp;" . __( "Bytes", 'quotes-and-tips' );
	}
}

/* add shortcode content  */
if ( ! function_exists( 'qtsndtps_shortcode_button_content' ) ) {
	function qtsndtps_shortcode_button_content( $content ) { ?>
		<div id="qtsndtps" style="display:none;">
			<fieldset>
				<?php _e( 'Add Quotes and Tips block to your page or post', 'quotes-and-tips' ); ?>
				<label>
					<input checked="checked" type="radio" name="qtsndtps_select" value='[quotes_and_tips]' />
					<span class="checkbox-title">
						<?php _e( 'Display quotes and tips', 'quotes-and-tips' ); ?>
					</span>
				</label><br/>
				<label>
					<input type="radio" name="qtsndtps_select" value='[quotes_and_tips type="quotes"]' />
					<span class="checkbox-title">
						<?php _e( 'Display quotes', 'quotes-and-tips' ); ?>
					</span>
				</label><br/>
				<label>
					<input type="radio" name="qtsndtps_select" value='[quotes_and_tips type="tips"]' />
					<span class="checkbox-title">
						<?php _e( 'Display tips', 'quotes-and-tips' ); ?>
					</span>
				</label>
				<input class="bws_default_shortcode" type="hidden" name="default" value="[quotes_and_tips]" />
				<div class="clear"></div>
			</fieldset>
		</div>
		<script type="text/javascript">
			function qtsndtps_shortcode_init() {
				(function($) {
					$( '.mce-reset input[name ="qtsndtps_select"]' ).change( function() {
						if ( $( this ).is( ':checked' ) ) {
							$( ' #bws_shortcode_display' ).text( $( this ).val() );
						}
					} );
				})(jQuery);
			}
		</script>
			<div class="clear"></div>

	<?php }
}

/* Add help tab on settings page */
if ( ! function_exists( 'qtsndtps_add_tabs' ) ) {
	function qtsndtps_add_tabs() {
		$screen = get_current_screen();
		if ( ( ! empty( $screen->post_type ) && 'quote' == $screen->post_type ) ||
			( ! empty( $screen->post_type ) && 'tips' == $screen->post_type ) ||
			( isset( $_GET['page'] ) && $_GET['page'] == 'quotes-and-tips.php' ) ) {
			$args = array(
				'id'		=> 'qtsndtps',
				'section'	=> '200538959'
			);
			bws_help_tab( $screen, $args );
		}
	}
}

if ( ! function_exists( 'qtsndtps_get_data' ) ) {
    function qtsndtps_get_data( $qtsndtps_id ) {

      $post_type = array('quote','tips');
      
      $qtsndtps_posts = array();

      if ( 'all' == $qtsndtps_id || is_array( $qtsndtps_id ) ) {

        $qtsndtps_id_list = ( is_array( $qtsndtps_id ) && ! empty( $qtsndtps_id ) ) ? $qtsndtps_id  : array();
        $args = ( is_array( $qtsndtps_id ) ) ? array( 'post_type' => $post_type,
                                                         'include' => $qtsndtps_id_list ) : array( 'post_type' => $post_type);
        $qtsndtps_posts = get_posts( $args );

	  } else if ( is_int( $qtsndtps_id ) || is_string( $qtsndtps_id ) ) {

	      $qtsndtps_int_id = is_int( $qtsndtps_id ) ? $qtsndtps_id : intval( $qtsndtps_id );
	      $qtsndtps_posts = get_post( $qtsndtps_int_id );

	  }
	    
	  $qtsndtps_posts_end = array();
	  foreach ( (array)$qtsndtps_posts as $key => $qtsndtps_post ) {

	    $qtsndtps_meta = get_post_meta( $qtsndtps_post->ID, '' );
	    unset($qtsndtps_meta['_edit_lock']);
	    unset($qtsndtps_meta['_edit_last']);
	  
	    $qtsndtps_posts[$key]->qtsndtps_post_meta = $qtsndtps_meta;
	  }

      return $qtsndtps_posts;
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

register_activation_hook( __FILE__, 'qtsndtps_plugin_activate' );

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
