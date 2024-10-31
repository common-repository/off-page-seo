<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Class
 * */
class ops_reciprocal
{
    /**
     * Reasonable limit for reciprocal check is 1500 backlinks
     */
    function run_reciprocal_check()
    {
        global $ops;

        $reciprocal_start = $ops->get_settings('ops_reciprocal_start');

        if (empty($reciprocal_start)) {
            $reciprocal_start = time();
            $ops->save_settings($reciprocal_start, 'ops_reciprocal_start');
        }


        $settings = $ops->get_settings();

        $args = [
            'post_type' => 'ops_backlink',
            'posts_per_page' => 5,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'reciprocal_check',
                    'value' => '1',
                    'compare' => '=='
                ],
                [
                    'relation' => 'OR',
                    [
                        'key' => 'reciprocal_check_last',
                        'compare' => 'NOT EXISTS'
                    ],
                    [
                        'key' => 'reciprocal_check_last',
                        'type' => 'numeric',
                        'value' => $reciprocal_start,
                        'compare' => '<='
                    ],
                ],
            ],
        ];

        $wp_query = new WP_Query($args);

        if ($wp_query->have_posts()) :
            while ($wp_query->have_posts()) : $wp_query->the_post();
                $result = $this->test_backlink(get_the_ID());

                update_post_meta(get_the_ID(), 'reciprocal_check_last', time());

                if ($result === false) {
                    update_post_meta(get_the_ID(), 'reciprocal_check_status', 'failed');
                }

                if ($result === 1) {

                    update_post_meta(get_the_ID(), 'reciprocal_check_status', 'not_found');

                    $url = get_post_meta(get_the_ID(), 'url', true);
                    $parsed_url = parse_url($url);

                    $ops->create_log_entry('notification', 'reciprocal', get_the_ID(), 'We could not find your backlink: ' . $url);

                    ob_start();
                    ?>
                    Dear user, <br>
                    we run a regular reciprocal check and we could not find your link on this website:
                    <br><br>
                    <a href="<?php echo $url ?>" target="_blank">
                        <?php echo $url ?>
                    </a>
                    <br><br>
                    If you don't want us to send you this notification again, please remove reciprocal check from this backlink.
                    <br><br>
                    <a href="<?php echo get_admin_url() ?>admin.php?page=ops_backlinks" target="_blank">
                        View all your backlinks.
                    </a>
                    <br><br>
                    Regards,<br>
                    Off Page SEO plugin
                    <?php
                    $message = ob_get_contents();
                    ob_end_clean();

                    $email = new ops_email();
                    $email->set_body($message);
                    $email->send_email($settings['core_email'], 'Link not found on ' . $parsed_url['host']);

                }

                if ($result === 2) {
                    update_post_meta(get_the_ID(), 'reciprocal_check_status', 'nofollow');
                }

                if ($result === 3) {
                    update_post_meta(get_the_ID(), 'reciprocal_check_status', 'ok');
                }

            endwhile;
        endif;

        if ($wp_query->found_posts > 5) {
            // set new cron
            wp_schedule_single_event(time() + 60, 'ops_reciprocal_check');
        } else {
            global $ops;
            $ops->create_log_entry('error', 'info', false, 'Finished CRON reciprocal check.');
            $ops->delete_settings('ops_reciprocal_start');
        }
    }

    function test_backlink($pid)
    {

        $url = get_post_meta($pid, 'url', true);

        /* *********** WP REMOTE GET *********** */
        $args = [
            'timeout' => 15,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => [],
            'cookies' => []
        ];


        $response = wp_remote_get($url, $args);
        if (is_wp_error($response)) {
            return false;
        } else {

            $test_url = get_home_url();

            $dom = new DOMDocument();
            @$dom->loadHTML($response['body']);
            $xpath = new DOMXPath($dom);
            $hrefs = $xpath->evaluate("/html/body//a");
            $result = $this->is_my_link_there($hrefs, $test_url);
            return $result;
        }

    }

    /**
     * 1 - not found
     * 2 - nofollow
     * 3 - all ok
     *
     * @param $hrefs
     * @param $my_url
     * @return int
     */
    function is_my_link_there($hrefs, $my_url)
    {
        for ($i = 0; $i < $hrefs->length; $i++) {
            $href = $hrefs->item($i);
            $url = $href->getAttribute('href');
            if (str_replace('/', '', $my_url) == str_replace('/', '', $url)) {
                $rel = $href->getAttribute('rel');
                if ($rel == 'nofollow') {
                    return 2;
                }
                return 3;
            }
        }
        return 1;
    }

}

