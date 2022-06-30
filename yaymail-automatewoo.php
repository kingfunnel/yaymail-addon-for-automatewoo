<?php
/**
 * Plugin Name: YayMail Addon for AutomateWoo
 * Plugin URI: https://yaycommerce.com/yaymail-woocommerce-email-customizer/
 * Description: Customize templates for AutomateWoo plugin
 * Version: 1.6
 * Author: YayCommerce
 * Author URI: https://yaycommerce.com
 * Text Domain: yaymail
 * WC requires at least: 3.0.0
 * WC tested up to: 5.8
 * Domain Path: /i18n/languages/
 */

namespace YayMailAutomateWoo;

use YayMail\Ajax;
use YayMail\MailBuilder\Shortcodes;
use YayMail\Page\Source\CustomPostType;

defined( 'ABSPATH' ) || exit;
spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__;
		$base_dir = __DIR__ . '/views';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class_name = substr( $class, $len );

		$file = $base_dir . str_replace( '\\', '/', $relative_class_name ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

// Add action link customize
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'YayMailAutomateWoo\\yaymail_addon_add_action_links' );
function yaymail_addon_add_action_links( $actions ) {

	if ( defined( 'YAYMAIL_PREFIX' ) ) {
		$links   = array(
			'<a href="' . admin_url( 'admin.php?page=yaymail-settings' ) . '" aria-label="' . esc_attr__( 'View WooCommerce Email Builder', 'yaymail' ) . '">' . esc_html__( 'Start Customizing', 'yaymail' ) . '</a>',
		);
		$actions = array_merge( $links, $actions );
	}
	return $actions;
}

// Add action link docs and support
add_filter( 'plugin_row_meta', 'YayMailAutomateWoo\\yaymail_addon_custom_plugin_row_meta', 10, 2 );
function yaymail_addon_custom_plugin_row_meta( $plugin_meta, $plugin_file ) {

	if ( strpos( $plugin_file, plugin_basename( __FILE__ ) ) !== false ) {
		$new_links = array(
			'docs'    => '<a href="https://yaycommerce.gitbook.io/yaymail/" aria-label="' . esc_attr__( 'View YayMail documentation', 'yaymail' ) . '">' . esc_html__( 'Docs', 'yaymail' ) . '</a>',
			'support' => '<a href="https://yaycommerce.com/support/" aria-label="' . esc_attr__( 'Visit community forums', 'yaymail' ) . '">' . esc_html__( 'Support', 'yaymail' ) . '</a>',
		);

		$plugin_meta = array_merge( $plugin_meta, $new_links );
	}

	return $plugin_meta;
}

// Add action notice
add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), 'YayMailAutomateWoo\\yaymail_addon_add_notification_after_plugin_row', 10, 2 );
function yaymail_addon_add_notification_after_plugin_row( $plugin_file, $plugin_data ) {

	if ( ! defined( 'YAYMAIL_PREFIX' ) ) {
		$wp_list_table = _get_list_table( 'WP_MS_Themes_List_Table' );
		?>
		<script>
		var plugin_row_element = document.querySelector('tr[data-plugin="<?php echo esc_js( plugin_basename( __FILE__ ) ); ?>"]');
		plugin_row_element.classList.add('update');
		</script>
		<?php
		echo '<tr class="plugin-update-tr' . ( is_plugin_active( $plugin_file ) ? ' active' : '' ) . '"><td colspan="' . esc_attr( $wp_list_table->get_column_count() ) . '" class="plugin-update colspanchange" >';
		echo '<div class="notice inline notice-warning notice-alt"><p>';
		echo esc_html__( 'To use this addon, you need to install and activate YayMail plugin. Get ', 'yaymail' ) . '<a href="' . esc_url( 'https://wordpress.org/plugins/yaymail/' ) . '">' . esc_html__( 'YayMail Free', 'yaymail' ) . '</a> or <a href="' . esc_url( 'https://yaycommerce.com/yaymail-woocommerce-email-customizer/' ) . '">' . esc_html__( 'YayMail Pro', 'yaymail' ) . '</a>.
					</p>
				</div>
			</td>
			</tr>';
	}

}

// function yaymail_dependence() {
// wp_enqueue_script( 'yaymail-automatewoo', plugin_dir_url( __FILE__ ) . 'assets/dist/js/app.js', array(), '1.0', true );
// wp_enqueue_style( 'yaymail-automatewoo', plugin_dir_url( __FILE__ ) . 'assets/dist/css/app.css', array(), '1.0' );
// }
// add_action( 'yaymail_before_enqueue_dependence', 'YayMailAutomateWoo\\yaymail_dependence' );


