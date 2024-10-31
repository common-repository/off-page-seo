<?php

class ops_listener
{

    public $secure_data;

    function __construct()
    {
        if (is_admin() == false) {
            return false;
        }


        // _GET
        if (isset($_GET['ops_action'])) {
            $this->sanitize_input($_GET);

            add_action('init', array($this, 'listen_for_get_actions'));
        }

        // _POST
        if (isset($_POST['ops_action']) && $_POST['ops_action'] != '') {
            $this->sanitize_input($_POST);

            add_action('init', array($this, 'listen_for_post_actions'));
        }

    }


    function listen_for_get_actions()
    {
        $nt_action = !empty($_GET['ops_action']) ? sanitize_text_field($_GET['ops_action']) : false;


    }

    function listen_for_post_actions()
    {

        $nt_action = !empty($_POST['ops_action']) ? sanitize_text_field($_POST['ops_action']) : false;

        if ($nt_action == 'ops_export_backlinks') {
            $this->export_backlinks();
        }


    }

    function export_backlinks()
    {

        // logged
        if (!is_user_logged_in()) {
            return false;
        }

        // check rights first
        global $ops;
        $settings = $ops->get_settings();

        if (current_user_can($settings['core_permissions']) == false) {
            return false;
        }


        $data = [
            [
                'Added',
                'URL',
                'Type',
                'Price',
                'Monthly Price',
                'Date',
                'Comment',
                'Contact',
                'Status',
            ]
        ];

        // get backlinks now

        $args = [
            'post_type' => 'ops_backlink',
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
            'posts_per_page' => -1
        ];

        $wp_query = new WP_Query($args);

        if ($wp_query->have_posts()) :
            while ($wp_query->have_posts()) : $wp_query->the_post();
                global $post;
                $data[] = [
                    $post->post_date,
                    get_post_meta(get_the_ID(), 'url', true),
                    get_post_meta(get_the_ID(), 'type', true),
                    get_post_meta(get_the_ID(), 'price', true),
                    get_post_meta(get_the_ID(), 'monthly_price', true),
                    get_post_meta(get_the_ID(), 'date', true),
                    get_post_meta(get_the_ID(), 'comment', true),
                    get_post_meta(get_the_ID(), 'contact', true),
                    get_post_meta(get_the_ID(), 'reciprocal_check_status', true),
                ];
            endwhile;
        endif;

        $exporter = new ops_export_data_excel('browser', 'ops-export-backlinks.xls');

        $exporter->initialize();

        foreach ($data as $row) {
            $exporter->addRow($row);
        }

        $exporter->finalize();

        exit;
    }


    function sanitize_input($unsecure_data)
    {

        $this->secure_data = [];

        $allowed_arrays = [
            'variation'
        ];

        foreach ($unsecure_data as $index => $value) {


            // do we have an array???
            if (is_array($value)) {

                if (!in_array($index, $allowed_arrays)) {

                    // check if we are in allowed arrays
                    wp_die('Error #4023123119. Please contact us with this message. Event recorded.');

                } else {

                    // go ahead and proccess this array
                    foreach ($value as $subindex => $subvalue) {

                        if (is_array($subvalue)) {

                            foreach ($subvalue as $subsubindex => $subsubvalue) {
                                $this->secure_data[sanitize_text_field($index)][sanitize_text_field($subindex)][sanitize_text_field($subsubindex)] = sanitize_text_field($this->nl2br(trim($subsubvalue)));
                            }

                        } else {
                            $this->secure_data[sanitize_text_field($index)][sanitize_text_field($subindex)] = sanitize_text_field($this->nl2br(trim($subvalue)));
                        }
                    }

                    // go to another element
                    continue;
                }

            }


            $this->secure_data[sanitize_text_field($index)] = sanitize_text_field($value);
        }
    }


}

new ops_listener();
