<?php
function  oxygen_role_create(WP_REST_Request $request)
{

  $body = $request->get_params();
  $role_name = $body['RoleName'];
  $role_id = 're_' . strtolower(str_replace(' ', '_', $role_name));
  $new_capabilities = $body['Capabilities'];
  $capabilities = array();
  $i = 0;
  foreach ($new_capabilities as $c) {
    $capabilities[$c] = true;
  }
  $result = add_role($role_id, __($role_name), $capabilities);
  oxygen_role_um_update($role_id);
  return $result;
}

function oxygen_role_update(WP_REST_Request $request)
{
  $body = $request->get_params();
  $role_name = $body['RoleName'];
  if ($role_name == 'Administrator') {
    $role_id = strtolower($role_name);
    $admin_role_set = get_role($role_id);
  } else {
    $role_id = 're_' . strtolower(str_replace(' ', '_', $role_name));
    $admin_role_set = get_role($role_id);
  
    $admin_role_set_en_de = json_decode(json_encode($admin_role_set), true);
  
    foreach ($admin_role_set_en_de['capabilities'] as $key => $value) {
  
      $admin_role_set->remove_cap($key);
    }
  }
  if (!empty($body['Capabilities'])) {
    foreach ($body['Capabilities'] as $value) {
      $admin_role_set->add_cap($value, true);
      //delete_option('remove_capability');
    }
  }
  oxygen_role_um_update($role_id);
  return true;
}
function oxygen_add_capability(WP_REST_Request $request)
{
  $body = $request->get_params();
  // $body = json_decode($body, true);
  $role_name = $body['RoleName'];
  $role_id =  strtolower($role_name);
  $admin_role_set = get_role($role_id);

  $admin_role_set_en_de = json_decode(json_encode($admin_role_set), true);

  if (!empty($body['Capabilities'])) {
    foreach ($body['Capabilities'] as $value) {
      $admin_role_set->add_cap($value, true);
      //delete_option('remove_capability');
    }
  }
  oxygen_role_um_update($role_id);
  return true;
}

function oxygen_role_delete(WP_REST_Request $request)
{
  $body = $request->get_params();
  $role_name = $body['RoleName'];
  $role_id = 're_' . strtolower(str_replace(' ', '_', $role_name));
  remove_role($role_id);
  return true;
}

function oxygen_role_um_update($role_id = 're_boss') {
  $id = $role_id;
  $role = array();
  $role['_um_is_custom'] = 0;
  $role['_um_priority'] = 2;
  $role['_um_can_access_wpadmin'] = 0;
  $role['_um_can_access_wpadmin'] = 1;
  $role['_um_can_not_see_adminbar'] = 0;
  $role['_um_can_edit_everyone'] = 0;
  $role['_um_can_delete_everyone'] = 0;
  $role['_um_can_edit_profile'] = 0;
  $role['_um_can_delete_profile'] = 0;
  $role['_um_can_view_all'] = 0;
  $role['_um_can_make_private_profile'] = 0;
  $role['_um_can_access_private_profile'] = 0;
  $role['_um_default_homepage'] = 0;
  $role['_um_default_homepage'] = 1;
  $role['_um_status'] = 'approved';
  $role['_um_auto_approve_act'] = 'redirect_profile';
  $role['_um_login_email_activate'] = 0;
  $role['_um_checkmail_action'] = 'show_message';
  $role['_um_checkmail_message'] = 'Thank you for registering. Before you can login we need you to activate your account by clicking the activation link in the email we just sent you.';
  $role['_um_pending_action'] = 'show_message';
  $role['_um_pending_message'] = 'Thank you for applying for membership to our site. We will review your details and send you an email letting you know whether your application has been successful or not.';
  $role['_um_after_login'] = 'redirect_admin';
  $role['_um_after_logout'] = 'redirect_home';
  $role['_um_after_delete'] = 'redirect_home';
  update_option("um_role_{$id}_meta", $role);
}

//route for create/write
add_action('rest_api_init', function () {
  register_rest_route('oxygen', '/role/create/', array(
    'methods' => 'POST',
    'callback' => 'oxygen_role_create'
  ));
  register_rest_route('oxygen', '/role/update/', array(
    'methods' => 'POST',
    'callback' => 'oxygen_role_update'
  ));
  register_rest_route('oxygen', '/role/delete/', array(
    'methods' => 'POST',
    'callback' => 'oxygen_role_delete'
  ));
  register_rest_route('oxygen', '/capability/add/', array(
    'methods' => 'POST',
    'callback' => 'oxygen_add_capability'
  ));
});
