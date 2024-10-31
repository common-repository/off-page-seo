<?php

class ops_ranking
{

    function update_ranks()
    {
        global $ops;

        if ($ops->is_premium_active()) {

            $ops->create_log_entry('info', 'ranking', false, __('Starting new ranking check. Type: premium update', 'off-page-seo'));

            $ops_ranking_addon = new ops_ranking_addon();
            $ops_ranking_addon->get_rank_from_ops();

            return true;
        } else {

            // when we started last check?
            $update_started_on = $ops->get_settings('ops_rank_update_started');

            // starting update now!
            if (empty($update_started_on)) {
                $update_started_on = time();
                $ops->save_settings($update_started_on, 'ops_rank_update_started');
                $ops->delete_settings('ops_rank_update_had_error');
                $ops->create_log_entry('info', 'ranking', false, __('Starting new ranking check round. Type: local check', 'off-page-seo'));
            }

            // check 1 keyword only
            $args = [
                'post_type' => 'ops_keyword',
                'orderby' => 'date',
                'order' => 'DESC',
                'posts_per_page' => 1,
                'post_status' => 'publish',
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key' => 'last_rank_update',
                        'compare' => 'NOT EXISTS'
                    ],
                    [
                        'key' => 'last_rank_update',
                        'type' => 'numeric',
                        'value' => $update_started_on,
                        'compare' => '<='
                    ],
                ],
            ];

            $wp_query = new WP_Query($args);

            if ($wp_query->have_posts()) :
                while ($wp_query->have_posts()) : $wp_query->the_post();

                    $position = $this->get_rank_directly_from_google(get_the_ID());
                    if ($position != false) {
                        $this->save_rank($position, get_the_ID());
                    } else {
                        $ops->save_settings(get_the_ID(), 'ops_rank_update_had_error');
                    }

                endwhile;
            else:
                // we finished the round, let's delete option and start again

                // first check interval
                $diff = time() - $update_started_on;

                if (!empty($ops->get_settings('ops_rank_update_had_error'))) {

                    $ops->delete_settings('ops_rank_update_had_error');

                    $settings = $ops->get_settings();
                    ob_start();
                    ?>
                    Dear user, <br>
                    regular rank update using your own website as requester on Google rank positions had one or more errors. <br><br>
                    Consider using our premium service which will have following benefits and make sure you never get blocked by Google!<br><br>
                    <a href="<?php echo get_admin_url() ?>?page=ops_premium" target="_blank">
                        Sign up
                    </a>
                    <br><br>
                    Regards, <br>
                    Off Page SEO plugin <br>

                    <?php
                    $message = ob_get_contents();
                    ob_end_clean();
                    $email = new ops_email();
                    $email->set_body($message);
                    $email->send_email($settings['core_email'], 'Rank update had one or more errors');
                }

                // it it has been more than around 2 days, we will start a new batch
                if ($diff / 86400 > 2) {
                    $ops->delete_settings('ops_rank_update_started');
                }

            endif;

            return true;
        }

    }

    function save_rank($position, $pid = false, $keyword = false, $time = false)
    {

        if ($pid == false) {
            $ops_keyword = new ops_keyword();
            $pid = $ops_keyword->get_keyword_id_by_title($keyword);
        }

        if (empty($pid)) {
            global $ops;
            $ops->create_log_entry('error', 'api', false, __('Error #436f5. Could not get keyword ID by title for ' . $keyword, 'off-page-seo'));
            return false;
        }

        update_post_meta($pid, 'last_rank_update', time());

        global $wpdb;

        $wpdb->insert($wpdb->prefix . 'ops_rankings', array(
            'id' => '',
            'keyword_id' => $pid,
            'time' => !empty($time) ? $time : time(),
            'ranking' => $position
        ));

    }


    function get_rank_directly_from_google($pid)
    {

        global $ops;

        $keyword = get_the_title($pid);

        $args = [
            'timeout' => 25,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36',
            'blocking' => true,
            'headers' => [],
            'cookies' => []
        ];


        $url = $this->get_google_request_url($keyword);

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response)) {
            $ops->create_log_entry('error', 'ranking', $pid, __('Error #10921233. Not getting remote request correctly. Error: ' . $response->get_error_message(), 'off-page-seo'));
            return false;
        } elseif ($response['response']['code'] != 200) {
            $ops->create_log_entry('error', 'ranking', $pid, __('Error #102923. Not getting 200 response from Google. Header code is: ' . $response['response']['code'], 'off-page-seo'));
            return false;
        } else {
            $html = $response['body'];
        }

        // google requires captcha
        if (stristr($html, 'protect our users') || stristr($html, 'answer/86640') || stristr($html, 'CaptchaRedirect')) {

            // create log
            $ops->create_log_entry('warning', 'rankings', false, __('Google blocked your server from scraping positions. You can use our premium service.', 'off-page-seo'));

            return false;
        }


        if (stristr($html, 'did not match any documents')) {

            // create log
            $ops->create_log_entry('warning', 'rankings', false, __('Nothing found in Google SERP for this keyword.', 'off-page-seo'));

            return false;
        }


        // method 2
        preg_match_all('{<div class="g".*?<div class=".*?"><a href="(.*?)"}', $html, $matches);

        if (empty($matches[1]) || count($matches[1]) == 0) {

            $ops->create_log_entry('warning', 'rankings', false, __('Empty result from Google links.', 'off-page-seo'));
            return false;
        }

        // get our host
        $home_url = get_home_url();
        $home_url_parsed = parse_url($home_url);

        $links = $matches[1];

        $pos = 0;
        foreach ($links as $possible_position) {

            $pos++;

            if (stristr($possible_position, '/url?')) {
                $possible_position_exploded = explode('=', $possible_position);
                $possible_position_exploded = explode('&amp;', $possible_position_exploded[1]);
                $possible_position = $possible_position_exploded[0];
            }

            if (stristr($possible_position, $home_url_parsed['host'])) {

                $possible_position_parsed = parse_url($possible_position);

                if (trim($possible_position_parsed['host']) == trim($home_url_parsed['host'])) {
                    echo $pos;
                    return $pos;
                }
            }
        }

        return 100;

    }

    function get_google_request_url($keyword)
    {
        global $ops;
        $settings = $ops->get_settings();
        $request = 'https://www.google.' . $settings['core_google_domain'] . '/search?hl=' . $settings['core_language'] . '&start=0&q=' . urlencode($keyword) . '&num=100&pws=0&adtest=off';
        return $request;
    }

}