add_filter(
	'yaymail_plugins',
	function( $plugins ) {
		global $wpdb;
		$AutomateWoo_templates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts where post_type = 'aw_workflow' " );
		$templates_name        = array();
		if ( count( $AutomateWoo_templates ) ) {
			foreach ( $AutomateWoo_templates as $key => $template ) {
				$templates_name[] = 'AutomateWoo_' . $template->ID;
			};
		}
		$templates_name[] = 'AutomateWoo_Referrals_Email';
		$plugins[]        = array(
			'plugin_name'      => 'AutomateWoo', // --> CHANGE HERE => name of plugin (maybe name of the class)
			'addon_components' => array(), // CHANGE HERE => main-name required
			'template_name'    => $templates_name,
		);
		return $plugins;
	},
	10,
	1
);

// Action create template default
add_filter( 'YaymailNewTempalteDefault', 'YayMailAutomateWoo\\yaymail_new_template_default', 100, 3 );
function yaymail_new_template_default( $array, $key, $value ) {

	if ( false !== strpos( $key, 'AutomateWoo' ) ) {
		$getHeading                                = 'Email heading';
		$defaultAutomateWoo                        = templateDefault\DefaultAutomateWoo::getTemplates( $value->id, $getHeading );
		$defaultAutomateWoo[ $value->id ]['title'] = $value->title;
		return $defaultAutomateWoo;
	}
	return $array;
}

/*
Action to defined shortcode
$arrData[0] : $custom_shortcode
$arrData[1] : $args
$arrData[2] : $templateName
*/

add_action(
	'yaymail_addon_defined_shorcode',
	function( $arrData ) {
		$templateWooFollowUp = apply_filters( 'YaymailCreateListAutomateWooNames', array() );
		if ( in_array( $arrData[2], $templateWooFollowUp ) ) {
			if ( isset( $arrData[1]['order'] ) && '' !== $arrData[1]['order'] ) {
				$order_id = $arrData[1]['order']->get_id();
			} else {
				$order_id = '';
			}
			$arrData[0]->setOrderId( $order_id, isset( $arrData[1]['sent_to_admin'] ) ? $arrData[1]['sent_to_admin'] : false, $arrData[1] );
			$arrData[0]->shortCodesOrderDefined( isset( $arrData[1]['sent_to_admin'] ) ? $arrData[1]['sent_to_admin'] : false, $arrData[1] );
		}
	}
);

// Filter to defined template
add_filter(
	'yaymail_addon_defined_template',
	function( $result, $template ) {
		$templateWooFollowUp = apply_filters( 'YaymailCreateListAutomateWooNames', array() );
		if ( in_array( $template, $templateWooFollowUp ) ) {
			return true;
		}
		return $result;
	},
	10,
	2
);

// CHANGE HERE
// Filter to add template to Vuex
add_filter(
	'yaymail_addon_templates',
	function( $addon_templates, $order, $post_id ) {
		$components = apply_filters( 'yaymail_plugins', array() );
		$position   = '';
		foreach ( $components as $key => $component ) {
			if ( 'AutomateWoo' === $component['plugin_name'] ) {
				$position = $key;
				break;
			}
		}
		foreach ( $components[ $position ]['addon_components'] as $key => $component ) {
			ob_start();
			do_action( 'YaymailAddon' . $component . 'Vue', $order, $post_id );
			$html = ob_get_contents();
			ob_end_clean();
			$addon_templates['automatewoo'] = array_merge( isset( $addon_templates['automatewoo'] ) ? $addon_templates['automatewoo'] : array(), array( $component . 'Vue' => $html ) );
		}
		return $addon_templates;
	},
	10,
	3
);

/** SHORTCODE WILL DO HERE */
// Add new shortcode to shortcodes list
add_filter(
	'yaymail_shortcodes',
	function( $shortcode_list ) {
		return $shortcode_list;
	},
	10,
	1
);

// Create shortcode
add_filter(
	'yaymail_do_shortcode',
	function( $shortcode_list, $yaymail_informations, $args = array() ) {
		return $shortcode_list;
	},
	10,
	3
);

function yaymail_addon_automatewoo_booking( $yaymail_informations, $args = array(), $val, $defaultVal, $parameters = array() ) {
	if ( isset( $args['workflow'] ) ) {
		$value = yaymail_get_variable( $val, $args['workflow'], $parameters );
		return $value;
	}
	return $defaultVal;
}

function yaymail_addon_automatewoo_shop( $yaymail_informations, $args = array(), $val, $defaultVal ) {
	if ( isset( $args['workflow'] ) ) {
		$value = yaymail_get_variable( $val, $args['workflow'] );
		return $value;
	}
	return $defaultVal;
}

