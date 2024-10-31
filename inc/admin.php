<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Settings_Tab_Next_Plugins_WC_Ask_question {
	/**
	 * Bootstraps the class and hooks required actions & filters.
	 *
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_next_plugins_wc_ask_question', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_next_plugins_wc_ask_question', __CLASS__ . '::update_settings' );
	}

	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 *
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['next_plugins_wc_ask_question'] = __( 'Ask Question Tab', 'nextplugins-woocommerce-ask-question-tab' );

		return $settings_tabs;
	}

	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	public static function settings_tab() {
		woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	public static function get_settings() {

		$settings = array(
			array(
				'name' => __( 'Ask Question Product Tab', 'nextplugins-woocommerce-ask-question-tab' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'next_plugins_wc_ask_question_section_title'
			),
			array(
				'name'    => __( 'Question Tab', 'nextplugins-woocommerce-ask-question-tab' ),
				'type'    => 'checkbox',
				'id'      => 'next_plugins_wc_ask_question_enable',
				'desc'    => __( 'Enable', 'nextplugins-woocommerce-ask-question-tab' ),
				'default' => 'no',
			),
			array(
				'name'    => __( 'E-mail address', 'nextplugins-woocommerce-ask-question-tab' ),
				'type'    => 'text',
				'id'      => 'next_plugins_wc_ask_question_email',
				'desc'    => __( 'E-mail address where to send question', 'nextplugins-woocommerce-ask-question-tab' ),
				'css'     => 'min-width:300px;',
				'default' => get_option( 'admin_email' ),
			),
			array(
				'title'   => __( 'Template', 'nextplugins-woocommerce-ask-question-tab' ),
				'id'      => 'next_plugins_wc_ask_question_template',
				'default' => 'after_company',
				'type'    => 'select',
				'options' => array(
					'simple'        => __( 'Default', 'nextplugins-woocommerce-ask-question-tab' ),
					'bootstrap'     => __( 'Bootstrap', 'nextplugins-woocommerce-ask-question-tab' ),
					'storefront'    => __( 'Storefront', 'nextplugins-woocommerce-ask-question-tab' ),
				)
			),
			array(
				'title'   => __( 'Cleanup Plugin on Uninstall', 'nextplugins-woocommerce-ask-question-tab' ),
				'desc'    => __( 'Remove all options', 'nextplugins-woocommerce-ask-question-tab' ),
				'id'      => 'next_plugins_wc_ask_question_cleanup_options',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			'section_end' => array(
				'type' => 'sectionend',
				'id'   => 'esce_end'
			)
		);

		return apply_filters( 'wc_settings_tabs_next_plugins_wc_ask_question', $settings );
	}
}

WC_Settings_Tab_Next_Plugins_WC_Ask_question::init();