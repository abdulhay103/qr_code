<?php 

	/**

	 * Plugin Name:			QR Code Viewer
	 * Plugin URI:			http://journeybyweb.com/
	 * Description:			Handle your posts "QR Code" easily with this plugin.
	 * Version:				1.0.0
	 * Requires at least:	5.2
	 * Requires PHP:		7.2
	 * Author:				Abdul Hay
	 * Author URI:			http://abdulhay.journeybyweb.com/
	 * License:				GPL v2 or later
	 * License URI:			https://www.gnu.org/licenses/gpl-2.0.html
	 * Text Domain:			QR_Code_Viewer
	 * Domain Path:			/languages

	*/

	/*function QR_Code_Viewer_activation_hook(){}
		register_activation_hook( __FILE__, 'QR_Code_Viewer_activation_hook' );

	function QR_Code_Viewer_deactivation_hook(){}
		register_deactivation_hook( __FILE__, 'QR_Code_Viewer_activation_hook' );*/


			// Global Variables

			$qrcv_countries = array(
				__('Afganistan', 'QR_Code_Viewer'), 
				__('Bangladesh', 'QR_Code_Viewer'), 
				__('Bhutan', 'QR_Code_Viewer'), 
				__('India', 'QR_Code_Viewer'), 
				__('Maldives', 'QR_Code_Viewer'), 
				__('Nepal', 'QR_Code_Viewer'), 
				__('Pakistan', 'QR_Code_Viewer'), 
				__('Sri Lanka', 'QR_Code_Viewer')
			);

	function qrcv_init(){
		global $qrcv_countries;
		$qrcv_countries = apply_filters( 'qrcv_countries', $qrcv_countries );
	}
	add_action( 'init', 'qrcv_init');
 
	function QR_Code_Viewer_load_textdomain(){
		load_plugin_textdomain( 'QR_Code_Viewer', false, dirname(__FILE__).'/languages' );
	}
	add_action( 'plugin_loaded', 'QR_Code_Viewer_load_textdomain');


	function qrcv_displaying_qr_code($content){
		$current_post_id = get_the_ID();
		$current_post_title = get_the_title($current_post_id);
		$current_post_url = urlencode(get_the_permalink($current_post_id));
		$current_post_type = get_post_type( $current_post_id);

		//post type check
		$excluded_post_types = apply_filters( 'qrcv_excluded_post_type', array());
		if (in_array($current_post_type, $excluded_post_types)) {
			return $content;
		}

		// Dimension Hook

		$height = get_option('qrcv_height');
		$width = get_option( 'qrcv_width');
		$height = $height ? $height : 150;
		$width = $width ? $width : 150;
		$dimension = apply_filters( 'qrcv_dimension', "{$width}x{$height}" );
 

		$image_src = sprintf('http://api.qrserver.com/v1/create-qr-code/?data=%s&size=%s', $current_post_url, $dimension );
		$content .= sprintf("<div class='qrcode_img' ><img src='%s' altr='%s' /></div>", $image_src, $current_post_title );
		return $content;
	}
	add_filter( 'the_content', 'qrcv_displaying_qr_code');


	function qrcv_setting_init(){
		add_settings_section( 'qrcv_section', __('Section For QR Code', 'QR_Code_Viewer'), 'qrcv_section_callback', 'general');

		add_settings_field( 'qrcv_height', __('QR Code Viewer', 'QR_Code_Viewer'), 'qrcv_displaying_field','general','qrcv_section', array('qrcv_height'));
		add_settings_field( 'qrcv_width', __('QR Code Viewer', 'QR_Code_Viewer'), 'qrcv_displaying_field','general', 'qrcv_section', array('qrcv_width'));
		add_settings_field( 'qrcv_countries_dropdrown', __('Select Country', 'QR_Code_Viewer'), 'qrcv_displaying_countries_dropdrown_field','general', 'qrcv_section');
		add_settings_field( 'qrcv_countries_checkbox', __('Select Multi Country', 'QR_Code_Viewer'), 'qrcv_displaying_countries_checkbox_field', 'general', 'qrcv_section' );
		add_settings_field( 'qrcv_toggle', __('Toggle Area', 'QR_Code_Viewer'), 'qrcv_displaying_toggle_field', 'general', 'qrcv_section' );



		register_setting( 'general', 'qrcv_height', array('sanitize_callback' => 'esc_attr') );
		register_setting( 'general', 'qrcv_width', array('sanitize_callback' => 'esc_attr') );
		register_setting( 'general', 'qrcv_countries_dropdrown', array('sanitize_callback' => 'esc_attr') );
		register_setting( 'general', 'qrcv_countries_checkbox');
		register_setting( 'general', 'qrcv_toggle');



	
		function qrcv_section_callback(){
			echo "<p>" . __('Setting for Posts To QR Code Plugin', 'QR_Code_Viewer'). "</p>";
		}

		function qrcv_displaying_field($args){
			$option = get_option($args[0]);
			printf("<input type='number' placeholder='Choseable Height' id='%s' name='%s' value='%s'/> Pixel", $args[0], $args[0], $option );
		}

		function qrcv_displaying_countries_dropdrown_field(){
			global $qrcv_countries;
			$option = get_option('qrcv_countries_dropdrown');
			printf("<select name='%s' id='%s'>", 'qrcv_countries_dropdrown', 'qrcv_countries_dropdrown' );
 
			foreach ($qrcv_countries as $country) {
				$selected = '';
				if ($option == $country){
					$selected = 'selected';
				};
				printf("<option value='%s' %s >%s</option>", $country, $selected, $country);
			}
			echo "</select>";
		}

		function qrcv_displaying_countries_checkbox_field(){
			global $qrcv_countries;
			$option = get_option('qrcv_countries_checkbox');

			foreach ($qrcv_countries as $country) {
				$selected = '';
				if (is_array($option) && in_array($country, $option)){
				 	$selected = 'checked';
				}
				printf("<input type='checkbox' name='qrcv_countries_checkbox[]' value='%s' %s /> %s <br/>", $country, $selected, $country);
			}
		}


		//for toggler
		function qrcv_displaying_toggle_field(){
			$option = get_option('qrcv_toggle');
			echo "<div id='toggle1'></div>";
			echo "<input type='hidden' name='qrcv_toggle' id='qrcv_toggle' value='".$option."' />";

		}

	}
	add_action('admin_init', 'qrcv_setting_init' );

	function qrcv_includes($screen){
		if ('options-general.php' == $screen) {
			wp_enqueue_style( 'minitoggle_css', plugin_dir_url(__FILE__) . "/includes/css/minitoggle.css");
			wp_enqueue_script( 'minitoggle_js', plugin_dir_url(__FILE__) . "/includes/js/minitoggle.js", array('jquery'), '1.0', true );
			wp_enqueue_script( 'qrcv_main_js', plugin_dir_url(__FILE__) . "/includes/js/qrcv_main.js", array('jquery'), time(), true );
		}
	}
	add_action( 'admin_enqueue_scripts', 'qrcv_includes');

?>
