<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Class
 * */
class ops_search_console
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
        add_submenu_page('ops', 'Search Console', 'Search Console', 'activate_plugins', 'ops_search_console', array($this, 'ops_search_console'));
    }

    /**
     * Add administration menu
     * */
    public function ops_search_console()
    {
        ?>

        <?php include(__DIR__ . '/../templates/part-popup.php') ?>

        <div class="wrap">
            <h1><span class="ops-c-gray">Off Page SEO</span> - Search Console</h1>
            <?php include(__DIR__ . '/../templates/part-message.php') ?>
            <?php include(__DIR__ . '/../templates/part-sections.php') ?>

            <?php
            global $ops;
            $google_tokens = $ops->get_settings('ops_google_auth_code');

            $from = time() - (60 * 60 * 24 * 30);
            $to = time();

            if (!empty($google_tokens)) {
                $ops_google = new ops_google();
                $query_data = $ops_google->get_google_data('query', date('Y-m-d', $from), date('Y-m-d', $to));
                $pages_data = $ops_google->get_google_data('page', date('Y-m-d', $from), date('Y-m-d', $to));

                ?>
                <div class="ops-row">
                    <div class="ops-half">
                        <h2>Most successful keywords</h2>
                        <?php if (!empty($query_data)): ?>
                            <table class="widefat ops-table">
                                <tr>
                                    <th>
                                        Keyword
                                    </th>
                                    <th>
                                        Clicks
                                    </th>
                                    <th>
                                        CTR
                                    </th>
                                    <th>
                                        Avg. position
                                    </th>
                                    <th>
                                        Impressions
                                    </th>
                                </tr>

                                <?php foreach ($query_data as $keyword => $item): ?>
                                    <tr>
                                        <td>
                                            <?php echo $keyword ?>
                                        </td>
                                        <td>
                                            <?php echo $item['clicks'] ?>
                                        </td>
                                        <td>
                                            <?php echo round($item['ctr'] * 100) ?>%
                                        </td>
                                        <td>
                                            <?php echo round($item['position']) ?>
                                        </td>
                                        <td>
                                            <?php echo $item['impressions'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else : ?>
                            No data. If your site is new, come back later. Otherwise check <a href="<?php echo get_admin_url() ?>admin.php?page=ops_settings&log=show">log</a> for any errors.
                        <?php endif; ?>
                    </div>

                    <div class="ops-half">
                        <h2>Most successful pages</h2>
                        <?php if (!empty($pages_data)): ?>
                            <table class="widefat ops-table">
                                <tr>
                                    <th>
                                        Page
                                    </th>
                                    <th>
                                        Clicks
                                    </th>
                                    <th>
                                        CTR
                                    </th>
                                    <th>
                                        Avg. position
                                    </th>
                                    <th>
                                        Impressions
                                    </th>
                                </tr>

                                <?php foreach ($pages_data as $page => $item): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo $page ?>">
                                                <?php echo str_replace(get_home_url(), '', $page) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php echo $item['clicks'] ?>
                                        </td>
                                        <td>
                                            <?php echo round($item['ctr'] * 100) ?>%
                                        </td>
                                        <td>
                                            <?php echo round($item['position']) ?>
                                        </td>
                                        <td>
                                            <?php echo $item['impressions'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else : ?>
                            No data. If your site is new, come back later. Otherwise check <a href="<?php echo get_admin_url() ?>admin.php?page=ops_settings&log=show">log</a> for any errors.
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="ops-sorry-message">
                    Please authorize Google Search Console in <a href="<?php echo admin_url() ?>admin.php?page=ops_settings">settings</a>.
                </div>
                <?php
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

                $args = [
                    'post_type' => 'ops_keyword',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                    'posts_per_page' => -1
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
                ?>

                <?php if ($wp_query->have_posts()) : ?>

                    <div class="ops-keyword-table">
                        <table class="widefat ops-table">
                            <tr>
                                <th style="width: 20px;"></th>
                                <th>Keyword</th>
                                <th>Positions</th>
                                <th>Backlinks</th>
                                <th>Actions</th>
                            </tr>
                            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

                                <?php $ranks = ops_get_ranks(get_the_ID()) ?>

                                <tr>
                                    <td><input type="checkbox" name="selected_keyword[]" value="<?php echo get_the_ID() ?>" class="ops-keyword-checkbox"></td>
                                    <td class="keyword">
                                        <?php the_title(); ?>
                                        <?php if (get_post_meta(get_the_ID(), 'main_graph', true)): ?>
                                            <img src="<?php echo plugins_url('off-page-seo/img/icons/star.svg') ?>" alt="icon" class="ops-fa-icon">
                                        <?php endif; ?>
                                    </td>
                                    <td class="previous">
                                        <?php if (!empty($ranks[3])): ?>
                                            <span class="ops-rank"><?php echo $ranks[3]['ranking'] ?></span>
                                            <img src="<?php echo plugins_url('off-page-seo/img/icons/long-arrow-alt-right.svg') ?>" alt="icon" class="ops-fa-icon">
                                        <?php endif; ?>

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
                                    <td class="backlinks">
                                        <?php
                                        $backlinks = ops_get_keyword_backlinks(get_the_ID());
                                        if (!empty($backlinks)) {
                                            ?>
                                            <a href="<?php echo get_admin_url() ?>admin.php?page=ops_backlinks&keyword_id=<?php echo get_the_ID() ?>">
                                                <?php echo count($backlinks); ?>
                                            </a>
                                            <?php
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td class="actions">
                                        <a href="" class="button ops-edit-keyword" data-pid="<?php echo get_the_ID() ?>">
                                            <img src="<?php echo plugins_url('off-page-seo/img/icons/edit.svg') ?>" alt="icon" class="ops-fa-icon">
                                        </a>
                                        <a href="" class="button ops-load-keyword-graph" data-pid="<?php echo get_the_ID() ?>">
                                            <img src="<?php echo plugins_url('off-page-seo/img/icons/chart-bar.svg') ?>" alt="icon" class="ops-fa-icon">
                                        </a>
                                        <?php
                                        $ops_ranking = new ops_ranking();
                                        $url = $ops_ranking->get_google_request_url(get_the_title());
                                        ?>
                                        <a href="<?php echo $url ?>" class="button" target="_blank">
                                            <img src="<?php echo plugins_url('off-page-seo/img/icons/external-link-alt.svg') ?>" alt="icon" class="ops-fa-icon">
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </table>

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
                                Never get blocked by Google
                            </li>
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

new ops_search_console();