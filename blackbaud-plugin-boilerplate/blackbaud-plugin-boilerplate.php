<?php
/*
Plugin name: Blackbaud: Plugin Boilerplate
Description: This plugin contains the bare minimum to create a Blackbaud-supported plugin.
Author: Blackbaud, Inc.
Version: 1.0.0
Text Domain: bb-plugin-demo
*/


# Exit if accessed directly.
if (!defined('ABSPATH')) exit;


function blackbaud_plugin_boilerplate_init($blackbaud) {
    $plugin = $blackbaud->register(array(
        'alias'               => 'bb_plugin_demo',
        'plugin_file'         => __FILE__,
        'plugin_basename'     => plugin_basename(__FILE__)
    ));
    $plugin->forge('updater');
}
add_action('blackbaud_ready', 'blackbaud_plugin_boilerplate_init');
