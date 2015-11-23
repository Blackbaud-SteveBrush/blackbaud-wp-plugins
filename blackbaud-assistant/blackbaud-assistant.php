<?php
/*
Plugin Name: Blackbaud: Assistant & Libraries
Description: Essential libraries for Blackbaud-supported plugins (DO NOT DEACTIVATE).
Author: Blackbaud Interactive Services
Version: 0.0.1
Text Domain: blackbaud-assistant
*/

namespace blackbaud;


# Exit if accessed directly.
if (!defined('ABSPATH')) exit;


# INCLUDE OUR LIBRARIES.
require_once 'classes/Core.php';
require_once 'classes/Field.php';
require_once 'classes/CustomPostType.php';
require_once 'classes/PostsColumns.php';
require_once 'classes/MetaBox.php';
require_once 'classes/MetaField.php';
require_once 'classes/TaxonomyField.php';
require_once 'classes/TaxonomyFields.php';
require_once 'classes/Shortcode.php';
require_once 'classes/TinyMCEShortcodeButton.php';
require_once 'classes/Asset.php';
require_once 'classes/SettingsPage.php';
require_once 'classes/SettingsField.php';
require_once 'classes/Updater.php';
require_once 'classes/Plugin.php';
require_once 'classes/Module.php';



# WRAP EVERYTHING IN A FUNCTION TO PRESERVE THE SCOPE.
function blackbaud_assistant_init($plugin) {



    # CREATE THE FACTORY.
    $plugin->module('Factory',
        function ($plugin, $blackbaud) {
            return array(
                'actions' => array(),
                'bbi_scripts' => array(),
                'last_plugin' => null,
                'plugins' => array(),
                'exposed_settings' => array(),
                'start' => function ($module) {
                    add_action('plugins_loaded', array($module, 'plugins_loaded'), 10, 0);
                },
                'get_plugin' => function ($alias, $module) {
                    return $module->plugins[$alias];
                },
                'add_bbi_script' => function ($options = array(), $module) {
                    $module->bbi_scripts[] = $options;
                    return $options;
                },
                'do_shortcode' => function ($post_content, $module) {
                    // Force-enqueue assets that are required for any shortcodes
                    foreach ($module->plugins as $plugin) {
                        if (isset($plugin->shortcode)) {
                            if (has_shortcode($post_content, $plugin->shortcode)) {
                                $assets = $plugin->get_forged('asset');
                                foreach ($assets as $asset) {
                                    $asset->enqueue();
                                }
                            }
                        }
                    }
                    return do_shortcode($post_content);
                },
                'get_settings_field' => function ($slug, $key) {
                    if ($settings = get_option($slug, false)) {
                        if ($settings && isset($settings[$key])) {
                            return $settings[$key];
                        }
                        return false;
                    }
                    return false;
                },
                'plugins_loaded' => function ($module) {
                    # Execute all actions when the plugins have been activated.
                    foreach ($module->actions as $action)
                    {
                        do_action($action, $module);
                    }
                },
                'register' => function ($request = null, $module) {
                    # Determine how the options variable is generated.
                    # Can be an array or returned from a callback.
                    if (is_array($request)) {
                        $options = $request;
                    }
                    else if (is_callable($request)) {
                        $options = call_user_func($request, $module);
                    }
                    else {
                        $options = array();
                    }

                    # Make sure $blackbaud is added as a property.
                    if (! isset($options ['blackbaud'])) {
                        $options['blackbaud'] = $module;
                    }

                    # Store the new plugin in various places to find it later.
                    $module->last_plugin = new Plugin($options);
                    $module->plugins[$options['alias']] = $module->last_plugin;

                    return $module->last_plugin;
                },
                'trigger' => function ($alias, $module) {
                    /**
                     * Adds an action to the list, to be ultimately triggered when all plugins
                     * have been loaded.
                     */
                    $module->actions[] = $alias;
                }
            );
        });



    # MAKE '$blackbaud' POINT TO THE FACTORY.
    $plugin->set('blackbaud', $plugin->module('Factory'));



    # BBI SCRIPT.
    $plugin->forge('asset', function ($plugin, $blackbaud) {
        return array(
            'type' => 'html',
            'access' => 'dashboard',
            'output' => function ($plugin, $blackbaud) {
                return '<div data-bbi-src="' . $plugin->get('url_root') . 'js/bbi-blackbaud-assistant.js"></div>';
            }
        );
    });



    # ASSETS.
    $plugin->forge('asset', function ($plugin) {
        return array(
            'access' => 'dashboard',
            'handle' => 'blackbaud_assistant_dashboard_styles',
            'source' => $plugin->get('url_root') . 'css/dashboard.blackbaud-assistant.css'
        );
    });



    # BBI APP INITS.
    $plugin->forge('asset', function ($plugin) {
        return array(
            'type' => 'html',
            'access' => 'dashboard',
            'output' => function ($plugin) {
                # Load all assets required for the Media Gallery picker.
                wp_enqueue_media();

                # Add our bbi action to the page.
                return '<div data-bbi-app="BlackbaudAssistant" data-bbi-action="dashboard"></div>';
            }
        );
    });



    # BBI NAMESPACE.
    $plugin->forge('asset', function ($plugin) {
        /**
         * Prints in the HEAD.
         * Adds all plugin-specific BBI Namespace scripts.
         */
        return array(
            'type' => 'html',
            'access' => 'global',
            'in_footer' => false,
            'output' => function ($plugin) {
                return $plugin->get_template('bbi-namespace.blackbaud-assistant.php', array('scripts' => $plugin->blackbaud->get('bbi_scripts')));
            }
        );
    });



    $plugin->module('Utilities', function ($plugin, $blackbaud) {
        return array(
            "snake_to_camel" => function ($val, $module) {
                $val = str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));
                $val = strtolower(substr($val, 0, 1)).substr($val, 1);
                return $val;
            }
        );
    });



    # UPDATER.
    $plugin->forge('updater');



    # ADD '$blackbaud' TO THE GLOBAL ARRAY.
    $GLOBALS['blackbaud'] = $plugin->module('Factory');



    # TELL OUR VARIOUS PLUGINS TO INITIALIZE!
    $plugin->module('Factory')->trigger('blackbaud_ready');


}


# CREATE THE BLACKBAUD APPLICATION.
blackbaud_assistant_init(new Plugin(array(
    'class_aliases' => array(
        'asset'                    => 'blackbaud\Asset',
        'custom_post_type'         => 'blackbaud\CustomPostType',
        'post_sortable_columns'    => 'blackbaud\PostsColumns',
        'meta_box'                 => 'blackbaud\MetaBox',
        'meta_field'               => 'blackbaud\MetaField',
        'shortcode'                => 'blackbaud\Shortcode',
        'settings_page'            => 'blackbaud\SettingsPage',
        'settings_field'           => 'blackbaud\SettingsField',
        'taxonomy_field'           => 'blackbaud\TaxonomyField',
        'taxonomy_fields'          => 'blackbaud\TaxonomyFields',
        'tinymce_shortcode_button' => 'blackbaud\TinyMCEShortcodeButton',
        'updater'                  => 'blackbaud\Updater'
    ),
    'alias'               => 'blackbaud',
    'plugin_file'         => __FILE__,
    'plugin_basename'     => plugin_basename(__FILE__),
    'url_root'            => plugins_url('assets/', __FILE__),
    'templates_directory' => plugin_dir_path(__FILE__) . 'templates/'
)));
