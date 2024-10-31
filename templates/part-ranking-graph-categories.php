<div class="ops-graph-categories">
    <?php
    $args = array(
        'taxonomy' => 'ops_keyword_cat',
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => false,
        'number' => 50,
    );
    $terms = get_terms($args);
    ?>
    <ul>
        <li>
            <div class="ops-group-wrapper">
                <a href="<?php echo get_admin_url() ?>admin.php?page=ops" class="<?php echo empty($_GET['group']) ? 'active' : '' ?>">
                    Main
                </a>
            </div>
        </li>
        <?php if (!empty($terms)): ?>
            <?php foreach ($terms as $term): ?>
                <li>
                    <div class="ops-group-wrapper">
                        <div class="ops-total-keywords">
                            <?php echo $term->count ?>&nbsp;<?php echo $term->count == 1 ? 'keyword' : 'keywords' ?>
                        </div>
                        <a href="<?php echo get_admin_url() ?>admin.php?page=ops&group=<?php echo $term->slug ?>" class="<?php echo !empty($_GET['group']) && $_GET['group'] == $term->slug ? 'active' : '' ?>">
                            <?php echo $term->name ?>
                        </a>
                        <div class="ops-remove-keyword-group" data-group-id="<?php echo $term->term_id ?>">
                            <img src="<?php echo plugins_url('off-page-seo/img/icons/trash-alt.svg') ?>" alt="icon" class="ops-fa-icon">
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <li>
            <div class="ops-group-wrapper">
                <a href="" class="ops-add-keyword-group">
                    + Add group
                </a>
            </div>
        </li>
    </ul>
</div>