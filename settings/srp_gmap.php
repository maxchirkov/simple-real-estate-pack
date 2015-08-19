<?php
function srp_gmap_options(){

  if(function_exists('greatrealestate_init') && $g_api = get_option('greatrealestate_googleAPIkey')){
	$disabled = "disabled";
	$note = 'Currently the <a href="' . ADMIN_URL . '/admin.php?page=greatrealestate-options">Great Real Estate plugin\'s</a> Google API key is being used. If you disable that plugin, you will need to re-enter the API key here.';
  }else{
      $disabled = null;
	$g_api = get_option('srp_gmap_api_key');
	$note = 'Paste your domain\'s <a title="get a Google API key" href="http://code.google.com/apis/maps/signup.html">Google API key</a> here to enable maps.';
  }

  echo '<div class="wrap srp">';
  echo '<h2>Google Maps</h2>';
  srp_updated_message();

  //convert old gmap options
  if(!$gmap_options = get_option('srp_gmap')){
      if($srp_gmap_yelp = get_option('srp_gmap_yelp')){
        $gmap_options['yelp'] = $srp_gmap_yelp;
      }
      if($srp_gmap_search = get_option('srp_gmap_search')){
        $gmap_options['search'] = $srp_gmap_search;
      }

      $gmap_options['mainmarker'] = 0;
      $gmap_options['mainmarker_label'] = 'Main Marker';
      update_option('srp_gmap', $gmap_options);
  }
  $srp_gmap = get_option('srp_gmap');

  ?>
  <form method="post" action="options.php">
  <?php settings_fields('srp-gmap-options'); ?>
  <div class="postbox-container" style="width:70%;">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
				<div class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Google Maps Options</span></h3>
					<div class="inside">	
					  <table class="form-table">
<!--
                                              <tr valign="bottom">
						  <th scope="row"><div align="right">Google Maps API Key: </div></th>
						  <td><input name="srp_gmap_api_key" type="text" value="<?php echo $g_api;?>" size="60" <?php echo $disabled;?>/>
							<br /> <?php echo $note;?>
							</td>
						</tr>
API key is no longer required in API version 3
-->
						<tr valign="bottom">
						  <th scope="row"><div align="right">Mapping options from Yelp: </div></th>
						  <td><input type="checkbox" name="srp_gmap[yelp]" <?php if(isset($srp_gmap['yelp']) && !empty($srp_gmap['yelp'])){ echo 'checked'; }?>/>
							 <a href="<?php echo ADMIN_URL;?>/admin.php?page=srp_yelp">Yelp API key</a> is required.
                                                         <br/>A box with options like Schools, Grocery Stores, Hospitals etc. will be added to your Google Maps.
							</td>
						</tr>
<!-- Google search is not supported yet in API v3
						<tr valign="bottom">
						  <th scope="row"><div align="right">Google Map Search: </div></th>
						  <td>
                                                        <input type="checkbox" name="srp_gmap[search]" <?php if(isset($srp_gmap['search']) && !empty($srp_gmap['search'])){ echo 'checked'; }?>/>
                                                  </td>
                                                </tr>
-->                                             <tr valign="bottom">
						  <th scope="row"><div align="right">Main Marker Legend: </div></th>
						  <td><input type="checkbox" name="srp_gmap[mainmarker]" <?php if (isset($srp_gmap['mainmarker']) && !empty($srp_gmap['mainmarker'])){ echo 'checked'; }?>/>
							 Show Main Marker icon in the legend below the map.
							</td>
						</tr>
                                                <tr valign="bottom">
						  <th scope="row"><div align="right">Main Marker Label: </div></th>
						  <td><input type="text" name="srp_gmap[mainmarker_label]" value="<?php echo (isset($srp_gmap['mainmarker_label'])) ? $srp_gmap['mainmarker_label'] : '';?>" />
							</td>
						</tr>
					  </table>
						<input type="hidden" name="action" value="update" />						
						<p class="submit">
						<input name="srp_gmap_submit" type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
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
?>