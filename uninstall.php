<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

$remove_options = get_option('next_plugins_wc_ask_question_cleanup_options', 'no');

if($remove_options == 'yes')
{
	$plugin_defined_options = array(
		'next_plugins_wc_ask_question_cleanup_options',
		'next_plugins_wc_ask_question_enable',
		'next_plugins_wc_ask_question_email',
	);

	foreach($plugin_defined_options as $option_name) {
		delete_option($option_name);
	}
}