<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Class
 * */
class ops_dashboard
{

    /**
     * Initialization
     * */
    public function __construct()
    {
        // insert admin menu
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    function admin_menu()
    {
        add_menu_page('Off Page SEO', 'Off Page SEO', 'read', 'ops', array($this, 'render_dashboard'), 'dashicons-groups', '2.0981816');
    }

    /**
     * Add administration menu
     * */
    public function render_dashboard()
    {

        ?>

        <?php include(__DIR__ . '/../templates/part-popup.php') ?>

        <div class="wrap">
            <h1>Off Page SEO</h1>
            <?php include(__DIR__ . '/../templates/part-message.php') ?>
            <?php include(__DIR__ . '/../templates/part-sections.php') ?>

            <?php
            if (empty($_GET['tab'])) {
                $this->keywords_overview();
            }
            ?>
        </div>
        <?php
    }


    function keywords_overview()
    {
        global $ops;
        $settings = $ops->get_settings();

        ?>

        <a href="" class="button button-primary ops-add-keyword">
            Add keywords
        </a>

        <br>
        <br>

        <div class="ops-row">
            <div class="ops-left">

                <?php include(__DIR__ . '/../templates/part-ranking-graph-categories.php') ?>

                <?php include(__DIR__ . '/../templates/part-ranking-graph.php') ?>

                <?php

                $group = !empty($_GET['group']) ? sanitize_text_field($_GET['group']) : false;

                $paged = !empty($_GET['pagenum']) ? sanitize_text_field($_GET['pagenum']) : 1;

                $args = [
                    'post_type' => 'ops_keyword',
                    'orderby' => 'date',
                    'paged' => $paged,
                    'order' => 'DESC',
                    'post_status' => 'publish',
                    'posts_per_page' => 50
                ];

                if (!empty($group)) {
                    $args['tax_query'] = [
                        [
                            'taxonomy' => 'ops_keyword_cat',
                            'field' => 'slug',
                            'terms' => $group
                        ]
                    ];
                }

                $wp_query = new WP_Query($args);

                $google_access_token = $ops->get_settings('ops_google_auth_code');
                $google_data = [];

                if (!empty($google_access_token)) {

                    $from = time() - (60 * 60 * 24 * 30);
                    $to = time();

                    $ops_google = new ops_google();
                    $google_data = $ops_google->get_google_data('query', date('Y-m-d', $from), date('Y-m-d', $to));

                }
                ?>

                <?php if ($wp_query->have_posts()) : ?>

                    <div class="ops-keyword-table">
                        <table class="widefat ops-table">
                            <tr>
                                <th style="width: 17px;"></th>
                                <th>Keyword</th>
                                <th>Positions</th>
                                <?php if (!empty($google_data)): ?>
                                    <th>Clicks</th>
                                    <th>Avg. pos.</th>
                                    <th>Impressions</th>
                                    <th>CTR</th>
                                <?php else: ?>
                                    <th>
                                        Search Console Data
                                    </th>
                                <?php endif; ?>
                            </tr>
                            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

                                <?php $ranks = ops_get_ranks(get_the_ID()) ?>
                                <tr>
                                    <td><input type="checkbox" name="selected_keyword[]" value="<?php echo get_the_ID() ?>" class="ops-keyword-checkbox"></td>
                                    <td class="keyword">
                                        <div class="ops-keyword-wrapper">
                                            <?php
                                            $backlinks = ops_get_keyword_backlinks(get_the_ID());
                                            ?>
                                            <div class="ops-keyword-control <?php echo !empty($backlinks) ? 'has-backlinks' : '' ?>">
                                                <a href="" class="ops-edit-keyword" data-pid="<?php echo get_the_ID() ?>">
                                                    <img src="<?php echo plugins_url('off-page-seo/img/icons/edit.svg') ?>" alt="icon" class="ops-fa-icon">
                                                </a>

                                                <?php
                                                if (!empty($backlinks)) {
                                                    ?>
                                                    <a href="<?php echo get_admin_url() ?>admin.php?page=ops_backlinks&keyword_id=<?php echo get_the_ID() ?>">
                                                        <img src="<?php echo plugins_url('off-page-seo/img/icons/link.svg') ?>" alt="icon" class="ops-fa-icon ops-link">
                                                    </a>
                                                    <?php
                                                }
                                                ?>

                                                <?php
                                                $ops_ranking = new ops_ranking();
                                                $url = $ops_ranking->get_google_request_url(get_the_title());
                                                ?>
                                                <a href="<?php echo $url ?>" target="_blank">
                                                    <img src="<?php echo plugins_url('off-page-seo/img/icons/external-link-alt.svg') ?>" alt="icon" class="ops-fa-icon ops-external-link">
                                                </a>

                                            </div>

                                            <a href="" class="ops-load-keyword-graph" data-pid="<?php echo get_the_ID() ?>">
                                                <?php the_title(); ?>
                                            </a>
                                        </div>
                                        <?php if (get_post_meta(get_the_ID(), 'main_graph', true)): ?>
                                            <img src="<?php echo plugins_url('off-page-seo/img/icons/star.svg') ?>" alt="icon" class="ops-fa-icon">
                                        <?php endif; ?>
                                    </td>
                                    <td class="previous">
                                        <?php if (!empty($ranks[2])): ?>
                                            <span class="ops-rank"><?php echo $ranks[2]['ranking'] ?></span>
                                            <img src="<?php echo plugins_url('off-page-seo/img/icons/long-arrow-alt-right.svg') ?>" alt="icon" class="ops-fa-icon">
                                        <?php endif; ?>

                                        <?php if (!empty($ranks[1])): ?>
                                            <span class="ops-rank"><?php echo $ranks[1]['ranking'] ?></span>
                                            <img src="<?php echo plugins_url('off-page-seo/img/icons/long-arrow-alt-right.svg') ?>" alt="icon" class="ops-fa-icon">
                                        <?php endif; ?>

                                        <?php if (!empty($ranks[0])): ?>
                                            <span class="ops-rank current">
                                                <b><?php echo $ranks[0]['ranking'] ?></b>
                                            </span>

                                            <?php if (!empty($ranks[1])): ?>
                                                <?php ops_get_ranks_difference($ranks[1]['ranking'], $ranks[0]['ranking']); ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>

                                    <?php if (!empty($google_data)): ?>
                                        <td>
                                            <?php if (!empty($google_data[get_the_title()])): ?>
                                                <?php echo $google_data[get_the_title()]['clicks'] ?>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if (!empty($google_data[get_the_title()])): ?>
                                                <?php echo round($google_data[get_the_title()]['position']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($google_data[get_the_title()])): ?>
                                                <?php echo $google_data[get_the_title()]['impressions'] ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>

                                            <?php if (!empty($google_data[get_the_title()])): ?>
                                                <?php echo round($google_data[get_the_title()]['ctr'] * 100) ?>%
                                            <?php endif; ?>
                                        </td>
                                    <?php else: ?>
                                        <td>
                                            <a href="<?php echo get_admin_url() ?>admin.php?page=ops_settings">Connect</a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        </table>

                        <?php if ($wp_query->max_num_pages > 1): ?>
                            <ul class="ops-pagination">
                                <?php for ($i = 0; $i < $wp_query->max_num_pages; $i++): ?>
                                    <li>
                                        <?php
                                        $query_string = [];
                                        if (!empty($group)) {
                                            $query_string['group'] = $group;
                                        }

                                        $query_string['pagenum'] = $i + 1;
                                        $query_string['page'] = 'ops';

                                        ?>
                                        <a href="<?php echo admin_url() ?>admin.php?<?php echo build_query($query_string) ?>" class="<?php echo $i + 1 == $paged ? 'active' : '' ?>">
                                            <?php echo $i + 1 ?>
                                        </a>
                                    </li>
                                <?php endfor ?>
                            </ul>
                        <?php endif; ?>


                        <?php if (!empty($group)): ?>
                            <a href="<?php echo get_admin_url() ?>admin.php?page=ops" class="ops-back-to-all-keywords">
                                <img src="<?php echo plugins_url('off-page-seo/img/icons/long-arrow-alt-left.svg') ?>" alt="icon" class="ops-fa-icon">
                                Back to all keywords
                            </a>
                        <?php endif; ?>

                        <div class="ops-select-all-keywords">
                            <input type="checkbox" name="select_all_keywords" value="1"> Select all
                        </div>

                        <div class="ops-mass-actions">

                            <form class="ops-add-selected-keywords-to-group">
                                <input type="hidden" name="action" value="ops_add_selected_keywords_to_groups">
                                <div class="ops-mass-actions-title">
                                    Assign to group
                                </div>
                                <?php
                                $args = array(
                                    'taxonomy' => 'ops_keyword_cat',
                                    'orderby' => 'name',
                                    'order' => 'ASC',
                                    'hide_empty' => false,
                                    'include' => 'all',
                                    'exclude' => 'all',
                                    'exclude_tree' => 'all',
                                    'number' => 100
                                );
                                $terms = get_terms($args);
                                ?>

                                <?php if (!empty($terms)): ?>
                                    <select name="group" id="" class="ops-select2" data-placeholder="Select group">
                                        <option></option>
                                        <?php foreach ($terms as $term): ?>
                                            <option value="<?php echo $term->term_id ?>">
                                                <?php echo $term->name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                                <input type="submit" class="button" value="Assign selected">
                            </form>

                            <?php if (!empty($group)): ?>
                                <div class="ops-remove-selected-keywords-from-group-wrapper">
                                    <div class="ops-mass-actions-title">
                                        Remove from current group
                                    </div>
                                    <a href="" class="button ops-remove-selected-keywords-from-group" data-group="<?php echo $group ?>">Remove selected</a>
                                </div>
                            <?php endif; ?>

                            <div class="ops-delete-selected-keywords-wrapper">
                                <div class="ops-mass-actions-title">
                                    Delete keywords
                                </div>
                                <a href="" class="button ops-button-red ops-delete-selected-keywords">Delete selected</a>
                            </div>


                            <div class="ops-clearfix"></div>
                            <div class="ops-mass-action-error"></div>
                            <img src="<?php echo plugins_url('off-page-seo/img/preloader-gray.gif') ?>" alt="preloader" class="ops-preloader">
                        </div>
                        <br><br>
                        Next rank update: <b><?php echo date($settings['core_date_format'] . ' H:i:s', wp_next_scheduled('ops_rank_update')) ?></b>. <a href="<?php echo get_admin_url() ?>admin.php?page=ops_tools">Manage</a>. <br>
                        Type: <b><?php echo $ops->is_premium() ? 'premium' : 'free' ?></b>. <?php if (!$ops->is_premium()): ?><a href="<?php echo get_admin_url() ?>admin.php?page=ops_premium">Get premium</a>. <?php endif; ?> <br>
                        Don't see any results? Check the <a href="<?php echo get_admin_url() ?>admin.php?page=ops_settings&log=show">log</a>.

                    </div>
                <?php endif; ?>
                <?php wp_reset_query(); ?>
            </div>
            <div class="ops-right ops-left-padding">
                <?php if ($ops->is_premium() === false): ?>
                    <div class="ops-premium-banner">
                        <div class="ops-premium-title">
                            Get more power with Premium
                        </div>
                        <ul>
                            <li>
                                Get consistent results
                            </li>
                            <li>
                                Unlimited keywords
                            </li>
                            <li>
                                Not really that expensive :)
                            </li>
                        </ul>
                        <a href="<?php echo get_admin_url() ?>admin.php?page=ops_premium" class="button button-primary">
                            Sign up
                        </a>
                        <div class="ops-example-pricing">
                            $3/month for 100 keywords
                        </div>
                        <img src="<?php echo plugins_url('off-page-seo/img/mascot.png') ?>" alt="premium mascot">
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

}

new ops_dashboard();