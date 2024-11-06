<?php
// Exit if accessed directly
if(!defined('ABSPATH')) exit;
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
	$unique_capabilities = array_unique($capability_array);
	if(isset($_REQUEST['role-editor-create-role']))
		{
			$role_editor_create_new_role = $_REQUEST['role_editor_create_new_role'];
			$role_editor_create_new_role_id = 're_' . strtolower(str_replace(' ', '_', $role_editor_create_new_role));
			$new_capability_array = $_REQUEST['wp-re-capabilities'];
			$allcapabilitesArray = array();
			$i =0;
			foreach($new_capability_array as $c)
			{
				$allcapabilitesArray[$c] = true;
			}
			$result = add_role($role_editor_create_new_role_id, __($role_editor_create_new_role), $allcapabilitesArray);
			// return $result;
		}
//$link = ABSPATH . 'wp-content/plugins/'.$foder_name;
?>
    <div class="role_editor_role_inner">
	    <h1 style="text-align: center; margin: 0; padding: 3px;">Add User Roles</h1>
	    <hr>
        <form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" enctype="multipart/form-data">
			<table class="form-table" role="presentation" style="margin: 1% 0;">
				<tbody>
					<tr>
						<td style="vertical-align: initial; width: 200px"><h3 style="margin: 0;">Role Title</h3></td>
						<td>
							<input id="role_editor_create_new_role" type="text" name="role_editor_create_new_role" placeholder="Enter Title Here" style="min-width: 50%;">
						</td>
					</tr>
				</tbody>
			</table>
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
					for($i = 0; $i < sizeof($unique_capabilities); $i++)
						{
					?>
							<div class="role_editor_item">
								<div class="role_editor_item_child">
									<input id="<?php echo $unique_capabilities[$i]; ?>" value="<?php echo $unique_capabilities[$i]; ?>" type="checkbox" name="wp-re-capabilities[]" class="role_edotor_input_checkbbox">
									<label for="<?php echo $unique_capabilities[$i]; ?>" style="vertical-align: text-bottom;"><?php echo $unique_capabilities[$i]; ?></label>
								</div>
							</div>
					<?php
						}
					?>
				</div>
			</div>
			
			<div  style="text-align: right;">
				<button type="button" class="button-primary close" onclick="window.location = 'admin.php?page=role-editor-dashboard'" style="background: #f3f5f6; color: #2771a1; font-size: small;">Cancel</button>
				<button name="role-editor-create-role" type="submit" class="button-primary">Add Role</button>
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