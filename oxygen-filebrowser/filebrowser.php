<?php

// function filebrowser_plugin_create_menu_entry()
// {

//   add_menu_page(
//     'Oxygen Browser',
//     'Oxygen Browser',
//     'manage_options',
//     'main-page-of-plugin',
//     'oxygen_file_show_main_page',
//     'dashicons-portfolio'
//   );

//   add_submenu_page(
//     'main-page-of-plugin',
//     'Browser',
//     'Browser',
//     'edit_posts',
//     'main-page-of-plugin',
//     'oxygen_file_show_import_page'
//   );
//   add_submenu_page(
//     'main-page-of-plugin',
//     'Import',
//     'Import',
//     'edit_posts',
//     'import-page-of-plugin',
//     'oxygen_file_show_import_page'
//   );
//   add_submenu_page(
//     'main-page-of-plugin',
//     'Export',
//     'Export',
//     'edit_posts',
//     'export-page-of-plugin',
//     'oxygen_file_show_export_page'
//   );
// }

// function oxygen_file_show_main_page() {
//    require_once('browser_page.php');
// }
// function oxygen_file_show_import_page() {
//    require_once('import_page.php');
// }
// function oxygen_file_show_export_page() {
//    require_once('export_page.php');
// }

// add_action('admin_menu', 'filebrowser_plugin_create_menu_entry');

function oxygen_file_upload_event($upload, $context) {
  oxygen_notify_sync_api($upload['file']);
  return $upload;
}
function oxygen_file_delete_event($upload) {
  oxygen_notify_sync_api($upload);
  return $upload;
}
function oxygen_file_edit_event($upload, $file) {
  oxygen_notify_sync_api($file);
  return $upload;
}
function oxygen_notify_sync_api($file) {
  try {
    //code...
    $dir = dirname($file); 
    if (is_link($file)) {
      $dir = readlink($dir);
    }
    // $urlparts = parse_url(site_url());
    // $domain = $urlparts ['host'];
    $domain  = 'localhost';
    $siteUrl = 'http://'.$domain.'/LivingScript_0070/OxygenSync';
    // $siteUrl = 'https://enix7t1gv24gf.x.pipedream.net';
    $response = wp_remote_post(
      $siteUrl,
      array(
        'method'  => 'POST',
        'headers' => [
          'Content-Type' => 'application/json',
        ],
        'body'    => json_encode([
          'LocalPath' =>  $dir,
          'Operation' => 'update'
        ])
      )
    );
  } catch (\Throwable $th) {
    //throw $th;
  }
}

function oxygen_page_save_event($post_id, $post, $update) {
  $dir = realpath('./wp-content/et-cache/'); 
  $domain  = 'localhost';
  $siteUrl = 'http://'.$domain.'/LivingScript_0070/OxygenSync';
  // $siteUrl = 'https://enix7t1gv24gf.x.pipedream.net';
  $response = wp_remote_post(
    $siteUrl,
    array(
      'method'  => 'POST',
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'body'    => json_encode([
        'LocalPath' =>  $dir,
        'Operation' => 'update'
      ])
    )
  );
}
add_filter( 'wp_handle_upload', 'oxygen_file_upload_event', 10, 2 );
add_filter( 'wp_delete_file', 'oxygen_file_delete_event',10, 1);
add_filter( 'wp_save_image_editor_file', 'oxygen_file_edit_event',10, 2);
// add_action('save_post', 'oxygen_page_save_event', 10, 3);
