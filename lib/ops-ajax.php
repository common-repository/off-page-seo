<?php

class ops_ajax
{

    function __construct()
    {

        add_action('wp_admin_footer', array($this, 'insert_ajax_url'));

        add_action('wp_ajax_ops_load_add_keyword', array($this, 'ops_load_add_keyword'));
        add_action('wp_ajax_ops_load_edit_keyword', array($this, 'ops_load_edit_keyword'));
        add_action('wp_ajax_ops_load_add_keyword_group', array($this, 'ops_load_add_keyword_group'));
        add_action('wp_ajax_ops_add_keyword', array($this, 'ops_add_keyword'));
        add_action('wp_ajax_ops_add_keyword_group', array($this, 'ops_add_keyword_group'));
        add_action('wp_ajax_ops_remove_keyword_group', array($this, 'ops_remove_keyword_group'));
        add_action('wp_ajax_ops_add_selected_keywords_to_group', array($this, 'ops_add_selected_keywords_to_group'));
        add_action('wp_ajax_ops_remove_selected_keywords_from_group', array($this, 'ops_remove_selected_keywords_from_group'));
        add_action('wp_ajax_ops_edit_keyword', array($this, 'ops_edit_keyword'));
        add_action('wp_ajax_ops_delete_keyword', array($this, 'ops_delete_keyword'));
        add_action('wp_ajax_ops_delete_selected_keywords', array($this, 'ops_delete_selected_keywords'));
        add_action('wp_ajax_ops_search_content', array($this, 'ops_search_content'));
        add_action('wp_ajax_ops_load_keyword_graph', array($this, 'ops_load_keyword_graph'));

        add_action('wp_ajax_ops_revoke_google_search_console_access', array($this, 'ops_revoke_google_search_console_access'));


        add_action('wp_ajax_ops_load_add_backlink', array($this, 'ops_load_add_backlink'));
        add_action('wp_ajax_ops_load_edit_backlink', array($this, 'ops_load_edit_backlink'));
        add_action('wp_ajax_ops_add_backlink', array($this, 'ops_add_backlink'));
        add_action('wp_ajax_ops_edit_backlink', array($this, 'ops_edit_backlink'));
        add_action('wp_ajax_ops_delete_backlink', array($this, 'ops_delete_backlink'));
        add_action('wp_ajax_ops_load_backlinks_for_date', array($this, 'ops_load_backlinks_for_date'));


        add_action('wp_ajax_ops_premium_sign_up', array($this, 'ops_premium_sign_up'));
        add_action('wp_ajax_ops_load_forget_premium', array($this, 'ops_load_forget_premium'));
        add_action('wp_ajax_ops_forget_premium', array($this, 'ops_forget_premium'));

        add_action('wp_ajax_ops_load_add_existing_api_key', array($this, 'ops_load_add_existing_api_key'));
        add_action('wp_ajax_ops_add_existing_api_key', array($this, 'ops_add_existing_api_key'));


    }

    function check_user_rights()
    {
        // check rights for plugin usage
        global $ops;
        if ($ops->current_user_can_control_plugin() == false) {
            wp_die('Insufficient rights.');
        }
    }


    /* KEYWORDS */
    function ops_load_add_keyword()
    {
        $this->check_user_rights();

        ?>
        <form action="" class="ops-form ops-form-add-keyword">
            <input type="hidden" name="action" value="ops_add_keyword">
            <h3>Add a new keywords</h3>
            <textarea name="keywords" placeholder="One keyword per line" autocomplete="off"></textarea>
            <br><br>
            <input type="radio" name="wp_id_type" checked value="0" id="assign_to_none" data-input-placeholder="Post ID"> <label for="assign_to_none">Don't assign</label>
            <input type="radio" name="wp_id_type" value="post" id="assign_to_post" data-input-placeholder="Post ID"> <label for="assign_to_post">Assign to post</label>
            <input type="radio" name="wp_id_type" value="term" id="assign_to_term" data-input-placeholder="Term ID"> <label for="assign_to_term">Assign to term</label>
            <br>
            <div class="ops-search">
                <div class="left">
                    <input type="text" name="search" placeholder="Search content or add ID directly --->" autocomplete="off">
                    <div class="ops-search-content-output"></div>
                </div>
                <div class="right">
                    <input type="number" name="wp_id" placeholder="">
                </div>
            </div>
            <br>
            <input type="checkbox" name="main_graph" value="1" id="main_graph"> <label for="main_graph">Add to main graph</label>
            <br><br>
            <input type="submit" class="button button-primary" value="Add keyword">
            <img src="<?php echo plugins_url('off-page-seo/img/preloader.gif') ?>" alt="preloader" class="ops-preloader">
        </form>
        <?php
        exit;
    }

