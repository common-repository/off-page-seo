<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Class
 * */
class ops_settings
{

    public function __construct()
    {
        // insert admin menu
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    function admin_menu()
    {
        add_submenu_page('ops', 'Settings', 'Settings', 'activate_plugins', 'ops_settings', array($this, 'ops_settings'));
    }

    /**
     * Initialization
     * */
    function ops_settings()
    {

        if (isset($_GET['log']) && $_GET['log'] == 'show') {
            $this->show_log();
            return;
        }

        // renders settings form
        $this->render_settings_form();
    }


    /**
     * Render Settings form
     * */
    public function render_settings_form()
    {

        global $ops;

        // display message that settings was updated
        if (isset($_POST['_wpnonce']) && check_admin_referer('nt_nonce_ops_settings') == 1) {
            if (!empty($_POST['core_permissions'])) {
                $this->save_settings();
            }

            if (!empty($_POST['access_token'])) {
                $ops->save_settings(sanitize_text_field($_POST['access_token']), 'ops_google_auth_code');
            }
            ?>
            <div class="updated" style="padding: 8px 20px;">
                Settings were updated.
            </div>
            <?php
        }

        ?>
        <div class="wrap">
            <h1><span class="ops-c-gray">Off Page SEO</span> - Settings</h1>
            <?php include(__DIR__ . '/../templates/part-message.php') ?>
            <?php include(__DIR__ . '/../templates/part-sections.php') ?>

            <div class="ops-row">
                <div class="ops-half">
                    <form method="POST" action="" class="ops-form ops-settings-form">
                        <?php wp_nonce_field('nt_nonce_ops_settings'); ?>

                        <?php $settings = $ops->get_settings(); ?>
                        <div class="ops-metabox ops-padding-15">
                            <h2>Core Settings</h2>
                            <hr>
                            <table class="widefat ops-table ops-no-border">
                                <tr>
                                    <td class="ops-settings-left">
                                        <label for="">
                                            Plugin Permissions
                                        </label>
                                    </td>
                                    <td>
                                        <select name="core_permissions" class="ops-select2">
                                            <?php if (is_multisite()): ?>
                                                <option value="manage_sites" <?php echo (isset($settings['core_permission']) && 'manage_sites' == $settings['core_permission']) ? "selected" : ""; ?>>
                                                    <?php _e('Super administrator', 'off-page-seo') ?>
                                                </option>
                                            <?php endif; ?>
                                            <option value="edit_theme_options" <?php echo (isset($settings['core_permission']) && 'edit_theme_options' == $settings['core_permission']) ? "selected" : ""; ?>>
                                                <?php _e('Administrator', 'off-page-seo') ?>
                                            </option>
                                            <option value="read_private_pages" <?php echo (isset($settings['core_permission']) && 'read_private_pages' == $settings['core_permission']) ? "selected" : ""; ?>>
                                                <?php _e('Editor', 'off-page-seo') ?>
                                            </option>
                                            <option value="upload_files" <?php echo (isset($settings['core_permission']) && 'upload_files' == $settings['core_permission']) ? "selected" : ""; ?>>
                                                <?php _e('Author', 'off-page-seo') ?>
                                            </option>
                                            <option value="edit_posts" <?php echo (isset($settings['core_permission']) && 'edit_posts' == $settings['core_permission']) ? "selected" : ""; ?>>
                                                <?php _e('Contributor', 'off-page-seo') ?>
                                            </option>
                                            <option value="read" <?php echo (isset($settings['core_permission']) && 'read' == $settings['core_permission']) ? "selected" : ""; ?>>
                                                <?php _e('Subscriber', 'off-page-seo') ?>
                                            </option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="ops-settings-left">
                                        <label for="">
                                            Your Language
                                        </label>
                                    </td>
                                    <td>
                                        <select name="core_language" class="ops-select2" data-search="1" data-placeholder="Select">
                                            <option ></option>
                                            <?php $languages = ops_get_lang_array() ?>
                                            <?php foreach ($languages as $key => $value): ?>
                                                <option value="<?php echo $key ?>" <?php echo ($key == $settings['core_language']) ? "selected" : ""; ?>><?php echo $value ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>


                                <tr>
                                    <td class="ops-settings-left">
                                        <label for="">
                                            Your Country
                                        </label>
                                    </td>
                                    <td>
                                        <select name="core_country" class="ops-select2" data-search="1" data-placeholder="Select">
                                            <option ></option>
                                            <?php $languages = ops_get_countries_array() ?>
                                            <?php foreach ($languages as $key => $value): ?>
                                                <option value="<?php echo $key ?>" <?php echo ($key == $settings['core_country']) ? "selected" : ""; ?>><?php echo $value ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="ops-settings-left">
                                        <label for="">
                                            Google Domain
                                        </label>
                                    </td>
                                    <td>
                                        <select name="core_google_domain" class="ops-select2" data-search="1" data-placeholder="Select">
                                            <option ></option>
                                            <?php $languages = ops_get_google_domains_array() ?>
                                            <?php foreach ($languages as $key => $value): ?>
                                                <option value="<?php echo $key ?>" <?php echo ($key == $settings['core_google_domain']) ? "selected" : ""; ?>><?php echo $value ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ops-settings-left">
                                        <label for="">
                                            Notification Email
                                        </label>
                                    </td>
                                    <td>
                                        <input type="text" name="core_email" value="<?php echo !empty($settings['core_email']) ? $settings['core_email'] : '' ?>">
                                    </td>
                                </tr>

                                <tr>
                                    <td class="ops-settings-left">
                                        <label for="">
                                            Date Format
                                        </label>
                                    </td>
                                    <td>
                                        <select name="core_date_format" class="ops-select2">
                                            <option value="m/d/Y" <?php echo (isset($settings['core_date_format']) && 'm/d/Y' == $settings['core_date_format']) ? "selected" : ""; ?>>
                                                04/16/2015
                                            </option>
                                            <option value="F d, Y" <?php echo (isset($settings['core_date_format']) && 'F d, Y' == $settings['core_date_format']) ? "selected" : ""; ?>>
                                                April 16, 2015
                                            </option>
                                            <option value="j.n.Y" <?php echo (isset($settings['core_date_format']) && 'j.n.Y' == $settings['core_date_format']) ? "selected" : ""; ?>>
                                                16. 4. 2015
                                            </option>
                                            <option value="jS M Y" <?php echo (isset($settings['core_date_format']) && 'jS M Y' == $settings['core_date_format']) ? "selected" : ""; ?>>
                                                16th Apr 2015
                                            </option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="ops-settings-left">
                                        <label for="">
                                            Currency
                                        </label>
                                    </td>
                                    <td>
                                        <input type="text" name="core_currency" value="<?php echo !empty($settings['core_currency']) ? $settings['core_currency'] : '' ?>">
                                    </td>
                                </tr>

                                <tr>
                                    <td class="ops-settings-left">
                                        <label for="">
                                            Frequency
                                        </label>
                                    </td>
                                    <td>
                                        <select name="core_frequency" class="ops-select2">
                                            <option value="ops_one_day_interval" <?php echo empty($settings['core_frequency']) || (isset($settings['core_frequency']) && 'ops_one_day_interval' == $settings['core_frequency']) ? "selected" : ""; ?>>
                                                Daily
                                            </option>
                                            <option value="ops_two_days_interval" <?php echo (isset($settings['core_frequency']) && 'ops_two_days_interval' == $settings['core_frequency']) ? "selected" : ""; ?>>
                                                Once every 2 days
                                            </option>
                                            <option value="ops_three_days_interval" <?php echo (isset($settings['core_frequency']) && 'ops_three_days_interval' == $settings['core_frequency']) ? "selected" : ""; ?>>
                                                Once every 3 days
                                            </option>
                                            <option value="ops_four_days_interval" <?php echo (isset($settings['core_frequency']) && 'ops_four_days_interval' == $settings['core_frequency']) ? "selected" : ""; ?>>
                                                Once every 4 days
                                            </option>
                                            <option value="ops_five_days_interval" <?php echo (isset($settings['core_frequency']) && 'ops_five_days_interval' == $settings['core_frequency']) ? "selected" : ""; ?>>
                                                Once every 5 days
                                            </option>
                                            <option value="ops_six_days_interval" <?php echo (isset($settings['core_frequency']) && 'ops_six_days_interval' == $settings['core_frequency']) ? "selected" : ""; ?>>
                                                Once every 6 days
                                            </option>
                                            <option value="ops_seven_days_interval" <?php echo (isset($settings['core_frequency']) && 'ops_seven_days_interval' == $settings['core_frequency']) ? "selected" : ""; ?>>
                                                Once every 7 days
                                            </option>
                                        </select>
                                        <p>
                                            Frequency in which we will check your keyword positions. <br>
                                            <b>Important:</b> this only applies to premium accounts. If you are running free version, we check positions continuously.
                                        </p>
                                    </td>
                                </tr>


                            </table>
                            <p>
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                            </p>
                        </div>

                    </form>
                    <br><br>

                    <a href="<?php echo get_admin_url() ?>admin.php?page=ops_settings&log=show">
                        Show log
                    </a>
                </div>



                <div class="ops-half">
                    <?php
                    $active_plugins = get_option('active_plugins');
                    $disallowed_plugins = [
                        'search-console/search-console.php'
                    ];
                    foreach ($disallowed_plugins as $disallowed_plugin) {
                        if (in_array($disallowed_plugin, $active_plugins)) {
                            ?>
                            <div class="ops-warning-message ops-bottom-margin">
                                Following plugin is not compatible with Off Page SEO: <?php echo $disallowed_plugin ?>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <?php if ($ops->is_php_sufficient()): ?>
                        <form method="POST" action="" class="ops-form ops-settings-form">
                            <?php wp_nonce_field('nt_nonce_ops_settings'); ?>


                            <?php $google_auth_code = $ops->get_settings('ops_google_auth_code'); ?>

                            <div class="ops-metabox ops-padding-15">
                                <h2>Google Search Console</h2>
                                <hr>
                                <table class="widefat ops-table ops-no-border">

                                    <?php if (empty($google_auth_code)): ?>
                                        <?php
                                        $ops_google = new ops_google();
                                        $url = $ops_google->get_auth_url();
                                        ?>
                                        <tr>
                                            <td colspan="2">
                                                <a href="<?php echo $url ?>" class="button button-primary" target="_blank">
                                                    Get auth code
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ops-settings-left">
                                                <label for="">
                                                    Auth code
                                                </label>
                                            </td>
                                            <td>
                                                <input type="text" name="access_token" value="<?php echo !empty($google_auth_code) ? $google_auth_code : '' ?>">
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2">
                                                Google Search Console is now connected.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <a href="" class="button ops-revoke-access-google-api" target="_blank">
                                                    Revoke access
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>


                                </table>


                                <p>
                                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
                                </p>
                            </div>

                        </form>
                    <?php endif; ?>


                    <?php if (version_compare(PHP_VERSION, '7.2.5', '<')) : ?>
                        <div class="ops-warning-message">
                            Your PHP version is outdated. To fully utilise this plugin, you'll need at least <b>7.2.5</b>. Your current version is <?php echo PHP_VERSION ?>.
                        </div>
                    <?php endif; ?>

                    <?php if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON == true) : ?>
                        <div class="ops-warning-message">
                            Warning! This plugin will not work properly, because your WP Cron is disabled.
                        </div>
                    <?php endif; ?>

                    <?php
                    if (class_exists('ops_addon')) {
                        global $ops_addon;
                        $ops_addon->validate_wp_json();
                    }
                    ?>
                </div>

            </div>
        </div>
        <?php
    }

    public function save_settings()
    {
        global $ops;
        // save script
        $sanitized = array();
        foreach ($_POST as $field_key => $field_value) {
            $sanitized[$field_key] = sanitize_text_field($field_value);
        }
        $ops->save_settings($sanitized);
    }


    function show_log()
    {
        global $ops;
        $logs = $ops->get_settings('ops_log');
        $settings = $ops->get_settings();
        ?>
        <div class="wrap">
            <h1><span class="ops-c-gray">Off Page SEO</span> - Log</h1>

            <p><?php _e('If you find any bug or if you have any idea how to improve this plugin, feel free to let us know at info@offpageseo.io', 'off-page-seo') ?></p>

            <table class="widefat ops-table" id="ops-log-table">
                <tr>
                    <th class="time"><?php _e('Time', 'off-page-seo') ?></th>
                    <th class="status"><?php _e('Status', 'off-page-seo') ?></th>
                    <th class="type"><?php _e('Type', 'off-page-seo') ?></th>
                    <th class="type_id"><?php _e('Type ID', 'off-page-seo') ?></th>
                    <th><?php _e('Message', 'off-page-seo') ?></th>
                </tr>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="<?php echo isset($log['type']) ? $log['type'] : 'info' ?>">
                            <td>
                                <?php echo date($settings['core_date_format'] . ' - H:i:s', $log['time']); ?>
                            </td>
                            <td>
                                <?php echo isset($log['status']) ? $log['status'] : '' ?>
                            </td>
                            <td>
                                <?php echo isset($log['type']) ? $log['type'] : '' ?>
                            </td>
                            <td>
                                <?php echo isset($log['type_id']) ? $log['type_id'] : '' ?>
                            </td>
                            <td>
                                <?php echo isset($log['message']) ? $log['message'] : '' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>

        <?php if (OPS_DEBUG == true): ?>
        global $ops; <br>
        $ops->create_log_entry('error', 'ranking', $pid, __('Message', 'off-page-seo'));
    <?php endif; ?>
        <?php

    }


}


new ops_settings();