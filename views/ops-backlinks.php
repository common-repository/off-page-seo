<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Class
 * */
class ops_backlinks
{

    public function __construct()
    {
        // insert admin menu
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    function admin_menu()
    {
        add_submenu_page('ops', 'Backlinks', 'Backlinks', 'activate_plugins', 'ops_backlinks', array($this, 'ops_backlinks'));
    }

    /**
     * Add administration menu
     * */
    public function ops_backlinks()
    {
        global $ops;
        $settings = $ops->get_settings();
        ?>

        <?php include(__DIR__ . '/../templates/part-popup.php') ?>

        <div class="wrap">
            <h1><span class="ops-c-gray">Off Page SEO</span> - Backlinks</h1>
            <?php include(__DIR__ . '/../templates/part-message.php') ?>
            <?php include(__DIR__ . '/../templates/part-sections.php') ?>

            <a href="" class="button button-primary ops-add-backlink">
                Add backlink
            </a>
            <br><br>

            <div class="ops-row">
                <div class="ops-left">

                    <div class="ops-backlinks-filter">
                        <form action="">
                            <input type="hidden" name="page" value="ops_backlinks">

                            <?php $search = !empty($_GET['search']) ? sanitize_text_field($_GET['search']) : false; ?>
                            <?php $type = !empty($_GET['type']) ? sanitize_text_field($_GET['type']) : false; ?>
                            <?php $keyword_id = !empty($_GET['keyword_id']) ? sanitize_text_field($_GET['keyword_id']) : false; ?>
                            <?php $reciprocal = !empty($_GET['reciprocal']) ? sanitize_text_field($_GET['reciprocal']) : false; ?>
                            <?php $price = !empty($_GET['price']) ? sanitize_text_field($_GET['price']) : false; ?>
                            <?php $from = !empty($_GET['from']) ? sanitize_text_field($_GET['from']) : false; ?>
                            <?php $to = !empty($_GET['to']) ? sanitize_text_field($_GET['to']) : false; ?>

                            <div class="ops-primary-filter">
                                <div class="ops-filter-search">
                                    <input type="text" name="search" value="<?php echo $search ?>" placeholder="Search in URL, note, contact">
                                </div>

                                <div class="ops-filter-type">
                                    <select name="type" id="" class="ops-select2" data-placeholder="Select type">
                                        <option></option>
                                        <option value="backlink" <?php selected('backlink', $type) ?>>Backlink</option>
                                        <option value="article" <?php selected('article', $type) ?>>Article</option>
                                        <option value="comment" <?php selected('comment', $type) ?>>Comment</option>
                                        <option value="sitewide" <?php selected('sitewide', $type) ?>>Sitewide</option>
                                    </select>
                                </div>
                                <?php
                                $args = [
                                    'post_type' => 'ops_keyword',
                                    'orderby' => 'title',
                                    'order' => 'ASC',
                                    'post_status' => 'publish',
                                    'no_found_rows' => true,
                                    'posts_per_page' => -1
                                ];

                                $wp_query = new WP_Query($args);
                                ?>
                                <?php if ($wp_query->have_posts()) : ?>

                                    <div class="ops-filter-keyword">
                                        <select name="keyword_id" id="" class="ops-select2" data-placeholder="Select keyword" data-search="1">
                                            <option></option>
                                            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                                                <option value="<?php echo get_the_ID() ?>" <?php selected(get_the_ID(), $keyword_id) ?>>
                                                    <?php the_title(); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <div class="ops-filter-reciprocal">
                                    <select name="reciprocal" id="" class="ops-select2" data-placeholder="Reciprocal filtering">
                                        <option></option>
                                        <option value="not_active" <?php selected('not_active', $reciprocal) ?>>Not active</option>
                                        <option value="active" <?php selected('active', $reciprocal) ?>>Active</option>
                                        <option value="not_found" <?php selected('not_found', $reciprocal) ?>>Not found</option>
                                        <option value="ok_nofollow" <?php selected('ok_nofollow', $reciprocal) ?>>OK, nofollow</option>
                                        <option value="ok" <?php selected('ok', $reciprocal) ?>>OK</option>
                                    </select>
                                </div>
                            </div>

                            <div class="ops-secondary-filter <?php echo !empty($price) || !empty($from) || !empty($to) ? 'ops-active' : '' ?>">
                                <div class="ops-filter-price">
                                    <select name="price" id="" class="ops-select2" data-placeholder="Price filtering">
                                        <option></option>
                                        <option value="only_with_monthly" <?php selected('only_with_monthly', $price) ?>>Only with monthly price</option>
                                        <option value="empty_price" <?php selected('empty_price', $price) ?>>Empty price</option>
                                        <option value="empty_monthly_price" <?php selected('empty_monthly_price', $price) ?>>Empty monthly price</option>
                                    </select>
                                </div>


                                <div class="ops-filter-time">
                                    <input type="date" name="from" value="<?php echo $from ?>">
                                </div>


                                <div class="ops-filter-time">
                                    <input type="date" name="to" value="<?php echo $to ?>">
                                </div>

                            </div>

                            <div class="ops-filter-submit">
                                <input type="submit" value="Filter" class="button button-primary">
                                <a href="" class="ops-show-secondary-filter">Show more filters</a>
                            </div>

                        </form>
                    </div>
                    <?php
                    if (!empty($search) || !empty($type) || !empty($keyword_id) || !empty($reciprocal) || !empty($price) || !empty($to) || !empty($from)) {
                        ?>
                        <h3>Filters are active (<a href="<?php echo get_admin_url() ?>admin.php?page=ops_backlinks">clear</a>)</h3>
                        <?php
                    }

                    $paged = !empty($_GET['pagenum']) ? sanitize_text_field($_GET['pagenum']) : 1;

                    $args = [
                        'post_type' => 'ops_backlink',
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'posts_per_page' => 12,
                        'paged' => $paged,
                        'meta_query' => [
                            'relation' => 'AND'
                        ]
                    ];

                    if (!empty($search)) {
                        $args['meta_query'][] = [
                            'relation' => 'OR',
                            [
                                'key' => 'url',
                                'value' => $search,
                                'compare' => 'LIKE',
                            ],
                            [
                                'key' => 'comment',
                                'value' => $search,
                                'compare' => 'LIKE',
                            ],
                            [
                                'key' => 'contact',
                                'value' => $search,
                                'compare' => 'LIKE',
                            ],
                        ];
                    }


                    if (!empty($type)) {
                        $args['meta_query'][] = [
                            'key' => 'type',
                            'value' => $type,
                            'compare' => '==',
                        ];
                    }

                    if (!empty($keyword_id)) {
                        $args['meta_query'][] = [
                            'key' => 'keyword_id',
                            'value' => $keyword_id,
                            'compare' => '==',
                        ];
                    }

                    if (!empty($reciprocal)) {

                        if ($reciprocal == 'active') {
                            $args['meta_query'][] = [
                                'key' => 'reciprocal_check',
                                'value' => '1',
                                'compare' => '==',
                            ];
                        }

                        if ($reciprocal == 'not_active') {
                            $args['meta_query'][] = [
                                'relation' => 'OR',
                                [
                                    'key' => 'reciprocal_check',
                                    'compare' => 'NOT EXISTS'
                                ]
                            ];
                        }

                        if ($reciprocal == 'not_found') {
                            $args['meta_query'][] = [
                                'key' => 'reciprocal_check_status',
                                'value' => 'not_found',
                                'compare' => '==',
                            ];
                        }

                        if ($reciprocal == 'ok_nofollow') {
                            $args['meta_query'][] = [
                                'key' => 'reciprocal_check_status',
                                'value' => 'ok_nofollow',
                                'compare' => '==',
                            ];
                        }

                        if ($reciprocal == 'ok') {
                            $args['meta_query'][] = [
                                'key' => 'reciprocal_check_status',
                                'value' => 'ok',
                                'compare' => '==',
                            ];
                        }
                    }

                    if (!empty($price)) {

                        if ($price == 'only_with_monthly') {
                            $args['meta_query'][] = [
                                'key' => 'monthly_price',
                                'compare' => 'EXISTS',
                            ];
                        }

                        if ($price == 'empty_price') {
                            $args['meta_query'][] = [
                                'relation' => 'OR',
                                [
                                    'key' => 'price',
                                    'compare' => 'NOT EXISTS'
                                ],
                                [
                                    'key' => 'price',
                                    'value' => '',
                                    'compare' => '=='
                                ]
                            ];
                        }

                        if ($price == 'empty_monthly_price') {
                            $args['meta_query'][] = [
                                'relation' => 'OR',
                                [
                                    'key' => 'monthly_price',
                                    'compare' => 'NOT EXISTS'
                                ],
                                [
                                    'key' => 'monthly_price',
                                    'value' => '',
                                    'compare' => '=='
                                ]
                            ];
                        }

                    }

                    if (!empty($from)) {
                        $from_timestamp = strtotime($from);

                        $args['date_query'] = [
                            [
                                'after' => array(
                                    'year' => date('Y', $from_timestamp),
                                    'month' => date('n', $from_timestamp),
                                    'day' => date('j', $from_timestamp),
                                ),
                                'inclusive' => true,
                            ],
                        ];
                    }

                    if (!empty($to)) {
                        $to_timestamp = strtotime($to);
                        $args['date_query'] = [
                            [
                                'before' => array(
                                    'year' => date('Y', $to_timestamp),
                                    'month' => date('n', $to_timestamp),
                                    'day' => date('j', $to_timestamp),
                                ),
                                'inclusive' => true,
                            ],
                        ];
                    }

                    $wp_query = new WP_Query($args);
                    ?>

                    <?php if ($wp_query->have_posts()) : ?>
                        <div class="ops-backlink-table">
                            <table class="widefat ops-table">
                                <tr>
                                    <th>URL</th>
                                    <th>Type</th>
                                    <th>Keyword</th>
                                    <th>Price</th>
                                    <th>Price monthly</th>
                                    <th>Reciprocal</th>
                                    <th>Added</th>
                                    <th>Actions</th>
                                </tr>
                                <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                                    <tr>
                                        <td class="url">
                                            <a href="<?php the_title(); ?>" target="_blank">
                                                <?php the_title(); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php echo get_post_meta(get_the_ID(), 'type', true); ?>
                                        </td>
                                        <td>
                                            <?php if ($keyword = get_post_meta(get_the_ID(), 'keyword_id', true)): ?>
                                                <?php echo get_the_title($keyword) ?>
                                            <?php else : ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo !empty(get_post_meta(get_the_ID(), 'price', true)) ? get_post_meta(get_the_ID(), 'price', true) . ' ' . $settings['core_currency'] : '-'; ?>
                                        </td>
                                        <td>
                                            <?php echo !empty(get_post_meta(get_the_ID(), 'monthly_price', true)) ? get_post_meta(get_the_ID(), 'monthly_price', true) . ' ' . $settings['core_currency'] : '-'; ?>
                                        </td>
                                        <td>
                                            <?php ops_the_reciprocal_status(get_the_ID()) ?>
                                        </td>
                                        <td>
                                            <?php the_time($settings['core_date_format']) ?>
                                        </td>
                                        <td>
                                            <a href="" class="button ops-edit-backlink" data-pid="<?php echo get_the_ID() ?>">
                                                <img src="<?php echo plugins_url('off-page-seo/img/icons/edit.svg') ?>" alt="icon" class="ops-fa-icon">
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </table>
                        </div>
                    <?php endif; ?>



                    <?php if ($wp_query->max_num_pages > 1): ?>
                        <ul class="ops-pagination">
                            <?php for ($i = 0; $i < $wp_query->max_num_pages; $i++): ?>
                                <li>
                                    <?php
                                    $query_string = [];
                                    if (!empty($search)) {
                                        $query_string['search'] = $search;
                                    }

                                    if (!empty($type)) {
                                        $query_string['type'] = $type;
                                    }

                                    if (!empty($reciprocal)) {
                                        $query_string['reciprocal'] = $reciprocal;
                                    }

                                    if (!empty($price)) {
                                        $query_string['price'] = $price;
                                    }

                                    if (!empty($from)) {
                                        $query_string['from'] = $from;
                                    }

                                    if (!empty($to)) {
                                        $query_string['to'] = $to;
                                    }

                                    $query_string['pagenum'] = $i + 1;
                                    $query_string['page'] = 'ops_backlinks';

                                    ?>
                                    <a href="<?php echo admin_url() ?>admin.php?<?php echo build_query($query_string) ?>" class="<?php echo $i + 1 == $paged ? 'active' : '' ?>">
                                        <?php echo $i + 1 ?>
                                    </a>
                                </li>
                            <?php endfor ?>
                        </ul>
                    <?php endif; ?>

                    <br><br>
                    <div class="ops-mass-actions">

                        <form action="<?php echo OPS_LISTENER_URL ?>" method="post" class="ops-export-backlinks" target="_blank">
                            <input type="hidden" name="ops_action" value="ops_export_backlinks">

                            <div class="ops-mass-actions-title">
                                Export to .xls
                            </div>

                            <input type="submit" class="button" value="Export all">
                        </form>

                        <div class="ops-clearfix"></div>
                        <div class="ops-mass-action-error"></div>
                        <img src="<?php echo plugins_url('off-page-seo/img/preloader-gray.gif') ?>" alt="preloader" class="ops-preloader">
                    </div>

                    <?php wp_reset_query(); ?>
                </div>
                <div class="ops-right ops-left-padding">
                    <div class="ops-metabox ops-padding-15">
                        <h2>Stats</h2>
                        <hr>
                        <table class="ops-table ops-stats-table ops-fullwidth ops-no-border">
                            <tr>
                                <td>
                                    Current monthly costs
                                </td>
                                <td>
                                    <b>
                                        <?php echo $this->get_total_monthly_spend('monthly_price') ?>&nbsp;<?php echo $settings['core_currency'] ?>
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    This month one-time costs
                                </td>
                                <td>
                                    <b>
                                        <?php echo $this->get_total_monthly_spend('price', date('m'), date('Y')) ?>&nbsp;<?php echo $settings['core_currency'] ?>
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Total one-time costs
                                </td>
                                <td>
                                    <b>
                                        <?php echo $this->get_total_monthly_spend() ?>&nbsp;<?php echo $settings['core_currency'] ?>
                                    </b>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }


    function get_total_monthly_spend($what = 'price', $month = false, $year = false)
    {
        $spend = 0;

        $args = [
            'post_type' => 'ops_backlink',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => $what,
                    'compare' => 'EXISTS',
                ],
                [
                    'key' => $what,
                    'compare' => '!=',
                    'value' => ''
                ],
                [
                    'key' => $what,
                    'compare' => '!=',
                    'value' => '0'
                ]
            ]
        ];

        if (!empty($month) && !empty($year)) {

            $ymd = $year . $month . '01';
            $start = new DateTime($ymd);

            $args['date_query'][] = [
                'after' => array(
                    'year' => $start->format('Y'),
                    'month' => $start->format('n'),
                    'day' => $start->format('j'),
                ),
                'inclusive' => true,
            ];

            $start->add(new DateInterval('P1M'));

            $args['date_query'][] = [
                'before' => array(
                    'year' => $start->format('Y'),
                    'month' => $start->format('n'),
                    'day' => $start->format('j'),
                ),
                'inclusive' => false,
            ];
        }

        $wp_query = new WP_Query($args);

        if ($wp_query->have_posts()) :
            while ($wp_query->have_posts()) : $wp_query->the_post();
                $this_spend = get_post_meta(get_the_ID(), $what, true);

                $spend = $spend + $this_spend;
            endwhile;
        endif;

        return $spend;
    }

}

new ops_backlinks();