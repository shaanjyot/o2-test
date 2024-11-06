<?php
/*
if(!empty($_POST)) 
{
*/
    if(isset($_POST['create-symlink']))
    	{
	    	$symlink_target =  $_POST['symlink_target'];
	    	$foder_name = basename($symlink_target).PHP_EOL;
	    	$foder_name = preg_replace('/@\w+/', '', $foder_name);
	    	$foder_name = str_replace("?", '', $foder_name);
	    	$foder_name = str_replace(" ", '', $foder_name);
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
				   wp_redirect( esc_url( add_query_arg() ) );
				   exit;
				} 
			else 
				{ 
				   echo ("Symlink cannot be created!"); 
				} 
			//echo readlink($link);
    	}
//} 
?>
<div style="display: none;" class="pop-outer">
    <div class="pop-inner">
	    <h1 style="text-align: center; margin: 0; padding: 8px;">Create New Symlink</h1>
	    <hr>
        <form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" enctype="multipart/form-data">
			<table class="form-table" role="presentation" style="margin: 6% 0;">
				<tbody>
					<tr>
						<td style="vertical-align: initial;"><h3 style="margin: 0;">Target</h3></td>
						<td>
							<input id="target" type="text" name="symlink_target" placeholder="eg: ./uploads/cache" style="min-width: 100%;">
							<div>
								<label for="target">
								<span class="description">Enter the plugin path whose symlink you want to add into your wp-plugin. This field should not be empty.</span>
								</label>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<div  style="text-align: right;">
				<button type="buttom" class="button-primary close" style="background: #f3f5f6; color: #2771a1; font-size: small;">Cancel</button>
				<button name="create-symlink" type="submit" class="button-primary">Create Symlink</button>
			</div>
		</form>
    </div>
</div>


 <style>
    .pop-outer {
        background-color: rgba(0, 0, 0, 0.5);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .pop-inner {
        background-color: #fff;
        width: 500px;
        padding: 5px;
        margin: 5% auto;
        border-radius: 10px;
    }
</style>
<script>
    jQuery(document).ready(function (){
        jQuery(".open").click(function (){
            jQuery(".pop-outer").fadeIn("slow");
        });
        jQuery(".close").click(function (){
            jQuery(".pop-outer").fadeOut("slow");
        });
    });
</script>