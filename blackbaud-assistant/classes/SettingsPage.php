<?php
namespace blackbaud;
class SettingsPage extends Core {
    protected $defaults;
    protected $default_field = array("slug" => null, "type" => "text", "label" => "", "default" => "");

    public function __construct($options = array()) {
        $this->defaults = array(
            "slug" => null,
            "parent_slug" => null,
            "page_title" => "Settings",
            "menu_title" => "Settings",
            "never_save" => false,
            "capability" => "manage_options",
            "callbacks" => array(
                "display" => array(
                    $this,
                    "display"
                ),
                "validation" => function($r) {
                    return $r;
                }
            ),
            "sections" => array(
                "sample_section" => array(
                    "title" => "Sample Section",
                    "callback" => function() {
                    },
                    "fields" => array()
                )
            )
        );

        $this->default_field['callback'] = array(
            $this,
            "build_field"
        );

        if (isset($options["callbacks"])) {
            $options["callbacks"] = array_merge($this->defaults["callbacks"], $options["callbacks"]);
        }

        parent::__construct($options);

    }



    /**
     * Adds a submenu page based on a parent slug.
     * This method also attaches the 'display' callback, which determines what
     * content gets added to the page.
     */
    public function add_submenu() {
        add_submenu_page($this->settings["parent_slug"], $this->settings["page_title"], $this->settings["menu_title"], $this->settings["capability"], $this->settings["slug"], array(
            $this,
            "callback_display"
        ));
    }



    /**
     * This method executes the callback function 'display', which can be overwritten
     * by the plugin.
     */
    public function callback_display() {
        if (is_callable($this->settings["callbacks"]["display"])) {
            call_user_func_array($this->settings["callbacks"]["display"], array(
                $this->plugin,
                $this->plugin->blackbaud
            ));
        }
    }



    /**
     * This is the default 'display' callback method.
     */
    public function display($plugin, $blackbaud) {
        $data = array(
            "page_title" => $this->page_title,
            'page_id' => $this->slug
        );

        ob_start();
        $this->do_settings_sections();
        $data['form_html'] = ob_get_clean();

        echo $this->plugin->blackbaud->plugin->get_template("settings-page.blackbaud-assistant.php", $data);
    }



    /**
     * Custom do_settings_sections function to use the Field class.
     * Prints the sections and fields to the screen.
     */
    private function do_settings_sections() {
        global $wp_settings_sections, $wp_settings_fields;

        if (!isset($wp_settings_sections[$this->slug])) {
            return;
        }

        foreach ((array) $wp_settings_sections[$this->slug] as $section) {
            $data = array();

            # Title.
            if ($section['title']) {
                $data['title'] = $section['title'];
            }

            /**
             * Section callback.
             * The callback needs to return the HTML content as a string, instead
             * of echo-ing it, which is default.
             */
            if ($section['callback']) {
                $data['section_additional_html'] = call_user_func($section['callback'], $section);
            }

            # Fields.
            if (isset($wp_settings_fields) && isset($wp_settings_fields[$this->slug]) && isset($wp_settings_fields[$this->slug][$section['id']])) {
                $data['field_html'] = $this->do_settings_fields($section['id']);
            }

            echo $this->plugin->blackbaud->plugin->get_template('settings-section.blackbaud-assistant.php', $data);

        }
    }



    /**
     * Returns HTML for a field, which is derived from the field's callback method.
     */
    private function do_settings_fields($section) {
        global $wp_settings_fields;
        $html = '';
        if (!isset($wp_settings_fields[$this->slug][$section])) {
            return;
        }
        foreach ($wp_settings_fields[$this->slug][$section] as $field) {
            $html .= call_user_func($field['callback'], $field['args']);
        }
        return $html;
    }



    /**
     * Returns the HTML content for a field.
     * This is the default callback method for a field and can be overwritten.
     */
    public function build_field(SettingsField $field) {
        return $field->rendering();
    }



    /**
     * Displays the HTML of the settings page.
     * Also takes care of what value should be inserted in the fields.
     */
    public function register_fields() {
        $settings_slug = $this->settings["slug"];

        # Always reset the options?
        if ($this->settings['never_save']) {
            delete_option($settings_slug);
        }

        register_setting($settings_slug, $settings_slug, $this->settings["callbacks"]["validation"]);

        $this->set_fields_default_values();

        foreach ($this->settings["sections"] as $section_slug => $section) {
            if (!isset($section["callback"])) {
                $section["callback"] = function() {
                };
            }

            add_settings_section($section_slug, $section["title"], $section["callback"], $settings_slug);

            foreach ($section["fields"] as $field) {
                $field                  = array_merge($this->default_field, $field);
                $field['settings_slug'] = $settings_slug;

                add_settings_field($field["slug"], $field["label"], $field['callback'], $settings_slug, $section_slug, $this->plugin->forge('settings_field', $field));
            }
        }
    }



    /**
     * Sets default values if settings option does not exist.
     */
    public function set_fields_default_values() {
        $settings_slug = $this->settings["slug"];
        if (false === ($options = get_option($settings_slug))) {
            $defaults = array();
            foreach ($this->settings['sections'] as $section) {
                foreach ($section['fields'] as $field) {
                    if (isset($field['default'])) {
                        $defaults[$field['slug']] = $field['default'];
                    }
                }
            }
            update_option($settings_slug, $defaults);
        }
    }



    /**
     * Initializer.
     */
    public function start() {
        register_activation_hook($this->plugin->get('plugin_file'), array(
            $this,
            "set_fields_default_values"
        ));
        add_action("admin_menu", array(
            $this,
            "add_submenu"
        ));
        add_action("admin_init", array(
            $this,
            "register_fields"
        ));
    }
}