    function ops_load_add_keyword_group()
    {
        $this->check_user_rights();

        ?>
        <form action="" class="ops-form ops-form-add-keyword-group">
            <input type="hidden" name="action" value="ops_add_keyword_group">
            <h3>Add a new keyword group</h3>
            <input type="text" name="name" placeholder="Group name" autocomplete="off">
            <br><br>
            <input type="submit" class="button button-primary" value="Add keyword">
            <img src="<?php echo plugins_url('off-page-seo/img/preloader.gif') ?>" alt="preloader" class="ops-preloader">
        </form>
        <?php
        exit;
    }

    function ops_load_edit_keyword()
    {
        $this->check_user_rights();

        $pid = sanitize_text_field($_POST['pid']);
        $main_graph = get_post_meta($pid, 'main_graph', true);
        $wp_id = get_post_meta($pid, 'wp_id', true);
        $wp_id_type = get_post_meta($pid, 'wp_id_type', true);
        ?>
        <form action="" class="ops-form ops-form-edit-keyword">
            <input type="hidden" name="action" value="ops_edit_keyword">
            <input type="hidden" name="pid" value="<?php echo $pid ?>">
            <h3>Edit keyword</h3>
            <input type="text" name="keyword" autocomplete="off" disabled value="<?php echo get_the_title($pid) ?>">
            <br><br>
            <input type="radio" name="wp_id_type" <?php echo empty($wp_id_type) ? 'checked' : '' ?> value="0" id="assign_to_none" data-input-placeholder="Post ID"> <label for="assign_to_none">Don't assign</label>
            <input type="radio" name="wp_id_type" <?php echo !empty($wp_id_type) && $wp_id_type == 'post' ? 'checked' : '' ?> value="post" id="assign_to_post" data-input-placeholder="Post ID"> <label for="assign_to_post">Assign to post</label>
            <input type="radio" name="wp_id_type" <?php echo !empty($wp_id_type) && $wp_id_type == 'term' ? 'checked' : '' ?> value="term" id="assign_to_term" data-input-placeholder="Term ID"> <label for="assign_to_term">Assign to term</label>
            <br>
            <div class="ops-search <?php echo !empty($wp_id_type) ? 'active' : '' ?>">
                <div class="left">
                    <input type="text" name="search" placeholder="Search ..." autocomplete="off">
                    <div class="ops-search-content-output"></div>
                </div>
                <div class="right">
                    <input type="number" name="wp_id" placeholder="" value="<?php echo $wp_id ?>">
                </div>
            </div>
            <br>
            <input type="checkbox" name="main_graph" value="1" id="main_graph" <?php echo !empty($main_graph) ? 'checked' : '' ?> > <label for="main_graph">Add to main graph</label>
            <br><br>
            <input type="submit" class="button button-primary" value="Edit keyword">
        </form>
        <br>
        <hr>

        <form action="" class="ops-form ops-form-delete-keyword">
            <input type="hidden" name="action" value="ops_delete_keyword">
            <input type="hidden" name="pid" value="<?php echo $pid ?>">
            <h3>Delete keyword</h3>
            <div class="ops-row">
                <div class="ops-left">
                    <input type="checkbox" name="confirm_deletion" value="1" id="confirm_deletion"> <label for="confirm_deletion">Confirm action</label>
                </div>
                <div class="ops-right">
                    <input type="submit" class="button ops-float-right" value="Delete keyword">
                </div>
            </div>

        </form>
        <?php
        exit;
    }

