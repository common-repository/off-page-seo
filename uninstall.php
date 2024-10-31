<?php

if (!defined('WP_UNINSTALL_PLUGIN')) exit();

// delete options
delete_option('ops_settings');
delete_option('ops_google_auth_code');
delete_option('ops_google_tokens');
delete_transient('ops_premium_account_data');

wp_clear_scheduled_hook('ops_rank_update');
wp_clear_scheduled_hook('ops_reciprocal_check');

