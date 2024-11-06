<?php 

//INSERT SIMPLIA HEADER/FOOTER

function insert_simplia_footer() {
    echo "<script>
	  var element = document.createElement('div');
	  element.id = 'CommAppWidgetsIdTwo'
	  document.body.appendChild(element);
	  window.addEventListener('DOMContentLoaded', function() {
		DeviceManager.DeviceAppWidgets.setOptions({
			widgetId:'_FFFFFFFFFFFFFF000055555555555555_',//Unique Oxygen Id for every widget
			targetId:'CommAppWidgetsIdTwo', //Div id where user wants to place icon
			showDescription: true
		})
		DeviceManager.DeviceAppWidgets.render();
	  })
	</script>";
	if(get_option( 'show_contact_button' ))
		echo '<script src="/public/as/_FFFFFFFFFFFFFF00001618436548084142_/at/_FFFFFFFFFFFFFF00001618436548084142_/contact-button.js"></script>';
}
add_action('wp_footer', 'insert_simplia_footer', 5);

function insert_simplia_header() {
	$color ="#ff0000";
	if(!empty(get_option( 'simplia_menu_color' )))
		$color = get_option( 'simplia_menu_color' );

	echo '<script src="https://simplia.com/DeviceManager/dist/DeviceManager.min.js"></script>';
	echo '<link rel="stylesheet"  href="https://simplia.com/simplia/dist/css/all.css" type="text/css" media="all"/>';
	echo '<style type="text/css">
	.float-top { 
		width: unset !important;
		right: 0; }
	 .float-top i { color: '.$color.' !important;}</style>';	
}

add_action('wp_head', 'insert_simplia_header', 5);

add_action('admin_menu', 'simplia_menu_settings_init');

function simplia_menu_settings_init() {

	//create new top-level menu
	add_submenu_page( 'options-general.php','Simplia Menu Settings', 'Simplia Menu Settings', 'administrator', 'simplia-menu-settings', 'simplia_menu_settings');
	add_submenu_page( 'options-general.php','Simplia Server Cache Settings', 'Simplia Server Cache Settings', 'administrator', 'simplia-server-cache-settings', 'simplia_server_cache_settings');

	//call register settings function
	add_action( 'admin_init', 'register_simplia_menu_settings' );
}


function register_simplia_menu_settings() {
	//register our settings
	register_setting( 'simplia-menu-settings-group', 'show_contact_button' );
	register_setting( 'simplia-menu-settings-group', 'simplia_menu_color' );
}

function simplia_menu_settings() { ?>
	<form method="post" action="options.php">
	<?php
	settings_fields( 'simplia-menu-settings-group' ); 
    do_settings_sections( 'simplia-menu-settings-group' ); 
	$checked = '';
	if(get_option( 'show_contact_button' ))
		$checked = 'checked="true"';
		
    ?>
	
	  <h1> <?php esc_html_e( 'Simplia Menu Settings', 'simplia' ); ?> </h1>
	 <table class="form-table">
		<tr valign="top">
			<th scope="row">Show Contact Button</th>
			<td> <input type="checkbox" <?php echo $checked; ?> id="show_contact_button" name="show_contact_button" value="true"></td>
		</tr>        
		<tr valign="top">
			<th scope="row">Simplia Menu Color</th>
			<td> <input type="text" id="simplia_menu_color" name="simplia_menu_color" value="<?php echo get_option( 'simplia_menu_color' ); ?>"></td>
		</tr>	
    </table>    
    <?php submit_button(); ?>
	</form>
<?php 
} 

function simplia_server_cache_settings(){
	if(!empty($_POST['clear_btn']) && $_POST["clear_btn"] == 'Clear'){
		$wp_name = !empty($_POST["simplia_wp_name"]) ? $_POST["simplia_wp_name"] : "";
		$domain_name = !empty($_POST["simplia_domain_name"]) ? $_POST["simplia_domain_name"] : "";  
		$server_url = array(
		'1' => 'https://debianlargeserver-0050-wordpress-0099.laxroute53.com/OxygenCoreWordPress/wp-json/oxygen/clear-cache?wpname='.$wp_name,
		'2' => 'https://debianlargeserver-0050-wordpress-0100.laxroute53.com/OxygenCoreWordPress/wp-json/oxygen/clear-cache?wpname='.$wp_name,
		'3' => 'https://debianlargeserver-0050-wordpress-0101.laxroute53.com/OxygenCoreWordPress/wp-json/oxygen/clear-cache?wpname='.$wp_name,
		'4' => 'https://simplia.com/CommonServices-0025/CloudFront/Invalidate?DomainName='.$domain_name
		);
		
		if(!empty($server_url)){
			foreach($server_url as $url){
				$ch = curl_init();
				$timeout = 5;
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

				$data = curl_exec($ch);

				curl_close($ch);
			}
			if($data == true){
				echo 'Server Cache Cleared Successfully.';
			}
			else{
				if($data == false){

					echo 'Server Cache Already Cleared Successfully.';
				}
			}
		}

			
			

		//echo "yes";
	}
		echo "WP_MEMORY_LIMIT:".WP_MEMORY_LIMIT;
	?>
	<form method="post" id="server_cache_form">
	  <h1> <?php esc_html_e( 'Simplia Server Cache Settings', 'simplia' ); ?> </h1>
	  <table class="form-table">
	  <tr valign="top">
				<th scope="row">Domain Name<span style="color:red;">*</span><p>(example: domain-name.com without without http://www.)</p></th>
				<td> <input type="text" id="simplia_domain_name" name="simplia_domain_name" required ></td>
		</tr>
	     <tr valign="top">
				<th scope="row">WP NAME<span style="color:red;">*</span><p>(example:  https://simplia.com/OxygenWordPress-0046/Organizations/WP_NAME/)</p></th>
				<td> <input type="text" id="simplia_wp_name" name="simplia_wp_name" required></td>
		</tr>
	  </table>
	  <p class="submit">
		<input type="submit" name="clear_btn" id="clear_btn" class="button button-primary" value="Clear">
	  </p>
	  </form>
	  	<?php
}


?>

