<?php

class srp_YelpSettings
{
	const OPTIONSHANDLE = 'srp_yelp_options';

	public $consumerKey;
	public $consumerToken;
	public $token;
	public $tokenSecret;


	function __construct()
	{
		$options = (array) get_option(self::OPTIONSHANDLE);

		$this->consumerKey 		= isset($options['consumer_key']) ? $options['consumer_key'] : null;
		$this->consumerSecret 	= isset($options['consumer_secret']) ? $options['consumer_secret'] : null;
		$this->token 			= isset($options['token']) ? $options['token'] : null;
		$this->tokenSecret 		= isset($options['token_secret']) ? $options['token_secret'] : null;
	}


	function apiCredentialsSet()
	{
		return  ($this->consumerKey && $this->consumerSecret && $this->token && $this->tokenSecret) ?
			true : false;
	}


	function optionsPage()
	{
		echo '<div class="wrap srp">';
		echo '<h2>Yelp API v2.0 by Yelp.com</h2>';
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
										<th scope="row"><div align="right">Consumer Key: </div></th>
										<td><input name="srp_yelp_options[consumer_key]" type="text" value="<?php echo $this->consumerKey;?>" size="30" />
											<br />
											To obtain your free API key <a href="https://www.yelp.com/login?return_url=%2Fdevelopers%2Fgetting_started%2Fapi_access" target="_blank">register at Yelp.com</a>.
											<br><em>Yelp limits API calls to 100 a day. To increase your calls allowance see <a href="http://www.yelp.com/developers/getting_started" target="_blank">official instructions</a>.</td>
									</tr>

									<tr valign="bottom">
										<th scope="row"><div align="right">Consumer Secret: </div></th>
										<td><input name="srp_yelp_options[consumer_secret]" type="text" value="<?php echo $this->consumerSecret;?>" size="30" /></td>
									</tr>

									<tr valign="bottom">
										<th scope="row"><div align="right">Token: </div></th>
										<td><input name="srp_yelp_options[token]" type="text" value="<?php echo $this->token;?>" size="30" /></td>
									</tr>

									<tr valign="bottom">
										<th scope="row"><div align="right">Token Secret: </div></th>
										<td><input name="srp_yelp_options[token_secret]" type="text" value="<?php echo $this->tokenSecret;?>" size="30" /></td>
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
}

function srp_Yelp_options_page(){
	$options = array(
		'consumer_key'		=> null,
		'consumer_secret'	=> null,
		'token'				=> null,
		'token_secret'		=> null,
	);

	$settings = array_merge($options, (array)get_option('srp_yelp_options'));


  echo '<div class="wrap srp">';
  echo '<h2>Yelp API v2.0 by Yelp.com</h2>';
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
						  <th scope="row"><div align="right">Consumer Key: </div></th>
						  <td><input name="srp_yelp_options[consumer_key]" type="text" value="<?php echo $settings['consumer_key'];?>" size="30" />
							<br /> 
							To obtain your free API key <a href="https://www.yelp.com/login?return_url=%2Fdevelopers%2Fgetting_started%2Fapi_access" target="_blank">register at Yelp.com</a>.
              <br><em>Yelp limits API calls to 100 a day. To increase your calls allowance see <a href="http://www.yelp.com/developers/getting_started" target="_blank">official instructions</a>.</td>
						</tr>

						  <tr valign="bottom">
							  <th scope="row"><div align="right">Consumer Secret: </div></th>
							  <td><input name="srp_yelp_options[consumer_secret]" type="text" value="<?php echo $settings['consumer_secret'];?>" size="30" /></td>
						  </tr>

						  <tr valign="bottom">
							  <th scope="row"><div align="right">Token: </div></th>
							  <td><input name="srp_yelp_options[token]" type="text" value="<?php echo $settings['token'];?>" size="30" /></td>
						  </tr>

						  <tr valign="bottom">
							  <th scope="row"><div align="right">Token Secret: </div></th>
							  <td><input name="srp_yelp_options[token_secret]" type="text" value="<?php echo $settings['token_secret'];?>" size="30" /></td>
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