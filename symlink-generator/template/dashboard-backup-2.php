<?php
/*
 * Template Name: Dashboard
 */
?> 
<!--<h2 class="nav-tab-wrapper">
	<a href="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>&tab=add" class="nav-tab nav-tab-active">Create Symlinks</a>	
	<a href="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>&tab=list" class="nav-tab nav-tab-active">All Symlinks</a>
</h2>-->
 <?php
/*
if(!empty($_POST)) 
{
*/
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
//} 
?>
<div id="popup_default" class="popup">
  <div class="popup-overlay"></div>
  <div class="popup-content">
    <a href="#" class="close-popup" data-id="popup_default">&times;</a>
  
	  <p> It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>	   <form method="post" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] )?>" enctype="multipart/form-data">
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
 </div>
</div>
<style>
	.popup {
  display: none;
	position: absolute;
	left: 0;
	right: 0;
	top: 0;
	bottom: 0;
	z-index: 10;
}
.popup-overlay {
	background: rgba(0,0,0,0.9);
	position: absolute;
	left: 0;
	right: 0;
	top: 0;
	bottom: 0;
}
.popup-content {
	position: absolute;
  background: #fff;
	width: 500px;
	margin: -58px 0 0 -264px;
	left: 50%;
	top: 50%;
	z-index: 11;
  padding: 14px;
}
.close-popup {
  display: inline-block;
  position: absolute;
  top: -8px;
  right: -30px;
  font-size: 42px;
}

/* Animations */
.fadeIn {
  animation: fadeIn 0.5s ease-in both;
  -webkit-animation: fadeIn 0.5s ease-in both;
}
@keyframes fadeIn {
  from { opacity: 0; }
}
@-webkit-keyframes fadeIn {
  from { opacity: 0; }
}

.fadeOut {
  animation: fadeOut 0.5s ease-out both;
  -webkit-animation: fadeOut 0.5s ease-out both;
}
@keyframes fadeOut {
  to { opacity: 0; }
}
@-webkit-keyframes fadeOut {
  to { opacity: 0; }
}

.scaleIn {
  animation: scaleIn 0.5s ease-in both;
  -webkit-animation: scaleIn 0.5s ease-in both;
}
@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.5); }
}
@-webkit-keyframes scaleIn {
  from { opacity: 0; -webkit-transform: scale(0.5); }
}

.scaleOut {
  animation: scaleOut 0.5s ease-out both;
  -webkit-animation: scaleOut 0.5s ease-out both;
}
@keyframes scaleOut {
  to { opacity: 0; transform: scale(0.5); }
}
@-webkit-keyframes scaleOut {
  to { opacity: 0; -webkit-transform: scale(0.5); }
}

.scaleUpIn {
  animation: scaleIn 0.5s ease-in both;
  -webkit-animation: scaleIn 0.5s ease-in both;
}
.scaleUpOut {
  animation: scaleUpOut 0.5s ease-in both;
  -webkit-animation: scaleUpOut 0.5s ease-in both;
}
@keyframes scaleUpOut {
  to { opacity: 0; transform: scale(1.2); }
}
@-webkit-keyframes scaleUpOut {
  to { opacity: 0; -webkit-transform: scale(1.2); }
}

.scaleDownIn {
  animation: scaleDownIn 0.5s ease-in both;
  -webkit-animation: scaleDownIn 0.5s ease-in both;
}
.scaleDownOut {
  animation: scaleOut 0.5s ease-in both;
  -webkit-animation: scaleOut 0.5s ease-in both;
}
@keyframes scaleDownIn {
  from { opacity: 0; transform: scale(1.2); }
}
@-webkit-keyframes scaleDownIn {
  from { opacity: 0; -webkit-transform: scale(1.2); }
}

.slideIn {
  animation: slideIn 0.5s ease-in both;
  -webkit-animation: slideIn 0.5s ease-in both;
}
@keyframes slideIn {
  from { opacity: 0; transform: translateY(-50%); }
}
@-webkit-keyframes slideIn {
  from { opacity: 0; -webkit-transform: translateY(-50%); }
}

.slideOut {
  animation: slideOut 0.5s ease-out both;
  -webkit-animation: slideOut 0.5s ease-out both;
}
@keyframes slideOut {
  to { opacity: 0; transform: translateY(50%); }
}
@-webkit-keyframes slideOut {
  to { opacity: 0; -webkit-transform: translateY(50%); }
}

