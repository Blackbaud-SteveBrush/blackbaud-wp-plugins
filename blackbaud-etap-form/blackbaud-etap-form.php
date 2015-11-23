<?php
/*
Plugin Name: Blackbaud: eTapestry DIY Form
Description: Allows you to easily add eTapestry DIY iFrame code to your posts and pages. Also includes the iFrame resizer script. <em>(Requires&nbsp;Blackbaud:&nbsp;Assistant&nbsp;&amp;&nbsp;Libraries)</em>
Author: Blackbaud, Inc.
Version: 0.0.1
Text Domain: blackbaud-etap-form
*/

# Exit if accessed directly.
if (!defined('ABSPATH')) exit;

function blackbaud_etap_init($blackbaud) {

    $plugin_options = array(
        'alias'               => 'blackbaud_etap_form',
        'shortcode'           => 'etap_iframe',
        'post_type'           => 'etap_iframe',
        'plugin_file'         => __FILE__,
        'plugin_basename'     => plugin_basename(__FILE__),
        'url_root'            => plugins_url('assets/', __FILE__),
        'templates_directory' => plugin_dir_path(__FILE__) . 'templates/',
    );

    function etap_shortcode($plugin, $blackbaud) {
        /**
         * Usage: [etap_iframe src="//app.etapestry.com/onlineforms/Vinfen/Donate.html"]
         */
        return array(
            'slug' => $plugin->get('shortcode'),
            'output' => function ($data) use ($plugin, $blackbaud) {
                return $plugin->get_template("iframe.blackbaud-etap-form.php", $data);
            }
        );
    }

    function etap_asset($plugin, $blackbaud) {
        return array(
            'access' => 'frontend',
            'handle' => 'etap_responsive_iframe',
            'for_shortcode' => $plugin->get('shortcode'),
            'source' => $plugin->get('url_root') . 'js/responsive-iframe.jquery.js'
        );
    }

    function etap_cpt($plugin, $blackbaud) {
        return array(
            'slug'                   => $plugin->get('post_type'),
            'supports'               => array('title'),
            'menu_icon'              => 'dashicons-media-code',
            'labels'                 => array(
                'name'               => __('eTap Forms', 'blackbaud-etap-form'),
                'singular_name'      => __('eTap Form', 'blackbaud-etap-form'),
                'menu_name'          => _x('eTap Forms', 'admin menu', 'blackbaud-etap-form'),
                'name_admin_bar'     => _x('eTap Form', 'add new on admin bar', 'blackbaud-etap-form'),
                'add_new'            => _x('Add New', 'etap', 'blackbaud-etap-form'),
                'add_new_item'       => __('Add New eTap Form', 'blackbaud-etap-form'),
                'new_item'           => __('New eTap Form', 'blackbaud-etap-form'),
                'edit_item'          => __('Edit eTap Form', 'blackbaud-etap-form'),
                'view_item'          => __('View eTap Form', 'blackbaud-etap-form'),
                'all_items'          => __('All eTap Forms', 'blackbaud-etap-form'),
                'search_items'       => __('Search eTap Forms', 'blackbaud-etap-form'),
                'parent_item_colon'  => __('Parent eTap Forms:', 'blackbaud-etap-form'),
                'not_found'          => __('No eTap Forms found.', 'blackbaud-etap-form'),
                'not_found_in_trash' => __('No eTap Forms found in Trash.', 'blackbaud-etap-form')
            )
        );
    }

    function etap_metabox($plugin, $blackbaud) {
        return array(
            'label'     => 'Embed Code Settings',
            'post_type' => $plugin->get('post_type'),
            'fields'    => array(
                array(
                    'slug'       => 'iframe_src',
                    'label'      => __('iFrame source:', 'blackbaud-etap-form'),
                    'helplet'    => __("Enter the SRC attribute from your DIY iframe snippet (e.g., &lt;iframe src=\"...\">&lt;/iframe>)", 'blackbaud-etap-form'),
                    'type'       => 'text',
                    'attributes' => array(
                        'class'       => 'form-control',
                        'maxlength'   => '500',
                        'spellcheck'  => 'false',
                        'placeholder' => 'e.g., https://app.etapestry.com/onlineforms/MyOrg/Donate.html'
                    )
                )
            )
        );
    }

    function etap_columns($plugin, $blackbaud) {
        return array(
            'post_type' => $plugin->get('post_type'),
            'columns' => array(
                'etap_iframe_shortcode' => array(
                    'label' => __('Shortcode', 'blackbaud-etap-form'),
                    'value' => function ($data) use ($plugin, $blackbaud) {
                        $src = get_post_meta($data['post_id'], 'iframe_src', true);
                        return '<code class="blackbaud-selectable blackbaud-shortcode-snippet" title="Click to select the shortcode. Ctrl+C(or Cmd+C) to copy to your clipboard.">[' . $plugin->get("shortcode") . ' src="' . $src . '"]</code>';
                    }
                )
            )
        );
    }

    $plugin = $blackbaud->register($plugin_options);
    $plugin->forge('shortcode', 'etap_shortcode');
    $plugin->forge('custom_post_type', 'etap_cpt');
    $plugin->forge('meta_box', 'etap_metabox');
    $plugin->forge('post_sortable_columns', 'etap_columns');
    $plugin->forge('asset', 'etap_asset');
    $plugin->forge('updater');
}
add_action('blackbaud_ready', 'blackbaud_etap_init');