    function ops_add_keyword()
    {

        $this->check_user_rights();

        if (empty($_POST['keywords'])) {
            $data = ['message' => 'Empty keyword!'];
            wp_send_json_error($data);
        }

        $keywords = explode(PHP_EOL, $_POST['keywords']);

        if (empty($keywords)) {
            $data = ['message' => 'Error separating keywords.'];
            wp_send_json_error($data);
        }

        $wp_id = !empty($_POST['wp_id']) ? sanitize_text_field($_POST['wp_id']) : '';
        $wp_id_type = !empty($_POST['wp_id_type']) ? sanitize_text_field($_POST['wp_id_type']) : '';
        $main_graph = !empty($_POST['main_graph']) ? sanitize_text_field($_POST['main_graph']) : '';

        foreach ($keywords as $keyword) {

            if (trim($keyword) == '') {
                continue;
            }

            $keyword = sanitize_text_field($keyword);

            $ops_keyword = new ops_keyword();
            $ops_keyword->add_keyword($keyword, $wp_id, $wp_id_type, $main_graph);
        }

        $data = ['url' => get_admin_url() . 'admin.php?page=ops&status=success&msg=' . urlencode('Keyword was added.')];
        wp_send_json_success($data);

    }


    function ops_add_keyword_group()
    {
        $this->check_user_rights();

        if (empty($_POST['name'])) {
            $data = ['message' => 'Empty name!'];
            wp_send_json_error($data);
        }

        $name = sanitize_text_field($_POST['name']);

        $term = get_term_by('name', $name, 'ops_keyword_cat');

        if (!empty($term)) {
            $data = ['message' => 'Group already exists.'];
            wp_send_json_error($data);
        }

        wp_insert_term($name, 'ops_keyword_cat');

        $data = ['url' => get_admin_url() . 'admin.php?page=ops&group=' . sanitize_title($name) . '&status=success&msg=' . urlencode('Keyword was added.')];
        wp_send_json_success($data);
    }

    function ops_remove_keyword_group()
    {
        $this->check_user_rights();

        if (empty($_POST['group_id'])) {
            $data = ['message' => 'Group empty!'];
            wp_send_json_error($data);
        }

        $group_id = sanitize_text_field($_POST['group_id']);

        wp_delete_term($group_id, 'ops_keyword_cat');

        $data = ['url' => get_admin_url() . 'admin.php?page=ops&status=success&msg=' . urlencode('Keyword group was deleted.')];
        wp_send_json_success($data);
    }


    function ops_add_selected_keywords_to_group()
    {

        $this->check_user_rights();

        $cat_id = !empty($_POST['group']) ? sanitize_text_field($_POST['group']) : '';

        if (empty($cat_id)) {
            $data = ['message' => 'Empty group!'];
            wp_send_json_error($data);
        }


        if (!empty($_POST['keyword_ids'])) {
            foreach ($_POST['keyword_ids'] as $keyword_id) {

                wp_set_post_terms($keyword_id, [$cat_id], 'ops_keyword_cat', true);

            }
        }

        $term = get_term_by('id', $cat_id, 'ops_keyword_cat');

        $data = ['url' => get_admin_url() . 'admin.php?page=ops&group=' . $term->slug . '&status=success&msg=' . urlencode('Keywords added to the group.')];
        wp_send_json_success($data);

    }

    function ops_remove_selected_keywords_from_group()
    {

        $this->check_user_rights();

        $group = !empty($_POST['group']) ? sanitize_text_field($_POST['group']) : '';

        if (empty($group)) {
            $data = ['message' => 'Empty group!'];
            wp_send_json_error($data);
        }


        if (!empty($_POST['keyword_ids'])) {
            foreach ($_POST['keyword_ids'] as $keyword_id) {

                ops_remove_post_term($keyword_id, $group, 'ops_keyword_cat');

            }
        }

        $data = ['url' => get_admin_url() . 'admin.php?page=ops&group=' . $group . '&status=success&msg=' . urlencode('Keywords added to the group.')];
        wp_send_json_success($data);

    }


    function ops_edit_keyword()
    {
        $this->check_user_rights();

        global $ops;
        $pid = false;

        if (!empty($_POST['pid'])) {
            $pid = sanitize_text_field($_POST['pid']);
        }

        if (empty($pid)) {
            $ops->create_log_entry('error', 'keyword', false, 'Empty PID in ajax call.');
            $data = ['message' => 'Empty PID in ajax call.'];
            wp_send_json_error($data);
        }

        // check post type
        if (get_post_type($pid) != 'ops_keyword') {
            $ops->create_log_entry('error', 'keyword', false, 'PID is not a keyword!');
            $data = ['message' => 'PID is not a keyword!'];
            wp_send_json_error($data);
        }

        // update shits
        update_post_meta($pid, 'wp_id', sanitize_text_field($_POST['wp_id']));
        update_post_meta($pid, 'wp_id_type', sanitize_text_field($_POST['wp_id_type']));

        if (!empty($_POST['main_graph'])) {
            update_post_meta($pid, 'main_graph', '1');
        } else {
            delete_post_meta($pid, 'main_graph');
        }

        $data = ['url' => get_admin_url() . 'admin.php?page=ops&status=success&msg=' . urlencode('Keyword was updated.')];
        wp_send_json_success($data);


    }

