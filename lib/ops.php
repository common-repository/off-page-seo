<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class
 * */
class ops
{

    private $settings_key = 'ops_settings';

    /**
     * Initialization
     * */
    public function __construct()
    {

        // To be completed
        add_action('plugins_loaded', array($this, 'load_languages'));

        // hook settings link to Plugin page
        add_filter('plugin_action_links_off-page-seo/off-page-seo.php', array($this, 'add_settings_link'));

        // few scripts in backend
        add_action('admin_enqueue_scripts', array($this, 'load_custom_wp_admin_style'));

        add_action('init', array($this, 'register_post_types'));

        add_filter('cron_schedules', array($this, 'cron_intervals'), 10, 1);

        // hook icon to admin bar
        add_action('admin_bar_menu', array($this, 'add_admin_bar_item'), 999);
    }


    function add_admin_bar_item($wp_admin_bar)
    {
        $args = array(
            'id' => 'off_page_seo',
            'title' => '<span class="ops-icon"></span>OPS',
            'href' => get_admin_url() . 'admin.php?page=ops',
            'meta' => array('class' => 'ops-admin-bar-icon'),
            'parent' => false
        );
        $wp_admin_bar->add_node($args);

    }


    function cron_intervals($schedules)
    {
        $schedules['ops_one_day_interval'] = array(
            'interval' => 86400,
            'display' => 'Once Every 1 Day'
        );

        $schedules['ops_two_days_interval'] = array(
            'interval' => 86400 * 2,
            'display' => 'Once Every 2 Days'
        );

        $schedules['ops_three_days_interval'] = array(
            'interval' => 86400 * 3,
            'display' => 'Once Every 3 Days'
        );

        $schedules['ops_four_days_interval'] = array(
            'interval' => 86400 * 4,
            'display' => 'Once Every 4 Days'
        );

        $schedules['ops_five_days_interval'] = array(
            'interval' => 86400 * 5,
            'display' => 'Once Every 5 Days'
        );

        $schedules['ops_six_days_interval'] = array(
            'interval' => 86400 * 6,
            'display' => 'Once Every 6 Days'
        );

        $schedules['ops_seven_days_interval'] = array(
            'interval' => 86400 * 7,
            'display' => 'Once Every 7 Days'
        );

        $schedules['ops_ten_minutes_interval'] = array(
            'interval' => 600,
            'display' => 'Once Every 10 Minutes'
        );

        return (array)$schedules;
    }


