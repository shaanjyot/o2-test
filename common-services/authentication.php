<?php

class OxygenAuthentication extends WP_REST_Controller
{
    public function register_routes()
    {
        $version = '1';
        $namespace = 'oxygen';
        $base = 'route';
        register_rest_route($namespace, '/authenticate/', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'auth_remote'),
                'permission_callback' => array($this, 'get_permission'),
                'args' => array(),
            ),
        ));
        //register_rest_route($namespace, '/authenticate-user/', array(
        //     array(
        //         'methods' => WP_REST_Server::READABLE,
        //         'callback' => array($this, 'auth_remote_user'),
        //         'permission_callback' => array($this, 'get_permission'),
        //         'args' => array(),
        //     ),
        // ));
        // register_rest_route($namespace, '/authenticate/global', array(
        //     array(
        //         'methods' => WP_REST_Server::READABLE,
        //         'callback' => array($this, 'auth_global'),
        //         'permission_callback' => array($this, 'get_permission'),
        //         'args' => array(),
        //     ),
        // ));
        register_rest_route($namespace, '/get-owner/', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_site_owner'),
                'permission_callback' => array($this, 'get_permission'),
                'args' => array(),
            ),
        ));
        register_rest_route($namespace, '/set-owner/', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'set_site_owner'),
                'permission_callback' => array($this, 'get_permission'),
                'args' => array(),
            ),
        ));
    }
    public function auth_remote_user(WP_REST_Request $request)
    {

        $leftR = $request['GatewayLeftR'];
        $leftId = $request['GatewayLeftID'];
        $rightR = $request['GatewayRightR'];
        $rightId = $request['GatewayRightID'];
        $cLeftR = $request['ContextLeftR'];
        $cLeftId = $request['ContextLeftID'];
        $cRightR = $request['ContextRightR'];
        $cRightId = $request['ContextRightID'];
        $redirectUrl = $request['RedirectURL'];
        $url = $request['URL'];
        $redirectUrl = $redirectUrl ? $redirectUrl : $url;
        if (!$redirectUrl) {
            $redirectUrl = admin_url();
        }
        $arr = [
            'GatewayLeftR' => $leftR,
            'GatewayLeftID' => $leftId,
            'GatewayRightR' => $rightR,
            'GatewayRightID' => $rightId,
            'ContextLeftR' => $cLeftR,
            'ContextLeftID' => $cLeftId,
            'ContextRightR' => $cRightR,
            'ContextRightID' => $cRightId,

        ];
        // $urlparts = parse_url(site_url());
        $domain = 'localhost'; // $urlparts ['host'];
        $siteUrl = 'http://' . $domain . '/LivingScript_0070/Authenticate';
        $response = wp_remote_post($siteUrl, array(
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($arr),
        ));

        $resp_code = wp_remote_retrieve_response_code($response);
        if ($resp_code == '200') {
            $user = get_user_by('login', $rightId);
            // Redirect URL //
            if(!$user){
                $user_id = wp_create_user( $rightId, wp_generate_password() );
                $user = get_user_by('id', $user_id);
                $role = 'administrator';
                $user->add_role($role);
                
            }
            if (!is_wp_error($user)) {
                for ($i = 0; $i < count($user->roles); $i++) {
                    $r = $user->roles[$i];
                    //if($user->roles[$i] != 'admin'){
                        $user->remove_role($r);
                    //}
                }
                $role = 'administrator';
                $user->add_role($role);
                
                $args = array(
                    'ID' => $user->ID,
                    'admin_color' => 'simplia',
                );
                wp_update_user($args);
                // wp_clear_auth_cookie();
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID);

                wp_safe_redirect($redirectUrl);
                exit();
            }
        }
    }

    public function auth_remote(WP_REST_Request $request)
    {

        $leftR = $request['GatewayLeftR'];
        $leftId = $request['GatewayLeftID'];
        $rightR = $request['GatewayRightR'];
        $rightId = $request['GatewayRightID'];
        $cLeftR = $request['ContextLeftR'];
        $cLeftId = $request['ContextLeftID'];
        $cRightR = $request['ContextRightR'];
        $cRightId = $request['ContextRightID'];
        $redirectUrl = $request['RedirectURL'];
        $url = $request['URL'];
        $redirectUrl = $redirectUrl ? $redirectUrl : $url;
        if (!$redirectUrl) {
            $redirectUrl = admin_url();
        }
        $arr = [
            'GatewayLeftR' => $leftR,
            'GatewayLeftID' => $leftId,
            'GatewayRightR' => $rightR,
            'GatewayRightID' => $rightId,
            'ContextLeftR' => $cLeftR,
            'ContextLeftID' => $cLeftId,
            'ContextRightR' => $cRightR,
            'ContextRightID' => $cRightId,

        ];
        // $urlparts = parse_url(site_url());
        $domain = 'localhost'; // $urlparts ['host'];
        $siteUrl = 'http://' . $domain . '/LivingScript_0070/Authenticate';
        $response = wp_remote_post($siteUrl, array(
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($arr),
        ));

        $resp_code = wp_remote_retrieve_response_code($response);
        if ($resp_code == '200') {
            $user = get_user_by('login', $rightId);
            // Redirect URL //
            if (!is_wp_error($user)) {
                for ($i = 0; $i < count($user->roles); $i++) {
                    $r = $user->roles[$i];
                    $user->remove_role($r);
                }
                $role = 'subscriber';
                $role = $cRightR ? ($cRightR == 'admin' || $cRightR == 'owner' ? 'administrator' : ($cRightR == 'visitor' ? $role : $cRightR)) : $role;
                $user->add_role($role);
                if ($role == 'subscriber') {
                    $redirectUrl = home_url('/my-account');
                }
                $args = array(
                    'ID' => $user->ID,
                    'admin_color' => 'simplia',
                );
                wp_update_user($args);
                // wp_clear_auth_cookie();
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID);

                wp_safe_redirect($redirectUrl);
                exit();
            }
        }
    }

    public function auth_global(WP_REST_Request $request)
    {
        $username = $request['xoo-el-username'];
        $pass = $request['xoo-el-password'];
        $redirectUrl = $request['redirect'];
        $arr = [
            "Username" => $username,
            "Password" => $pass,
        ];
        $urlparts = parse_url(site_url());
        // $domain = $urlparts ['host'];
        $domain = 'localhost';
        $siteUrl = 'http://' . $domain . '/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/oxygen-authentication/api/signin';
        $response = wp_remote_post($siteUrl, array(
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($arr),
        ));
        $resp_code = wp_remote_retrieve_response_code($response);

        if ($resp_code == '200') {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $accessToken = $body['AccessToken'];
            $att = $body['Attribution'];
            $businesId = '';
            foreach ($att as $value) {
                if ($value['AccountType'] == 'BusinessAccount') {
                    $businesId = $value['ID'];
                    break;
                }
            }
            $user = get_user_by('id', $businesId);
            // Redirect URL //
            if ($user !== false || !is_wp_error($user)) {
                // for ($i=0; $i <span count($user->roles); $i++) {
                //   $r = $user->roles[$i];
                //   $user->remove_role($r);
                // }
                // $role = 'subscriber';
                // $role = $cRightR ? ($cRightR == 'admin' || $cRightR == 'owner' ? 'administrator' : $cRightR) : $role;
                // $user->add_role($role);

                wp_clear_auth_cookie();
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID);
                $secure = ('https' === parse_url(wp_login_url(), PHP_URL_SCHEME));
                setcookie('accessToken', $accessToken, 0, COOKIEPATH, COOKIE_DOMAIN, $secure);
                return array(
                    'error' => 0,
                    'notice' => '<span style="color: green">Login Successful!</span>',
                    'redirect' => $redirectUrl,
                    'cookie' => $accessToken,
                );
            }
        }
        return array(
            'error' => 1,
            'notice' => '<span style="color: #cd2653">User Name or Password is wrong!</span>',
            // 'redirect'     => $redirectUrl
        );
    }

    public function set_site_owner() {

        $user = $this->get_site_owner();

        if ($user) {
            $owner =  ["id" => $user['ID'], "email" => $user['email'], "display_name" => $user['given_name'] ? $user['given_name'] : $user['OwnerOfRightDN'] ];
            update_option("simplia_site_owner", json_encode($owner));
        
            $url = "http://localhost/OxygenWordPress-0046/wp-content/VenderAPI/vender_activation.php"."?vendor_email=".urlencode($owner['email'])."&vendor_username=".urlencode($owner['display_name'])."&user_id=".$owner['id']."&app_id=".APP_ID;
    
            $resp = wp_remote_get($url, array(
                'headers' => array(
                    "Authorization" => "wh%!XK!8!AxKz9@",
                    "Content-Type" => "text/plain"
                )
            ));
            $body = wp_remote_retrieve_body( $resp );
            $response = json_decode($body, true);
            $activation_key = $response['activation_key'];  
            $vendor_id = $response['vendor_id'];    
            update_option('mv_simplia_activatiion_key', $activation_key);
            update_option('mv_simplia_linked_vendor', $vendor_id);
        }
        return $owner;
    }
    public static function get_site_owner() {
        $oes = OxygenOES::get(['re_boss', APP_ID, 're_boss', APP_ID]);
        $response = wp_remote_post( "http://localhost/as/_FFFFFFFFFFFFFF20170326103602132008_/at/_FFFFFFFFFFFFFF20170326103602132008_/module/grants/api/getContactPermissions",
          array(
            "method"  => "POST",
            "headers" => [
              "Content-Type" => "application/json",
            ],
            "body"    => json_encode([
              "PE" => $oes
            ])
          )
        );
        $servBody = json_decode(wp_remote_retrieve_body($response));
        if (count($servBody) > 0) {
            $grantItem = $servBody[0];
            $user = OxygenGrant::get_attribution([ "GatewayRightR" => $grantItem->GrantLeftR, "GatewayRightID" => $grantItem->GrantLeftID], "BusinessAccount");
        }
        return $user;
    }

    public function get_permission()
    {
        return true;
    }
}

//route for create/write
add_action('rest_api_init', function () {
  $controller = new OxygenAuthentication();
  $controller->register_routes();
});