.slideLeftIn {
  animation: slideLeftIn 0.5s ease-in both;
  -webkit-animation: slideLeftIn 0.5s ease-in both;
}
@keyframes slideLeftIn {
  from { opacity: 0; transform: translateX(-50%); }
}
@-webkit-keyframes slideLeftIn {
  from { opacity: 0; -webkit-transform: translateX(-50%); }
}

.slideLeftOut {
  animation: slideLeftOut 0.5s ease-out both;
  -webkit-animation: slideLeftOut 0.5s ease-out both;
}
@keyframes slideLeftOut {
  to { opacity: 0; transform: translateX(50%); }
}
@-webkit-keyframes slideLeftOut {
  to { opacity: 0; -webkit-transform: translateX(50%); }
}

.flipLeftIn {
    -webkit-transform-origin: 50% 50%;
    -moz-transform-origin: 50% 50%;
    transform-origin: 50% 50%;
    -webkit-animation: flipLeftIn .5s both ease-out;
    -moz-animation: flipLeftIn .5s both ease-out;
    animation: flipLeftIn .5s both ease-out;
}
@-webkit-keyframes flipLeftIn {
    from {-webkit-transform: translateZ(-1000px) rotateY(90deg); opacity: .2;}
}
@keyframes flipLeftIn {
    from {transform: translateZ(-1000px) rotateY(90deg);opacity: .2;}
}

.flipLeftOut {
    -webkit-transform-origin: 50% 50%;
    -moz-transform-origin: 50% 50%;
    transform-origin: 50% 50%;
    -webkit-animation: flipLeftOut .5s both ease-in;
    -moz-animation: flipLeftOut .5s both ease-in;
    animation: flipLeftOut .5s both ease-in;
}

@-webkit-keyframes flipLeftOut {
    to {-webkit-transform: translateZ(1000px) rotateY(-90deg); opacity: 0;}
}
@keyframes flipLeftOut {
    to {transform: translateZ(1000px) rotateY(-90deg); opacity: 0;}
}

.flipRightIn {
    -webkit-transform-origin: 50% 50%;
    -moz-transform-origin: 50% 50%;
    transform-origin: 50% 50%;
    -webkit-animation: flipRightIn .5s both ease-out;
    -moz-animation: flipRightIn .5s both ease-out;
    animation: flipRightIn .5s both ease-out;
}
@-webkit-keyframes flipRightIn {
    from {-webkit-transform: translateZ(-1000px) rotateY(-90deg); opacity: .2;}
}
@keyframes flipRightIn {
    from {transform: translateZ(-1000px) rotateY(-90deg);opacity: .2;}
}

.flipRightOut {
    -webkit-transform-origin: 50% 50%;
    -moz-transform-origin: 50% 50%;
    transform-origin: 50% 50%;
    -webkit-animation: flipRightOut .5s both ease-in;
    -moz-animation: flipRightOut .5s both ease-in;
    animation: flipRightOut .5s both ease-in;
}

@-webkit-keyframes flipRightOut {
    to {-webkit-transform: translateZ(1000px) rotateY(90deg); opacity: 0;}
}
@keyframes flipRightOut {
    to {transform: translateZ(1000px) rotateY(90deg); opacity: 0;}
}


.rotateIn {
    -webkit-transform-origin: 50% 50%;
    -moz-transform-origin: 50% 50%;
    transform-origin: 50% 50%;
    -webkit-animation: rotateIn .5s both ease-out;
    -moz-animation: rotateIn .5s both ease-out;
    animation: rotateIn .5s both ease-out;
}
@-webkit-keyframes rotateIn {
    from { -webkit-transform: translateZ(-3000px) rotateZ(-360deg); opacity: 0;}
}
@-moz-keyframes rotateIn {
    from {-moz-transform: translateZ(-3000px) rotateZ(-360deg);opacity: 0;}
}
@keyframes rotateIn {
    from {transform: translateZ(-3000px) rotateZ(-360deg);opacity: 0;}
}

.rotateOut {
    -webkit-transform-origin: 50% 50%;
    -moz-transform-origin: 50% 50%;
    transform-origin: 50% 50%;
    -webkit-animation: rotateOut .5s both ease-in;
    -moz-animation: rotateOut .5s both ease-in;
    animation: rotateOut .5s both ease-in;
}
@-webkit-keyframes rotateOut {
    to {-webkit-transform: translateZ(-3000px) rotateZ(360deg);opacity: 0;}
}
@-moz-keyframes rotateOut {
    to {-moz-transform: translateZ(-3000px) rotateZ(360deg);opacity: 0;}
}
@keyframes rotateOut {
    to { transform: translateZ(-3000px) rotateZ(360deg); opacity: 0;}
}

