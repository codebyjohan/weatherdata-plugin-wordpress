<?php

/*

Plugin Name: Plugin 1 - Weather data
Description: This plugin will help to retrive weather data
Author: Johan Östby
Version: 1.0

*/

defined('ABSPATH') or die('You cannot access to this page');

if ( ! class_exists('WeatherData') ) {
	Class WeatherData{
		function __construct(){
			add_action( 'admin_menu', array( $this, 'weatherSettings') );
			add_shortcode( 'weather_data_ui', array($this,'weather_data_ui_callback') );
			// Get settings options
			$this->weather_data_get_settings();
			// check if the options and hook the functions accordingly
			if($this->shop_page)
				add_action( 'woocommerce_archive_description', array( $this, 'apply_weather_data_shortcode') );
			if($this->cart_page)
				add_action( 'woocommerce_before_cart', array( $this, 'apply_weather_data_shortcode') );
			if($this->single_page)
				add_action( 'woocommerce_before_single_product', array( $this, 'apply_weather_data_shortcode') );
			if($this->checkout_page)
				add_action( 'woocommerce_before_checkout_form', array( $this, 'apply_weather_data_shortcode') );
			if($this->myaccount_page)
				add_action( 'woocommerce_before_my_account', array( $this, 'apply_weather_data_shortcode') );
		}

		function weather_data_get_settings(){
			$this->shop_page = $this->weather_option_sanitizer( 'weather_data_shop_page' );
			$this->cart_page = $this->weather_option_sanitizer( 'weather_data_cart_page' );
			$this->checkout_page = $this->weather_option_sanitizer( 'weather_data_checkout_page' );
			$this->single_page = $this->weather_option_sanitizer( 'weather_data_single_page' );
			$this->myaccount_page = $this->weather_option_sanitizer( 'weather_data_myaccount_page' );
		}


		function weather_option_sanitizer( $option_name ){
			return sanitize_option( $option_name, get_option( $option_name ) );
		}

		function apply_weather_data_shortcode(){
			do_shortcode( '[weather_data_ui]' );
		}


		function weather_data_ui_callback(){
			// get cache data
			$weatherdata = $this->weather_data_get_cache();
			?>
			<div style="text-align: center;margin:32px 0px;">
			<h2>Weather data of London</h2>
			<table style="margin:16px 0px;">
				<tr>
					<th>State</th>
					<th>Min temp. (Celsius)</th>
					<th>Max temp. (Celsius)</th>
					<th>Date</th>
				</tr>
			<?php
			foreach ($weatherdata['consolidated_weather'] as $data) {
				$icon_url = plugin_dir_url( __FILE__ ).'icons/'.$data['weather_state_abbr'].'.svg'; // Prepare icon url
				?>
				<tr>
					<td><img style="width:50px;margin:8px;" src="<?php echo $icon_url; ?>" /><?php echo $data['weather_state_name']; ?></td>
					<td><?php echo $data['min_temp']; ?></td>
					<td><?php echo $data['max_temp']; ?></td>
					<td><?php echo date('M d, Y',strtotime($data['applicable_date'])); ?></td>
				</tr>
				<?php
			}
			echo '</table></div>';
		}

		function weather_data_get_cache(){
			// get cache data
			$cache_data = $this->weather_option_sanitizer('weather_data_cache');
			// check if there is cache data
			if( $cache_data ){
				// get cached time
				$cache_time = $cache_data['cache_time'];
				// set expire time offset
				$check_time_str = current_time( 'mysql' ).' -60 minutes';
				// check cache expired or not
				if( strtotime( $check_time_str ) >= $cache_time ) {
					// unset cache if expired
					update_option( 'weather_data_cache', false );
					// call this method again to set the cache since the $cache_data would be false then it will trigger the else statements
					return $this->weather_data_get_cache();
				} else {
					// if didn't expire then return cache data
					return $cache_data['weather_data'];
				}
			} else {
				// call API
				$weatherdata = $this->weather_data_getApiData(); // Call api
				// set cache
				$set_cache = array(
					'weather_data' => $weatherdata,
					'cache_time' => current_time( 'timestamp' )
				);
				// set cache in database
				update_option( 'weather_data_cache', $set_cache );
				// return weather data
				return $weatherdata;
			}
		}


		function weatherData_activate(){
			flush_rewrite_rules(); // This is Rewrite rule - it actually refresh system
		}


		function weather_data_getApiData(){
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://www.metaweather.com/api/location/44418/",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			));
			$response = curl_exec($curl);
			curl_close($curl);
			return json_decode( $response, true );
		}


		function weatherSettings(){
			add_options_page( 'Weather settings', 'Weather settings', 'manage_options', 'weather_settings', array($this,'weather_settings_callback' ));
		}


		function weather_settings_callback(){
			include( plugin_dir_path( __FILE__ ).'/settings-template.php' );
		}
	}
}

if ( class_exists('WeatherData') ) {
	$weatherData = new WeatherData();
	register_activation_hook( __FILE__, array( $weatherData, 'weatherData_activate' ) );
}