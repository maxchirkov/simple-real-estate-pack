<?php

/*
+----------------------------------------------------------------+
+	Stats-tinymce V1.0
+	by Max Chirkov
+   required for Stats and WordPress 2.5
+----------------------------------------------------------------+
*/

require_once( SRP_INC .'/srp-wp-load.php');
require_once( SRP_INC .'/srp-tinymce-widgets.php' );

global $wpdb, $metrics, $pricerangequartile, $rollingaverage;

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));

$states_arr = srp_get_states();
$states = '<select name="simpleAltos_state" id="simpleAltos_state" style="width: 165px;">'."\n";
foreach($states_arr as $k => $v){
	$states .= "\t".'<option value="'.$k.'">'.$v.'</option>'."\n";
}
$states .= "</select>\n";

foreach($metrics as $k => $v){
	$left_axis .= "\t".'<input type="checkbox" name="simpleAltos_stats" value="'.$k.':l">'. $v ."<br />\n";
}

foreach($metrics as $k => $v){
	$right_axis .= "\t".'<input type="checkbox" name="simpleAltos_stats" value="'.$k.':r">'. $v ."<br />\n";
}

foreach($pricerangequartile as $k => $v){
	$price_range_quartile .= "\t".'<input type="checkbox" name="simpleAltos_pricerangequartile" value="'.$k.'">'. $v ."<br />\n";
}

