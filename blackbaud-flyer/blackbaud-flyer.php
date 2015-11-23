<?php
/*
Plugin Name: Blackbaud: Flyer
Description: Create interactive modals and banners for your WordPress site. <em>(Requires&nbsp;Blackbaud:&nbsp;Assistant&nbsp;&amp;&nbsp;Libraries)</em>
Author: Blackbaud Interactive Services
Version: 0.5.0
Text Domain: blackbaud-flyer
*/

# Exit if accessed directly.
if (!defined('ABSPATH')) exit;

# EXECUTE WHEN BLACKBAUD IS READY.
function blackbaud_flyer_init($blackbaud) {



    # REGISTER.
    $plugin = $blackbaud->register(function($blackbaud) {
        return array(
            'alias'               => 'blackbaud_flyer',
            'post_type'           => 'blackbaud_flyer',
            'shortcode'           => 'blackbaud_flyer',
            'settings_slug'       => 'blackbaud_flyer',
            'plugin_file'         => __FILE__,
            'plugin_basename'     => plugin_basename(__FILE__),
            'url_root'            => plugins_url('assets/', __FILE__),
            'templates_directory' => plugin_dir_path(__FILE__) . 'templates/'
        );
    });



    $plugin->forge('asset', function ($plugin, $blackbaud) {
        return array(
            'type' => 'html',
            'access' => 'frontend',
            'for_shortcode' => $plugin->get('shortcode'),
            'output' => function ($plugin, $blackbaud) {
                return '<div data-bbi-src="' . $plugin->get('url_root') . 'js/bbi-blackbaud-flyer.js"></div>';
            }
        );
    });



    # CUSTOM POST TYPE.
    $plugin->forge('custom_post_type',
        function($plugin, $blackbaud) {
            return array(
                'slug'                   => $plugin->get('post_type'),
                'description'            => __('Create any number of lightboxes or alerts.', 'blackbaud-flyer'),
                'supports'               => array('title', 'editor', 'thumbnail', 'excerpt'),
                'labels'                 => array(
                    'name'               => __('Flyers', 'blackbaud-flyer'),
                    'singular_name'      => __('Flyer', 'blackbaud-flyer'),
                    'menu_name'          => _x('Flyers', 'admin menu', 'blackbaud-flyer'),
                    'name_admin_bar'     => _x('Flyer', 'add new on admin bar', 'blackbaud-flyer'),
                    'add_new'            => _x('Add New', 'flyer', 'blackbaud-flyer'),
                    'add_new_item'       => __('Add New Flyer', 'blackbaud-flyer'),
                    'new_item'           => __('New Flyer', 'blackbaud-flyer'),
                    'edit_item'          => __('Edit Flyer', 'blackbaud-flyer'),
                    'view_item'          => __('View Flyer', 'blackbaud-flyer'),
                    'all_items'          => __('All Flyers', 'blackbaud-flyer'),
                    'search_items'       => __('Search Flyers', 'blackbaud-flyer'),
                    'parent_item_colon'  => __('Parent Flyers:', 'blackbaud-flyer'),
                    'not_found'          => __('No flyers found.', 'blackbaud-flyer'),
                    'not_found_in_trash' => __('No flyers found in Trash.', 'blackbaud-flyer')
                )
            );
        });



    # SORTABLE COLUMNS.
    $plugin->forge('post_sortable_columns',
        function($plugin, $blackbaud) {
            return array(
                'post_type' => $plugin->get('post_type'),
                'columns' => array(
                    'blackbaud_flyer_id' => array(
                        'label' => __('Flyer ID', 'blackbaud-flyer'),
                        'value' => function ($data) use ($plugin, $blackbaud) {
                            return $data['post_id'];
                        }
                    ),
                    'blackbaud_flyer_shortcode' => array(
                        'label' => __('Shortcode', 'blackbaud-flyer'),
                        'value' => function ($data) use ($plugin, $blackbaud) {
                            return '<code class="blackbaud-selectable blackbaud-shortcode-snippet" title="Click to select the shortcode. Ctrl+C(or Cmd+C) to copy to your clipboard.">[' . $plugin->get("shortcode") . ' flyer_id="' . $data['post_id'] . '"]</code>';
                        }
                    )
                )
            );
        });



    # META BOXES.
    $plugin->forge('meta_box',
        function($plugin, $blackbaud) {
            return array(
                'slug'      => 'settings',
                'label'     => 'Flyer Settings',
                'post_type' => $plugin->get('post_type'),
                'fields'    => array(
                    array(
                        'slug'    => 'thumbnail_is_background',
                        'label'   => __('Featured Image is flyer background', 'blackbaud-flyer'),
                        'default' => '0',
                        'type'    => 'checkbox'
                    ),
                    array(
                        'slug'    => 'auto_launch',
                        'label'   => __('Launch automatically when page loads.', 'blackbaud-flyer'),
                        'default' => '1',
                        'type'    => 'checkbox',
                        'parent_attributes' => array(
                            'data-checkbox-group-selector' => '.blackbaud-flyer-auto-launch'
                        )
                    ),
                    array(
                        'slug'    => 'show_once',
                        'label'   => __('Launch automatically only once', 'blackbaud-flyer'),
                        'default' => '0',
                        'type'    => 'checkbox',
                        'helplet' => 'Requires browser cookies',
                        'parent_attributes' => array(
                            'class' => 'form-group blackbaud-flyer-auto-launch',
                            'data-checkbox-group-selector' => '.blackbaud-flyer-auto-launch-time'
                        )
                    ),
                    array(
                        'slug'            => 'cookie_expires',
                        'label'           => __('Cookie expires after days:', 'blackbaud-flyer'),
                        'type'            => 'text',
                        'default'         => '30',
                        'attributes'      => array(
                            'class'       => 'form-control',
                            'maxlength'   => '250',
                            'placeholder' => '(optional)'
                        ),
                        'parent_attributes' => array(
                            'class' => 'form-group blackbaud-flyer-auto-launch-time'
                        )
                    ),
                    array(
                        'slug'            => 'launcher_label',
                        'label'           => __('Launcher Button Label:', 'blackbaud-flyer'),
                        'type'            => 'text',
                        'default'         => '',
                        'helplet'         => 'Leave blank to omit.',
                        'attributes'      => array(
                            'class'       => 'form-control',
                            'maxlength'   => '500',
                            'placeholder' => '(optional)'
                        )
                    ),
                    array(
                        'slug'            => 'button_label',
                        'label'           => __('Call-to-action Button Label:', 'blackbaud-flyer'),
                        'type'            => 'text',
                        'default'         => '',
                        'helplet'         => 'Leave blank to omit.',
                        'attributes'      => array(
                            'class'       => 'form-control',
                            'maxlength'   => '500',
                            'placeholder' => '(optional)'
                        )
                    ),
                    array(
                        'slug'            => 'button_url',
                        'label'           => __('Call-to-action Button Link (URL):', 'blackbaud-flyer'),
                        'type'            => 'text',
                        'default'         => '',
                        'attributes'      => array(
                            'class'       => 'form-control',
                            'maxlength'   => '5000',
                            'placeholder' => 'http://'
                        )
                    ),
                    array(
                        'slug'          => 'css_class',
                        'label'         => __('Custom CSS Class (Modal):', 'blackbaud-flyer'),
                        'type'          => 'text',
                        'default'       => '',
                        'attributes'    => array(
                            'class'     => 'form-control',
                            'maxlength' => '500'
                        )
                    ),
                    array(
                        'slug'          => 'html_before',
                        'label'         => __('HTML Before Content:', 'blackbaud-flyer'),
                        'type'          => 'textarea',
                        'default'       => '',
                        'attributes'    => array(
                            'class'     => 'form-control accepts-code',
                            'maxlength' => '5000'
                        )
                    ),
                    array(
                        'slug'          => 'html_after',
                        'label'         => __('HTML After Content:', 'blackbaud-flyer'),
                        'type'          => 'textarea',
                        'default'       => '',
                        'attributes'    => array(
                            'class'     => 'form-control accepts-code',
                            'maxlength' => '5000'
                        )
                    )
                )
            );
        });



    # TINYMCE SHORTCODE BUTTON.
    $plugin->forge('tinymce_shortcode_button',
        function ($plugin, $blackbaud) {
            return array(
                'slug'             => 'BBFlyerMCEButton',
                'post_type'        => $plugin->get('post_type'),
                'javascript_file'  => $plugin->get('url_root') . 'js/tinymce.blackbaud-flyer.js',
                'shortcode_slug'   => $plugin->get('shortcode'),
                'shortcode_id_key' => 'flyer_id'
            );
        });



    # SHORTCODE.
    $plugin->forge('shortcode',
        function ($plugin, $blackbaud) {
            return array(
                'slug' => $plugin->get('shortcode'),
                'output' => function ($data) use ($plugin, $blackbaud) {

                    extract(shortcode_atts(array('flyer_id' => '0'), $data));

                    $post          = get_post($flyer_id);
                    $custom_fields = get_post_custom($flyer_id);
                    $data          = get_object_vars($post);

                    # Push the meta data to the options array.
                    foreach ($custom_fields as $k => $v) {
                        if (isset($v[0])) {
                            $data['meta'][$k] = $v[0];
                        }
                    }

                    $data['post_content'] = $blackbaud->do_shortcode(wpautop($data['post_content']));

                    # Save the thumbnail's src.
                    $data['meta']['thumbnail'] = wp_get_attachment_url(get_post_thumbnail_id($flyer_id));

                    # Output the HTML.
                    return $plugin->get_template('modal.blackbaud-flyer.php', $data);
                }
            );
        });



    # Bootstrap JS.
    $plugin->forge('asset',
        function($plugin, $blackbaud) {
            if ($blackbaud->get_settings_field($plugin->get('settings_slug'), 'bootstrap_styles') === "on") {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $plugin->get('shortcode'),
                    'handle' => 'blackbaud_flyer_bootstrap_js',
                    'source' => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'
                );
            }
        });



    # Bootstrap CSS.
    $plugin->forge('asset',
        function($plugin, $blackbaud) {
            if ($blackbaud->get_settings_field($plugin->get('settings_slug'), 'bootstrap_styles') === "on") {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $plugin->get('shortcode'),
                    'handle' => 'blackbaud_flyer_bootstrap_css',
                    'source' => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'
                );
            }
        });



    # Dashboard CSS.
    $plugin->forge('asset',
        function($plugin, $blackbaud) {
            return array(
                'access' => 'dashboard',
                'handle' => 'blackbaud_flyer_dashboard_styles',
                'source' => $plugin->get('url_root') . 'css/dashboard.blackbaud-flyer.css'
            );
        });



    # Front-end CSS.
    $plugin->forge('asset',
        function($plugin, $blackbaud) {
            if ($blackbaud->get_settings_field($plugin->get('settings_slug'), 'default_styles') === "on") {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $plugin->get('shortcode'),
                    'handle' => 'blackbaud_flyer_frontend_styles',
                    'source' => $plugin->get('url_root') . 'css/blackbaud-flyer.css'
                );
            }
        });



    # HELP & ABOUT PAGE.
    $plugin->forge('settings_page',
        function($plugin, $blackbaud) {
            return array(
                'slug' => $plugin->get('post_type') . '_help',
                'parent_slug' => 'edit.php?post_type=' . $plugin->get('post_type'),
                'menu_title' => 'Help',
                'page_title' => 'Blackbaud Flyers: Help',
                'callbacks' => array(
                    'display' => function($plugin, $blackbaud) {
                        echo $plugin->get_template("help.blackbaud-flyer.php");
                    }
                )
            );
        });



    # SETTINGS PAGE.
    $plugin->forge('settings_page',
        function($plugin, $blackbaud) {
            return array(
                'slug' => $plugin->get('settings_slug'),
                'parent_slug' => 'edit.php?post_type=' . $plugin->get('post_type'),
                'sections' => array(
                    'optimizations' => array(
                        'title' => 'Optimizations',
                        'fields' => array(
                            array(
                                'slug' => 'default_styles',
                                'type' => 'checkbox',
                                'label' => 'Include default style sheet',
                                'default' => '1'
                            ),
                            array(
                                'slug' => 'bootstrap_styles',
                                'type' => 'checkbox',
                                'label' => 'Include Twitter Bootstrap (v.3.3.2) styles and scripts',
                                'default' => '1'
                            )
                        )
                    )
                )
            );
        });



    # UPDATER.
    $plugin->forge('updater');



}



# SUBSCRIBE TO BLACKBAUD'S READY EVENT.
add_action('blackbaud_ready', 'blackbaud_flyer_init');
