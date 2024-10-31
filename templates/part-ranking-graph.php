<?php
global $ops;
$settings = $ops->get_settings();

$from = time() - (60 * 60 * 24 * 30);
$to = time();

$group = !empty($_GET['group']) ? sanitize_text_field($_GET['group']) : false;

if (!empty($group)) {

    $args = [
        'post_type' => 'ops_keyword',
        'orderby' => 'date',
        'order' => 'DESC',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'tax_query' => [
            [
                'taxonomy' => 'ops_keyword_cat',
                'field' => 'slug',
                'terms' => $group
            ]
        ]
    ];

    $wp_query = new WP_Query($args);

    $keyword_ids = [];
    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()) : $wp_query->the_post();
            $keyword_ids[] = get_the_ID();
        endwhile;
    endif;


    // empty, use main graph data
    $backlink_args = [
        'post_type' => 'ops_backlink',
        'orderby' => 'date',
        'order' => 'DESC',
        'posts_per_page' => -1,
        'date_query' => [
            [
                'after' => array(
                    'year' => date('Y', $from),
                    'month' => date('n', $from),
                    'day' => date('j', $from),
                ),
                'before' => array(
                    'year' => date('Y', $to),
                    'month' => date('n', $to),
                    'day' => date('j', $to),
                ),
                'inclusive' => true,
            ],
        ],
        'meta_query' => [
            [
                'key' => 'keyword_id',
                'value' => $keyword_ids,
                'compare' => 'IN'
            ],
        ]
    ];


} else {

    $args = [
        'post_type' => 'ops_keyword',
        'orderby' => 'date',
        'order' => 'DESC',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'main_graph',
                'value' => '1',
                'compare' => '=='
            ],
        ],
    ];

    if (!empty($keyword_id)) {
        unset($args['meta_query']);
        $args['post__in'] = [$keyword_id];
    }

    $wp_query = new WP_Query($args);


    // empty, use main graph data
    $backlink_args = [
        'post_type' => 'ops_backlink',
        'orderby' => 'date',
        'order' => 'DESC',
        'posts_per_page' => -1,
        'date_query' => [
            [
                'after' => array(
                    'year' => date('Y', $from),
                    'month' => date('n', $from),
                    'day' => date('j', $from),
                ),
                'before' => array(
                    'year' => date('Y', $to),
                    'month' => date('n', $to),
                    'day' => date('j', $to),
                ),
                'inclusive' => true,
            ],
        ],
    ];

    if (!empty($keyword_id)) {
        $backlink_args['meta_query'] = [
            [
                'key' => 'keyword_id',
                'value' => $keyword_id,
                'compare' => '=='
            ],
        ];
    }

}


$backlink_wp_query = new WP_Query($backlink_args);

$backlinks = [];
if ($backlink_wp_query->have_posts()) :
    while ($backlink_wp_query->have_posts()) : $backlink_wp_query->the_post();
        global $post;
        $backlinks['items'][date('Ymd', strtotime($post->post_date))][] = get_the_ID();
    endwhile;
endif;

if (!empty($backlinks['items'])) {
    $highest = 0;
    foreach ($backlinks['items'] as $date => $day_links) {
        if (count($day_links) > $highest) {
            $highest = count($day_links);
        }
    }

    $backlinks['highest'] = $highest;
}

