<?php
namespace blackbaud;
class Module extends Core {
    public $plugin;
    private $reserved_keys = array('plugin', 'blackbaud');

    public function __construct($arguments = array(), Plugin $plugin) {
        if (!empty($arguments)) {
            foreach ($arguments as $property => $argument) {
                foreach ($this->reserved_keys as $key) {
                    if ($property == $key) {
                        die("The key '{$key}' is reserved. Please choose another property name for your module.");
                        break;
                    }
                }
                $this->{$property} = $argument;
            }
        }

        $this->plugin = $plugin;

        # Execute the object's start() method.
        if (isset($this->start) && is_callable($this->start)) {
            call_user_func_array($this->start, array(
                &$this
            ));
        }
    }

    public function __call($method, $arguments) {
        if (is_callable($this->{$method})) {
            if (count($arguments) > 0 && !empty($arguments[0])) {
                $arguments[] = &$this;
            } else {
                $arguments = array(
                    &$this
                );
            }
            $arguments[] = $this->plugin;

            return call_user_func_array($this->$method, $arguments);
        }
    }
}
