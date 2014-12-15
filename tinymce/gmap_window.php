<?php

/*
+----------------------------------------------------------------+
+	GMap-tinymce V1.0
+	by Max Chirkov
+   required for Stats and WordPress 2.5
+----------------------------------------------------------------+
*/

require_once( dirname( dirname(__FILE__) ) .'/includes/srp-wp-load.php');
require_once(dirname( dirname(__FILE__) ) .'/includes/srp-tinymce-widgets.php');

global $wpdb;

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));

$states_arr = srp_get_states();
$states = '<select name="simpleGMap_state" id="listings_state" style="width: 165px;">'."\n";
foreach($states_arr as $k => $v){
	$states .= "\t".'<option value="'.$k.'">'.$v.'</option>'."\n";
}
$states .= "</select>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Insert Google Map</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
	<script type="text/javascript">	
	//<![CDATA[
	var srp_geo = '<?php echo GMAP_API ?>';
	var srp_url = "<?php echo SRP_URL?>";
	var srp_wp_admin = "<?php echo ADMIN_URL?>";
	//]]>
	</script>	
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/jquery/jquery.js"></script>	
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
        <script language="javascript" type="text/javascript" src="<?php echo SRP_URL ?>/js/srp-gre-admin.js"></script>
	<script language="javascript" type="text/javascript">
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}
	
	function insertsimpleGMapLink() {		
		var tagtext;		
		
		// who is active ?
		//if (simpleGMap.className.indexOf('current') != -1) {			
						
			var address			= document.getElementById('listings_address').value;
			var state			= document.getElementById('listings_state').value;
			var city			= document.getElementById('listings_city').value;
			var zip				= document.getElementById('listings_postcode').value;
			var lat				= document.getElementById('listings_latitude').value;
			var lng				= document.getElementById('listings_longitude').value;
			var title			= document.getElementById('simpleGMap_title').value;
			var description                 = document.getElementById('simpleGMap_description').value;

                                if(document.getElementById('profile').checked){
                                    tagtext = '[srp_profile lat="' + lat + '" lng="' + lng + '" address="' + address + '" city="' + city + '" state="' + state + '" zip_code="' + zip + '"]';
                                    tagtext += '<h3 style="font-size: 100%; line-height: normal;">' + title + '</h3>';
                                    tagtext += '<p>' + description + '</p>';
                                    tagtext += '[/srp_profile]';
                                }else{
                                    tagtext = '[srpmap lat="' + lat + '" lng="' + lng + '"]';
                                    tagtext += '<h3 style="font-size: 100%; line-height: normal;">' + title + '</h3>';
                                    tagtext += '<p>' + address + '<br />' + city + ', ' + state + ' ' + zip + '</p>';
                                    tagtext += '<p>' + description + '</p>';
                                    tagtext += '[/srpmap]';
                                }
		
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
	<form name="simpleGMapForm" action="#">
	<div class="tabs">
		<ul>
			<li id="simpleGMap_tab1" class="current"><span><a href="javascript:mcTabs.displayTab('simpleGMap_tab1','simpleGMap_panel');" onmousedown="return false;"><?php _e("Enter Location", 'simpleGMap'); ?></a></span></li>
			<li id="simpleGMap_tab2"><span><a href="javascript:mcTabs.displayTab('simpleGMap_tab2','simpleGMap_panel2');" onmousedown="return false;"><?php _e("InfoWindow Content", 'simpleGMap'); ?></a></span></li>
		</ul>
	</div>
	
	<div id="simpleGMap_options" class="panel_wrapper" style="height:285px">
		<!-- simpleGMap panel -->
		<div id="simpleGMap_panel" class="panel current">
		<p style="text-align: center; color: red;">All fields are required.</p>
			<table border="0" cellpadding="4" cellspacing="0" style="width:100%;">
			<tr>
			  <td><?php _e("Street Address:", 'simpleGMap_address'); ?></td>
			  <td><input type="text" name="simpleGMap_address" id="listings_address" size="30" /></td>
  </tr>
			<tr>
			  <td><label for="simpleGMap_city"><?php _e("City:", 'simpleGMap_city'); ?></label></td>
			  <td><input type="text" name="simpleGMap_city" id="listings_city" size="30" /></td>
			</tr>
			<tr>
			  <td><label for="simpleGMap_state"><?php _e("State:", 'simpleGMap_state'); ?></label></td>
			  <td><?php print $states; ?></td>
			</tr>
			<tr>
			  <td><label for="simpleGMap_zipcode"><?php _e("Zipcode:", 'simpleGMap_zip'); ?></label></td>
			  <td><input type="text" name="simpleGMap_zipcode" id="listings_postcode" size="30" /></td>
			</tr>
                        <tr>
			  <td><label for="simpleGMap_propetytype"><?php _e("Include Neighborhood Profile:", 'simpleGMap_lng'); ?></label></td>
			  <td>
			  	<input type="checkbox" name="simpleGMap_profile" id="profile" /></td>
 			</tr>
			<tr>
			  <td><label for="simpleGMap_timespan"><?php _e("Latitude:", 'simpleGMap_lat'); ?></label></td>
			  <td>
			  	<input type="text" name="simpleGMap_lat" id="listings_latitude" size="30" />			  </td>
 			</tr>
			<tr>
			  <td><label for="simpleGMap_propetytype"><?php _e("Longitude:", 'simpleGMap_lng'); ?></label></td>
			  <td>
			  	<input type="text" name="simpleGMap_lng" id="listings_longitude" size="30" /></td>
 			</tr>
			<tr>
			  <td>&nbsp;</td>
				<td><input id="srp_get_coord" type="button" name="get_coord" value="Get Lat/Long" /><p><span id="test_geo_link"></span></p></td>
                        </tr>                        
		</table>

		</div>
		<!-- end simpleGMap panel -->
		
		<div id="simpleGMap_panel2" class="panel">
		<br />
			<p>This information will appear in the info blurb when you click on the marker/pin of your location.</p>
<table border="0" cellpadding="4" cellspacing="0" style="width:100%;">
			<tr>
			  <td><?php _e("Title:", 'simpleGMap_title'); ?></td>
			  <td><input type="text" name="simpleGMap_title" id="simpleGMap_title" size="30" /></td>
  </tr>
			<tr>
			  <td><label for="simpleGMap_description"><?php _e("Description:", 'simpleGMap_description'); ?></label></td>
			  <td><textarea name="simpleGMap_description" id="simpleGMap_description" cols="30" rows="5"></textarea></td>
			</tr>
			</table>

		</div>
		<!-- end simpleGMap panel -->
		
		<!-- end simpleGMap panel -->
		
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'simpleGMap'); ?>" onclick="tinyMCEPopup.close();" />
		</div>


		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'simpleGMap'); ?>" onclick="insertsimpleGMapLink();" />
		</div>
	</div>
</form>
</body>
</html>