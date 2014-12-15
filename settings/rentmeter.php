<?php

function srp_RentMeter_options_page(){

  echo '<div class="wrap srp">';
  echo '<h2>Rental Rates Meter by Rentometer.com</h2>';
  srp_updated_message();
  ?>
  <form method="post" action="options.php">
  <?php settings_fields('srp-rentometer-options'); ?>
  	<div class="postbox-container" style="width:70%;">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
				<div class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Rental Rates Meter Options</span></h3>
					<div class="inside">											  
					  <table class="form-table">
						<tr valign="bottom">
						  <th scope="row"><div align="right">Rentometer API Key: </div></th>
						  <td><input name="srp_rentometer_api_key" type="text" value="<?php echo get_option('srp_rentometer_api_key');?>" size="30" />
							<br /> 
							To obtain your free API key <a href="http://www.rentometer.com/api_users/subscribe/" target="_blank">register at Rentometer.com</a>.</td>
						</tr>
					  </table>
						<p class="submit">
						<input name="srp_RentMeter_submit" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<?php
		echo srp_settings_right_column();
	?></form>

  <?php
  
  echo '</div>';
}
?>