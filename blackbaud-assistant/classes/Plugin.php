<?php
namespace blackbaud;

class Plugin extends Core {
    public $blackbaud;
    public $last_forged;
    protected $plugin_file;
    private $actions = array();
    protected $modules = array();
    protected $exposed_settings = array();
    private $forged = array();
    public $last_module;

    public function add_module($slug, $callback) {
        if (is_callable($callback)) {
            $options = call_user_func_array($callback, array(
                $this,
                $this->blackbaud
            ));
            $this->last_module    = new Module($options, $this);
            $this->modules[$slug] = $this->last_module;
            return $this->last_module;
        }
        return $this;
    }

    public function forge($what = "", $request = null) {
        # Determine how the options variable is generated.
        # Can be an array or returned from a callback.
        if (is_array($request)) {
            $options = $request;
        } else if (is_callable($request)) {
            $options = call_user_func_array($request, array(
                $this,
                $this->blackbaud
            ));
        } else {
            $options = array();
        }

        # Send the plugin as a property of this object.
        if (!isset($options["plugin"])) {
            $options["plugin"] = $this;
        }

        # Determine what to do based on what's being forged.
        switch ($what) {
            case "bbi_script":
                $this->last_forged = $this->blackbaud->add_bbi_script($options);
                break;

            default:
                $this->last_forged = $this->instantiate($what, $options);
                break;
        }

        if ($this->last_forged) {
            $this->forged[$what][] = $this->last_forged;
        }

        return $this->last_forged;

    }

    public function get_forged($type = "", $slug = "") {
        if (isset($this->forged[$type])) {
            if ($slug === "") {
                return $this->forged[$type];
            } else {
                foreach ($this->forged[$type] as $this_forged) {
                    if ($this_forged->get("slug") === $slug) {
                        return $this_forged;
                    }
                }
                return false;
            }
        } else {
            return false;
        }
    }

    private function instantiate($what, $options) {
        $aliases = $this->blackbaud->plugin->get("class_aliases");

        # No aliases are set, so we won't be able to create an object.
        # Make sure the aliases are being sent through the global $blackbaud object.
        if (!isset($aliases[$what])) {
            return false;
        }

        # We'll be saving this object in this plugin.
        # Make sure the array is ready to store it.
        if (!isset($this->forged[$what])) {
            $this->forged[$what] = array();
        }

        # Create the object and add it to storage.
        return new $aliases[$what]($options);
    }

    protected function start($settings = array()) {
        $error_prefix = '[! BLACKBAUD PLUGIN ERROR !] Insufficient data provided to Blackbaud::register() method. ';
        if (!isset($settings['plugin_file'])) {
            die($error_prefix . "Your plugin '$this->alias' must have the property 'plugin_file' set.");
        }
        if (!isset($settings['plugin_basename'])) {
            die($error_prefix . "Your plugin '$this->alias' must have the property 'plugin_basename' set.");
        }
        add_action('wp_footer', array($this, 'expose_settings'), 10, 0);
    }

    public function module($slug, $constructor = null) {
        if (is_callable($constructor)) {
            return $this->add_module($slug, $constructor);
        }
        return $this->modules[$slug];
    }

    public function expose_setting($key, $data) {
        $this->exposed_settings[$key] = $data;
    }

    public function expose_settings() {
        $data = array(
            "alias" => $this->alias,
            "settings" => $this->exposed_settings
        );
        echo $this->blackbaud->plugin->get_template('exposed-settings.blackbaud-assistant.php', $data);
    }
}