?>
<?php if ($wp_query->have_posts()) : ?>
    <div class="ops-graph-wrapper">
        <div class="ops-graph">
            <div class="ops-graph-inner">

                <div class="lines">
                    <div class="line line-0"></div>
                    <div class="line line-1"></div>
                    <div class="line line-2"></div>
                    <div class="line line-3"></div>
                    <div class="line line-4"></div>
                </div>

                <div class="x-axis">
                    <?php for ($i = 0; $i < 30; $i++) : ?>
                        <div class="x-value x-value-<?php echo $i ?>" style="right: <?php echo round(100 / 30 * $i, 2) ?>%">
                            <?php
                            $date = date($settings['core_date_format'], time() - (60 * 60 * 24 * $i));
                            echo $date;
                            ?>
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="y-axis">
                    <?php for ($i = 10; $i >= 0; $i--) : ?>
                        <div class="y-value y-value-<?php echo $i ?>" style="top: <?php echo round(100 / 10 * $i) ?>%">
                            <?php
                            echo $i == 0 ? 1 : $i * 10;
                            ?>
                        </div>
                    <?php endfor; ?>


                </div>

                <?php for ($i = 10; $i >= 0; $i--) : ?>
                    <div class="y-line y-line-<?php echo $i ?>" style="top: <?php echo round(100 / 10 * $i) ?>%"></div>
                <?php endfor; ?>

                <div class="values">
                    <?php
                    $i = 0;
                    $keywords = [];
                    while ($wp_query->have_posts()) : $wp_query->the_post();
                        $colour = ops_get_graph_colour($i);
                        $keywords[get_the_ID()] = [
                            'title' => get_the_title(),
                            'color' => $colour
                        ];
                        $i++;

                        $ops_keyword = new ops_keyword();
                        $rankings = $ops_keyword->get_keyword_ranks(get_the_ID(), $from, $to);

                        $previous_x = '';
                        $previous_y = '';

                        if (!empty($rankings)) {
                            foreach ($rankings as $day => $rank) {
                                $y = $rank['ranking'];
                                $x = 100 / 30 * round(($to - $rank['time']) / (60 * 60 * 24));
                                ?>
                                <div class="value value-<?php echo get_the_ID() ?>" style="top: <?php echo $y ?>%;right: <?php echo $x ?>%; background-color:<?php echo $colour ?>">
                                    <div class="text">
                                        <b> <?php echo get_the_title() ?></b> <br>
                                        <?php echo date($settings['core_date_format'], $rank['time']) ?> <br>
                                        Position: <?php echo $rank['ranking'] ?><br>
                                    </div>
                                </div>

                                <?php if (!empty($previous_x)): ?>
                                    <?php
                                    // calculate length
                                    $a = ($previous_y - $y) * 0.3;
                                    $b = $previous_x - $x;
                                    $c = round(sqrt($a * $a + $b * $b), 5);

                                    $sin = round($b / $c, 5);

                                    if ($a < 0) {
                                        $rotate = 90 - rad2deg(asin($sin));
                                    } else {
                                        $rotate = rad2deg(asin($sin)) - 90;
                                    }
                                    ?>
                                    <div class="lane lane-<?php echo get_the_ID() ?>" style="top: <?php echo $y ?>%;right: <?php echo $x ?>%;width: <?php echo $c ?>%; transform: rotate(<?php echo $rotate ?>deg);background-color:<?php echo $colour ?>;">

                                    </div>
                                <?php endif; ?>
                                <?php

                                $previous_x = $x;
                                $previous_y = $y;
                            }
                        }
                    endwhile;
                    ?>

                    <?php if (!empty($backlinks['items'])): ?>
                        <div class="backlinks">
                            <?php foreach ($backlinks['items'] as $date => $item): ?>
                                <?php
                                $x = 100 / 30 * round(($to - strtotime($date)) / (60 * 60 * 24));
                                $height = count($item) / $backlinks['highest'] * 100;
                                ?>
                                <div class="backlink" data-date="<?php echo $date ?>" style="right:<?php echo $x ?>%; height: <?php echo $height ?>%" data-keyword-id="<?php echo !empty($keyword_id) ? $keyword_id : false ?>">
                                    <div class="total">
                                        <div class="number">
                                            <?php echo count($item) ?> new backlinks
                                        </div>
                                        <div class="more">(click for detail)</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>

            </div>

        </div>

        <?php if (!empty($keywords)): ?>
            <div class="ops-graph-filter">
                <div class="ops-filter-graph-title">
                    Filter keywords:
                </div>
                <?php foreach ($keywords as $pid => $keyword): ?>
                    <div class="ops-filter-graph-keyword" data-id="<?php echo $pid ?>" style="border-color:<?php echo $keyword['color'] ?>; color:<?php echo $keyword['color'] ?>">
                        <?php echo $keyword['title'] ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="ops-sorry-message">
        <?php if (!empty($group)): ?>
            Start by adding some keywords to the group.
        <?php else: ?>
            Start by adding some keywords.
        <?php endif; ?>
    </div>
<?php endif; ?>