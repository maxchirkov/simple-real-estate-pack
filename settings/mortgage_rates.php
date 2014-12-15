<?php

function srp_MortgageRates_options_page(){

  echo '<div class="wrap srp">';
  echo '<h2>Mortgage Rates by Zillow.com</h2>';
  srp_updated_message();
  /*Converting deprecated options*/
  if(!$mortgage_rates_options = get_option('srp_mortgage_rates')){
      $mortgage_rates_options = array(
          'getratesummary_api_key' => get_option('srp_getratesummary_api_key'),
          'getratesummary_state' => get_option('srp_getratesummary_state'),
          'display_rates' => get_option('srp_display_rates'),
          'use_rates_in_calcs' => get_option('srp_use_rates_in_calcs'),
      );
      update_option('srp_mortgage_rates', $mortgage_rates_options);
  }
  $srp_mortgage_rates = get_option('srp_mortgage_rates');
  ?>    
  	<div class="postbox-container" style="width:70%;">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
				<div class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Mortgage Rates Options</span></h3>
					<div class="inside">
					<form method="post" action="options.php">
					  <?php settings_fields('srp-mortgage-rates-options'); ?>
					  <table class="form-table">
						<tr valign="bottom">
						  <th scope="row"><div align="right">GetRateSummary API Key: </div></th>
						  <td><input name="srp_mortgage_rates[getratesummary_api_key]" type="text" value="<?php echo $srp_mortgage_rates['getratesummary_api_key'];?>" size="30" />
							<br /> To obtain your free API key <a href="https://www.zillow.com/webservice/Registration.htm" target="_blank">register at Zillow.com</a> and select <strong>GetRateSummary API</strong> service.
						   </td>
						</tr>
						<tr valign="bottom">
						  <th scope="row"><div align="right">Sate <em>(optional)</em>: </div></th>
						  <td>
						  <?php
								$srp_getratesummary_state = $srp_mortgage_rates['getratesummary_state'];
									
								$states = srp_get_states();
								foreach($states as $k=>$v){
									if($srp_getratesummary_state == $k){ $selected = ' selected '; }else{ $selected = ''; }
									$output .= "\t" . '<option value="' . $k . '"' . $selected . '>' . $v . '</option>' . "\n";
								}
								
								echo '<select name="srp_mortgage_rates[getratesummary_state]">'."\n";
								echo "<option value=\"\"> </option>\n";
								echo $output;
								echo '</select>'."\n";
						  ?>
							<br />The state for which to return average mortgage rates. If omitted, national average mortgage rates are returned.</td>
						</tr>
						<tr valign="bottom">
						  <th scope="row"><div align="right">Display Rates: </div></th>
						  <td>
							<?php if(!$srp_display_rates =$srp_mortgage_rates['display_rates']){ $srp_display_rates = 0; } ?>
							<label>
							  <input type="radio" name="srp_mortgage_rates[display_rates]" value="0" <?php if($srp_display_rates == 0){ echo 'checked="checked"'; }?>>
							Current</label>
							  <br>
							  <label>
							  <input type="radio" name="srp_mortgage_rates[display_rates]" value="1" <?php if($srp_display_rates == 1){ echo 'checked="checked"'; }?>>
							Current and Last Week</label>
						  </td>
						</tr>
						<tr valign="bottom">
						  <th scope="row"><div align="right">Use Live Rates in Calulators: </div></th>
						  <td>
							<label>
							  <input type="checkbox" name="srp_mortgage_rates[use_rates_in_calcs]" <?php if($mortgage_rates_options['use_rates_in_calcs']){ echo 'checked'; }?>>
							</label>
							Live rates from Zillow.com will be used in mortgage calculators instead of the default rate.
						  </td>
						</tr>
					  </table>						
						<p class="submit">
						<input name="simpleMortgageRates_submit" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
		echo srp_settings_right_column();
	?>
  <?php
  
  echo '</div>';
}
?>