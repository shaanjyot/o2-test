<?php

/**
 * Plugin Name: Oxygen Plugins
 * Plugin URI: https://laxroute53.com
 * Description: Collections of Oxygen Plugins (File Browser, Role Editor, Symlink Generator)
 * Version: 1.0.0
 * Author: Lotus Interworks
 * Author URI: https://lotusinterworks.com
 * License:  © 2010-2020 Lotus Interworks Inc.
 */

 

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

define( 'OXYGEN_PLUGINS_PATH', __FILE__ );
define( 'OXYGEN_PLUGINS_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define('WP_MEMORY_LIMIT', '256M');


include_once(OXYGEN_PLUGINS_DIR . '/common-services/common-services.php');
include_once(OXYGEN_PLUGINS_DIR . '/oxygen-filebrowser/filebrowser.php');
require_once(OXYGEN_PLUGINS_DIR . '/symlink-generator/symlink-generator.php');
require_once(OXYGEN_PLUGINS_DIR . '/role-editor/role-editor.php');
require_once(OXYGEN_PLUGINS_DIR . '/frontend-scripts/insert_simplia_header_footer.php');
require_once(OXYGEN_PLUGINS_DIR . '/pwa-caching.php');
require_once(OXYGEN_PLUGINS_DIR . '/wc-cart-addon.php');
require_once(OXYGEN_PLUGINS_DIR . '/trp_404_redirect.php');
require_once(OXYGEN_PLUGINS_DIR . '/wc_structured_data.php');
require_once(OXYGEN_PLUGINS_DIR . '/email_log.php');

add_filter('wp_get_nav_menu_items', 'my_wp_get_nav_menu_items', 10, 3);
function my_wp_get_nav_menu_items($items, $menu, $args) {
    foreach($items as $key => $item)
        $items[$key]->description = '';

    return $items;
}

/*Set expiry time for admin and others*/
add_filter('auth_cookie_expiration', 'auth_cookie_expiration_filter', 10, 3);
function auth_cookie_expiration_filter($expiration, $user_id, $remember) {
    return YEAR_IN_SECONDS;   
}


