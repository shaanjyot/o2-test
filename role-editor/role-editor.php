<?php


define('RE_ROLE_EDITOR_VERSION', '1.0');
define('RE_ROLE_EDITOR_REQUIRED_WP_VERSION', '4.9');
define('RE_ROLE_EDITOR_PLUGIN', __FILE__);
define('RE_ROLE_EDITOR_PLUGIN_BASENAME', plugin_basename(RE_ROLE_EDITOR_PLUGIN));
define('RE_ROLE_EDITOR_PLUGIN_NAME', trim(dirname(RE_ROLE_EDITOR_PLUGIN_BASENAME), '/'));
define('RE_ROLE_EDITOR_PLUGIN_DIR', untrailingslashit(dirname(RE_ROLE_EDITOR_PLUGIN)));

register_activation_hook(OXYGEN_PLUGINS_PATH, array('Role_Editor_Class', 'on_activation'));
register_deactivation_hook(OXYGEN_PLUGINS_PATH, array('Role_Editor_Class', 'on_deactivation'));
register_uninstall_hook(OXYGEN_PLUGINS_PATH, array('Role_Editor_Class', 'on_uninstall'));
//register_activation_hook( __FILE__, 'write_text' );		
register_activation_hook(OXYGEN_PLUGINS_PATH, 'get_add_cap');
add_action('plugins_loaded', array('Role_Editor_Class', 'init'));
add_action('admin_enqueue_scripts', 'ds_admin_theme_style');
add_action('login_enqueue_scripts', 'ds_admin_theme_style');
function ds_admin_theme_style() {
	if (!current_user_can( 'manage_options' )) {
		echo '<style>.update-nag, .updated, .error, .is-dismissible { display: none; }</style>';
	}
}

class Role_Editor_Class
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
		add_action('admin_menu', 'wpdocs_register_role_editor_menu_page');
		add_action('_core_updated_successfully', 'my_upgrade_function', 10, 2);
		add_action('admin_menu', 'dashboard_condition');
		add_filter('all_plugins', 'plugin_permissions');
	}
}
function no_re_cache()
{
  if ( ! defined( 'DONOTCACHEPAGE' ) ) {
      define( 'DONOTCACHEPAGE', true );
  }
  if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
      define( 'DONOTCACHEOBJECT', true );
  }
  if ( ! defined( 'DONOTCACHEDB' ) ) {
      define( 'DONOTCACHEDB', true );
  }
}

function plugin_permissions($plugins)
{
	global $current_user, $plugin_credentials;
	$viewable_plugins = array();

	if (!isset($current_user->allcaps['dashboard_access'])) {
		unset($plugins['role-editor/role-editor.php']);
		remove_menu_page('role-editor.php');
	}
	return $plugins;
}

function dashboard_condition()
{
	global $wp_roles;
	$user = wp_get_current_user();
	$roles = $user->roles;
	if (in_array('re_boss', $roles)) {
		
	}
	$admin_role_set = get_role($roles[0]);

	$admin_role_set_en_de = json_decode(json_encode($admin_role_set), true);
	$catdata = array();
	foreach ($admin_role_set_en_de['capabilities'] as $key => $value) {
		$catdata[] = $key;
	}
	$str = implode(',', $catdata);
	$str = explode(',', $str);

	if (!in_array('manage_options', $str)) {
		// remove_menu_page('index.php');
		remove_menu_page('role-editor-dashboard');
		remove_menu_page('symlink-gen-dashboard');
		// if (strpos(get_current_file_url(), 'index.php') !== false)
		// 	wp_redirect('edit.php');

		// if (isset($_REQUEST['page'])  == 'role-editor-dashboard')
		// 	wp_redirect('edit.php');
	}
}

function get_current_file_url($Protocol = 'http://')
{
	return $_SERVER['REQUEST_URI'];
}

