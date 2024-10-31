<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dashboard Class
 * */
class ops_keyword
{
    function delete_keyword($pid)
    {
        $post_data = array(
            'ID' => $pid,
            'post_status' => 'draft',
        );
        wp_update_post($post_data);
    }

    function add_keyword($title, $wp_id = false, $wp_id_type = false, $main_graph = false)
    {
        // check if keyword exist or not
        $keyword_id = $this->get_keyword_id_by_title($title);

        if (!empty($keyword_id)) {
            // do we have draft status? If so, publish it
            $post_data = array(
                'ID' => $keyword_id,
                'post_status' => 'publish',
            );
            wp_update_post($post_data);

            if (!empty($main_graph) && $main_graph == '1') {
                update_post_meta($keyword_id, 'main_graph', '1');
            }

            if (!empty($wp_id)) {
                update_post_meta($keyword_id, 'wp_id', $wp_id);
                update_post_meta($keyword_id, 'wp_id_type', $wp_id_type);
            }
            return false;
        }

        // we don't have this keyword, let's add one
        $post_data = array(
            'post_title' => $title,
            'post_status' => 'publish',
            'post_type' => 'ops_keyword',
        );

        $pid = wp_insert_post($post_data);

        if (!empty($pid)) {

            if (!empty($wp_id)) {
                update_post_meta($pid, 'wp_id', $wp_id);
                update_post_meta($pid, 'wp_id_type', $wp_id_type);
            }

            if (!empty($main_graph) && $main_graph == '1') {
                update_post_meta($pid, 'main_graph', '1');
            }
        }

    }


    function get_keyword_ranks($keyword_id, $from = false, $to = false)
    {

        if (empty($from)) {
            $from = time() - (60 * 60 * 24 * 30);
        }

        if (empty($to)) {
            $to = time();
        }

        global $wpdb;

        // search posts table
        $q = 'SELECT * FROM ' . $wpdb->prefix . 'ops_rankings WHERE keyword_id = "' . $keyword_id . '" AND time > ' . $from . ' AND time < ' . $to;

        $db_results = $wpdb->get_results($q, ARRAY_A);

        if (!empty($db_results)) {
            $output = [];

            foreach ($db_results as $db_result) {
                $date = date('Ymd', $db_result['time']);
                $output[$date] = $db_result;
            }
            return $output;
        } else {
            return false;
        }

    }


    function get_keyword_id_by_title($keyword)
    {
        global $wpdb;

        // search posts table
        $q = 'SELECT ID FROM ' . $wpdb->prefix . 'posts WHERE post_title = "' . $keyword . '" AND post_type = "ops_keyword" ';

        $db_results = $wpdb->get_results($q, ARRAY_A);

        if (!empty($db_results)) {
            return $db_results[0]['ID'];
        } else {
            return false;
        }
    }


}