    function ops_delete_keyword()
    {
        $this->check_user_rights();

        global $ops;
        $pid = false;

        if (!empty($_POST['pid'])) {
            $pid = sanitize_text_field($_POST['pid']);
        }

        if (empty($pid)) {
            $ops->create_log_entry('error', 'keyword', false, 'Empty PID in ajax call.');
            $data = ['message' => 'Empty PID in ajax call.'];
            wp_send_json_error($data);
        }

        // check post type
        if (get_post_type($pid) != 'ops_keyword') {
            $ops->create_log_entry('error', 'keyword', false, 'PID is not a keyword!');
            $data = ['message' => 'PID is not a keyword!'];
            wp_send_json_error($data);
        }

        $confirm = !empty($_POST['confirm_deletion']) ? true : false;
        if (empty($confirm)) {
            $data = ['message' => 'Please confirm deletion.'];
            wp_send_json_error($data);
        }

        $ops_keyword = new ops_keyword();
        $ops_keyword->delete_keyword($pid);


        $data = ['url' => get_admin_url() . 'admin.php?page=ops&status=success&msg=' . urlencode('Keyword was deleted.')];
        wp_send_json_success($data);

        exit;

    }

    function ops_delete_selected_keywords()
    {
        $this->check_user_rights();

        if (!empty($_POST['keyword_ids'])) {
            foreach ($_POST['keyword_ids'] as $keyword_id) {
                $ops_keyword = new ops_keyword();
                $ops_keyword->delete_keyword($keyword_id);

            }
        }

        $data = ['url' => get_admin_url() . 'admin.php?page=ops&status=success&msg=' . urlencode('Keywords were deleted.')];
        wp_send_json_success($data);

        exit;

    }

    function ops_search_content()
    {
        $this->check_user_rights();

        $s = sanitize_text_field($_POST['search']);
        $type = !empty($_POST['type']) ? sanitize_text_field($_POST['type']) : 'post';

        if ($type == 'post') {

            $args = [
                'post_type' => 'any',
                'orderby' => 'date',
                'order' => 'DESC',
                's' => $s,
                'no_found_rows' => true,
                'posts_per_page' => 15
            ];

            $wp_query = new WP_Query($args);

            if ($wp_query->have_posts()) :
                while ($wp_query->have_posts()) : $wp_query->the_post();
                    ?>
                    <div class="ops-search-content" data-pid="<?php echo get_the_ID() ?>"><?php the_title() ?></div>
                <?php
                endwhile;
                ?>
                <div class="ops-search-content-ok">
                    <i class="far fa-check"></i> Post ID set up.
                </div>
            <?php
            else:
                ?>
                <div class="ops-search-content-nothing">
                    <i class="far fa-surprise"></i> Nothing found.
                </div>

            <?php
            endif;
        }

        if ($type == 'term') {
            global $wpdb;

            $q = 'SELECT * FROM ' . $wpdb->prefix . 'terms WHERE name LIKE "%%' . $s . '%%"';
            $db_results = $wpdb->get_results($q, ARRAY_A);

            if (!empty($db_results)) {
                foreach ($db_results as $db_result) {
                    ?>
                    <div class="ops-search-content" data-pid="<?php echo $db_result['term_id'] ?>"><?php echo $db_result['name'] ?></div>
                    <?php
                }
                ?>
                <div class="ops-search-content-ok">
                    <i class="far fa-check"></i> Term ID set up.
                </div>
                <?php
            } else {
                ?>

                <div class="ops-search-content-nothing">
                    <i class="far fa-surprise"></i> Nothing found.
                </div>
                <?php
            }

        }

        exit;
    }

    function ops_load_keyword_graph()
    {
        $this->check_user_rights();


        $keyword_id = sanitize_text_field($_POST['pid']);

        ?>
        <h3><?php echo get_the_title($keyword_id) ?></h3>
        <?php
        include(__DIR__ . '/../templates/part-ranking-graph.php');

        exit;
    }


