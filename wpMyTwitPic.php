<?php
/* 
Plugin Name: wp_MyTwitPic
Plugin URI: http://www.abristolgeek.co.uk/
Version: 1.4
Author: <a href="http://www.abristolgeek.com/">jamesakadamingo</a>
Description: Pulls your twitpic photos and displays them on a page.
*/

if (!class_exists("wpMyTwitPic")) 
{
	class wpMyTwitPic
	{
		var $adminOptionsName = "wpMyTwitPic_adminOptions";
		
		function wpMyTwitPic()
		{
			//Constructor	
		}
		
		function init()
		{
			$this->getAdminOptions();
		}
		
		//Returns an array of admin options
		function getAdminOptions()
		{
			$wpMyTwitPic_adminOptions = array(
				'wpmt_show_pics' => 'true',
				'wpmt_username' => 'jamesakadamingo',
				'wpmt_count' => '20',
				'wpmt_margin' => '0',
				'wpmt_border' => '0',
				'wpmt_bordercolour' => 'black',
				'wpmt_credit' => '1'
			);
			
			$myOptions = get_option($this->adminOptionsName);
			
			if (!empty($myOptions))
			{
				foreach ($myOptions as $key => $option)
					$wpMyTwitPic_adminOptions[$key] = $option;	
			}
		
			update_option($this->adminOptionsName,$wpMyTwitPic_adminOptions);
			
			return $wpMyTwitPic_adminOptions;
		}
		
		function addContent($content = '') {
			$myOptions = $this->getAdminOptions();
	
			$pContent = ' '.$content;

			//We are allowed to show the pics
			if (strpos($pContent,'[wpMyTwitPic]') > 0)
			{
				//This is a place that we are meant to be showing the pics.
				$twitPicData = $this->ShowTwitpics($myOptions['wpmt_username'],$myOptions['wpmt_count'],$myOptions['wpmt_margin'],$myOptions['wpmt_border'],$myOptions['wpmt_bordercolour']);
					
				$content = str_replace('[wpMyTwitPic]',$twitPicData,$content);
				
				if( $myOptions['wpmt_credit'] == '1' ) {
					$content.= '<div><small><a href="http://www.abristolgeek.co.uk/wordpress-bits/homebrew-plugins/wpmytwitpic/" target="_blank" title="wpMyTwitPic - a TwitPic plugin for WordPress">Generated by wpMyTwitPic - TwitPic on your blog!</a></small></div>';
				}
				
			}
			
			return $content;
				
		}

		function showAdminPanel() {
			$myOptions = $this->getAdminOptions();
			
			if ( isset($_POST['update_wpMyTwitPicOptions']) ) {

				if ( !check_admin_referer('wpMyTwitPic_update') ) {
					echo '<div class="error">Your request was not completed, a security check failed (NONCE_ERR).</div>';
					
				} else {

					$error = '';
					
					if ( isset($_POST['wpmt_username']) ) {
						$myOptions['wpmt_username'] = $_POST['wpmt_username'];	
						
					}
					if ( isset($_POST['wpmt_count']) ) {
						$myOptions['wpmt_count'] = intval($_POST['wpmt_count']);	
						
					}
					if ( isset($_POST['wpmt_margin']) ) {
						$myOptions['wpmt_margin'] = $_POST['wpmt_margin'];	
						
					}
					if ( isset($_POST['wpmt_border']) ) {
						$myOptions['wpmt_border'] = $_POST['wpmt_border'];	
						
					}
					if ( isset($_POST['wpmt_bordercolour']) ) {
						$myOptions['wpmt_bordercolour'] = $_POST['wpmt_bordercolour'];	
						
					}
					if ( isset($_POST['wpmt_credit']) ) {
						$myOptions['wpmt_credit'] = $_POST['wpmt_credit'];	
					}

					if( !is_int(intval($_POST['wpmt_count'])) ) {
						$error = 'Please enter a valid whole number for the number of images to show.<br/>';	
						
					}
					if( $_POST['wpmt_margin'] == '' ) {
						$myOptions['wpmt_margin'] = '0';
						
					}
					if( $_POST['wpmt_border'] == '' ) {
						$myOptions['wpmt_border'] = '0';
						
					}
					if( $_POST['wpmt_bordercolour'] == '' ) {
						$myOptions['wpmt_bordercolour'] = 'black';
						
					}
					
					if ( $error !== '' ) {
						echo '<div class="error"><p><strong>Error: '.$error.'</strong></p></div>';	

					} else {
						update_option($this->adminOptionsName,$myOptions);					
						echo '<div class="updated"><p><strong>Settings Updated</strong></p></div>';

					}
					
				}
			}
			
			?>
			
            	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
					<?php if ( function_exists('wp_nonce_field') ) wp_nonce_field('wpMyTwitPic_update'); ?>
                	<h2>wpMyTwitPic Admin</h2>
                    <p>
	                    Please enter the your Twitter details, and any other settings you wish to make. Please note that your twitter
    	                login details are not required, as TwitPic photos are not private and can be viewed with just your username.
                    </p>
                    <p>
        	            <label style="display:inline-block;width:400px;float:left;">Your Twitter Username</label>
            	        <input type="text" id="wpmt_username" name="wpmt_username" value="<?php _e(apply_filters('format_to_edit',$myOptions['wpmt_username']), 'wpMyTwitPic') ?>"/>
                    </p>
                    <p>
                     	<label style="display:inline-block;width:400px;float:left;">Number of images to show</label>
                        <input type="text" id="wpmt_count" name="wpmt_count" value="<?php _e(apply_filters('format_to_edit',$myOptions['wpmt_count']), 'wpMyTwitPic') ?>" />
                    </p> 
                    <p>
                     	<label style="display:inline-block;width:400px;float:left;">Image margin in pixels</label>
                        <input type="text" id="wpmt_margin" name="wpmt_margin" value="<?php _e(apply_filters('format_to_edit',$myOptions['wpmt_margin']), 'wpMyTwitPic') ?>" />
                    </p> 
                    <p>
                     	<label style="display:inline-block;width:400px;float:left;">Image border in pixels</label>
                        <input type="text" id="wpmt_border" name="wpmt_border" value="<?php _e(apply_filters('format_to_edit',$myOptions['wpmt_border']), 'wpMyTwitPic') ?>" />
                    </p>
                    <p>
                     	<label style="display:inline-block;width:400px;float:left;">Image border colour (include # if using hex)<br/>( 
                        	<a href="http://www.w3schools.com/html/html_colornames.asp" target="_blank" title="List of valid html colour values" rel="nofollow">
                            List of HTML Colours
                            </a>
                            )</label>
                        <input type="text" id="wpmt_bordercolour" name="wpmt_bordercolour" value="<?php _e(apply_filters('format_to_edit',$myOptions['wpmt_bordercolour']), 'wpMyTwitPic') ?>" />
                    </p>                
                    <br/>
                    <p>
                    	<label style="display:inline-block;width:400px;float:left;">Credit wpMyTwitPic in really tiny letters after the images?</label>

                        <select id="wpmt_credit" name="wpmt_credit">
                        	<option value="1" <?php if ( $myOptions['wpmt_credit'] == 1 ) { echo 'selected="selected"'; } ?>>Yes :-)</option>
                            <option value="0" <?php if ( $myOptions['wpmt_credit'] == 0 ) { echo 'selected="selected"'; } ?>>No :-(</option>
                        </select>
                    </p>
                    
                    <br/>
                    <p>
	                    <input type="submit" id="update_wpMyTwitPicOptions" name="update_wpMyTwitPicOptions" value="<?php _e('Update Settings', 'wpMyTwitPic') ?>"/>
                    </p>
                    
                    <div>
                    	Thank you for using wpMyTwitPic, if you have any suggestions or questions about wpMyTwitPic then feel free to contact me on the 
                        <a href="http://www.abristolgeek.co.uk/wordpress-bits/homebrew-plugins/wpmytwitpic/" target="_blank" title="wpMyTwitPit @ ABristolGeek.co.uk">
                        wpMyTwitPic homepage</a>
                        at www.ABristolGeek.co.uk.
                        <p>
                            It's always nice to know when someone is using your work so if you would be kind enough to leave me a comment on my 
                            <a href="http://www.abristolgeek.co.uk/wordpress-bits/homebrew-plugins/wpmytwitpic/" target="_blank" title="wpMyTwitPit @ ABristolGeek.co.uk">
                            blog
                            </a> it would make me smile!
                        </p>
                        <p>
                        	Thank you,<br/><br/>
                            jamesakadamingo (follow me on <a href="http://twitter.com/jamesakadamingo" target="_blank" title="jamesakadamingo on Twitter">Twitter<a/>)
                        </p>
                        
                    </div>
                </form>
			<?php
		} //END of showAdminPanel

		function ShowTwitpics($username = 'jamesakadamingo',$count=20,$margin=0,$border=0,$bordercolor='black')
		 {
			$file = @file_get_contents("http://www.twitpic.com/photos/".$username."/feed.rss");
			$output ='';
			for($i = 1; $i <= $count; ++$i) {
				$pic = explode('"><img src="', $file);
				$pic = explode('"></a>]]></description>', $pic[$i]);
				$pic = trim($pic[0]);
				$pic = str_replace('.jpg','',$pic);
				
				$url = explode('<guid>', $file);
				$url = explode('</guid>', $url[$i]);
				$url = trim($url[0]);
				
				$output .= '<a href="'.$url.'" target="_new" /><img src="'.$pic.'" width="100" height="100" style="margin: '.$margin.'px; border: '.$border.'px solid '.$bordercolor.';" class="mypictures" /></a>';
				
			}
			return $output;
		}
		
	}
}

if (class_exists("wpMyTwitPic"))
{
	$wpMyTwitPic = new wpMyTwitPic();
}

//Initialize the admin panel
if (!function_exists("wpMyTwitPic_ap")) {
	function wpMyTwitPic_ap() {
		global $wpMyTwitPic;
		if (!isset($wpMyTwitPic)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('wpMyTwitPic Options', 'wpMyTwitPic', 9, basename(__FILE__), array(&$wpMyTwitPic, 'showAdminPanel'));
		}
	}	
}


if (isset($wpMyTwitPic))
{
	//Actions
	add_action('wp_MyTwitPic/wp_MyTwitPic.php',  array(&$wpMyTwitPic, 'init'));
	add_action('the_content',array(&$wpMyTwitPic,'addContent'),1);	
	add_action('admin_menu', 'wpMyTwitPic_ap');
}

?>