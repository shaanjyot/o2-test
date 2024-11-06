<?php
// WP_List_Table is not loaded automatically so we need to load it in our application
if(!class_exists('WP_List_Table'))
	{
	    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
	}

//Create a new table class that will extend the WP_List_Table
class SG_Symlink_Table_List extends WP_List_Table
	{
		public function prepare_items()
		{
			$orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : "";
			$order = isset($_GET['order']) ? trim($_GET['order']) : "";
			
			$search_term = isset($_POST['s']) ? trim($_POST['s']) : "";
			
			$this->items = $this->wp_list_table_data($orderby, $order, $search_term);
			
			$columns = $this->get_columns();
			$hidden = $this->get_hidden_columns();
			$sortable = $this->get_sortable_columns();
			
			$this->_column_headers = array($columns, $hidden, $sortable);
			$this->process_bulk_action();
		}

    public function wp_list_table_data($orderby = '', $order = '', $search_term = '')
    	{
			global $wpdb;
			$table_name = $wpdb->prefix . 'sg_symlink_generator';
			$user_table = $wpdb->prefix . 'users';
			if(!empty($search_term))
        		{
// 					$all_posts = $wpdb->get_results("SELECT * from " . $table_name . " WHERE post_type = 'post' AND post_status = 'publish' AND (post_title LIKE '%$search_term%' OR post_content LIKE '%$search_term%')");
        		}
        	else 
        		{
					if($orderby == "created_at" && $order == "desc")
					{
						$sql = "SELECT wpsg.*, wpus.user_nicename FROM $table_name AS wpsg JOIN $user_table AS wpus ON (wpsg.created_by_user_id = wpus.id)  ORDER BY wpsg.created_at DESC";
						$all_posts = $wpdb->get_results($sql);
						//SELECT * from " . $table_name . " ORDER BY created_at DESC
            		} 
					else
					{
						$sql = "SELECT wpsg.*, wpus.user_nicename FROM $table_name AS wpsg JOIN $user_table AS wpus ON (wpsg.created_by_user_id = wpus.id)";
						$all_posts = $wpdb->get_results($sql);//"SELECT * from $table_name"
            		}
				}
			$posts_array = array();

        if (count($all_posts) > 0) {

            foreach ($all_posts as $index => $post) {
                $posts_array[] = array(
                    "id" => $post->id,
                    "created_by_user_id" => $post->user_nicename,
                    "target" => $post->target,
                    "created_at" => $post->created_at
                );
            }
        }

        return $posts_array;
    }

    public function get_hidden_columns() {
		return array("id");
    }

    public function get_sortable_columns() {

        return array( "created_at" => array("created_at", true) );
    }

    public function get_columns() {

        $columns = array(
	        "cb" => '<input type="checkbox" />',
            "id" => "ID",
            "created_by_user_id" => "Created By User",
            "target" => "Target",
            "created_at" => "Create Date"
        );

        return $columns;
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
            case 'created_by_user_id':
            case 'target':
            case 'created_at':
                return $item[$column_name];
            default:
                return "no value";
        }
    }
    
	/**
	* Render the bulk edit checkbox
	*
	* @param array $item
	*
	* @return string
	*/
	function column_cb($item)
		{
		  	return sprintf('<input type="checkbox" name="ids-to-delete[]" value="%s" />', $item['id']);
		}
    
	/**
	* Returns an associative array containing the bulk action
	*
	* @return array
	*/
 	public function get_bulk_actions()
 		{
			$actions = ['bulk-delete' => 'Delete'];
			return $actions;
		}
	
/**
* Delete a customer record.
*
* @param int $id customer ID
*/
	public static function delete_customer($id)
		{
			global $wpdb;
			$table_name = $wpdb->prefix . 'sg_symlink_generator';
			
			$all_posts = $wpdb->get_results("SELECT * from $table_name WHERE id = '$id'");
			$file = $all_posts[0]->target;
			
			
			
			if(file_exists($file))
				{
					if(is_link($file))
						{
							unlink($file);
							$wpdb->delete("$table_name", [ 'id' => $id ], ['%d']);
						}
					else
						{
							exit("$file exists but not symbolic link\n");
						}
				}
		}
		
		
	/**
        * [OPTIONAL] this is example, how to render column with actions,
        * when you hover row "Edit | Delete" links showed
        *
        * @param $item - row (key, value array)
        * @return HTML
        */
    function column_name($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit' => sprintf('<a href="?page=persons_form&id=%s">%s</a>', $item['created_by_user_id'], __('Edit', 'custom_table_example')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['created_by_user_id'], __('Delete', 'custom_table_example')),
        );

        return sprintf('%s %s', $item['created_by_user_id'], $this->row_actions($actions));
    }	
		
		
		
	public function process_bulk_action()
		{

		  //Detect when a bulk action is being triggered...
			if('delete' === $this->current_action())
				{
				// In our file that handles the request, verify the nonce.
					$nonce = esc_attr( $_REQUEST['_wpnonce'] );
					if(!wp_verify_nonce($nonce, 'sp_delete_customer'))
						{
							die( 'Go get a life script kiddies' );
						}
					else
						{
							self::delete_customer(absint( $_GET['customer']));
							wp_redirect(esc_url(add_query_arg()));
							exit;
						}
				}
			
			// If the delete bulk action is triggered
			if((isset($_POST['action']) && $_POST['action'] == 'bulk-delete') || (isset( $_POST['action2']) && $_POST['action2'] == 'bulk-delete'))
				{
					$delete_ids = esc_sql( $_POST['ids-to-delete'] );
					//var_dump($delete_ids);
					// loop over the array of record IDs and delete them
					foreach ($delete_ids as $id)
						{
							self::delete_customer( $id );
						}
					wp_redirect( esc_url( add_query_arg() ) );
					exit;
				}
		
		}
}