foreach($rollingaverage as $k => $v){
	$rolling_average .= "\t".'<input type="checkbox" name="simpleAltos_rollingaverage" value="'.$k.'">'. $v ."<br />\n";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Insert Altos Statistical Chart</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript">
	function hide_steps(){
		jQuery('.steps').hide();
	}

	function go_step(step){
		hide_steps();
		jQuery('.' + step).show();
		var num = step.replace('step', '');
		mcTabs.displayTab('simpleAltos_tab'+num,'simpleAltos_panel'+num);
	}

	jQuery(document).ready( function() {
		hide_steps();
		jQuery('.step1').show();
	});
	
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}
	
	function insertsimpleAltosLink() {
		
		var tagtext;		
		
		// who is active ?
		//if (simpleAltos.className.indexOf('current') != -1) {			
			
			var stats = '';
			for(var i=0; i<document.getElementsByName('simpleAltos_stats').length; i++){
				if(document.getElementsByName('simpleAltos_stats')[i].checked){
					stats += document.getElementsByName('simpleAltos_stats')[i].value + ',';
				}
			}			
			var ra = '';
			for(var i=0; i<document.getElementsByName('simpleAltos_rollingaverage').length; i++){
				if(document.getElementsByName('simpleAltos_rollingaverage')[i].checked){
					ra += document.getElementsByName('simpleAltos_rollingaverage')[i].value;
				}
			}
			var q = '';
			for(var i=0; i<document.getElementsByName('simpleAltos_pricerangequartile').length; i++){
				if(document.getElementsByName('simpleAltos_pricerangequartile')[i].checked){
					q += document.getElementsByName('simpleAltos_pricerangequartile')[i].value;
				}
			}
			var ts = '';
			for(var i=0; i<document.getElementsByName('simpleAltos_timespan').length; i++){
				if(document.getElementsByName('simpleAltos_timespan')[i].checked){
					ts += document.getElementsByName('simpleAltos_timespan')[i].value;
				}
			}
			var rt = '';
			for(var i=0; i<document.getElementsByName('simpleAltos_propertytype').length; i++){
				if(document.getElementsByName('simpleAltos_propertytypes')[i].checked){
					rt += document.getElementsByName('simpleAltos_propertytypes')[i].value;
				}
			}
			
			var var_arr = new Array();
			var_arr['s']		= stats;
			var_arr['ra']		= ra;
			var_arr['q']		= q;
			var_arr['st']		= document.getElementById('simpleAltos_state').value;
			var_arr['c']		= document.getElementById('simpleAltos_city').value;
			var_arr['z']		= document.getElementById('simpleAltos_zipcode').value;
			var_arr['sz']		= document.getElementById('simpleAltos_chartsize').value;
			var_arr['ts']		= ts;
			var_arr['rt']		= rt;
						
			if ( var_arr['s'] != '' && var_arr['c'] != '' && var_arr['st'] != '' ){
			
				var x;				
				var graph_url = 'http://charts.altosresearch.com/altos/app?';
				
				var graph = '<img src="' + graph_url;
				var n = 0;
				var amp = '';
				for(x in var_arr){
					if(n > 0) { amp = '&amp;'; }
					if(var_arr[x] != ''){
						graph += amp + x + '=' + var_arr[x];
						n++;
					}
				}
				
				graph +='&amp;service=chart&amp;pai=55179304" />';
				
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
	<form name="simpleAltosForm" action="#">
	<div class="tabs">
		<ul>
			<li id="simpleAltos_tab1" class="current"><span><a href="javascript:mcTabs.displayTab('simpleAltos_tab1','simpleAltos_panel1');go_step('step1');" onmousedown="return false;"><?php _e("Altos Chart", 'simpleAltos'); ?></a></span></li>
			<li id="simpleAltos_tab2"><span><a href="javascript:mcTabs.displayTab('simpleAltos_tab2','simpleAltos_panel2');go_step('step2');" onmousedown="return false;"><?php _e("Choose Statistics", 'simpleAltos'); ?></a></span></li>
			<li id="simpleAltos_tab3"><span><a href="javascript:mcTabs.displayTab('simpleAltos_tab3','simpleAltos_panel3');go_step('step3');" onmousedown="return false;"><?php _e("More Options", 'simpleAltos'); ?></a></span></li>
		</ul>
	</div>
	
	<div id="simpleAltos_options" class="panel_wrapper" style="height:215px">
		<!-- simpleAltos panel -->
		<div id="simpleAltos_panel1" class="panel current">
		<br />		
			<table border="0" cellpadding="4" cellspacing="0" style="width:100%;">
			<tr>
			  <td><label for="simpleAltos_city"><?php _e("City:", 'simpleAltos_city'); ?></label></td>
			  <td><input type="text" name="simpleAltos_city" id="simpleAltos_city" size="30" /></td>
			</tr>
			<tr>
			  <td><label for="simpleAltos_state"><?php _e("State:", 'simpleAltos_state'); ?></label></td>
			  <td><?php print $states; ?></td>
			</tr>
			<tr>
			  <td><label for="simpleAltos_zipcode"><?php _e("Zipcode:", 'simpleAltos_zipcode'); ?></label></td>
			  <td><input type="text" name="simpleAltos_zipcode" id="simpleAltos_zipcode" size="30" /></td>
			</tr>
			<tr>
			  <td><label for="simpleAltos_timespan"><?php _e("Timespan:", 'simpleAltos_period'); ?></label></td>
			  <td>
			  	<select name="simpeAltos_timespan" id="simpleAltos_timespan">
					<option value="a">30 Days</option>
					<option value="b">60 Days</option>
					<option value="c">90 Days</option>
					<option value="d">180 Days</option>
					<option value="e">1 Year</option>
					<option value="f">All Historical Data</option>
				</select>			  </td>
 			</tr>
			<tr>
			  <td><label for="simpleAltos_propetytype"><?php _e("Property Type:", 'simpleAltos_propetytype'); ?></label></td>
			  <td>
			  	<input type="checkbox" name="simpleAltos_propetytype" value="sf" checked="checked">
			  	Single Family Homes<br />
			  	<input type="checkbox" name="simpleAltos_propetytype" value="mf">Condos/Townhomes			  </td>
 			</tr>
			<tr>
			  <td>
				  <label for="simpleAltos_propetytype"><?php _e("Chart Size:", 'simpleAltos_propetytype'); ?></label></td>
				<td>
					<select name="simpeAltos_chartsize" id="simpleAltos_chartsize">
						<option value="t">Tiny [150x100]</option>
						<option value="sl">Small Landscape [240x160]</option>
						<option value="b">Blog Sidebar [180x120]</option>
						<option value="a">Landscape [520x240]</option>
						<option value="s">Small [240x160]</option>
						<option value="i">Intermediate [360x240]</option>
						<option value="w">Tower [180x200]</option>
						<option value="m">Medium [180x320]</option>
						<option value="l">Large [600x400]</option>
					</select>				</td>
  </tr>
			</table>

		</div>
		<!-- end simpleAltos panel -->
		<!-- simpleAltos pane2 -->
		<div id="simpleAltos_panel2" class="panel">
		<br />		
		<table border="0" cellpadding="4" cellspacing="0" style="width:100%;">
  <tr>
    <td nowrap="nowrap"><label for="simpleAltos_stats"><?php _e("Left Axis:", 'simpleAltos_stats'); ?></label></td>
    <td><?php echo $left_axis; ?></td>
  </tr>
  <tr>
    <td><label for="simpleAltos_stats"><?php _e("Left Axis:", 'simpleAltos_stats'); ?></label></td>
    <td><?php echo $right_axis; ?></td>
  </tr>
</table>

		</div>
		<!-- end simpleAltos panel -->
		<!-- simpleAltos pane2 -->
		<div id="simpleAltos_panel3" class="panel">
		<br />
		
		<table border="0" cellpadding="4" cellspacing="0" style="width:100%;">
  <tr>
    <td nowrap="nowrap"><label for="simpleAltos_pricerangequartile">
      <?php _e("Price Range Quartile:", 'simpleAltos_pricerangequartile'); ?>
    </label></td>
    <td><?php echo $price_range_quartile; ?></td>
  </tr>
  <tr>
    <td><label for="simpleAltos_rollingaverage">
      <?php _e("Rolling Average:", 'simpleAltos_rollingaverage'); ?>
    </label></td>
    <td><?php echo $rolling_average; ?></td>
  </tr>
</table>
		
		</div>
		<!-- end simpleAltos panel -->
		
	</div>
	<div class="mceActionPanel steps step1" style="text-align:center">
			<input type="button" id="step2" name="step2" class="updateButton" value="<?php _e("Step 2", 'simpleAltos'); ?>" onclick="go_step('step2');"/>
	</div>
	<div class="mceActionPanel steps step2" style="text-align:center">
			<input type="button" id="step3" name="step3" class="updateButton" value="<?php _e("Step 3", 'simpleAltos'); ?>" onclick="go_step('step3');"/>
	</div>
	<div class="mceActionPanel steps step3">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'simpleAltos'); ?>" onclick="tinyMCEPopup.close();" />
		</div>


		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'simpleAltos'); ?>" onclick="insertsimpleAltosLink();" />
		</div>
	</div>
</form>
</body>
</html>
<?php

?>
