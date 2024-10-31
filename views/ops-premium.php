<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Class
 * */
class ops_premium
{

    public function __construct()
    {
        // insert admin menu
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    function admin_menu()
    {
        add_submenu_page('ops', 'Premium', 'Premium', 'activate_plugins', 'ops_premium', array($this, 'ops_premium'));
    }

    /**
     * Initialization
     * */
    function ops_premium()
    {
        // renders settings form
        $this->render_premium_dashboard();
    }


    /**
     * Render Settings form
     * */
    public function render_premium_dashboard()
    {


        global $ops;
        $settings = $ops->get_settings();

        //        if($ops->is_premium()){
        //            echo "Y";
        //        } else {
        //            echo "N";
        //        }
        ?>
        <?php include(__DIR__ . '/../templates/part-popup.php') ?>

        <div class="wrap">
            <h1><span class="ops-c-gray">Off Page SEO</span> - Premium</h1>
            <?php include(__DIR__ . '/../templates/part-message.php') ?>
            <?php include(__DIR__ . '/../templates/part-sections.php') ?>

            <?php if ($ops->is_premium() === false): ?>

                <div class="ops-row">
                    <div class="ops-how-it-works">
                        <h2 class="ops-text-center">
                            How to get premium features
                        </h2>

                        <div class="ops-steps-wrapper">
                            <div class="ops-step">
                                <div class="ops-number">
                                    1
                                </div>
                                <div class="ops-title">
                                    Download addon plugin
                                </div>
                                <div class="ops-info">
                                    <p>
                                        Addon is installed along with the standard version of the plugin.
                                    </p>
                                    <a href="<?php echo OPS_URL ?>/premium/off-page-seo-premium-addon.zip" class="button button-primary" target="_blank">
                                        Download now
                                    </a>
                                </div>
                            </div>

                            <div class="ops-step">
                                <div class="ops-number">
                                    2
                                </div>
                                <div class="ops-title">
                                    Install the plugin
                                </div>
                                <div class="ops-info">
                                    <p>
                                        Click on "<b>Upload Plugin</b>" next to the title on this page.
                                    </p>
                                    <a href="<?php echo get_admin_url() ?>plugin-install.php" class="button button-primary" target="_blank">
                                        Install now
                                    </a>
                                </div>
                            </div>


                            <div class="ops-step">
                                <div class="ops-number">
                                    3
                                </div>
                                <div class="ops-title">
                                    Activate the plugin
                                </div>
                                <div class="ops-info">
                                    <p>
                                        Now, simply <b>activate</b> the plugin.
                                    </p>
                                    <a href="<?php echo get_admin_url() ?>plugins.php" class="button button-primary" target="_blank">
                                        Go and activate
                                    </a>
                                </div>
                            </div>


                            <div class="ops-step">
                                <div class="ops-number">
                                    4
                                </div>
                                <div class="ops-title">
                                    Sign up
                                </div>
                                <div class="ops-info">
                                    <p>
                                        Reload this page and <b>sign up</b> for a new account or add an existing API key.
                                    </p>
                                    <a href="<?php echo get_admin_url() ?>admin.php?page=ops_premium" class="button button-primary">
                                        Reload
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>


            <div class="ops-row">
                <div class="ops-half">
                    <div class="ops-pricing-calculator">
                        <div class="ops-calculator">
                            <h2>Pricing calculator</h2>
                            <hr>
                            <div class="ops-cell">
                                <label for="keywords">How many keywords do you have?</label>
                                <input type="number" name="keywords" min="1" autocomplete="off" data-per-request="0.003">
                            </div>

                            <div class="ops-cell">
                                <label for="period">How often do you want to update?</label>
                                <select name="period" id="" class="ops-select2">
                                    <option value="1">Daily</option>
                                    <option value="2">Once every 2 days</option>
                                    <option value="3" selected>Once every 3 days</option>
                                    <option value="4">Once every 4 days</option>
                                    <option value="5">Once every 5 days</option>
                                    <option value="6">Once every 6 days</option>
                                    <option value="7">Once every 7 days</option>
                                </select>
                            </div>

                            <div class="ops-result">
                                <span>Estimated costs:</span> <span class="ops-estimated-costs">-</span>/month
                            </div>

                            <?php if ($ops->is_premium()): ?>
                                <a href="<?php echo OPS_URL ?>user/" class="button button-primary" target="_blank">Add credit</a>
                            <?php endif; ?>
                        </div>

                        <div class="ops-pricing">
                            <div class="ops-icon">
                                <img src="<?php echo plugins_url('off-page-seo/img/icons/usd-circle.svg') ?>" alt="icon" class="ops-fa-icon">
                            </div>
                            <div class="ops-title">
                                Keyword SERP
                            </div>
                            <div class="ops-basic-price">
                                <b>$0.003</b>/requested position
                            </div>
                            <div class="ops-description">
                                This means around <b>$3/month</b> for 100 keywords that are checked every 3 days.
                            </div>
                        </div>

                    </div>

                </div>

                <div class="ops-half">
                    <?php
                    if ($ops->is_premium()) {
                        $nt_ops_premium_addon = new ops_premium_addon();
                        $nt_ops_premium_addon->render_signup_form();
                    } else {
                        ?>
                        <div class="ops-metabox"></div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }


}


new ops_premium();