function yaymail_addon_automatewoo_customer( $yaymail_informations, $args = array(), $val, $defaultVal ) {
	if ( isset( $args['workflow'] ) ) {
		$value = yaymail_get_variable( $val, $args['workflow'] );
		return $value;
	}
	return $defaultVal;
}

function yaymail_get_variable( $val, $workflow, $parameters = array() ) {
	$variable = \AutomateWoo\Variables::get_variable( $val );
	$value    = '';

	if ( $variable && method_exists( $variable, 'get_value' ) ) {

		if ( \AutomateWoo\DataTypes\DataTypes::is_non_stored_data_type( $variable->get_data_type() ) ) {
			$value = $variable->get_value( array( 'type' => 'featured' ), $workflow );
		} else {
			$data_item = $workflow->get_data_item( $variable->get_data_type() );

			if ( $data_item ) {
				$value = $variable->get_value( $data_item, $parameters, $workflow );
			}
		}
	}
	return $value;
}
/** END SHORTCODE */


// Create HTML with Vue syntax to display in Vue
// CHANGE HERE => Name of action follow : YaymailAddon + main-name + Vue
// CHANGE SOURCE VUE TOO
// add_action( 'YaymailAddonYithVendorInformationVue', 'YayMailAutomateWoo\\yith_vendor_information_vue', 100, 5 );
// function yith_vendor_information_vue( $order, $postID = '' ) {
// if ( '' === $order ) {
// ob_start();
// include plugin_dir_path( __FILE__ ) . '/views/vue-template/YaymailAddonYithVendorInformation.php';
// $html = ob_get_contents();
// ob_end_clean();
// } else {
// ob_start();
// include plugin_dir_path( __FILE__ ) . '/views/vue-template/YaymailAddonYithVendorInformation.php';
// $html = ob_get_contents();
// ob_end_clean();
// if ( '' === $html ) {
// $html = '<div></div>';
// }
// }
// echo $html;
// }

// Create HTML to display when send mail
// CHANGE HERE => Name of action follow: YaymailAddon + main-name
// add_action( 'YaymailAddonYithVendorInformation', 'YayMailAutomateWoo\\yaymail_addon_yith_vendor_information', 100, 5 );
// function yaymail_addon_yith_vendor_information( $args = array(), $attrs = array(), $general_attrs = array(), $id = '', $postID = '' ) {
// $order = $args['order'];
// if ( isset( $order ) ) {
// ob_start();
// include plugin_dir_path( __FILE__ ) . '/views/template/YaymailAddonYithVendorInformation.php';
// $html = ob_get_contents();
// ob_end_clean();
// $html = do_shortcode( $html );
// echo wp_kses_post( $html );
// } else {
// echo wp_kses_post( '' );
// }
// }

add_filter(
	'YaymailCreateAutomateWooTemplates',
	function( $AutomateWoo = array() ) {
		global $wpdb;
		$AutomateWoo_templates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts where post_type = 'aw_workflow' " );
		if ( count( $AutomateWoo_templates ) ) {
			foreach ( $AutomateWoo_templates as $key => $template ) {
				$AutomateWoo[ 'AutomateWoo_' . $template->ID ] = (object) array(
					'id'      => 'AutomateWoo_' . $template->ID,
					'title'   => $template->post_title,
					'enabled' => '',
				);
			};
		}
		return $AutomateWoo;
	},
	100,
	1
);

add_filter(
	'YaymailCreateListAutomateWooNames',
	function( $AutomateWoo = array() ) {
		global $wpdb;
		$AutomateWoo_templates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts where post_type = 'aw_workflow' " );
		if ( count( $AutomateWoo_templates ) ) {
			foreach ( $AutomateWoo_templates as $key => $template ) {
				$AutomateWoo[] = 'AutomateWoo_' . $template->ID;
			};
		}
		$AutomateWoo[] = 'AutomateWoo_Referrals_Email';
		return $AutomateWoo;
	},
	100,
	1
);

add_filter(
	'YaymailCreateSelectAutomateWooTemplates',
	function( $settingEnableDisables = array() ) {
		global $wpdb;
		$AutomateWoo_templates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts where post_type = 'aw_workflow' " );
		if ( count( $AutomateWoo_templates ) ) {
			foreach ( $AutomateWoo_templates as $key => $template ) {
				$settingEnableDisables[ 'AutomateWoo_' . $template->ID ] = '0';
				$postID = CustomPostType::postIDByTemplate( 'AutomateWoo_' . $template->ID );
				if ( $postID ) {
					if ( get_post_meta( $postID, '_yaymail_status', true ) ) {
						$settingEnableDisables[ 'AutomateWoo_' . $template->ID ] = '1';
					}
				}
			};
		}
		return $settingEnableDisables;
	},
	100,
	1
);



