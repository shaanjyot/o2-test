<?php
/*
 * Template Name: Dashboard
 */
 
 
 //print_r($_REQUEST);
 ?>
 
 <h2 class="nav-tab-wrapper">
	<a href="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>&tab=add" class="nav-tab nav-tab-active">Create Symlinks</a>
	
	
	<a href="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>&tab=list" class="nav-tab nav-tab-active">All Symlinks</a>

</h2>
 <?php
 if($_REQUEST['tab']=="add"){
if(!empty($_POST)) 
{
    if(isset($_POST['create-symlink']))
    	{
	    	$symlink_target =  $_POST['symlink_target'];
	    	$foder_name = basename("$symlink_target").PHP_EOL;
			$link = ABSPATH . 'wp-content/plugins/'.$foder_name;
			$result = symlink($symlink_target, $link);
			if($result)  
				{ 
				   echo ("Symlink has been created!"); 
				   global $wpdb;
				   $user_id = get_current_user_id();
				   $table_name = $wpdb->prefix . 'sg_symlink_generator';
				   $currentDateTime = date('Y-m-d H:i:s');
				   $wpdb->insert($table_name, array('created_by_user_id' => $user_id, 'target' => $link, 'created_at' => $currentDateTime));
				} 
			else 
				{ 
				   echo ("Symlink cannot be created!"); 
				} 
			//echo readlink($link);
    	}
} 
?>


<form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" enctype="multipart/form-data">
	<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th>Target</th>
				<td>
					<input id="target" type="text" name="symlink_target" placeholder="eg: ./uploads/cache" style="min-width: 50%;">
					<div>
						<label for="target">
						<span class="description">Enter the plugin path whose symlink you want to add into your wp-plugin. This field should not be empty.</span>
						</label>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<p>
		<input name="create-symlink" type="submit" class="button-primary" value="Create Symlink">
	</p>
</form>

<?php
	
	}
	
if($_REQUEST['tab']=="list"){ 
	
	 global $wpdb;
				   $user_id = get_current_user_id();
				   $table_name = $wpdb->prefix . 'sg_symlink_generator';
				   $currentDateTime = date('Y-m-d H:i:s');	
				   $results = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE 1 ");	
				   print_r($results);  
				   	?>
	
	<table>
		<thead>
			<tr>
			<td>S.No.</td>
			<td>User Id</td>
			<td>Target</td>		
			</tr>
		</thead>
		
	</table>
		
	
	<?php
	
}
?>
	