function my_upgrade_function()
{
	$file = "/opt/lampp/htdocs/onbuy/wp-includes/functions.php";
	$text = "if(!function_exists('dashboard_condition')){

    		function dashboard_condition(){
                    global \$wp_roles;
					\$user = wp_get_current_user();
 					\$roles = \$user->roles;
					\$admin_role_set=get_role( \$roles[0] );
					\$admin_role_set_en_de=json_decode(json_encode(\$admin_role_set),true);
					\$catdata=array(); 
					foreach (\$admin_role_set_en_de['capabilities'] as \$key => \$value) {
						\$catdata[] = \$key;
					}
					\$str=implode(',', \$catdata);
					\$str=explode(',', \$str);
					if(!in_array('dashboard_access', \$str)){
						remove_menu_page( 'index.php' );
					}
    			}
    		}
    		add_action('admin_menu', 'dashboard_condition');";
	$file = fopen($file, 'a');
	fwrite($file, $text);
}
function wpdocs_register_role_editor_menu_page()
{
	add_menu_page('Role Editor', 'Role Editor', 'manage_options', 'role-editor-dashboard', 'role_editor_dashboard', 'dashicons-buddicons-buddypress-logo');
	add_submenu_page("role-editor-dashboard", "Dashboard", "Dashboard", "manage_options", "role-editor-dashboard", "role_editor_dashboard");
	add_submenu_page("role-editor-dashboard", "About", "About", "manage_options", "about_role_editor", "about_role_editor");
}

function write_text()
{

	$file = "/opt/lampp/htdocs/onbuy/wp-includes/functions.php";
	$text = "if(!function_exists('dashboard_condition')){

    		function dashboard_condition(){
                    global \$wp_roles;
					\$user = wp_get_current_user();
 					\$roles = \$user->roles;
					\$admin_role_set=get_role( \$roles[0] );
					\$admin_role_set_en_de=json_decode(json_encode(\$admin_role_set),true);
					\$catdata=array(); 
					foreach (\$admin_role_set_en_de['capabilities'] as \$key => \$value) {
						\$catdata[] = \$key;
					}
					\$str=implode(',', \$catdata);
					\$str=explode(',', \$str);
					if(!in_array('dashboard_access', \$str)){
						remove_menu_page( 'index.php' );
					}
    			}
    			

    		}

    		add_action('admin_menu', 'dashboard_condition');";
	$file = fopen($file, 'a');
	fwrite($file, $text);
}
function get_add_cap()
{
	global $wp_roles;
	$roles = $wp_roles->roles;
	foreach ($roles as $key => $value) {
		$wp_roles->add_cap($key, 'dashboard_access');
	}
}
function role_editor_dashboard()
{
	no_re_cache();
	require_once(RE_ROLE_EDITOR_PLUGIN_DIR . '/includes/wp-list-table.php');
	$sg_symlink_table = new RE_Role_Editor_Table_List();
	$sg_symlink_table->prepare_items();
?>
	<div class="wrap">
		<div id="icon-users" class="icon32"></div>
		<?php
		if (isset($_REQUEST['role-editing-mode'])) {
			echo '<h1 class="wp-heading-inline">Edit Role : </h1>';
			require_once(RE_ROLE_EDITOR_PLUGIN_DIR . '/template/role-editing.php');
		} else if (isset($_REQUEST['add-new-role'])) {
			require_once(RE_ROLE_EDITOR_PLUGIN_DIR . '/template/add-new-role.php');
		} else {

		?>
			<h1 class="wp-heading-inline">User Roles</h1>
			<button type="button" class="page-title-action open" onclick="window.location = '<?php echo esc_url($_SERVER['REQUEST_URI']) . '&add-new-role'; ?>'">Add New</button>
			<form method="post" id="SG_Symlink_Table_List_filter" style="margin-top: 20px;">
				<?php
				$sg_symlink_table->display();
				?>
			</form>
		<?php

		}
		?>
	</div>
<?php
}
function about_role_editor()
{
	echo '<h1>About</h1>';
}
