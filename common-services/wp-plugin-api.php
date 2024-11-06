<?php

require_once 'utility/rsn.php';
require_once 'utility/deployer.php';
require_once 'utility/gdn.php';

class OxygenPluginManagement extends WP_REST_Controller
{
  public function register_routes()
  {
    $version = '1';
    $namespace = 'oxygen';
    $base = 'route';
    register_rest_route($namespace, '/plugin/list/', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'plugin_list'),
        'permission_callback' => array($this, 'get_permission'),
        'args' => array(),
      ),
    ));
    register_rest_route($namespace, '/plugin/activate/', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'plugin_activate'),
        'permission_callback' => array($this, 'get_permission'),
        'args' => array(),
      ),
    ));
    register_rest_route($namespace, '/plugin/deactivate/', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'plugin_deactivate'),
        'permission_callback' => array($this, 'get_permission'),
        'args' => array(),
      ),
    ));
    register_rest_route($namespace, '/plugin/update/', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'plugin_update'),
        'permission_callback' => array($this, 'get_permission'),
        'args' => array(),
      ),
    ));
    register_rest_route($namespace, '/package/change/', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'package_change'),
        'permission_callback' => array($this, 'get_permission'),
        'args' => array(),
      ),
    ));
    register_rest_route($namespace, '/cache-clean/', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'cache_clean'),
        'permission_callback' => array($this, 'get_permission'),
        'args' => array(),
      ),
    ));
    register_rest_route($namespace, '/clear-cache/', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'cache_clean_file'),
        'permission_callback' => array($this, 'get_permission'),
        'args' => array(),
      ),
    ));
    register_rest_route($namespace, '/db-prefix/', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_db_prefix'),
        'permission_callback' => array($this, 'get_permission'),
        'args' => array(),
      ),
    ));
    register_rest_route($namespace, '/setup-site/', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'setup_site'),
        'permission_callback' => array($this, 'get_permission'),
        'args' => array(),
      ),
    ));
   
    register_rest_route($namespace, '/test/', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => array($this, 'get_oxygen_test'),
        'permission_callback' => array($this, 'get_permission'),
        'args' => array(),
      ),
    ));
  }
  public function plugin_list(WP_REST_Request $request)
  {

    $body = $request->get_params();
    if (!function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $pl = get_plugins();
    return $pl;
  }

  public function plugin_activate(WP_REST_Request $request)
  {
    $body = $request->get_params();
    if (!function_exists('activate_plugin')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $result = activate_plugins($body['PluginPath']);

    return $result;
  }

  public function plugin_deactivate(WP_REST_Request $request)
  {
    $body = $request->get_params();
    if (!function_exists('deactivate_plugin')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $result = deactivate_plugins($body['PluginPath']);

    return $result;
  }

  public function plugin_update()
  {
    $oxygenRsn = new OxygenRSN();
    $deployer = new Deployer();
    $ocs = $deployer->getInstances();
    // foreach ($ocs as $oc) {
    //   $result = ['Registrant' => $oc['Family'], 'RSNType' => 'ShortName', 'OxygenID' => $oc['Node'], 'Hold' => true];
    //   $rsn = $oxygenRsn->get($result);
    //   $deployer->updateConfig($result, $rsn);
    // }

    return $ocs;
  }

  public function package_change(WP_REST_Request $request)
  {
    $body = $request->get_params();
    global $wpdb;
    // $results = $wpdb->get_results("SELECT * FROM `BusinessServices` WHERE PackageName = 1", OBJECT);
    $result = $wpdb->get_results($wpdb->prepare(
      "
    SELECT OptionType, OptionName, OptionValue FROM `BusinessServices` WHERE PackageName = %s
    ",
      array(
        $body['PackageName'],
      )
    ));
    return $result;
  }

  public function cache_clean(WP_REST_Request $request)
  {
    $body = $request->get_params();
    $force = isset($body['force']) ? true : false;
    $dirname = WP_CONTENT_DIR . '/cache';
    $etdir = WP_CONTENT_DIR . '/et-cache';
    $debug_file = WP_CONTENT_DIR . '/debug.log';
    $wt_source = WP_CONTENT_DIR . '/plugins/w3-total-cache/wp-content/';
    $wt_file = WP_CONTENT_DIR . '/w3tc-config/master.php';
    $wt_file_db = WP_CONTENT_DIR . '/db.php';

    if (function_exists('w3tc_flush_all')) {
      w3tc_flush_all();
      op_remove_dir($etdir);
      $has_dir = op_remove_dir($dirname);
      // check file exist and rebuild
      if (!file_exists($wt_file) || !file_exists($wt_file_db) || $force) {
        op_xcopy($wt_source, WP_CONTENT_DIR);
        try {
          oxygen_http_post('/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/propagator/api/publish', [
            'json' => [
              'Payload' => [
                'Channel' => OxygenOES::get([
                  '2016-11-19T21:30:28.950008ZFFF-FFFFZ', // Channels-0023
                  '2016-11-19T21:44:18.688007ZFFF-FFFFZ', // BasicXXX-0021
                  '_FFFFFFFFFFFFFF20170203194832993006_', // BasicXXX-0021-0029-0037
                  'All'
                ]),
                'Message' => [
                  'MessageType'  => 'API',
                  'MessageBody'  => [
                    'FunctionName'  => 'syncFromEFS',
                    'Arguments'  => [
                      'EBSPath'  => '/ebs/UserData/_FFFFFFFFFFFFFF00001579713184157445_/_FFFFFFFFFFFFFF00001579713184157445_/Organizations/' . constant('APP_NAME') . '/wp-content',
                      'user'  => 'www-data',
                      'group'  => 'www-data'
                    ]
                  ]
                ]
              ]
            ]
          ]);
        } catch (\Throwable $th) {
          return $th;
        }
      }

      return 'cache cleaned ' . $has_dir;
    }
    op_remove_file($debug_file);
    return 'no-cache';
  }

  public function cache_clean_file(WP_REST_Request $request) {
    $body = $request->get_params();
    $dir = isset($body['wpname']) ? '/usr/local/apps/OxygenWordPress-0046/Organizations/' . $body['wpname'] . '/wp-content'  : WP_CONTENT_DIR;
    
    $dirname = $dir . '/cache';
    $etdir = $dir . '/et-cache';
    $debug_file = $dir . '/debug.log';
    $wt_source = $dir . '/plugins/w3-total-cache/wp-content/';
    $wt_file = $dir . '/w3tc-config/master.php';
    $wt_file_db = $dir . '/db.php';
    w3tc_flush_all();
    op_remove_dir($etdir);
    $has_dir = op_remove_dir($dirname);
    return $has_dir;
    
  }

  public function get_db_prefix()
  {
    global $wpdb;

    // Current site prefix
    return $wpdb->prefix;
  }


  public function get_oxygen_test()
  {
    // $db = new OxygenGDN();
    // return $db->getOxygenID('sirajul@lolobyte.com-0100');
    $oes = OxygenOES::get(['re_boss', APP_ID, 're_boss', APP_ID]);
    $res = oxygen_http_post("/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/grants/api/getContactPermissions",
      [
        "PE" => $oes
      ]
    );
    if (count($res) > 0) {
      $grantItem = $res['0'];
      $res = oxygen_http_post("/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/attribution/api/get",
        [ "GatewayRightR" => $grantItem->GrantLeftR, "GatewayRightID" => $grantItem->GrantLeftID]
      );
    }
    return $res;
  }

  public function setup_site(WP_REST_Request $request)
  {
    
    global $wpdb;
    if (!function_exists('activate_plugin')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    activate_plugins('admin-menu-editor/menu-editor.php');
    // OxygenAuthentication::set_site_owner();
    $wpdb->query( 
      $wpdb->prepare( 
        "update $wpdb->options set option_value = (select option_value from zzzzzy_options where option_name = 'zzzzzy_user_roles') where option_name = '$wpdb->prefix"."user_roles'"
        )
      );
      $wpdb->query( 
        $wpdb->prepare( 
          "update $wpdb->options set option_value = (select option_value from zzzzzy_options where option_name = 'ws_menu_editor') where option_name = 'ws_menu_editor'"
          )
        );
    $this->cache_clean($request);
    return 'done';
  }

  public function get_permission()
  {
    return true;
  }

}

//route for create/write
add_action('rest_api_init', function () {
  $controller = new OxygenPluginManagement();
  $controller->register_routes();
});
