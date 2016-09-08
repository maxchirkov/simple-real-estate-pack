<?php

/*
+----------------------------------------------------------------+
+	Stats-tinymce V1.0
+	by Max Chirkov
+   required for Stats and WordPress 2.5
+----------------------------------------------------------------+
*/

require_once( dirname( dirname(__FILE__) ) .'/includes/srp-wp-load.php');
require_once(dirname( dirname(__FILE__) ) .'/includes/srp-tinymce-widgets.php');

global $wpdb, $graph_types;

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));

	$options = '<select name="simpleTrulia_type" id="simpleTrulia_type" style="width: 165px;">'."\n";
foreach($graph_types as $k => $type){
	$options .= "\t".'<option value="'.$k.'">'.$type.'</option>'."\n";
}
	$options .= "</select>\n";

$states_arr = srp_get_states();
$states = '<select name="simpleTrulia_state" id="simpleTrulia_state" style="width: 165px;">'."\n";
foreach($states_arr as $k => $v){
	$states .= "\t".'<option value="'.$k.'">'.$v.'</option>'."\n";
}
$states .= "</select>\n";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Insert Trulia Stats Graphs</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript">
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}
	
	function insertsimpleTruliaLink() {
		
		var tagtext;
		
		var simpleTrulia = document.getElementById('simpleTrulia_options');
		
		
		// who is active ?
		//if (simpleTrulia.className.indexOf('current') != -1) {			
			var brs = new Array();
			brs[0] = 1;
			brs[1] = 2;
			brs[2] = 3;
			brs[3] = 4;
			brs[4] = 'all';
			
			var type = document.getElementById('simpleTrulia_type').value;
			var width = document.getElementById('simpleTrulia_width').value;
			var height = document.getElementById('simpleTrulia_height').value;						
			var city = document.getElementById('simpleTrulia_city').value;
			var state = document.getElementById('simpleTrulia_state').value;
			var zipcode = document.getElementById('simpleTrulia_zipcode').value;
			var period = document.getElementById('simpleTrulia_period').value;
			var bedrooms = document.simpleTruliaForm.simpleTrulia_bedrooms;
			var a = '';
						
			for(var i = 0; i < bedrooms.length; i++){
				if(bedrooms[i].checked) {										
					for(var x in brs){
						if(brs[x] == bedrooms[i].value){
							delete brs[x];
						}
					}
				}
			}												
			
			
			var var_arr = new Array();
			var_arr['width']	= width;
			var_arr['height']	= height;
			var_arr['type']		= type;
			var_arr['city']		= city;
			var_arr['state']	= state;
			var_arr['zipcode']	= zipcode;
			var_arr['period']	= period;
			var_arr['br']		= a;
			
			var var_default = new Array();
			var_default['width']	= 500;
			var_default['height']	= 300;
			var_default['type']		= 'qma_median_sales_price';
			var_default['city']		= '';
			var_default['state']	= '';
			var_default['zipcode']	= '';
			var_default['period']	= 1;
			var_default['exclude']	= brs.join("|");
			
			var out = new Array();
			for(var i in var_default){
				if(var_arr[i]){
					out[i] = var_arr[i];
				}else{
					out[i] = var_default[i];
				}
			}			
			
			if ( type != '' && city != '' && state != '' ){
			
				var alt_text = new Array();
				alt_text['qma_median_sales_price'] = city + ' median sales prices';
				alt_text['qma_sales_volume'] = 'Number of sold homes in ' + city;
				alt_text['average_listing_price'] = city + '  average property price';
				alt_text['listing_volume'] = city + ' – number of properties';
				alt_text['qma_price_per_sqft'] = city + ' average price per square foot';
			
				tagtext = "[truliastats";
				var x;
				for (x in var_arr)
				{
					if(var_arr[x] != ''){
						tagtext += " " + x + "=\"" + var_arr[x] + "\"";
					}
				}
				tagtext += "]";
			
				var city_ = city.split(' ').join('_');
				var city_state = city_ + '-' + state;
				
				var graph_url = 'http://graphs.trulia.com/real_estate/' + city_state + '/graph.png?version=<?php echo TRULIA_VER;?>';
				
				var graph = '<img src="' + graph_url;
				for(x in out){
					if(out[x] != ''){
						graph += '&amp;' + x + '=' + out[x];
					}
				}
				
				graph += '" alt="' + alt_text[var_arr['type']] + '" />';
				
				tagtext = graph;
			}else{
				tinyMCEPopup.close();
			}
		//}
	
		
		if(window.tinyMCE) {
            window.parent.send_to_editor(tagtext);
            tinyMCEPopup.editor.execCommand('mceRepaint');
            tinyMCEPopup.close();
		}
		
		return;
	}
	</script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="simpleTruliaForm" action="#">
	<div class="tabs">
		<ul>
			<li id="simpleTrulia_tab1" class="current"><span><a href="javascript:mcTabs.displayTab('simpleTrulia_tab1','simpleTrulia_panel');" onmousedown="return false;"><?php _e("Trulia Stats Graphs", 'simpleTrulia'); ?></a></span></li>
		</ul>
	</div>
	
	<div id="simpleTrulia_options" class="panel_wrapper" style="height:265px">
		<!-- simpleTrulia panel -->
		<div id="simpleTrulia_panel" class="panel current">
		<br />
			<table border="0" cellpadding="4" cellspacing="0" style="width:100%;">
			 <tr>
				<td nowrap="nowrap"><label for="simpleTrulia_type"><?php _e("Select Graph Type:", 'simpleTrulia_type'); ?></label></td>
				<td><?php echo $options;?>            </td>
			</tr>
			<tr>
			  <td><label for="simpleTrulia_city"><?php _e("City:", 'simpleTrulia_city'); ?></label></td>
			  <td><input type="text" name="simpleTrulia_city" id="simpleTrulia_city" size="30" /></td>
			</tr>
			<tr>
			  <td><label for="simpleTrulia_state"><?php _e("State:", 'simpleTrulia_state'); ?></label></td>
			  <td><?php print $states; ?></td>
			</tr>
			<tr>
			  <td><label for="simpleTrulia_zipcode"><?php _e("Zipcode:", 'simpleTrulia_zipcode'); ?></label></td>
			  <td><input type="text" name="simpleTrulia_zipcode" id="simpleTrulia_zipcode" size="30" /></td>
			</tr>
			<tr>
			  <td><label for="simpleTrulia_period"><?php _e("Period:", 'simpleTrulia_period'); ?></label></td>
			  <td>
			  	<select name="simpeTrulia_period" id="simpleTrulia_period">
					<option value="1">1 Year</option>
					<option value="5">5 Years</option>
					<option value="all">Maximum Available</option>
				</select>
			  </td>
 			</tr>
			<tr>
			  <td><label for="simpleTrulia_bedrooms"><?php _e("Bedrooms:", 'simpleTrulia_bedrooms'); ?></label></td>
			  <td>
			  	<input type="checkbox" name="simpleTrulia_bedrooms" value="1">1 Br
				<input type="checkbox" name="simpleTrulia_bedrooms" value="2">2 Br
				<input type="checkbox" name="simpleTrulia_bedrooms" value="3">3 Br
				<input type="checkbox" name="simpleTrulia_bedrooms" value="4">4 Br
				<input type="checkbox" name="simpleTrulia_bedrooms" value="all" checked="checked">All Properties				
			  </td>
 			</tr>
			<tr>
				<td colspan="2"><small>The <strong>bedrooms</strong> options might not be available for some areas.</small></td>
			</tr>
			<tr>
				<td style="text-align:center">
					<label for="simpleTrulia_width"><?php _e("Width:", 'simpleTrulia_width'); ?></label><input type="text" name="width" value="500" id="simpleTrulia_width" size="5" />px				</td>
				<td style="text-align:center">
					<label for="simpleTrulia_height"><?php _e("Height:", 'simpleTrulia_height'); ?></label> <input type="text" name="height" value="300" id="simpleTrulia_height" size="5" />px				</td>
				</tr>
			</table>

		</div>
		<!-- end simpleTrulia panel -->
				
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'simpleTrulia'); ?>" onclick="tinyMCEPopup.close();" />
		</div>


		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'simpleTrulia'); ?>" onclick="insertsimpleTruliaLink();" />
		</div>
	</div>
</form>
</body>
</html>