    function ops_revoke_google_search_console_access()
    {
        $this->check_user_rights();

        global $ops;
        $ops->delete_settings('ops_google_auth_code');
        $ops->delete_settings('ops_google_tokens');

        $data = ['url' => get_admin_url() . 'admin.php?page=ops_settings&status=success&msg=' . urlencode('Access revoked.')];
        wp_send_json_success($data);

        exit;
    }


    /* BACKLINKS */

    function ops_load_add_backlink()
    {
        $this->check_user_rights();

        ?>
        <form action="" class="ops-form ops-form-add-backlink">
            <input type="hidden" name="action" value="ops_add_backlink">
            <h3>Add a new backlink</h3>
            <label for="" class="ops-label">URL</label>
            <input type="text" name="url" placeholder="" autocomplete="off">

            <br><br>

            <div class="ops-row">
                <div class="ops-half">
                    <label for="" class="ops-label">Type</label>
                    <select name="type" id="" class="ops-select2">
                        <option value="backlink">Backlink</option>
                        <option value="article">Article</option>
                        <option value="comment">Comment</option>
                        <option value="sitewide">Sitewide</option>
                    </select>
                </div>
                <div class="ops-half">
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
                        <label for="" class="ops-label">Keyword</label>
                        <select name="keyword_id" id="" class="ops-select2" data-placeholder="Assign to keywoard" data-search="1">
                            <option></option>
                            <option value="0">None</option>
                            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                                <option value="<?php echo get_the_ID() ?>"><?php the_title() ?></option>
                            <?php endwhile; ?>
                        </select>
                    <?php endif; ?>
                </div>
            </div>
            <br>
            <div class="ops-row">
                <div class="ops-half">
                    <label for="" class="ops-label">One time price</label>
                    <input type="number" name="price" placeholder="">
                </div>
                <div class="ops-half">
                    <label for="" class="ops-label">Monthly price</label>
                    <input type="number" name="monthly_price" placeholder="">
                </div>
            </div>
            <br>
            <input type="checkbox" name="reciprocal_check" id="reciprocal_check" value="1" checked> <label for="reciprocal_check">Reciprocal check</label>
            <em style="position: relative; top: -1px;">(we will check your link each 7 days if is still in place)</em>
            <br><br>

            <div class="ops-row">
                <div class="ops-half">
                    <label for="" class="ops-label">Date added (dd/mm/yyyy)</label>
                    <input type="date" name="date" placeholder="" autocomplete="off" value="<?php echo date('Y-m-d', time()) ?>" max="<?php echo date('Y-m-d', time()) ?>">
                    <br><br>
                </div>
            </div>

            <label for="" class="ops-label">Note</label>
            <input type="text" name="comment" placeholder="" autocomplete="off">
            <br><br>
            <label for="" class="ops-label">Contact</label>
            <input type="text" name="contact" placeholder="Email address, phone number">
            <br><br>
            <input type="submit" class="button button-primary" value="Add keyword">
            <img src="<?php echo plugins_url('off-page-seo/img/preloader.gif') ?>" alt="preloader" class="ops-preloader">
        </form>
        <?php
        exit;
    }

