<?php
namespace blackbaud;
class SettingsField extends Field {
    protected $settings_slug;

    public function start() {
        $this->name = $this->settings_slug . '[' . $this->slug . ']';
        $this->id = $this->name;
        $this->template = 'settings-field.blackbaud-assistant.php';
        $this->set_default_value();
        $this->build();
    }

    public function set_default_value() {
        $storage = get_option($this->settings_slug);

        if (empty($storage)) {
            if (isset($this->default)) {
                $value = $this->default;
            } else {
                $value = "";
            }
        } else {
            if (isset($storage[$this->slug])) {
                $value = $storage[$this->slug];
            } else {
                $value = "";
            }
        }

        $this->value = $value;
    }
}
