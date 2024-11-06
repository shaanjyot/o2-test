<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

	// global $wp_roles;
	$roles = wp_roles()->roles;

	$capability_array = array();


	if(sizeof($roles) > 0) 
		{
			foreach($roles as $index => $role)
				{
					
					$capability = $role['capabilities'];
					
					foreach($capability as $key => $cpas)
						{
							array_push($capability_array, $key);
						}
				}
	    }
	$edit_capabilities = array_unique($capability_array);
	//echo "<pre>";
	//echo sizeof($edit_capabilities);
	//print_r($edit_capabilities);
    $role = $_GET['id'];
   	$admin_role_set = get_role( $role );
	$admin_role_set_en_de = json_decode(json_encode($admin_role_set),true);
		
	//print_r($admin_role_set_en_de['capabilities']);
	//echo "</pre>";

	if(isset($_POST['update_role'])){

		$role = $_GET['id'];
		$admin_role_set = get_role( $role );

		$admin_role_set_en_de = json_decode(json_encode($admin_role_set),true);

		foreach ($admin_role_set_en_de['capabilities'] as $key => $value) {
			
			$admin_role_set->remove_cap( $key);
		}
		if(!empty($_POST['wp-re-capabilities'])) {

	        foreach($_POST['wp-re-capabilities'] as $value){
				

				$admin_role_set->add_cap( $value, true );

				//delete_option('remove_capability');

	        }

	    }

	    header("refresh: 1");

	}

?>

 <div class="role_editor_role_inner">
	    <h1 style="text-align: center; margin: 0; padding: 3px;">Edit User Roles</h1>
	    <hr>
        <form method="post" action="">
        	<h2> Current Role :  <?php echo $_GET['id'];?></h2> 
			<div style="margin: 1%;">
				<h2>WP Capabilities</h2>
				<div class="role_editor_item" style="margin-bottom: 10px">
					<div class="role_editor_item_child">
						<input id="role_edotor_input_checkbbox_select_all" type="checkbox" class="role_edotor_input_checkbbox">
						<label for="role_edotor_input_checkbbox_select_all" style="vertical-align: text-bottom;">Select All</label>
					</div>
				</div>
				<div>
				<?php
					foreach($edit_capabilities as $_key => $cap_title)
						{
							//print_r($cap_title);
							$checked = isset($admin_role_set_en_de['capabilities'][$cap_title]) ? 'role_edotor_input_checkbbox_checked' : 'unchecked';
							$checked1 = isset($admin_role_set_en_de['capabilities'][$cap_title]) ? 'checked="checked"' : " "; 

							//print($checked);
				?>
							<div class="role_editor_item">
								<div class="role_editor_item_child <?php print($checked);?>">
									<input <?php print($checked1);?> id="<?php echo $cap_title; ?>" value="<?php echo $cap_title; ?>" type="checkbox" name="wp-re-capabilities[]" class="role_edotor_input_checkbbox <?php print($checked);?>">
									<label for="<?php echo $cap_title; ?>" style="vertical-align: text-bottom;"><?php echo $cap_title; ?></label>
								</div>
							</div>
						<?php
						}
					?>
				</div>
			</div>
			
			<div  style="text-align: right;">
				<button type="button" class="button-primary close" onclick="window.location = 'admin.php?page=role-editor-dashboard'" style="background: #f3f5f6; color: #2771a1; font-size: small;">Cancel</button>
				<button name="update_role" type="submit" class="button-primary">Update Role</button>
			</div>
		</form>
    </div>

 <style>
	.role_editor_item_child
	 {
		 border: 2px solid #cacaca; width: fit-content; padding: 5px 12px 3px 3px; border-radius: 29px;
	 }
	.role_edotor_input_checkbbox
	{
		height: 24px !important;
	    width: 24px !important;
	    border: 2px solid #cacaca !important;
	    border-radius: 16px !important;
	}
	input[type=checkbox].role_edotor_input_checkbbox:checked::before{
    content: "";
    height: 16px;
    width: 16px;
    background: #4b9af7;
    border-radius: 10px;
    margin: 2px;
}

.role_edotor_input_checkbbox_checked
	{
	    border: 2px solid #4b9af7 !important;
	}

/*
input[type=checkbox].role_edotor_input_checkbbox:checked + .role_edotor_input_checkbbox {
    content: "";
    height: 16px;
    width: 16px;
    background: #4b9af7;
    border-radius: 10px;
    margin: 2px;
    border: 1px solid red;
}
*/


	input[type=checkbox].role_edotor_input_checkbbox:focus
		{
			outline: none !important;
		}
	.role_editor_item
	{
		width: 33%; display: inline-block;
		padding: 4px 0;
	}
    .role_editor_role_inner {
        background-color: #fff;
/*         width: 500px; */
        padding: 5px;
        margin: 1% auto;
        border-radius: 6px;
    }
</style>
<script>
    jQuery(document).ready(function (){
	    jQuery('#role_edotor_input_checkbbox_select_all').on('click',function(){
	        if(this.checked){
		        jQuery('.role_edotor_input_checkbbox, .role_editor_item_child').addClass('role_edotor_input_checkbbox_checked');
	            jQuery('.role_edotor_input_checkbbox').each(function(){
	                this.checked = true;
	            });
	        }else{
		        jQuery('.role_edotor_input_checkbbox, .role_editor_item_child').removeClass('role_edotor_input_checkbbox_checked');
	             jQuery('.role_edotor_input_checkbbox').each(function(){  this.checked = false; });
	        }
	    });
	    
	    jQuery('.role_edotor_input_checkbbox').on('click',function(){
		    if(this.checked)
		    	{
			    	jQuery(this).parent().addClass('role_edotor_input_checkbbox_checked');
			    	jQuery(this).addClass('role_edotor_input_checkbbox_checked');
		    	}
		    else
		    	{
			    	jQuery(this).parent().removeClass('role_edotor_input_checkbbox_checked');
			    	jQuery(this).addClass('role_edotor_input_checkbbox_checked');
		    	}
	        if(jQuery('.role_edotor_input_checkbbox:checked').length == jQuery('.role_edotor_input_checkbbox').length)
	        	jQuery('#role_edotor_input_checkbbox_select_all').prop('checked',true);
	        else
	            jQuery('#role_edotor_input_checkbbox_select_all').prop('checked',false);
	        
	    });
	    
	    
/*
        jQuery(".open").click(function (){
            jQuery(".pop-outer").fadeIn("slow");
        });
        jQuery(".close").click(function (){
            jQuery(".pop-outer").fadeOut("slow");
        });
*/
    });
</script>