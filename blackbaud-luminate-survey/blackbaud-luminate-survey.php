<?php
/*
Plugin Name: Blackbaud: Luminate Survey
Description: Easily embed Luminate Online survey forms on your WordPress site. <em>(Requires&nbsp;Blackbaud:&nbsp;Assistant&nbsp;&amp;&nbsp;Libraries)</em>
Author: Blackbaud Interactive Services
Version: 0.0.1
Text Domain: luminate-survey
*/


# Exit if accessed directly.
if (!defined('ABSPATH')) exit;



# EXECUTE WHEN BLACKBAUD IS READY.
function blackbaud_luminate_survey_init($blackbaud) {

    $plugin_options = array(
        'alias'               => 'blackbaud_luminate_survey',
        'post_type'           => 'luminate_survey',
        'shortcode'           => 'luminate_survey',
        'plugin_file'         => __FILE__,
        'plugin_basename'     => plugin_basename(__FILE__),
        'url_root'            => plugins_url('assets/', __FILE__),
        'templates_directory' => plugin_dir_path(__FILE__) . 'templates/',
    );



    /**
     * 1) Check if user is logged in to Luminate Online.
     *    a) If so, proceed to step 2
     *    b) If not, inform the user and provide a link to the login form.
     * 2) Create survey from API response
     * 3)
     */



    function survey_cpt($plugin, $blackbaud) {
        return array(
            'slug'        => $plugin->get('post_type'),
            'description' => __('Allows you to easily create and store various Luminate Online Surveys.', 'luminate-survey'),
            'supports'    => array('title'),
            'menu_icon'   => 'dashicons-clipboard',
            'labels'      => array(
                'name'               => __('Surveys', 'luminate-survey'),
                'singular_name'      => __('Survey', 'luminate-survey'),
                'menu_name'          => _x('Surveys', 'admin menu', 'luminate-survey'),
                'name_admin_bar'     => _x('Survey', 'add new on admin bar', 'luminate-survey'),
                'add_new'            => _x('Add New', 'survey', 'luminate-survey'),
                'add_new_item'       => __('Add New Survey', 'luminate-survey'),
                'new_item'           => __('New Survey', 'luminate-survey'),
                'edit_item'          => __('Edit Survey', 'luminate-survey'),
                'view_item'          => __('View Survey', 'luminate-survey'),
                'all_items'          => __('All Surveys', 'luminate-survey'),
                'search_items'       => __('Search Surveys', 'luminate-survey'),
                'parent_item_colon'  => __('Parent Surveys:', 'luminate-survey'),
                'not_found'          => __('No surveys found.', 'luminate-survey'),
                'not_found_in_trash' => __('No surveys found in Trash.', 'luminate-survey')
            )
        );
    }



    function survey_metabox($plugin, $blackbaud) {
        return array(
            'label'     => 'Survey Settings',
            'post_type' => $plugin->get('post_type'),
            'fields'    => array(
                array(
                    'slug'       => 'survey_id',
                    'label'      => __('Luminate Online Survey ID:', 'luminate-survey'),
                    'helplet'    => __("This ID is set by Luminate Online and found beneath the Survey's title after you login.", 'luminate-survey'),
                    'type'       => 'text',
                    'attributes' => array(
                        'class'       => 'form-control',
                        'maxlength'   => '10',
                        'spellcheck'  => 'false',
                        'placeholder' => 'e.g., 1040'
                    )
                )
            )
        );
    }



    function survey_columns($plugin, $blackbaud) {
        return array(
            'post_type' => $plugin->get('post_type'),
            'columns' => array(
                'luminate_survey_id' => array(
                    'label' => __('Survey ID', 'luminate-survey'),
                    'meta_key' => 'survey_id',
                    'value' => function ($data) use ($plugin, $blackbaud) {
                        return get_post_meta($data['post_id'], 'survey_id', true);
                    }
                ),
                'luminate_form_shortcode' => array(
                    'label' => __('Shortcode', 'luminate-survey'),
                    'value' => function ($data) use ($plugin, $blackbaud) {
                        $survey_id = get_post_meta($data['post_id'], 'survey_id', true);
                        return '<code class="blackbaud-selectable blackbaud-shortcode-snippet" title="Click to select the shortcode. Ctrl+C(or Cmd+C) to copy to your clipboard.">[' . $plugin->get('shortcode') . ' survey_id="' . $survey_id . '"]</code>';
                    }
                )
            )
        );
    }



    function survey_shortcode($plugin, $blackbaud) {
        return array(
            'slug' => $plugin->get('shortcode'),
            'output' => function ($data) use ($plugin, $blackbaud) {
                return $plugin->get_template("survey-init.blackbaud-luminate-survey.php", $data);
            }
        );
    }



    function survey_help_page($plugin, $blackbaud) {
        return array(
            'slug'        => $plugin->get('post_type') . '_help',
            'parent_slug' => 'edit.php?post_type=' . $plugin->get('post_type'),
            'menu_title'  => 'Help',
            'page_title'  => 'Luminate Survey: About & Help',
            'callbacks'   => array(
                'display' => function($plugin, $blackbaud) {
                    echo $plugin->get_template("help.blackbaud-luminate-survey.php");
                }
            )
        );
    }



    function survey_settings_page($plugin, $blackbaud) {
        return array(
            'slug'        => $plugin->get('post_type') . '_settings',
            'parent_slug' => 'edit.php?post_type=' . $plugin->get('post_type'),
            'sections'    => array(
                'api_basic'  => array(
                    'title'  => 'API Keys:',
                    'fields' => array(
                        array(
                            'slug'  => 'key',
                            'type'  => 'text',
                            'label' => 'Key:'
                        ),
                        array(
                            'slug'    => 'version',
                            'type'    => 'text',
                            'label'   => 'API Version:',
                            'default' => '1.0'
                        ),
                        array(
                            'slug'  => 'secret',
                            'type'  => 'text',
                            'label' => 'Secret Key:',
                            'helplet' => "Used for signed redirects"
                        )
                    )
                ),
                'api_user'   => array(
                    'title'  => 'API User (Server API):',
                    'fields' => array(
                        array(
                            'slug'  => 'login_name',
                            'type'  => 'text',
                            'label' => 'Login Username:'
                        ),
                        array(
                            'slug'  => 'login_password',
                            'type'  => 'text',
                            'label' => 'Login Password:'
                        )
                    )
                ),
                'api_endpoints' => array(
                    'title'  => 'Resources:',
                    'fields' => array(
                        array(
                            'slug'       => 'http',
                            'type'       => 'text',
                            'label'      => 'Unsecure URL:',
                            'attributes' => array(
                                'placeholder' => 'http://myorg.convio.net/site/'
                            ),
                            'helplet'    => 'Please include a trailing slash.'
                        ),
                        array(
                            'slug'       => 'https',
                            'type'       => 'text',
                            'label'      => 'Secure URL:',
                            'attributes' => array(
                                'placeholder' => 'https://secure2.convio.net/myorg/site/'
                            ),
                            'helplet'    => 'Please include a trailing slash.'
                        )
                    )
                )
            )
        );
    }



    function dashboard_css($plugin, $blackbaud) {
        return array(
            'access' => 'dashboard',
            'handle' => 'luminate_survey_dashboard_styles',
            'source' => $plugin->get('url_root') . 'css/dashboard.blackbaud-luminate-survey.css'
        );
    }



    function luminate_extend($plugin, $blackbaud) {

        $settings = get_option($plugin->get('post_type') . '_settings');

        $plugin->expose_setting('api', array(
            'key'                => isset($settings['key'])   ? $settings['key']   : '',
            'secure'             => isset($settings['https']) ? $settings['https'] : '',
            'nonsecure'          => isset($settings['http'])  ? $settings['http']  : '',
            'handlebarsTemplate' => json_encode($plugin->get_template('survey.blackbaud-luminate-survey.hbs'))
        ));

        return array(
            'type' => 'html',
            'access' => 'frontend',
            'for_shortcode' => $plugin->get('shortcode'),
            'output' => function ($plugin, $blackbaud) {
                $html  = '<script src="//cdnjs.cloudflare.com/ajax/libs/luminateExtend/1.7.1/luminateExtend.min.js"></script>';
                $html .= '<div data-bbi-src="' . $plugin->get('url_root') . 'js/bbi-blackbaud-luminate-survey.js"></div>';
                return $html;
            }
        );
    }



    $plugin = $blackbaud->register($plugin_options);
    $plugin->forge('custom_post_type', 'survey_cpt');
    $plugin->forge('meta_box', 'survey_metabox');
    $plugin->forge('post_sortable_columns', 'survey_columns');
    $plugin->forge('asset', 'dashboard_css');
    $plugin->forge('asset', 'luminate_extend');
    $plugin->forge('shortcode', 'survey_shortcode');
    $plugin->forge('settings_page', 'survey_help_page');
    $plugin->forge('settings_page', 'survey_settings_page');
    $plugin->forge('updater');
}


# SUBSCRIBE TO BLACKBAUD'S READY EVENT.
add_action('blackbaud_ready', 'blackbaud_luminate_survey_init');
