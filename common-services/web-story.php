<?php

class SimpliaStory extends WP_REST_Controller {
  public function init() {
    add_action('rest_after_insert_web-story', array($this, 'set_cookie'), 10, 1);
  }
  public function register_routes()
  {
    $version = '1';
    $namespace = 'oxygen';
    $base = 'route';
    register_rest_route($namespace, '/edit-story/(?P<id>\d+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'edit_story'),
            'permission_callback' => array($this, 'get_permission'),
            'args' => array(),
        ),
    ));
    register_rest_route($namespace, '/edit-story/(?P<id>\d+)/(?P<aid>\d+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'edit_story'),
            'permission_callback' => array($this, 'get_permission'),
            'args' => array(),
        ),
    ));
    register_rest_route($namespace, '/duplicate-story/(?P<id>\d+)', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'duplicate_story'),
            'permission_callback' => array($this, 'get_permission'),
            'args' => array(),
        ),
    ));
  }
  public function set_cookie($args) {
    try {
      setcookie('story_status', $args->post_status, strtotime('+1 day'), "/");
      setcookie('story_id', $args->ID, strtotime('+1 day'), "/");
      setcookie('story_name', $args->post_name, strtotime('+1 day'), "/");
    } catch (\Throwable $th) {
      //throw $th;
    }
    // if (isset($_COOKIE['sessionId'])) {
    // }
    return;
  }
  public function edit_story(WP_REST_Request $request) {
    $new_post_id = $this->duplicate_story($request);
    $redirect = home_url().'/wp-admin/post.php?action=edit&post='.$new_post_id;
    return wp_safe_redirect($redirect);
  }
  public function duplicate_story(WP_REST_Request $request) {
    global $wpdb;
    global $wp_error;
    $post_id = $request['id'];
    $title   = get_the_title($post_id);
    $oldpost = get_post($post_id);
    $post_author = $oldpost->post_author;
    if (isset($request['aid'])) {
      $post_author = $request['aid'];
    }
    $post    = array(
      'post_title' => $title,
      'post_date' => $oldpost->post_date,
      'post_date_gmt' => $oldpost->post_date_gmt,
      'post_status' => 'draft',
      'post_type' => $oldpost->post_type,
      'post_content' => $oldpost->post_content,
      'post_author' => $post_author,
      'comment_status' =>"closed",
      'ping_status' => "closed",
      'post_password' => '',
      'post_name' => $title. '-'.time(),
      'to_ping' => "",
      'pinged'  => "",
      'post_modified' => $oldpost->post_modified,
      'post_modified_gmt' => $oldpost->post_modified_gmt,
      'post_content_filtered' => $oldpost->post_content_filtered,
      // 'post_parent' => 0,
      // 'guid' => "http://localhost:8888/?post_type=web-story&#038;p=49",
      'menu_order' => 0, 
      // 'post_mime_type' => "",
      // 'comment_count'=> "0",
      // 'filter' => "raw"
    );
    if ( false === $wpdb->insert( $wpdb->posts, $post ) ) {
			if ( $wp_error ) {
        $message = '';
				return new WP_Error( 'db_insert_error', $message, $wpdb->last_error );
			} else {
				return 0;
			}
		}
    // Copy post metadata
    $new_post_id = (int) $wpdb->insert_id;
    $data = get_post_custom($post_id);
    foreach ( $data as $key => $values) {
      foreach ($values as $value) {
        add_post_meta( $new_post_id, $key, $value );
      }
    }
    return $new_post_id;
  }
  public function get_permission() {
    return true;
  }
  
}

add_action('rest_api_init', function () {
  $controller = new SimpliaStory();
  $controller->init();
  $controller->register_routes();
});