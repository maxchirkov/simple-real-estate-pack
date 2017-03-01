<?php

/*
+----------------------------------------------------------------+
+	Calculators-tinymce V1.0
+	by Max Chirkov
+   required for Stats and WordPress 2.5
+----------------------------------------------------------------+
*/

require_once( dirname( dirname(__FILE__) ) .'/includes/srp-wp-load.php');
require_once(dirname( dirname(__FILE__) ) .'/includes/srp-tinymce-widgets.php');

$options = get_option('srp_mortgage_calc_options');
global $wpdb;

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Insert Calculator</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>	
	<script language="javascript" type="text/javascript">
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}
	
	jQuery(document).ready( function() {
		function hide_all(){
			jQuery('table#mortgage').hide();
			jQuery('table#afford').hide();
			jQuery('table#closing').hide();
			jQuery('table#rates').hide();
		}
		hide_all();
		
		function show_calc(id){
			hide_all();
			jQuery('table#'+id).hide().slideDown('slow');
		}
		
		if(typeof jQuery('#calc_type').val() !== 'undefined'){
			show_calc(jQuery('#calc_type').val());
		}

		
		jQuery('select#calc_type').change( function() {
			show_calc(jQuery(this).val());
		});
	});		
	
	function insertsimpleCalcsLink() {
		
		var tagtext;		
		
		//Mortgage Calculator
		if(jQuery('select#calc_type').val() == 'mortgage'){
			var param = ['price_of_home', 'interest_rate', 'title', 'before_title', 'after_title', 'down_payment', 'mortgage_term', 'width'];
			var result = '';
			for(var i in param){
				//alert(m_param[i]+' = ' + jQuery('.'+m_param[i]).val());
				var value = jQuery('#mortgage .'+param[i]).val();
				if(typeof(value) !== 'undefined' && value.length > 0){
					result += ' ' + param[i] + '="' + value + '"';
				}
			}
			tagtext = '[mortgage' + result + ']';
		}else
		//Affordability Calculator
		if(jQuery('select#calc_type').val() == 'afford'){
			var param = ['interest_rate', 'title', 'before_title', 'after_title', 'down_payment', 'width'];
			var result = '';
			for(var i in param){
				//alert(m_param[i]+' = ' + jQuery('.'+m_param[i]).val());
				var value = jQuery('#afford .'+param[i]).val();
				if(typeof(value) !== 'undefined' && value.length > 0){
					result += ' ' + param[i] + '="' + value + '"';
				}
			}
			tagtext = '[affordability' + result + ']';
		}else
        //Closing Cost Estimator
        if(jQuery('select#calc_type').val() == 'closing'){
            var param = ['loan_amount', 'title', 'before_title', 'after_title', 'width'];
            var result = '';
            for(var i in param){
                //alert(m_param[i]+' = ' + jQuery('.'+m_param[i]).val());
                var value = jQuery('#closing .'+param[i]).val();
                if(typeof(value) !== 'undefined' && value.length > 0){
                    result += ' ' + param[i] + '="' + value + '"';
                }
            }
            tagtext = '[closingcosts' + result + ']';
        }else
        //Rates
        if(jQuery('select#calc_type').val() == 'rates'){
            var param = ['title', 'before_title', 'after_title', 'width'];
            var result = '';
            for(var i in param){
                //alert(m_param[i]+' = ' + jQuery('.'+m_param[i]).val());
                var value = jQuery('#rates .'+param[i]).val();
                if ((typeof value != 'undefined')
                    && value.length > 0){
                    result += ' ' + param[i] + '="' + value + '"';
                }
            }
            tagtext = '[mortgage_rates' + result + ']';
        }else{
				tinyMCEPopup.close();
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
	<form name="simpleCalcsForm" action="#">
	<div class="tabs">
		<ul>
			<li id="simpleCalcs_tab1" class="current"><span><a href="javascript:mcTabs.displayTab('simpleCalcs_tab1','simpleCalcs_panel');" onmousedown="return false;"><?php _e("Insert Calculator", 'simpleCalcs'); ?></a></span></li>
		</ul>
	</div>
	
	<div id="simpleCalcs_options" class="panel_wrapper" style="height:320px">
		<!-- simpleCalcs panel -->
		<div id="simpleCalcs_panel" class="panel current">
		<br />
		
		<table width="320" border="0" cellpadding="4" cellspacing="0">
		  <tr>
			<td colspan="2"><div align="center" style="font-weight: bold; background:#F3F6FB; border: 1px solid #D2DFFF; padding: 3px 0;">Insert: 
				<select id="calc_type">
					<option selected="selected" value="mortgage">Mortgage Calculator</option>
					<option value="afford">Affordability Calculator</option>
					<option value="closing">Closing Cost Estimator</option>
				</select>
			</div></td>
		  </tr>
		</table>
		<table id="mortgage" width="320" border="0" cellpadding="4" cellspacing="0">
		  
		  <tr>
			<td colspan="2"><div align="center"><strong> Mortgage Calculator Parameters </strong></div></td>
		  </tr>
		  <tr>
			<td colspan="2"><div align="center" style="background: #FDDFB3;">All Parameters are Optional</div></td>
		  </tr>
		  <tr>
			<td width="130">Price of Home </td>
			<td width="190"><input type="text" name="textfield" class="price_of_home"></td>
		  </tr>
		  <tr>
			<td>Interest Rate </td>
			<td><input name="textfield2" type="text" size="10" class="interest_rate" value="<?php echo $options['annual_interest_rate']; ?>">
			  %</td>
		  </tr>		  
		  <tr>
			<td>Mortgage Term </td>
			<td><select name="select" class="mortgage_term">
				<option value="30">30 years</option>
				<option value="15">15 years</option>
			  </select>    </td>
		  </tr>
		  <tr>
			<td>Down Payment </td>
			<td><input name="textfield6" type="text" size="10" class="down_payment">
			  %</td>
		  </tr>
		  <tr>
			<td>Widget Title </td>
			<td><input type="text" name="textfield3" class="title"></td>
		  </tr>
		  <tr>
			<td>Before Title </td>
			<td><input type="text" name="textfield4" class="before_title"></td>
		  </tr>
		  <tr>
			<td>After Title </td>
			<td><input type="text" name="textfield5" class="after_title"></td>
		  </tr>
		  <tr>
			<td>Widget Width </td>
			<td><input name="textfield7" type="text" size="10" class="width">
			  px</td>
		  </tr>
		</table>
		<table id="afford" width="320" border="0" cellpadding="4" cellspacing="0">
          <tr>
            <td colspan="2"><div align="center"><strong>Affordability Calculator Parameters </strong></div></td>
          </tr>
          <tr>
            <td colspan="2"><div align="center" style="background: #FDDFB3;">All Parameters are Optional</div></td>
          </tr>

          <tr>
            <td width="130">Interest Rate </td>
            <td width="190"><input name="textfield23" type="text" size="10" class="interest_rate" value="<?php echo $options['annual_interest_rate']; ?>">
              %</td>
          </tr>

          <tr>
            <td>Widget Title </td>
            <td><input type="text" name="textfield33" class="title"></td>
          </tr>
          <tr>
            <td>Before Title </td>
            <td><input type="text" name="textfield43" class="before_title"></td>
          </tr>
          <tr>
            <td>After Title </td>
            <td><input type="text" name="textfield53" class="after_title"></td>
          </tr>
          <tr>
            <td>Widget Width </td>
            <td><input name="textfield73" type="text" size="10" class="width">
              px</td>
          </tr>
        </table>
        <table id="closing" width="320" border="0" cellpadding="4" cellspacing="0">
          <tr>
            <td colspan="2"><div align="center"><strong>Closing Cost Estimator</strong></div></td>
          </tr>
          <tr>
            <td colspan="2"><div align="center" style="background: #FDDFB3;">All Parameters are Optional</div></td>
          </tr>
          <tr>
            <td>Loan Amount </td>
            <td><input type="text" name="textfield8" class="loan_amount"></td>
          </tr>
          <tr>
            <td width="130">Widget Title </td>
            <td width="190"><input type="text" name="textfield332" class="title"></td>
          </tr>
          <tr>
            <td>Before Title </td>
            <td><input type="text" name="textfield432" class="before_title"></td>
          </tr>
          <tr>
            <td>After Title </td>
            <td><input type="text" name="textfield532" class="after_title"></td>
          </tr>
          <tr>
            <td>Widget Width </td>
            <td><input name="textfield732" type="text" size="10" class="width">
              px</td>
          </tr>
        </table>

            <table id="rates" width="320" border="0" cellpadding="4" cellspacing="0">
                <tr>
                    <td colspan="2"><div align="center"><strong>Mortgage Rates Widget</strong></div></td>
                </tr>
                <tr>
                    <td colspan="2"><div align="center" style="background: #FDDFB3;">All Parameters are Optional</div></td>
                </tr>
                <tr>
                    <td width="130">Widget Title </td>
                    <td width="190"><input type="text" name="textfield332" class="title"></td>
                </tr>
                <tr>
                    <td>Before Title </td>
                    <td><input type="text" class="before_title"></td>
                </tr>
                <tr>
                    <td>After Title </td>
                    <td><input type="text" class="after_title"></td>
                </tr>
                <tr>
                    <td>Widget Width </td>
                    <td><input type="text" size="10" class="width">
                        px</td>
                </tr>
            </table>
		
		</div>
		<!-- end simpleCalcs panel -->
		
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'simpleCalcs'); ?>" onclick="tinyMCEPopup.close();" />
		</div>


		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'simpleCalcs'); ?>" onclick="insertsimpleCalcsLink();" />
		</div>
	</div>
</form>
</body>
</html>