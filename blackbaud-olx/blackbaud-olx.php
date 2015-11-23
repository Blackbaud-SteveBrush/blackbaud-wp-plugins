<?php
/*
Plugin Name: Blackbaud: Online Express
Description: Easily embed Online Express forms on your WordPress site. <em>(Requires&nbsp;Blackbaud:&nbsp;Assistant&nbsp;&amp;&nbsp;Libraries)</em>
Author: Blackbaud Interactive Services
Version: 0.0.1
Text Domain: olx-forms
*/


# Exit if accessed directly.
if (!defined('ABSPATH')) exit;


include 'functions.php';


# EXECUTE WHEN BLACKBAUD IS READY.
function blackbaud_olx_init($blackbaud) {



    # APPLICATION.
    $plugin = $blackbaud->register(function($blackbaud) {
        return array(
            'alias' => 'olx_forms',
            'post_type' => 'olx_forms',
            'shortcode' => 'olx_form',
            'plugin_file' => __FILE__,
            'plugin_basename' => plugin_basename(__FILE__),
            'url_root' => plugins_url('assets/', __FILE__),
            'templates_directory' => plugin_dir_path(__FILE__) . 'templates/',
            'settings_slug' => 'olx_forms_settings',
            'social_sharing_defaults' => array(
                'active' => 'false',
                'activateOnLoad' => 'true',
                'buttonLabel' => 'Share',
                'buttonIcon' => 'share-alt',
                'includeDefaultStyles' => '1',
                'includeBootstrap' => '1',
                'includeFontAwesome' => '1',
                'introductionTitle' => 'Share Your Contribution',
                'introductionBody' => 'Please take some time to share with your friends and family how you supported this organization.',
                'shareTitle' => '',
                'shareSummary' => '',
                'shareUrl' => '',
                'shareImage' => '',
                'shareThisPublisherId' => ''
            )
        );
    });



    # ASSETS.
    # BBI APPLICATION.
    $plugin->forge('asset', function ($plugin, $blackbaud) {

        $plugin->expose_setting('socialSharing', array(
            'handlebarsTemplate' => json_encode($plugin->get_template('sharing-modal.blackbaud-olx.hbs'))
        ));

        return array(
            'type' => 'html',
            'access' => 'frontend',
            'for_shortcode' => $plugin->get('shortcode'),
            'output' => function ($plugin, $blackbaud) {
                return '<div data-bbi-src="' . $plugin->get('url_root') . 'js/bbi-blackbaud-olx.js"></div>';
            }
        );
    });

    # Bootstrap JS.
    $plugin->forge('asset', function ($plugin, $blackbaud) {
        if ($blackbaud->get_settings_field($plugin->get('settings_slug'), 'bootstrap_styles')) {
            return array(
                'access' => 'frontend',
                'for_shortcode' => $plugin->get('shortcode'),
                'handle' => 'blackbaud_olx_bootstrap_js',
                'source' => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'
            );
        }
    });

    # Bootstrap CSS.
    $plugin->forge('asset',
        function($plugin, $blackbaud) {
            if ($blackbaud->get_settings_field($plugin->get('settings_slug'), 'bootstrap_styles')) {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $plugin->get('shortcode'),
                    'handle' => 'blackbaud_olx_bootstrap_css',
                    'source' => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'
                );
            }
        });

    # Font-awesome CSS.
    $plugin->forge('asset',
        function($plugin, $blackbaud) {
            if ($blackbaud->get_settings_field($plugin->get('settings_slug'), 'font_awesome_styles')) {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $plugin->get('shortcode'),
                    'handle' => 'blackbaud_olx_font_awesome_css',
                    'source' => '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'
                );
            }
        });

    # Dashboard CSS.
    $plugin->forge('asset',
        function($plugin, $blackbaud) {
            return array(
                'access' => 'dashboard',
                'handle' => 'olx_forms_dashboard_styles',
                'source' => $plugin->get('url_root') . 'css/dashboard.blackbaud-olx.css'
            );
        });

    # Front-end CSS.
    $plugin->forge('asset',
        function($plugin, $blackbaud) {
            if ($blackbaud->get_settings_field($plugin->get('settings_slug'), 'default_styles')) {
                return array(
                    'access' => 'frontend',
                    'for_shortcode' => $plugin->get('shortcode'),
                    'handle' => 'olx_front_end_styles',
                    'source' => $plugin->get('url_root') . 'css/blackbaud-olx.css',
                    'type' => 'css'
                );
            }
        });



    # CUSTOM POST TYPE.
    $plugin->forge('custom_post_type',
        function($plugin, $blackbaud) {
            return array(
                'slug' => $plugin->get('post_type'),
                'description' => __('Stores the embed code for your various Online Express forms.', 'olx-forms'),
                'supports' => array('title'),
                'labels' => array(
                    'name' => __('Online Express Forms', 'olx-forms'),
                    'singular_name' => __('Form', 'olx-forms'),
                    'menu_name' => _x('Online Express Forms', 'admin menu', 'olx-forms'),
                    'menu_icon' => 'dashicons-slides',
                    'name_admin_bar' => _x('Online Express Form', 'add new on admin bar', 'olx-forms'),
                    'add_new' => _x('Add New', 'form', 'olx-forms'),
                    'add_new_item' => __('Add New Form', 'olx-forms'),
                    'new_item' => __('New Form', 'olx-forms'),
                    'edit_item' => __('Edit Form', 'olx-forms'),
                    'view_item' => __('View Form', 'olx-forms'),
                    'all_items' => __('All Forms', 'olx-forms'),
                    'search_items' => __('Search Online Express Forms', 'olx-forms'),
                    'parent_item_colon' => __('Parent Forms:', 'olx-forms'),
                    'not_found' => __('No forms found.', 'olx-forms'),
                    'not_found_in_trash' => __('No Online Express Forms found in Trash.', 'olx-forms')
                )
            );
        });



    # SORTABLE COLUMNS.
    $plugin->forge('post_sortable_columns',
        function($plugin, $blackbaud) {
            return array(
                'post_type' => $plugin->get('post_type'),
                'columns' => array(
                    'olx_form_id' => array(
                        'label' => __('Form ID', 'olx-forms'),
                        'value' => function ($data) use ($plugin, $blackbaud) {
                            return $data['post_id'];
                        }
                    ),
                    'olx_form_shortcode' => array(
                        'label' => __('Shortcode', 'olx-forms'),
                        'value' => function ($data) use ($plugin, $blackbaud) {
                            return '<code class="blackbaud-selectable" title="Click to select the shortcode. Ctrl+C(or Cmd+C) to copy to your clipboard.">[' . $plugin->get('shortcode') . ' form_id="' . $data['post_id'] . '"]</code>';
                        }
                    )
                )
            );
        });



    # META BOX: EMBED CODE.
    $plugin->forge('meta_box',
        function($plugin, $blackbaud) {
            return array(
                'label' => 'Form Settings',
                'post_type' => $plugin->get('post_type'),
                'fields' => array(
                    array(
                        'slug' => 'embed_code',
                        'label' => __('Embed Code:', 'olx-forms'),
                        'type' => 'textarea',
                        'attributes' => array(
                            'class' => 'form-control accepts-code',
                            'maxlength' => '5000',
                            'spellcheck' => 'false'
                        )
                    ),
                    array(
                        'slug' => 'html_after',
                        'label' => __('HTML After:', 'olx-forms'),
                        'type' => 'textarea',
                        'attributes'  => array(
                            'class' => 'form-control accepts-code',
                            'maxlength' => '5000',
                            'spellcheck' => 'false'
                        )
                    )
                )
            );
        });



    # META BOX: SOCIAL SHARING.
    $plugin->forge('meta_box',
        function($plugin, $blackbaud) {
            return array(
                'slug' => 'social_sharing',
                'label' => 'Social Sharing',
                'post_type' => $plugin->get('post_type'),
                'fields' => array(
                    array(
                        'slug'  => 'social_sharing',
                        'label' => __('Allow social sharing on confirmation screen', 'olx-forms'),
                        'default' => false,
                        'type'  => 'checkbox',
                        'parent_attributes' => array(
                            'data-checkbox-group-selector' => '.blackbaud-confirmation-social-sharing'
                        )
                    ),
                    array(
                        'slug'  => 'social_sharing_activate_on_load',
                        'label' => __('Auto-display lightbox.', 'olx-forms'),
                        'default' => true,
                        'type'  => 'checkbox',
                        'parent_attributes' => array(
                            'class' => 'form-group blackbaud-confirmation-social-sharing'
                        )
                    ),
                    array(
                        'slug'  => 'social_sharing_intro_title',
                        'label' => __('Lightbox: Title', 'olx-forms'),
                        'default' => __('Share Your Contribution', 'olx-forms'),
                        'type'  => 'text',
                        'attributes'  => array(
                            'class' => 'form-control',
                            'maxlength' => '500'
                        ),
                        'parent_attributes' => array(
                            'class' => 'form-group blackbaud-confirmation-social-sharing'
                        )
                    ),
                    array(
                        'slug'  => 'social_sharing_intro_description',
                        'label' => __('Lightbox: Body Text (accepts HTML)', 'olx-forms'),
                        'default' => __('Please take some time to share with your friends and family how you supported this organization.', 'olx-forms'),
                        'type'  => 'textarea',
                        'attributes'  => array(
                            'class' => 'form-control',
                            'maxlength' => '5000'
                        ),
                        'parent_attributes' => array(
                            'class' => 'form-group blackbaud-confirmation-social-sharing'
                        )
                    ),
                    array(
                        'slug'  => 'social_sharing_share_title',
                        'label' => __('Sharing Title (default)', 'olx-forms'),
                        'type'  => 'text',
                        'attributes'  => array(
                            'class' => 'form-control',
                            'maxlength' => '500'
                        ),
                        'parent_attributes' => array(
                            'class' => 'form-group blackbaud-confirmation-social-sharing'
                        )
                    ),
                    array(
                        'slug'  => 'social_sharing_share_summary',
                        'label' => __('Sharing Summary (default)', 'olx-forms'),
                        'type'  => 'textarea',
                        'attributes'  => array(
                            'class' => 'form-control',
                            'maxlength' => '1500'
                        ),
                        'parent_attributes' => array(
                            'class' => 'form-group blackbaud-confirmation-social-sharing'
                        )
                    ),
                    array(
                        'slug'  => 'social_sharing_share_url',
                        'label' => __('Sharing URL', 'olx-forms'),
                        'type'  => 'text',
                        'attributes'  => array(
                            'class' => 'form-control',
                            'maxlength' => '1500',
                            'placeholder' => 'http://'
                        ),
                        'parent_attributes' => array(
                            'class' => 'form-group blackbaud-confirmation-social-sharing'
                        )
                    ),
                    array(
                        'slug'  => 'social_sharing_share_image',
                        'label' => __('Sharing Image', 'olx-forms'),
                        'type'  => 'media-gallery-picker',
                        'attributes'  => array(
                            'class' => 'form-control',
                            'maxlength' => '1500',
                            'placeholder' => 'http://'
                        ),
                        'parent_attributes' => array(
                            'class' => 'form-group blackbaud-confirmation-social-sharing'
                        )
                    )
                )
            );
        });



    # TINYMCE SHORTCODE BUTTON.
    $plugin->forge('tinymce_shortcode_button',
        function($plugin, $blackbaud) {
            return array(
                'slug'             => 'OLXFormsMCEButton',
                'post_type'        => $plugin->get('post_type'),
                'javascript_file'  => $plugin->get('url_root') . 'js/tinymce.blackbaud-olx.js',
                'shortcode_slug'   => $plugin->get('shortcode'),
                'shortcode_id_key' => 'form_id'
            );
        });



    # SHORTCODE.
    $plugin->forge('shortcode',
        function($plugin, $blackbaud) {
            return array(
                'slug' => $plugin->get('shortcode'),
                'output' => function($data) use ($plugin, $blackbaud) {
                    extract(shortcode_atts(array('form_id' => '0'), $data));
                    ob_start();
                    # Use the template tag.
                    the_olx_form($form_id);
                    return ob_get_clean();
                }
            );
        });



    # HELP & ABOUT PAGE.
    $plugin->forge('settings_page',
        function($plugin, $blackbaud) {
            return array(
                'slug' => $plugin->get('post_type') . '_help',
                'parent_slug' => 'edit.php?post_type=' . $plugin->get('post_type'),
                'menu_title' => 'Help',
                'page_title' => 'Online Express Forms: About & Help',
                'callbacks' => array(
                    'display' => function($plugin, $blackbaud) {
                        echo $plugin->get_template("help.blackbaud-olx.php");
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
                    'social_sharing' => array(
                        'title' => 'Social Sharing',
                        'fields' => array(
                            array(
                                'slug' => 'publisher_id',
                                'type' => 'text',
                                'label' => 'ShareThis Publisher ID:'
                            ),
                            array(
                                'slug' => 'share_button_label',
                                'type' => 'text',
                                'label' => 'Share Button Label:',
                                'default' => 'Share'
                            ),
                            array(
                                'slug' => 'share_button_icon',
                                'type' => 'text',
                                'label' => 'Share Button Icon Suffix (Font Awesome):',
                                'default' => 'share-alt'
                            )
                        )
                    ),
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
                            ),
                            array(
                                'slug' => 'font_awesome_styles',
                                'type' => 'checkbox',
                                'label' => 'Include Font Awesome icons',
                                'default' => '1'
                            )
                        )
                    )
                )
            );
        });



    # OLX SOCIAL SHARING.
    $plugin->add_module('SocialSharing',
        function($plugin, $blackbaud) {
            return array(
                'get_data' => function($post_id, $module) use ($plugin, $blackbaud) {
                    $defaults = $plugin->get('social_sharing_defaults');
                    $settings = get_option($plugin->get('settings_slug'));
                    $cpt = $plugin->get_forged('custom_post_type', 'olx_forms');
                    $data = array(
                        'active'               => $cpt->meta($post_id, 'social_sharing'),
                        'activateOnLoad'       => $cpt->meta($post_id, 'social_sharing_activate_on_load'),
                        'buttonLabel'          => (isset ($settings['buttonLabel'])) ? $settings['buttonLabel'] : $defaults['buttonLabel'],
                        'buttonIcon'           => (isset ($settings['buttonIcon'])) ? $settings['buttonIcon'] : $defaults['buttonIcon'],
                        'shareTitle'           => htmlentities ($cpt->meta($post_id, 'social_sharing_share_title')),
                        'shareSummary'         => htmlentities ($cpt->meta($post_id, 'social_sharing_share_summary')),
                        'shareUrl'             => $cpt->meta($post_id, 'social_sharing_share_url'),
                        'shareImage'           => $cpt->meta($post_id, 'social_sharing_share_image'),
                        'shareThisPublisherId' => (isset ($settings['shareThisPublisherId'])) ? $settings['shareThisPublisherId'] : $defaults['shareThisPublisherId'],
                        'introductionTitle'    => htmlentities ($cpt->meta($post_id, 'social_sharing_intro_title')),
                        'introductionBody'     => htmlentities ($blackbaud->do_shortcode($cpt->meta($post_id, 'social_sharing_intro_description')))
                    );
                    foreach ($data as $k => $v) {
                        if (!empty($v)) {
                            $data[$k] = $v;
                        } else {
                            $data[$k] = $defaults[$k];
                        }
                    }
                    return $data;
                },
                'meta_tags' => function($module) use ($plugin, $blackbaud) {
                    global $wp_query;
                    if (!isset($wp_query->post) || !is_object($wp_query->post)) {
                        return false;
                    }
                    $postType = $wp_query->post->post_type;
                    if ($postType == 'page') {
                        $content = $wp_query->post->post_content;
                        $content = explode ('[olx_form ', $content);
                        if (count ($content) > 1) {
                            $content = explode ('form_id=', $content [1]);
                            $content = explode (']', $content [1]);
                            $postId = str_replace ('"', '', str_replace ("'", '', $content [0]));
                        } else {
                            return false;
                        }
                    } else if ($postType == $plugin->get("post_type")) {
                        $postId = $wp_query->post->ID;
                    } else {
                        return false;
                    }
                    $data = $module->get_data($postId);
                    if ($data['active'] == 'true') {
                        $plugin->get_template("meta-tags.php", $data);
                    }
                }
            );
        });



    # UPDATER.
    $plugin->forge('updater');



    # ADD META TAGS ON THE FRONT-END.
    if (!is_admin()) {
        add_action('wp_head', array($plugin->module('SocialSharing'), 'meta_tags'));
    }



}


# SUBSCRIBE TO BLACKBAUD'S READY EVENT.
add_action('blackbaud_ready', 'blackbaud_olx_init');
