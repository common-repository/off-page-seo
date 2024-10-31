<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Class
 * */
class ops_tools
{

    public function __construct()
    {
        // insert admin menu
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    function admin_menu()
    {
        add_submenu_page('ops', 'Tools', 'Tools', 'activate_plugins', 'ops_tools', array($this, 'ops_tools'));
    }


    /**
     * Render Settings form
     * */
    function ops_tools()
    {

        // display message that settings was updated
        if (isset($_POST['_wpnonce']) && check_admin_referer('nt_nonce_ops_tools') == 1) {

            if ($_POST['ops_action'] == 'schedule_rank_update') {
                $this->schedule_rank_update();

                ?>
                <div class="updated" style="padding: 8px 20px;">
                    Please reaload the site. Scheduled successfully to run in 30 seconds.
                </div>
                <?php
            }


            if ($_POST['ops_action'] == 'schedule_reciprocal_check') {
                $this->schedule_reciprocal_check();

                ?>
                <div class="updated" style="padding: 8px 20px;">
                    Scheduled successfully to run in 30 seconds.
                </div>
                <?php
            }

        }

        global $ops;
        $settings = $ops->get_settings();


        ?>


        <div class="wrap">
            <h1><span class="ops-c-gray">Off Page SEO</span> - Tools</h1>
            <?php include(__DIR__ . '/../templates/part-message.php') ?>
            <?php include(__DIR__ . '/../templates/part-sections.php') ?>


            <div class="ops-row">

                <div class="ops-half">
                    <div class="ops-tool-box">
                        <h2>Request rank update</h2>
                        <hr>
                        <form method="POST" action="" class="ops-form">
                            <input type="hidden" name="ops_action" value="schedule_rank_update">
                            <?php wp_nonce_field('nt_nonce_ops_tools'); ?>

                            <p>
                                Next rank update is scheduled on <b><?php echo date($settings['core_date_format'] . ' H:i:s', wp_next_scheduled('ops_rank_update')) ?></b>. You can schedule update to run in 30 seconds.
                                <br><br>

                                <?php if ($ops->is_premium() === true): ?>
                                    It takes us up to 45 minutes to figure out positions for you. Please be patient once you request a new rank update. You can always check the progress in your account in <a href="https://offpageseo.io/user/requests/" target="_blank">offpageseo.io</a> administration.
                                    <br><br>
                                <?php endif; ?>
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Request">
                            </p>

                        </form>
                    </div>
                </div>

                <div class="ops-half">
                    <div class="ops-tool-box">
                        <h2>Schedule reciprocal check</h2>
                        <hr>
                        <form method="POST" action="" class="ops-form ops-settings-form">
                            <input type="hidden" name="ops_action" value="schedule_reciprocal_check">
                            <?php wp_nonce_field('nt_nonce_ops_tools'); ?>


                            <p>
                                Next reciprocal check is scheduled on <b><?php echo date($settings['core_date_format'] . ' H:i:s', wp_next_scheduled('ops_reciprocal_check')) ?></b>. You can schedule update to run in 30 seconds.
                                <br><br>
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Schedule">
                            </p>

                        </form>
                    </div>
                </div>


            </div>
        </div>
        <?php
    }

    function schedule_rank_update()
    {

        wp_clear_scheduled_hook('ops_rank_update');

        if (defined('OPS_DEBUG') && OPS_DEBUG == true) {
            echo "Live run:";

            $ops_ranking = new ops_ranking();
            echo $ops_ranking->update_ranks();

        } else {
            ?>
            <script>
                // location.reload();
            </script>
            <?php
        }

    }

    function schedule_reciprocal_check()
    {
        wp_clear_scheduled_hook('ops_reciprocal_check');
        wp_schedule_event(time() + 30, 'ops_seven_days_interval', 'ops_reciprocal_check');
    }


}


new ops_tools();