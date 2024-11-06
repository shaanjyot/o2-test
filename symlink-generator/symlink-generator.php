<?php


define('SG_SYMLINK_GENERATOR_VERSION', '1.0');
define('SG_SYMLINK_GENERATOR_REQUIRED_WP_VERSION', '4.9');
define('SG_SYMLINK_GENERATOR_PLUGIN', __FILE__);
define('SG_SYMLINK_GENERATOR_PLUGIN_BASENAME', plugin_basename(SG_SYMLINK_GENERATOR_PLUGIN));
define('SG_SYMLINK_GENERATOR_PLUGIN_NAME', trim(dirname(SG_SYMLINK_GENERATOR_PLUGIN_BASENAME), '/'));
define('SG_SYMLINK_GENERATOR_PLUGIN_DIR', untrailingslashit(dirname(SG_SYMLINK_GENERATOR_PLUGIN)));

register_activation_hook(OXYGEN_PLUGINS_PATH, array('Symlink_Generator_Class', 'on_activation'));
register_deactivation_hook(OXYGEN_PLUGINS_PATH, array('Symlink_Generator_Class', 'on_deactivation'));
register_uninstall_hook(OXYGEN_PLUGINS_PATH, array('Symlink_Generator_Class', 'on_uninstall'));

add_action('plugins_loaded', array('Symlink_Generator_Class', 'init'));

class Symlink_Generator_Class
{
    protected static $instance;

    public static function init()
    {
        is_null(self::$instance) and self::$instance = new self;
        return self::$instance;
    }



    public static function on_activation()
    {
        if (!current_user_can('activate_plugins'))
            return;
        $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
        check_admin_referer("activate-plugin_{$plugin}");
        self::create_table();

        # Uncomment the following line to see the function in action
        # exit( var_dump( $_GET ) );
    }

    public static function on_deactivation()
    {
        if (!current_user_can('activate_plugins'))
            return;
        $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
        check_admin_referer("deactivate-plugin_{$plugin}");

        # Uncomment the following line to see the function in action
        # exit( var_dump( $_GET ) );
    }

    public static function on_uninstall()
    {
        if (!current_user_can('activate_plugins'))
            return;
        check_admin_referer('bulk-plugins');

        // Important: Check if the file is the one
        // that was registered during the uninstall hook.
        if (__FILE__ != WP_UNINSTALL_PLUGIN)
            return;

        # Uncomment the following line to see the function in action
        # exit( var_dump( $_GET ) );
    }

    public function __construct()
    {
        add_action('admin_menu', 'wpdocs_register_my_custom_menu_page');

    }
    function create_table() {
        
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'sg_symlink_generator';
        if (!$wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            $sql = "CREATE TABLE $table_name (
					id int(255) NOT NULL AUTO_INCREMENT,
					created_by_user_id int(255),
					target varchar(255),
					created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',				
					PRIMARY KEY (id)
				) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
}

function wpdocs_register_my_custom_menu_page()
{
    //add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )

    //add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', int $position = null )
    add_menu_page('Symlink Generator', 'Symlink Generator', 'manage_options', 'symlink-gen-dashboard', 'symlink_gen_dashboard', 'dashicons-buddicons-buddypress-logo');
    add_submenu_page("symlink-gen-dashboard", "Dashboard", "Dashboard", "manage_options", "symlink-gen-dashboard", "symlink_gen_dashboard");
    add_submenu_page("symlink-gen-dashboard", "About", "About", "manage_options", "about", "about");
}

function symlink_gen_dashboard()
{
    require_once(SG_SYMLINK_GENERATOR_PLUGIN_DIR . '/includes/wp-list-table.php');
    $sg_symlink_table = new SG_Symlink_Table_List();
    $sg_symlink_table->prepare_items();
?>
    <div class="wrap">
        <div id="icon-users" class="icon32"></div>
        <h1 class="wp-heading-inline">Dashboard</h1>
        <button type="button" class="page-title-action open">Add New</button>
        <form method="post" id="SG_Symlink_Table_List_filter" style="margin-top: 20px;">
            <?php
            $sg_symlink_table->display();
            ?>
        </form>
        <?php require_once(SG_SYMLINK_GENERATOR_PLUGIN_DIR . '/template/dashboard.php');  ?>
    </div>
<?php
}
function about()
{
    echo '<h1>About</h1>';
}
?>