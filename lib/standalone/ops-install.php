<?php

class ops_install
{
    public function __construct()
    {
        $this->ops_create_tables();
    }

    function ops_create_tables()
    {

        global $wpdb;

        $create_table_query_ranks = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ops_rankings` (
              id int(11) NOT NULL AUTO_INCREMENT,
              keyword_id int(11) NOT NULL,
              time text NOT NULL,
              ranking text NOT NULL,
              PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8;";
        $d = $wpdb->query($create_table_query_ranks);


    }


}

new ops_install();