    function ops_load_edit_backlink()
    {
        $this->check_user_rights();

        $pid = sanitize_text_field($_POST['pid']);
        $url = get_post_meta($pid, 'url', true);
        $type = get_post_meta($pid, 'type', true);
        $keyword_id = get_post_meta($pid, 'keyword_id', true);
        $reciprocal_check = get_post_meta($pid, 'reciprocal_check', true);

        $price = get_post_meta($pid, 'price', true);
        $monthly_price = get_post_meta($pid, 'monthly_price', true);

        $comment = get_post_meta($pid, 'comment', true);
        $contact = get_post_meta($pid, 'contact', true);

        $post = get_post($pid);
        ?>
        <form action="" class="ops-form ops-form-edit-backlink">
            <input type="hidden" name="action" value="ops_edit_backlink">
            <input type="hidden" name="pid" value="<?php echo $pid ?>">
            <h3>Add a new backlink</h3>
            <label for="" class="ops-label">URL</label>
            <input type="text" name="url" placeholder="URL" autocomplete="off" value="<?php echo $url ?>">
            <br><br>
            <div class="ops-row">
                <div class="ops-half">
                    <label for="" class="ops-label">Type</label>
                    <select name="type" id="" class="ops-select2">
                        <option value="backlink" <?php selected('backlink', $type) ?>>Backlink</option>
                        <option value="article" <?php selected('article', $type) ?>>Article</option>
                        <option value="comment" <?php selected('comment', $type) ?>>Comment</option>
                        <option value="sitewide" <?php selected('sitewide', $type) ?>>Sitewide</option>
                    </select>
                </div>
                <div class="ops-half">
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
                        <label for="" class="ops-label">Keyword</label>
                        <select name="keyword_id" id="" class="ops-select2" data-placeholder="Assign to keywoard" data-search="1">
                            <option></option>
                            <option value="0" <?php selected('0', $keyword_id) ?>>None</option>
                            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                                <option value="<?php echo get_the_ID() ?>" <?php selected(get_the_ID(), $keyword_id) ?>><?php the_title() ?></option>
                            <?php endwhile; ?>
                        </select>
                    <?php endif; ?>
                </div>
            </div>

            <br>
            <div class="ops-row">
                <div class="ops-half">
                    <label for="" class="ops-label">One time price</label>
                    <input type="number" name="price" placeholder="One time price" value="<?php echo $price ?>">
                </div>
                <div class="ops-half">
                    <label for="" class="ops-label">Monthly price</label>
                    <input type="number" name="monthly_price" placeholder="Monthly price" value="<?php echo $monthly_price ?>">
                </div>
            </div>
            <br>
            <input type="checkbox" name="reciprocal_check" id="reciprocal_check" value="1" <?php echo !empty($reciprocal_check) ? 'checked' : '' ?>> <label for="reciprocal_check">Reciprocal check</label>
            <em style="position: relative; top: -1px;">(we will check your link each 7 days if is still in place)</em>
            <br><br>


            <div class="ops-row">
                <div class="ops-half">
                    <label for="" class="ops-label">Date added (dd/mm/yyyy)</label>
                    <input type="date" name="date" placeholder="" autocomplete="off" value="<?php echo date('Y-m-d', strtotime($post->post_date)) ?>" max="<?php echo date('Y-m-d', time()) ?>">
                    <br><br>
                </div>
            </div>


            <label for="" class="ops-label">Note</label>
            <input type="text" name="comment" placeholder="" autocomplete="off" value="<?php echo $comment ?>">
            <br><br>
            <label for="" class="ops-label">Contact</label>
            <input type="text" name="contact" placeholder="Email address, phone number" value="<?php echo $contact ?>">
            <br><br>
            <input type="submit" class="button button-primary" value="Edit keyword">
        </form>
        <br>
        <hr>

        <form action="" class="ops-form ops-form-delete-backlink">
            <input type="hidden" name="action" value="ops_delete_backlink">
            <input type="hidden" name="pid" value="<?php echo $pid ?>">
            <h3>Delete backlink</h3>
            <div class="ops-row">
                <div class="ops-left">
                    <input type="checkbox" name="confirm_deletion" value="1" id="confirm_deletion"> <label for="confirm_deletion">Confirm deletion</label>
                </div>
                <div class="ops-right">
                    <input type="submit" class="button ops-float-right" value="Delete backlink">
                </div>
            </div>

        </form>
        <?php
        exit;
    }

    function ops_add_backlink()
    {
        $this->check_user_rights();


        if (empty($_POST['url'])) {
            $data = ['message' => 'Empty URL!'];
            wp_send_json_error($data);
        }


        $post_data = array(
            'post_title' => sanitize_text_field($_POST['url']),
            'post_status' => 'publish',
            'post_type' => 'ops_backlink',
            'post_date' => sanitize_text_field($_POST['date']) . ' 00:01:00'
        );

        $pid = wp_insert_post($post_data);

        if (!empty($pid)) {

            unset($_POST['date']);
            unset($_POST['action']);

            foreach ($_POST as $key => $value) {
                update_post_meta($pid, sanitize_text_field($key), sanitize_text_field($value));
            }

            update_post_meta($pid, 'reciprocal_check_status', 'not_checked');
        }

        $data = ['url' => get_admin_url() . 'admin.php?page=ops_backlinks&status=success&msg=' . urlencode('Backlink was added.')];
        wp_send_json_success($data);

    }

