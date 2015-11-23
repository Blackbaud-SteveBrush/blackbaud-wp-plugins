<?php
namespace blackbaud;
class Updater extends Core {
    protected $defaults = array(
        "endpoint" => "//api.blackbaud.com/services/wordpress/updater/plugins",
        "always_check" => false
    );

    private $plugin_basename;
    private $plugin_file;

    protected $endpoint;
    protected $always_check;
    protected $latest_package;
    protected $plugin_data;

    protected function start() {
        if (is_admin()) {
            $protocol              = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
            $this->endpoint        = $protocol . $this->endpoint;
            $this->plugin_file     = $this->plugin->get("plugin_file");
            $this->plugin_basename = $this->plugin->get("plugin_basename");

            if ($this->always_check) {
                set_site_transient('update_plugins', null);
            }
            add_filter("pre_set_site_transient_update_plugins", array(
                $this,
                "check_for_updates"
            ));
            add_filter("plugins_api", array(
                $this,
                "fetch_plugin_info"
            ), 10, 3);
        }
    }

    public function check_for_updates($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        if ($this->is_update_available()) {
            $transient->response[$this->plugin_basename] = $this->fetch_latest_package();
        }
        return $transient;
    }

    public function fetch_plugin_info($def, $action, $data) {
        if ($action !== 'plugin_information') {
            return false;
        }
        if (empty($data->slug) || ($data->slug != $this->plugin_basename)) {
            return false;
        }
        # Push in plugin version information to display in the details lightbox.
        if ($package = $this->fetch_latest_package()) {
            return $package;
        }
        return false;
    }

    private function is_update_available() {
        $this->plugin_data = get_plugin_data($this->plugin_file);
        if ($package = $this->fetch_latest_package()) {
            if (version_compare($package->new_version, $this->plugin_data['Version'], '>')) {
                return true;
            }
        }
        return false;
    }

    private function fetch_latest_package() {
        if (isset($this->latest_package)) {
            return $this->latest_package;
        }

        $url      = $this->endpoint . '?plugin=' . urlencode($this->plugin_basename);
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return false;
        }

        $response_body = wp_remote_retrieve_body($response);
        $result        = json_decode($response_body);

        if ($this->is_response_error($result)) {
            return false;
        }

        $this->latest_package          = $result;
        $this->latest_package->slug    = $this->plugin_basename;
        $this->latest_package->package = $this->endpoint . '/' . $this->latest_package->zip_file;

        # Parse 'sections' into appropriate format.
        if (is_object($this->latest_package->sections)) {
            $this->latest_package->sections = get_object_vars($this->latest_package->sections);
        } elseif (!isset($this->latest_package->sections)) {
            $this->latest_package->sections = array(
                'description' => ''
            );
        }

        # Parse 'banners' into appropriate format.
        if (!empty($this->latest_package->banners)) {
            $this->latest_package->banners = is_object($this->latest_package->banners) ? get_object_vars($this->latest_package->banners) : $this->latest_package->banners;
            $this->latest_package->banners = array_intersect_key($this->latest_package->banners, array(
                'high' => true,
                'low' => true
            ));
        }

        return $this->latest_package;
    }

    private function is_response_error($response) {
        if ($response === false) {
            return true;
        }
        if (!is_object($response)) {
            return true;
        }
        if (isset($response->error)) {
            return true;
        }
        return false;
    }
}
