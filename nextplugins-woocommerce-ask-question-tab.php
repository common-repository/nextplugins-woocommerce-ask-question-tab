<?php
/**
 * Plugin Name: NextPlugins WooCommerce Ask Question Tab
 * Plugin URI: https://www.nextplugins.com/woocommerce-ask-question-tab
 * Description: Simple form to send question about product.
 * Version: 1.0.4
 * Author: NextPlugins
 * Requires at least: 4.4
 * Author URI: https://www.nextplugins.com
 * Text Domain: nextplugins-woocommerce-ask-question-tab
 * Domain Path: /languages/
 * License: GPLv2 or later
 *
 * WC tested up to: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NextPlugins_WC_Ask_Question_Tab {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.0.4';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	private $options = array(
		'next_plugins_wc_ask_question_cleanup_options',
		'next_plugins_wc_ask_question_enable',
		'next_plugins_wc_ask_question_email',
	);

	/**
	 * Initialize the plugin.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '2.6', '>=' ) ) {

			if(is_admin()){
				require_once 'inc/admin.php';
			}

			$enabled = get_option( 'next_plugins_wc_ask_question_enable', 'no' );

			if($enabled == 'yes') {
				add_filter( 'woocommerce_product_tabs', array( $this, 'question_tab' ) );

				add_action( 'wp_ajax_nopriv_send_product_question', array($this, 'send_product_question') );
				add_action( 'wp_ajax_send_product_question', array($this, 'send_product_question') );
			}

			add_action('updated_option', array( $this, 'update_option' ), 10, 3);
		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'nextplugins-woocommerce-ask-question-tab' );

		load_textdomain( 'nextplugins-woocommerce-ask-question-tab', trailingslashit( WP_LANG_DIR ) . 'plugins/nextplugins-woocommerce-ask-question-tab-' . $locale . '.mo' );
		load_plugin_textdomain( 'nextplugins-woocommerce-ask-question-tab', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'NextPlugins WooCommerce Ask Question Tab plugin depends on the last version of %s to work!', 'nextplugins-woocommerce-ask-question-tab' ), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">' . __( 'WooCommerce', 'nextplugins-woocommerce-ask-question-tab' ) . '</a>' ) . '</p></div>';
	}

	/**
	 * Autoload options
	 *
	 * @param $option
	 * @param $old_value
	 * @param $value
	 */
	public function update_option($option, $old_value, $value)
	{
		if(in_array($option, $this->options))
		{
			update_option($option, $value, true);
		}
	}

	public function question_tab( $tabs ) {
		$tabs['ask_question_tab'] = array(
			'title'       => __( 'Ask question', 'nextplugins-woocommerce-ask-question-tab' ),
			'priority'    => 50,
			'callback'    => array($this, 'get_question_tab')
		);
		return $tabs;
	}

	public function get_question_tab() {
		$template = get_option( 'next_plugins_wc_ask_question_template', 'simple' );

		$template = apply_filters( 'next_plugins_wc_ask_question_template', $template );

		$_template = locate_template('ask-question-tab/'.$template.'.php', true);

		if(!$_template) {
			require_once 'templates/'.$template.'.php';
		}
	}

	public function send_product_question() {

		$messages = array();
		$has_error = false;

		if ( ! wp_verify_nonce( $_POST['check'], 'nextplugins_check' ) ) {
			$data = array('has_error' => true, 'messages' => 'This is nonsense!');
			echo wp_json_encode($data);
			exit;
		}

		if(isset($_POST['name'])) $name = esc_html(trim($_POST['name']));
		if(isset($_POST['phone'])) $phone = esc_html(trim($_POST['phone']));
		if(isset($_POST['email'])) $email = trim($_POST['email']);
		if(isset($_POST['message'])) $message = esc_html(trim($_POST['message']));
		if(isset($_POST['human'])) $human = (int)$_POST['human'];
		if(isset($_POST['product'])) $product = trim($_POST['product']);

		if(empty($name))
		{
			$messages[] = __("Please fill name field.", 'nextplugins-woocommerce-ask-question-tab');
			$has_error = true;
		}

		if(empty($phone) && empty($email))
		{
			$messages[] = __("Please fill phone or email field.", 'nextplugins-woocommerce-ask-question-tab');
			$has_error = true;
		}

		if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$messages[] = __("Please enter valid email.", 'nextplugins-woocommerce-ask-question-tab');
			$has_error = true;
		}

		if(empty($message))
		{
			$messages[] = __("Please fill message field.", 'nextplugins-woocommerce-ask-question-tab');
			$has_error = true;
		}

		if(empty($product))
		{
			$messages[] = __("Missing product.", 'nextplugins-woocommerce-ask-question-tab');
			$has_error = true;
		}

		if(empty($human) || $human != 5 || !empty($_POST['surname']))
		{
			$messages[] = __("Human verification incorrect.", 'nextplugins-woocommerce-ask-question-tab');
			$has_error = true;
		}

		if($has_error === false)
		{
			$to = get_option('next_plugins_wc_ask_question_email');
			$to = trim($to);
			if(empty($to)) $to = get_bloginfo('admin_email');

			$subject = __("Question about product", 'nextplugins-woocommerce-ask-question-tab')." - ".get_bloginfo('name');

			$headers = 'From: '. $to . "\r\n";

			$email_message = __("Name:", 'nextplugins-woocommerce-ask-question-tab')." $name \r\n";
			if(!empty($phone)) $email_message .= __("Phone:", 'nextplugins-woocommerce-ask-question-tab')." $phone \r\n";
			if(!empty($email)) $email_message .= __("Email:", 'nextplugins-woocommerce-ask-question-tab')." $email \r\n";
			$email_message .= __("Message:", 'nextplugins-woocommerce-ask-question-tab')." $message \r\n";
			$email_message .= __("Product:", 'nextplugins-woocommerce-ask-question-tab')." $product \r\n";

			$sent = wp_mail($to, $subject, strip_tags($email_message), $headers);
			if($sent)
			{
				$messages[] = __("Thanks! Your message has been sent.", 'nextplugins-woocommerce-ask-question-tab');
			}
			else
			{
				$messages[] = __("Message was not sent. Try Again.", 'nextplugins-woocommerce-ask-question-tab');
				$has_error = true;
			}
		}

		$data = array('has_error' => $has_error, 'messages' => $messages);

		echo wp_json_encode($data);
		exit;
	}
}

add_action( 'plugins_loaded', array( 'NextPlugins_WC_Ask_Question_Tab', 'get_instance' ), 10 );