    function ops_edit_backlink()
    {
        $this->check_user_rights();

        global $ops;
        $pid = false;

        if (!empty($_POST['pid'])) {
            $pid = sanitize_text_field($_POST['pid']);
        }

        if (empty($pid)) {
            $ops->create_log_entry('error', 'backlink', false, 'Error #1029x. Empty PID in ajax call.');
            $data = ['message' => 'Empty PID in ajax call.'];
            wp_send_json_error($data);
        }

        // check post type
        if (get_post_type($pid) != 'ops_backlink') {
            $ops->create_log_entry('error', 'backlink', false, 'PID is not a backlink!');
            $data = ['message' => 'PID is not a backlink!'];
            wp_send_json_error($data);
        }

        foreach ($_POST as $key => $value) {
            update_post_meta($pid, sanitize_text_field($key), sanitize_text_field($value));
        }

        if (empty($_POST['reciprocal_check'])) {
            delete_post_meta($pid, 'reciprocal_check');
        }

        $args = [
            'ID' => $pid,
            'post_title' => sanitize_text_field($_POST['url']),
            'post_date' => sanitize_text_field($_POST['date']) . ' 00:01:00'
        ];

        wp_update_post($args);

        $data = ['url' => get_admin_url() . 'admin.php?page=ops_backlinks&status=success&msg=' . urlencode('Backlink was updated.')];
        wp_send_json_success($data);


    }

    function ops_delete_backlink()
    {
        $this->check_user_rights();

        global $ops;
        $pid = false;

        if (!empty($_POST['pid'])) {
            $pid = sanitize_text_field($_POST['pid']);
        }

        if (empty($pid)) {
            $ops->create_log_entry('error', 'backlink', false, 'Empty backlink PID in ajax call.');
            $data = ['message' => 'Empty backlink PID in ajax call.'];
            wp_send_json_error($data);
        }

        // check post type
        if (get_post_type($pid) != 'ops_backlink') {
            $ops->create_log_entry('error', 'backlink', false, 'PID is not a backlink!');
            $data = ['message' => 'PID is not a keyword!'];
            wp_send_json_error($data);
        }

        $confirm = !empty($_POST['confirm_deletion']) ? true : false;
        if (empty($confirm)) {
            $data = ['message' => 'Please confirm deletion.'];
            wp_send_json_error($data);
        }

        wp_delete_post($pid, true);

        $data = ['url' => get_admin_url() . 'admin.php?page=ops_backlinks&status=success&msg=' . urlencode('Backlink was deleted.')];
        wp_send_json_success($data);

        exit;

    }

    function ops_load_backlinks_for_date()
    {
        $this->check_user_rights();

        $date = sanitize_text_field($_POST['date']);
        $timestamp = strtotime($date);
        $keyword_id = !empty($_POST['keyword_id']) ? sanitize_text_field($_POST['keyword_id']) : false;

        $args = [
            'post_type' => 'ops_backlink',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => -1,
            'date_query' => [
                [
                    'year' => date('Y', $timestamp),
                    'month' => date('n', $timestamp),
                    'day' => date('j', $timestamp),
                ]
            ],
        ];

        if (!empty($keyword_id)) {
            $args['meta_query'] = [
                [
                    'key' => 'keyword_id',
                    'value' => $keyword_id,
                    'compare' => '=='
                ],
            ];
        }

        $wp_query = new WP_Query($args);

        global $ops;
        $settings = $ops->get_settings();
        ?>
        <h3>
            Backlinks for <?php echo date($settings['core_date_format'], $timestamp) ?>
            <?php if (!empty($keyword_id)) : ?>
                and <?php echo get_the_title($keyword_id) ?>
            <?php endif; ?>
        </h3>
        <?php
        if ($wp_query->have_posts()) :
            ?>
            <table class="widefat ops-table">
                <tr>
                    <th>URL</th>
                    <th>Type</th>
                    <th>Keyword</th>
                </tr>
                <?php
                while ($wp_query->have_posts()) : $wp_query->the_post();
                    ?>
                    <tr>
                        <td class="url">
                            <a href="<?php the_title(); ?>" target="_blank" class="ops-small-link">
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
                    </tr>
                <?php
                endwhile;
                ?>
            </table>
            <br>

            <a href="<?php echo get_admin_url() ?>admin.php?page=ops_backlinks&date=<?php echo $timestamp ?>&keyword_id=<?php echo $keyword_id ?>" class="button">More details</a>
        <?php
        endif;
        exit;
    }



