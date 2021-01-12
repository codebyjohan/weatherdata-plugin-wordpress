<?php

if(isset($_POST['save_settings'])){ // check if form submitted
	// update all options
	update_option( 'weather_data_shop_page', filter_var( $_POST['shop_page'] , FILTER_SANITIZE_STRING ) );
	update_option( 'weather_data_cart_page', filter_var( $_POST['cart_page'] , FILTER_SANITIZE_STRING ) );
	update_option( 'weather_data_checkout_page', filter_var( $_POST['checkout_page'] , FILTER_SANITIZE_STRING ) );
	update_option( 'weather_data_single_page', filter_var( $_POST['single_page'] , FILTER_SANITIZE_STRING ) );
	update_option( 'weather_data_myaccount_page', filter_var( $_POST['myaccount_page'] , FILTER_SANITIZE_STRING ) );
	// alert code
	echo '<div class="notice notice-success">'.__('Settings has been saved!','weatherData').'</div>';
	// Get settings options after updating the settings
	$this->weather_data_get_settings();
}
?>
<h1><?php _e('Settings','weatherData'); ?></h1>
<form method="post">
	<table class="form-table">
		<tr>
			<th><?php _e('Show in shop page','weatherData'); ?></th>
			<td>
				<input type="checkbox" name="shop_page" <?php echo $this->shop_page ? "checked" : ""; ?>>
			</td>
		</tr>
		<tr>
			<th><?php _e('Show in cart page','weatherData'); ?></th>
			<td>
				<input type="checkbox" name="cart_page" <?php echo $this->cart_page ? "checked" : ""; ?>>
			</td>
		</tr>
		<tr>
			<th><?php _e('Show in Checkout page page','weatherData'); ?></th>
			<td>
				<input type="checkbox" name="checkout_page" <?php echo $this->checkout_page ? "checked" : ""; ?>>
			</td>
		</tr>
		<tr>
			<th><?php _e('Show in Single product page','weatherData'); ?></th>
			<td>
				<input type="checkbox" name="single_page" <?php echo $this->single_page ? "checked" : ""; ?>>
			</td>
		</tr>
		<tr>
			<th><?php _e('Show in My account page','weatherData'); ?></th>
			<td>
				<input type="checkbox" name="myaccount_page" <?php echo $this->myaccount_page ? "checked" : ""; ?>>
			</td>
		</tr>
		<tr>
			<th><input type="submit" class="button button-primary" name="save_settings" value="<?php _e('Save settings','weatherData'); ?>"></th>
		</tr>
	</table>
</form>