    function register_post_types()
    {
        register_post_type('ops_keyword', array(
            'labels' => array(
                'name' => __('Keywords'),
                'singular_name' => __('Keyword')
            ),
            'hierarchical' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_in_nav_menus' => true,
            'show_ui' => false,
            'public' => false,
        ));

        register_post_type('ops_backlink', array(
            'labels' => array(
                'name' => __('Backlinks'),
                'singular_name' => __('Backlink')
            ),
            'hierarchical' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_in_nav_menus' => true,
            'show_ui' => false,
            'public' => false,
        ));

        $labels = array(
            'name' => _x('Keyword Categories', 'taxonomy general name'),
            'singular_name' => _x('Keyword Category', 'taxonomy singular name'),
            'search_items' => __('Search Keyword'),
            'all_items' => __('All Keyword Categories'),
            'parent_item' => __('Parent Keyword Category'),
            'parent_item_colon' => __('Parent Keyword Category:'),
            'edit_item' => __('Edit Keyword Category'),
            'update_item' => __('Update Keyword Category'),
            'add_new_item' => __('Add New Keyword Category'),
            'new_item_name' => __('New Keyword Category'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'public' => false,
            'rewrite' => array(),
        );

        register_taxonomy('ops_keyword_cat', 'ops_keyword', $args);
    }


    function load_languages()
    {
        load_plugin_textdomain('off-page-seo', false, dirname(plugin_basename(__FILE__)) . '/../../languages/');
    }


    public function add_settings_link($links)
    {
        $mylinks = array(
            '<a href="' . admin_url('admin.php?page=ops_settings') . '">Settings</a>',
        );
        return array_merge($links, $mylinks);
    }

    function load_custom_wp_admin_style()
    {

        if (empty($_GET['page'])) {
            return false;
        }

        $ops_pages = [
            'ops',
            'ops_backlinks',
            'ops_premium',
            'ops_settings',
            'ops_tools',
            'ops_search_console'
        ];

        if (in_array($_GET['page'], $ops_pages)) {
            wp_enqueue_style('ops_css', plugins_url('off-page-seo/css/ops-style.css'), [], filesize(__DIR__ . '/../css/ops-style.css'));
            wp_enqueue_script('ops_select2', plugins_url('off-page-seo/js/select2.min.js'));
            wp_enqueue_script('ops_js', plugins_url('off-page-seo/js/ops-script.js'), ['ops_select2'], filesize(__DIR__ . '/../js/ops-script.js'));
        }

    }

    function create_log_entry($status, $type, $type_id, $message)
    {
        $log = $this->get_settings('ops_log');

        if (empty($log)) {
            $log = array();
        }
        $new_entry = array(
            'time' => time(),
            'status' => $status,
            'type' => $type,
            'type_id' => $type_id,
            'message' => $message
        );
        array_unshift($log, $new_entry);
        $new_log = array_slice($log, 0, 250);
        $this->save_settings($new_log, 'ops_log');
    }

    function is_premium()
    {
        if (defined('OPS_PREMIUM') && OPS_PREMIUM == true) {
            return true;
        } else {
            return false;
        }
    }

    function is_premium_active()
    {
        if ($this->is_premium() == false) {
            return false;
        }

        $premium_data = $this->get_premium_data();

        $output = [];

        if (!empty($premium_data['no_key'])) {
            $output['error'] = 'No API key';
            return $output;
        }

        if (!empty($premium_data['error'])) {
            $output['error'] = $premium_data['error'];
            return $output;
        }

        if (!empty($premium_data['success']->credit) && $premium_data['success']->credit < 1) {
            $output['error'] = 'Insufficient credit ($' . $premium_data['success']->credit . ')';
            return $output;
        }

        return true;
    }


    function get_api_key()
    {
        return $this->get_settings('ops_api_key');
    }

    function current_user_can_control_plugin()
    {
        $settings = $this->get_settings();

        if (current_user_can($settings['core_permissions'])) {
            return true;
        }

        return false;
    }

    function get_premium_data()
    {

        if (defined('OPS_DEBUG') && OPS_DEBUG == true) {
            delete_transient('ops_premium_account_data');
        }

        if (false === ($output = get_transient('ops_premium_account_data'))) {


            $output = [];
            $api_key = $this->get_settings('ops_api_key');

            // ops_premium_account_data
            if (empty($api_key)) {
                $output['error'] = 'No API key';
                $output['no_key'] = true;
                return $output;
            }

            $args = [
                'timeout' => 25,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => [],
                'cookies' => []
            ];

            $query = [
                'api_key' => $api_key,
                'home_url' => get_home_url()
            ];

            $url = OPS_API_URL . 'get_account_data/?' . http_build_query($query);

            $response = wp_remote_get($url, $args);

            if (is_wp_error($response)) {

                global $ops;
                $ops->create_log_entry('error', 'api', false, __('Error #123651. API POST request error. Response: ', 'off-page-seo') . $response->get_error_message());

                return false;

            } else {
                $body = json_decode($response['body']);

                if (!empty($body->data->status) && $body->data->status == 471) {
                    $output['error'] = $body->message;
                    return $output;
                }
            }

            $output['success'] = $body;

            set_transient('ops_premium_account_data', $output, 60 * 5);
        }

        return $output;


    }

    /**
     * Set settings to this class
     */
    function save_settings($settings, $key = false, $sitewide = false)
    {

        if ($key == false) {
            $current_settings = $this->get_settings();
            if (!empty($current_settings['core_frequency']) && $settings['core_frequency'] != $current_settings['core_frequency']) {
                wp_clear_scheduled_hook('ops_rank_update');
                $this->create_log_entry('error', 'ranking', '', __('Clearing scheduled hook because of frequency change.', 'off-page-seo'));
            }
        }

        if ($key == false) {
            $key = $this->settings_key;
        }

        if (is_multisite()) {
            if ($sitewide == true) {
                update_site_option($key, $settings);
            } else {
                update_option($key, $settings);
            }
        } else {
            update_option($key, $settings);
        }
    }


    /**
     * Recognize if the site is multisite. It returns either blog settings or page settings.
     * returns: array
     */
    function get_settings($key = false, $sitewide = false)
    {
        // any other key than default?
        if ($key == false) {
            $key = $this->settings_key;
        }

        // get settings
        if (is_multisite()) {
            if ($sitewide == true) {
                $settings = get_site_option($key);
            } else {
                $settings = get_option($key);
            }
        } else {
            $settings = get_option($key);
        }

        if ($key == $this->settings_key && empty($settings)) {
            return [
                'core_permissions' => 'administrator',
                'core_language' => 'English',
                'core_country' => 'UK',
                'core_google_domain' => 'com',
                'core_email' => '',
                'core_date_format' => 'm/d/Y',
                'core_currency' => 'USD'
            ];
        }

        if (is_serialized($settings)) {
            $settings = unserialize($settings);
        }

        // return;
        return $settings;
    }

    function delete_settings($key, $sitewide = false)
    {
        if (is_multisite()) {
            if ($sitewide == true) {
                delete_site_option($key);
            } else {
                delete_option($key);
            }
        } else {
            delete_option($key);
        }
    }

    function is_php_sufficient()
    {
        if (version_compare(PHP_VERSION, '7.2.5', '<')) {
            return false;
        } else {
            return true;
        }
    }
}

$ops = new ops();