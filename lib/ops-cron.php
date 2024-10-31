<?php

class ops_cron
{

    /**
     * Register shortcodes
     */
    function __construct()
    {

        // prevent error on activation
        $settings = get_option('ops_settings');
        if (empty($settings)) {
            return false;
        }

        global $ops;


        if (wp_next_scheduled('ops_rank_update') == '') {
            $is_premium_active = $ops->is_premium_active();

            if ($is_premium_active === true) {
                $settings = $ops->get_settings();
                wp_schedule_event(time() + 30, $settings['core_frequency'], 'ops_rank_update');
                $ops->create_log_entry('error', 'ranking', '', __('Scheduling new CRON ' . $settings['core_frequency'] . ' - Premium rank update', 'off-page-seo'));
            } else {
                wp_schedule_event(time() + 30, 'ops_ten_minutes_interval', 'ops_rank_update');
                $ops->create_log_entry('error', 'ranking', '', __('Scheduling new CRON 10 minutes - Free rank update', 'off-page-seo'));

                if (!empty($is_premium_active['error'])) {
                    $ops->create_log_entry('error', 'ranking', '', __('You have premium plugin active, but there is a different error causing us to use Free mode: ' . $is_premium_active['error'], 'off-page-seo'));
                }
            }
        }

        if (wp_next_scheduled('ops_reciprocal_check') == '') {
            wp_schedule_event(time() + 30, 'ops_seven_days_interval', 'ops_reciprocal_check');
        }

        // hook action to the cron
        add_action('ops_rank_update', array($this, 'rank_update_callback'));

        add_action('ops_reciprocal_check', array($this, 'reciprocal_check_callback'));

    }


    function reciprocal_check_callback()
    {
        global $ops;
        $ops->create_log_entry('error', 'reciprocal', false, 'Starting CRON reciprocal check.');

        $ops_reciprocal = new ops_reciprocal();
        $ops_reciprocal->run_reciprocal_check();
    }

    function rank_update_callback()
    {

        $ops_ranking = new ops_ranking();
        $ops_ranking->update_ranks();
    }

}


new ops_cron();