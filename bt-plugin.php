<?php
/*
Plugin Name: BT Utilities
Description: Allows adding custom code to the header and footer of your WordPress site.
Version: 1.0
Author: Uttam Purohit
*/

if (!defined('ABSPATH')) {
    exit;
}
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;


// Include required files
require_once plugin_dir_path(__FILE__) . 'site-top-banner.php';
require_once plugin_dir_path(__FILE__) . 'bt-header-footer-code.php';

// Add menu page
function bt_header_footer_menu() {
    // Main menu item
    add_menu_page(
        'BT Utilities',                // Page title
        'BT Utilities',                // Menu title
        'manage_options',              // Capability
        'bt-utilities',                // Menu slug
        '',      // Function to display content
        'dashicons-editor-code',       // Icon
    );

    // Submenu item 1
    add_submenu_page(
        'bt-utilities',                // Parent slug
        'BT Header Footer Code',       // Page title
        'BT Header Footer Code',       // Menu title
        'manage_options',              // Capability
        'bt-header-footer-code',       // Menu slug
        'bt_header_footer_page'   // Function to display content
    );

    // Submenu item 2
    add_submenu_page(
        'bt-utilities',                // Parent slug
        'BT Site Top Bar',             // Page title
        'BT Site Top Bar',             // Menu title
        'manage_options',              // Capability
        'bt-site-top-bar',             // Menu slug
        'bt_site_top_banner_page'         // Function to display content
    );
	
	remove_submenu_page('bt-utilities', 'bt-utilities');

}
add_action('admin_menu', 'bt_header_footer_menu');


$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/Uttam49/bt-utilities/',
	__FILE__,
	'bt-utilities'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('stable');