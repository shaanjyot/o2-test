<?php
// WP_List_Table is not loaded automatically so we need to load it in our application
if(!class_exists('WP_List_Table'))
	{
	    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
	}

//Create a new table class that will extend the WP_List_Table
class RE_Role_Editor_Table_List extends WP_List_Table
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
	    	global $wp_roles;
			$roles = $wp_roles->roles;
	
			$user_roles_array = array();

			if(sizeof($roles) > 0) 
				{
					foreach($roles as $index => $role)
						{
			                $user_roles_array[] = array("id" => $index, "name" => $role['name']);
			            }
			    }
			return $user_roles_array;
    	}

    public function get_hidden_columns() {
		return array();
    }

    public function get_sortable_columns() {

        return array( "created_at" => array("created_at", true) );
    }

    public function get_columns() {

        $columns = array(
	        "cb" => '<input type="checkbox" />',
            "name" => "Role Title",
            "id" => "Role ID",
          //  "created_at" => "Create Date"
        );

        return $columns;
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
            case 'name':
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
							$wpdb->delete("$table_name", ['id' => $id ], ['%d']);
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
            'edit' => sprintf('<a href="?page=role-editor-dashboard&role-editing-mode=enabled&id=%s">%s</a>', $item['id'], __('Edit', 'custom_table_example')),
            'delete' => sprintf('<a  onclick="ConfirmDelete()" class="action_delete" href="?page=role-editor-dashboard&action=delete&id=%s">%s</a>', $item['id'], __('Delete', 'custom_table_example')),

            //'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['name'], __('Delete', 'custom_table_example')),
        );

        return sprintf('%s %s', $item['name'], $this->row_actions($actions));
    }	
		
		
		
	public function process_bulk_action()
		{

		  //Detect when a bulk action is being triggered...
			if('delete' === $this->current_action())
				{

					$role = $_GET['id'];
					remove_role($role);
					$redirect_url = admin_url('admin.php?page=role-editor-dashboard');
					header("Location:$redirect_url");
				// In our file that handles the request, verify the nonce.
/*					$nonce = esc_attr( $_REQUEST['_wpnonce'] );
					if(!wp_verify_nonce($nonce, 'sp_delete_customer'))
						{
							die( 'Go get a life script kiddies' );
						}
					else
						{
							self::delete_customer(absint( $_GET['customer']));
							wp_redirect(esc_url(add_query_arg()));
							exit;
						}*/
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
?>
<script>

function ConfirmDelete()
{
  var x = confirm("Are you sure you want to delete?");
  if (x)
      return true;
  else
    return false;
}


</script>