.rotateCubeIn {
    -webkit-transform-origin: 50% 100%;
    -webkit-animation: rotateCubeIn .6s both ease-in;
    -moz-transform-origin: 50% 100%;
    -moz-animation: rotateCubeIn .6s both ease-in;
    transform-origin: 50% 100%;
    animation: rotateCubeIn .6s both ease-in;
}
@-webkit-keyframes rotateCubeIn {
    0% {opacity: .3;
        -webkit-transform: translateY(-100%) rotateX(90deg);}
    50% {-webkit-animation-timing-function: ease-out;
        -webkit-transform: translateY(-50%) translateZ(-200px) rotateX(45deg); }
}
@-moz-keyframes rotateCubeIn {
    0% {opacity: .3;
        -moz-transform: translateY(-100%) rotateX(90deg); }
    50% {-moz-animation-timing-function: ease-out;
        -moz-transform: translateY(-50%) translateZ(-200px) rotateX(45deg);}
}
@keyframes rotateCubeIn {
    0% {opacity: .3;
        transform: translateY(-100%) rotateX(90deg);}
    50% {animation-timing-function: ease-out;
        transform: translateY(-50%) translateZ(-200px) rotateX(45deg);}
}

.rotateCubeOut {
    -webkit-transform-origin: 50% 0;
    -webkit-animation: rotateCubeOut .6s both ease-in;
    -moz-transform-origin: 50% 0;
    -moz-animation: rotateCubeOut .6s both ease-in;
    transform-origin: 50% 0;
    animation: rotateCubeOut .6s both ease-in;
}
@-webkit-keyframes rotateCubeOut {
    50% {-webkit-animation-timing-function: ease-out;-webkit-transform: translateY(50%) translateZ(-200px) rotateX(-45deg);  }
    100% { opacity: .3; -webkit-transform: translateY(100%) rotateX(-90deg); }
}
@-moz-keyframes rotateCubeOut {
    50% { -moz-animation-timing-function: ease-out;-moz-transform: translateY(50%) translateZ(-200px) rotateX(-45deg);  }
    100% { opacity: .3;-moz-transform: translateY(100%) rotateX(-90deg); }
}
@keyframes rotateCubeOut {
    50% {animation-timing-function: ease-out;
        transform: translateY(50%) translateZ(-200px) rotateX(-45deg); }
    100% { opacity: .3; transform: translateY(100%) rotateX(-90deg);}
}

.popup {
    -webkit-perspective: 1200px;
    -moz-perspective: 1200px;
    perspective: 1200px;
}
.popup-content {
    -webkit-backface-visibility: hidden;
    -moz-backface-visibility: hidden;
    backface-visibility: hidden;
    -webkit-transform: translate3d(0,0,0);
    -moz-transform: translate3d(0,0,0);
    transform: translate3d(0,0,0);
    -webkit-transform-style: preserve-3d;
    -moz-transform-style: preserve-3d;
    transform-style: preserve-3d;
}</style>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-popup-overlay/2.1.5/jquery.popupoverlay.js"></script>
	<script>
		
		// jQuery extend functions for popup
(function($) {
	alert('hello');
  $.fn.openPopup = function( settings ) {
    var elem = $(this);
    // Establish our default settings
    var settings = $.extend({
      anim: 'fade'
    }, settings);
    elem.show();
    elem.find('.popup-content').addClass(settings.anim+'In');
  }
  
  $.fn.closePopup = function( settings ) {
    var elem = $(this);
    // Establish our default settings
    var settings = $.extend({
      anim: 'fade'
    }, settings);
    elem.find('.popup-content').removeClass(settings.anim+'In').addClass(settings.anim+'Out');
    
    setTimeout(function(){
        elem.hide();
        elem.find('.popup-content').removeClass(settings.anim+'Out')
      }, 500);
  }
    
}(jQuery));

// Click functions for popup
$('.open-popup').click(function(){
	
  $('#'+$(this).data('id')).openPopup({
    anim: (!$(this).attr('data-animation') || $(this).data('animation') == null) ? 'fade' : $(this).data('animation')
  });
});
$('.close-popup').click(function(){
  $('#'+$(this).data('id')).closePopup({
    anim: (!$(this).attr('data-animation') || $(this).data('animation') == null) ? 'fade' : $(this).data('animation')
  });
});

// To open/close the popup at any functions call the below
// $('#popup_default').openPopup();
// $('#popup_default').closePopup();

		
		$(document).ready(function() {
		
		$(".popup-content").css("display", "none");
		/*$("#createsymlink").click(function() {
			//alert('hello');
					$("#contactdiv").css("display", "block");
					});*/
				
		
});
</script>