    /* OTHER */
    function ops_premium_sign_up()
    {
        $this->check_user_rights();


        if (empty($_POST['email'])) {
            $data = ['message' => 'Please enter data.'];
            wp_send_json_error($data);
        }

        foreach ($_POST as $key => $value) {
            $body[sanitize_text_field($key)] = sanitize_text_field($value);
        }

        $body['home_url'] = get_home_url();

        $args = [
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8'
            ],
            'body' => json_encode($body),
            'cookies' => []
        ];

        $url = OPS_API_URL . 'signup/';

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {

            global $ops;
            $ops->create_log_entry('error', 'api', false, __('Error #u29v4. API POST request error. Response: ', 'off-page-seo') . $response->get_error_message());

            $data = ['message' => 'Error API call. Check log for more details.'];
            wp_send_json_error($data);

        } else {
            $body = json_decode($response['body']);

            if (!empty($body->data->status) && $body->data->status == 471) {
                $send = ['message' => $body->message];
                wp_send_json_error($send);
            }

            if (empty($body->api_key)) {
                $send = ['message' => 'Error getting API key. Please contact us with this message.'];
                wp_send_json_error($send);
            }
        }

        global $ops;
        $ops->save_settings($body->api_key, 'ops_api_key');

        // clear rank update so we can do next batch with premium
        wp_clear_scheduled_hook('ops_rank_update');

        wp_send_json_success($body);
    }

    function ops_load_forget_premium()
    {
        $this->check_user_rights();

        ?>
        <form action="" class="ops-form ops-form-forget-premium">
            <input type="hidden" name="action" value="ops_forget_premium">
            <h3>Forget premium account data</h3>
            <p>
                This action will remove API key and all data associated with your account on <a href="https://offpageseo.io">offpageseo.io</a> website.
            </p>
            <div class="ops-row">
                <div class="ops-left">
                    <input type="checkbox" name="confirm_action" value="1" id="confirm_action"> <label for="confirm_action">Confirm action</label>
                </div>
                <div class="ops-right">
                    <input type="submit" class="button ops-float-right" value="Forget now!">
                </div>
            </div>

            <div class="ops-error"></div>


        </form>
        <?php
        exit;
    }

    function ops_forget_premium()
    {
        $this->check_user_rights();

        if (empty($_POST['confirm_action'])) {
            $send = ['message' => 'Please confirm action.'];
            wp_send_json_error($send);
        }

        global $ops;
        $ops->delete_settings('ops_api_key');
        delete_transient('ops_premium_account_data');

        // clear rank update so we can do next batch with local
        wp_clear_scheduled_hook('ops_rank_update');

        $data = ['url' => get_admin_url() . 'admin.php?page=ops_premium&status=success&msg=' . urlencode('Premium account data deleted.')];
        wp_send_json_success($data);

        exit;

    }


    function ops_load_add_existing_api_key()
    {
        $this->check_user_rights();

        ?>
        <form action="" class="ops-form ops-form-add-existing-api-key">
            <input type="hidden" name="action" value="ops_add_existing_api_key">
            <h3>Add existing API key</h3>
            <p>
                This website will be linked to your account using API key.
            </p>
            <div class="ops-row">
                <input type="text" name="api_key" placeholder="Your API key">
            </div>
            <br>
            <div class="ops-row">
                <input type="submit" class="button button-primary" value="Submit">
            </div>

            <div class="ops-error"></div>

        </form>
        <?php
        exit;
    }

    function ops_add_existing_api_key()
    {
        $this->check_user_rights();

        if (empty($_POST['api_key'])) {
            $send = ['message' => 'Empty API key.'];
            wp_send_json_error($send);
        }

        global $ops;

        // save api key first
        $ops->save_settings(sanitize_text_field($_POST['api_key']), 'ops_api_key');

        // clear rank update so we can do next batch with premium
        wp_clear_scheduled_hook('ops_rank_update');

        // delete transient shit
        delete_transient('ops_premium_account_data');

        $data = ['url' => get_admin_url() . 'admin.php?page=ops_premium&status=success&msg=' . urlencode('API key added.')];
        wp_send_json_success($data);

        exit;

    }


    /* OTHER */
    function insert_ajax_url()
    {
        ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
        <?php
    }

}


new ops_ajax();