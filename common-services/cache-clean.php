<?php

function cache_clean_cron_schedules($schedules)
{
    $schedules['hourly'] = array(
        'interval' => 3600,
        'display' => 'Once Every 1 Hour',
    );

    return $schedules;
}
add_filter('cron_schedules', 'cache_clean_cron_schedules', 10, 1);

/**
 * Activate
 */
function cache_clean_cron_activate()
{
    if (!wp_next_scheduled('cache_clean_cron')) {
        wp_schedule_event(time(), 'hourly', 'cache_clean_cron');
    }
}
register_activation_hook(__FILE__, 'cache_clean_cron_activate');

/**
 * Deactivate
 */
function cache_clean_cron_deactivate()
{
    wp_unschedule_event(wp_next_scheduled('cache_clean_cron'), 'cache_clean_cron');
}
register_deactivation_hook(__FILE__, 'cache_clean_cron_deactivate');

/**
 * Crontest
 */
function cache_clean_cron()
{
    // wp_mail( get_option( 'admin_email' ), 'Cron Test', 'All good in the hood!' );
    $dirname = WP_CONTENT_DIR . '/cache';
    // array_map('unlink', glob("$dirname/*.*"));

    // file_put_contents(WP_CONTENT_DIR . '/my-debug.txt', "CRON FOR CLEANUP", FILE_APPEND);
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all();
        op_remove_dir($dirname);
    }
}
add_action('cache_clean_cron', 'cache_clean_cron');


