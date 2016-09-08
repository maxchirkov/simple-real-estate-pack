<?php

function srp_Yelp_options_page(){

  echo '<div class="wrap srp">';
  echo '<h2>Yelp API by Yelp.com</h2>';
  srp_updated_message();
  ?>
  <form method="post" action="options.php">
  <?php settings_fields('srp-yelp-options'); ?>
  <div class="postbox-container" style="width:70%;">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
				<div class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Yelp API Options</span></h3>
					<div class="inside">	
					  <table class="form-table">
						<tr valign="bottom">
						  <th scope="row"><div align="right">Yelp <strong>API v1.0</strong> Key: </div></th>
						  <td><input name="srp_yelp_api_key" type="text" value="<?php echo get_option('srp_yelp_api_key');?>" size="30" />
							<br /> 
							To obtain your free API key <a href="https://www.yelp.com/login?return_url=%2Fdevelopers%2Fgetting_started%2Fapi_access" target="_blank">register at Yelp.com</a>.
              <br><em>Yelp limits API calls to 100 a day. To increase your calls allowance see <a href="http://www.yelp.com/developers/getting_started" target="_blank">official instructions</a>.</td>
						</tr>
					  </table>
						<p class="submit">
						<input name="srp_yelp_submit" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
						</p>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<?php
		echo srp_settings_right_column();
	?>	
</form>

  <?php
  
  echo '</div>';
}