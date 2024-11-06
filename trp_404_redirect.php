<?php 

/* To FIX TranslatePress Plugin Default Language 404 issues*/

if( !function_exists('redirect_404_to_page') ){
	
    add_action( 'template_redirect', 'redirect_404_to_page' );
	
      function redirect_404_to_page(){
	
		$site_uri =  $_SERVER['REQUEST_URI'];
		$url_parts = explode("/",$site_uri);
		$url_parts_count = count($url_parts);
		$site_url =  get_site_url();	
		$default_language_slug ="";
		
		if(is_404()):			
			if (check_plugin_state('TRP_Translate_Press')) { 
			
				// Check the TranslatePress Plugin is active
				//If active, fetch the languages settings and Default langauge
				$trp_instance = TRP_Translate_Press::get_trp_instance();
				$translator = $trp_instance->get_component('translation_render');
				$translator_original_language = $trp_instance->get_component('settings')->get_setting('default-language');
				$trp_languages = $trp_instance->get_component( 'languages' );
				$trp_settings  = $trp_instance->get_component( 'settings' );
				$settings      = $trp_settings->get_settings();	
				$default_language_slug = $settings['url-slugs'][$translator_original_language];	
				
				if((strpos($site_uri, "/".$default_language_slug) !== false) && (strpos($site_uri, "/".$default_language_slug."/") == false))
				{
						if($url_parts[$url_parts_count-1]===$default_language_slug ){							
							wp_safe_redirect( home_url('/') );
							exit;
						}
				}
				elseif((strpos($site_uri, "/".$default_language_slug."/") !== false))
				{	
					$location_path = explode("/".$default_language_slug."/",$site_uri); 
					$location = home_url('/').$location_path[1];
					wp_safe_redirect( $location );
					exit;
				}
				elseif((strpos($site_uri, "spanish") !== false && $translator_original_language == "es_CL" && $default_language_slug !=="spanish" ))
				{	
					if($url_parts[$url_parts_count-1]==="spanish" ){							
						wp_safe_redirect( home_url('/') );
						exit;
					}
					else
					{
						$location_path = explode("/spanish/",$site_uri); 
						$location = home_url('/').$location_path[1];
						wp_safe_redirect( $location );
					}						
				}				
				elseif((strpos($site_uri, "english") !== false && $translator_original_language == "es_CL" && $settings['url-slugs']['en_US']!=="english" ))
				{	
					if($url_parts[$url_parts_count-1]==="english" ){							
						wp_safe_redirect( home_url('/').$settings['url-slugs']['en_US'] );
						exit;
					}
					else
					{
						$location_path = explode("/english/",$site_uri); 
						$location = home_url('/').$settings['url-slugs']['en_US']."/".$location_path[1];
						wp_safe_redirect( $location );
					}						
				}
					 
			}	
		endif;
		
	}
}

/*Check Plugin Status */
function check_plugin_state($plugin_class){
	if(class_exists($plugin_class))		
		return true;	// 'plugin is active'
	else		
		return false;	// 'plugin is not active'	
}
add_action('admin_init', 'check_plugin_state');

?>