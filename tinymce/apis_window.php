<?php
/*
+----------------------------------------------------------------+
+	Calculators-tinymce V1.0
+	by Max Chirkov
+   required for Stats and WordPress 2.5
+----------------------------------------------------------------+
*/
require_once(dirname(dirname(__FILE__)) . '/includes/srp-wp-load.php');
require_once(dirname(dirname(__FILE__)) . '/includes/srp-tinymce-widgets.php');

global $wpdb;

// check for rights
if (!is_user_logged_in() || !current_user_can('edit_posts'))
{
    wp_die(__("You are not allowed to be here"));
}

function states_select($name, $id_ = null, $class_ = null)
{
    $states_arr = srp_get_states();
    if ($id_)
    {
        $id = ' id="' . $id_ . '"';
    }
    if ($class_)
    {
        $class = ' class="' . $class_ . '"';
    }
    $states = '<select name="' . $name . '"' . $id . $class . ' style="width: 165px;">' . "\n";
    foreach ($states_arr as $k => $v)
    {
        $states .= "\t" . '<option value="' . $k . '">' . $v . '</option>' . "\n";
    }
    $states .= "</select>\n";

    return $states;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Nearby Businesses by Yelp</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <script type="text/javascript">
        //<![CDATA[
        var srp_geo = "<?php echo GMAP_API ?>";
        var srp_url = "<?php echo SRP_URL?>";
        var srp_wp_admin = "<?php echo ADMIN_URL?>";
        //]]>
    </script>
    <?php
    $gmap = get_option('srp_gmap');
    ?>
    <script language="javascript" type="text/javascript"
            src="<?php echo get_option('siteurl') ?>/wp-includes/js/jquery/jquery.js"></script>
    <script language="javascript" type="text/javascript"
            src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
    <script language="javascript" type="text/javascript"
            src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
    <script language="javascript" type="text/javascript"
            src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
    <script language="javascript" type="text/javascript"
            src="http://maps.google.com/maps/api/js?key=<?php echo @$gmap['api_key']; ?>"></script>
    <script language="javascript" type="text/javascript"
            src="<?php echo SRP_URL ?>/js/src/srp-gre-admin.js"></script>
    <script language="javascript" type="text/javascript">
        function init()
        {
            tinyMCEPopup.resizeToInnerSize();
        }

        //hides all variable options
        function hide_all()
        {
            jQuery('#get_coordinates').hide();
            jQuery('#distance').hide();
            jQuery('table#bycity').hide();
            jQuery('table#byzip').hide();
            jQuery('table#bycoord').hide();
            jQuery('.optional_params').show();
            jQuery('.collapsible table').hide();
        }

        //resets panels and options to their defaults
        function reset_panels()
        {
            var panel = get_current_panel();
            hide_all();
            //with geocoding option
            if (jQuery('#location_type').val() === 'bycoord' || panel == 'simpleAPIs_panel2')
            {
                //                jQuery('#simpleAPIs_options').css({"height": "264px"});
                jQuery('.collapsible').css({"height": "16px"});
                jQuery('#get_coordinates').fadeIn('slow');
                jQuery('#distance').show();
            }
            //without geocoding option
            else
            {
                //                jQuery('#simpleAPIs_options').css({"height": "280px"});
            }
            check_location(jQuery('#location_type').val()); //display parameters for currently selected location type
        }

        //retruns current panel's ID
        function get_current_panel()
        {
            var id = jQuery('div.current').attr('id');
            return id;
        }

        function show_param(id)
        {
            jQuery('table#' + id).hide().slideDown('slow');
        }

        function check_location(location_type)
        {
            if (typeof location_type !== 'undefined')
            {
                show_param(location_type);
            }
        }

        function toggle_one()
        {
            var panel = get_current_panel();
            toggle_optional();
            jQuery('#simpleAPIs_options').animate({height: "100px"}, {queue: true, duration: 500});
            jQuery('.collapsible').animate({height: "180px"}, {queue: true, duration: 500});
            jQuery('.collapsible .section_title').next("table").slideUp('slow').show();
            jQuery('.collapsible .section_title').addClass('active');
        }

        function toggle_two()
        {
            var panel = get_current_panel();
            toggle_optional();
            jQuery('#simpleAPIs_options').animate({height: "264px"}, {queue: true, duration: 500});
            jQuery('.collapsible').animate({height: "16px"}, {queue: true, duration: 500});
            jQuery('.collapsible .section_title').next("table").slideDown('slow').hide();
            jQuery('.collapsible .section_title').removeClass('active');
        }

        function toggle_optional()
        {
            var panel = get_current_panel();
            if (jQuery('#' + panel + ' .optional_params').is(':hidden'))
            {
                jQuery('#' + panel + ' .optional_params').slideDown("slow");
                jQuery('#' + panel + ' .section_title').removeClass('active');
            }
            else
            {
                jQuery('#' + panel + ' .optional_params').slideUp('slow');
                jQuery('#' + panel + ' .section_title').addClass('active');
            }
        }

        function reset_toggle()
        {
            if (jQuery('.collapsible .section_title').next('table').is(':visible'))
            {
                toggle_two();
            }
            else
            {
                toggle_one();
            }
        }

        //reset panels on tab clicks
        function tab_click()
        {
            setTimeout(reset_panels, 200);
        }

        jQuery(document).ready(
            function ()
            {
                reset_panels();

                if (typeof jQuery('#location_type').val() !== 'undefined')
                {
                    check_location(jQuery('#location_type').val());
                }


                jQuery('select#location_type').change(
                    function ()
                    {
                        reset_panels();
                    }
                );

                jQuery('.collapsible .section_title').click(
                    function ()
                    {
                        reset_toggle();
                    }
                );


                jQuery('#listings_latitude').focus(
                    function ()
                    {
                        var panel = get_current_panel();
                        if (jQuery(this).attr('type') == 'hidden')
                        {
                            jQuery('#' + panel + ' input.lat').val(jQuery('#listings_latitude').val());
                            jQuery('#' + panel + ' input.lng').val(jQuery('#listings_longitude').val());
                            jQuery('#listings_latitude').val('');
                            jQuery('#listings_longitude').val('');
                        }
                    }
                );

            }
        );

        function insertsimpleAPIsLink()
        {

            var tagtext;

            //Check if tab 1 is current
            if (jQuery('#simpleAPIs_tab1').attr('class') == 'current')
            {
                var param = [
                    'location_title', 'city', 'state', 'zip', 'lat', 'lng', 'groupby', 'output'
                ];

                //By City
                if (jQuery('select#location_type').val() == 'bycity' && jQuery('#bycity input.city').val().length > 0 && jQuery('#bycity select.state').val().length > 0)
                {
                    var result = '';
                    for (var i in param)
                    {
                        var value = jQuery('#simpleAPIs_panel .bycity .' + param[i]).val();
                        if (typeof(value) !== 'undefined' && value.length > 0)
                        {
                            result += ' ' + param[i] + '="' + value + '"';
                        }
                    }
                    tagtext = '[schoolsearch' + result + ']';
                }

                //By Zipcode
                if (jQuery('select#location_type').val() == 'byzip' && jQuery('#byzip input.zip').val().length > 0)
                {
                    var result = '';
                    for (var i in param)
                    {
                        var value = jQuery('#simpleAPIs_panel .byzip .' + param[i]).val();
                        if (typeof(value) !== 'undefined' && value.length > 0)
                        {
                            result += ' ' + param[i] + '="' + value + '"';
                        }
                    }
                    tagtext = '[schoolsearch' + result + ']';
                }

                //By Coord
                if (jQuery('select#location_type').val() == 'bycoord' && jQuery('#bycoord input.lat').val().length > 0 && jQuery('#bycoord input.lng').val().length > 0)
                {
                    var param = [
                        'location_title', 'city', 'state', 'zip', 'lat', 'lng', 'distance',
                        'groupby', 'output'
                    ];
                    var result = '';
                    for (var i in param)
                    {
                        var value = jQuery('#simpleAPIs_panel .bycoord .' + param[i]).val();
                        if (typeof(value) !== 'undefined' && value.length > 0)
                        {
                            result += ' ' + param[i] + '="' + value + '"';
                        }
                    }
                    tagtext = '[schoolsearch' + result + ']';
                }

            }
            else if (jQuery('#simpleAPIs_tab2').attr('class') == 'current')
            {

                var param = ['lat', 'lng', 'radius', 'output', 'sortby', 'term'];
                var result = '';

                if (jQuery('#simpleAPIs_panel2 input.lat').val().length > 0 && jQuery('#simpleAPIs_panel2 input.lng').val().length > 0)
                {
                    for (var i in param)
                    {
                        var value = jQuery('#simpleAPIs_panel2 .' + param[i]).val();

                        if (typeof(value) !== 'undefined' && value.length > 0)
                        {
                            result += ' ' + param[i] + '="' + value + '"';
                        }
                    }

                    var location = [];
                    jQuery('#coord_form [id^="listings_"]').each(
                        function ()
                        {
                            var value = jQuery(this).val();
                            if (value.length)
                            {
                                location.push(value);
                            }
                        }
                    );

                    if (location.length)
                    {
                        result += ' location="' + location.join(' ') + '"';
                    }

                    tagtext = '[yelp' + result + ']';
                }
            }

            if (window.tinyMCE)
            {
                window.parent.send_to_editor(tagtext);
                tinyMCEPopup.editor.execCommand('mceRepaint');
                tinyMCEPopup.close();
            }

            return;
        }
    </script>
    <base target="_self"/>
    <style type="text/css">
        <!--
        .section_title {
            /*cursor: pointer;*/
            font-weight: bold;
            /*color: #2B6FB6;*/
            text-align: center;
            line-height: 24px;
        }

        .optional_params .section_title {
            background: #FDDFB3;
            cursor: auto;
        }

        .active {
            background: #A2C5E8 url(<?php echo SRP_IMG; ?>/plus-minus.gif) 5px 1px no-repeat;
        }

        .collapsible {
            border: 1px solid #919B9C;
            border-top: none;
            background: #FFFFFF;
        }

        .panel_wrapper {
            height: auto !important;
        }

        .panel.current {
            height: auto !important;
        }

        .button-primary {
            lign-items: flex-start;
            background-attachment: scroll;
            background-clip: border-box;
            background-color: rgb(0, 133, 186);
            background-image: none;
            background-origin: padding-box;
            background-size: auto;
            border-bottom-color: rgb(0, 103, 153);
            border-bottom-left-radius: 3px;
            border-bottom-right-radius: 3px;
            border-bottom-style: solid;
            border-bottom-width: 1px;
            border-image-outset: 0px;
            border-image-repeat: stretch;
            border-image-slice: 100%;
            border-image-source: none;
            border-image-width: 1;
            border-left-color: rgb(0, 103, 153);
            border-left-style: solid;
            border-left-width: 1px;
            border-right-color: rgb(0, 103, 153);
            border-right-style: solid;
            border-right-width: 1px;
            border-top-color: rgb(0, 115, 170);
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
            border-top-style: solid;
            border-top-width: 1px;
            box-shadow: rgb(0, 103, 153) 0px 1px 0px 0px;
            box-sizing: border-box;
            color: rgb(255, 255, 255);
            cursor: pointer;
            display: inline-block;
            font-family: "Open Sans", sans-serif;
            font-size: 12px;
            font-stretch: normal;
            font-style: normal;
            font-variant-caps: normal;
            font-variant-ligatures: normal;
            font-variant-numeric: normal;
            font-weight: normal;
            height: 26px;
            letter-spacing: normal;
            line-height: 24px;
            margin-bottom: 0px;
            margin-left: 0px;
            margin-right: 0px;
            margin-top: 0px;
            padding-bottom: 2px;
            padding-left: 12px;
            padding-right: 12px;
            padding-top: 0px;
            text-align: center;
            text-decoration: none;
            text-indent: 0px;
            text-rendering: auto;
            text-shadow: rgb(0, 103, 153) 0px -1px 1px, rgb(0, 103, 153) 1px 0px 1px, rgb(0, 103, 153) 0px 1px 1px, rgb(0, 103, 153) -1px 0px 1px;
            text-transform: none;
            vertical-align: top;
            white-space: nowrap;
            width: auto;
            word-spacing: 0px;
            writing-mode: horizontal-tb;
            -webkit-appearance: none;
            -webkit-font-smoothing: subpixel-antialiased;
            -webkit-rtl-ordering: logical;
            -webkit-user-select: none;
        }

        #test_geo_link {
            font-size: 11px;
        }
        -->
    </style>
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';"
      style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
<form name="simpleAPIsForm" action="#">
    <div class="tabs">
        <ul>
            <!--			<li id="simpleAPIs_tab1" class="current"><span><a href="javascript:mcTabs.displayTab('simpleAPIs_tab1','simpleAPIs_panel');" onmousedown="tab_click();return false;">-->
            <?php //_e("Insert Local Schools", 'simpleAPIs'); ?><!--</a></span></li>-->
            <li id="simpleAPIs_tab2" class="current"><span><a
                        href="javascript:mcTabs.displayTab('simpleAPIs_tab2','simpleAPIs_panel2');"
                        onmousedown="tab_click();return false;"><?php _e("Insert Yelp Businesses", 'simpleAPIs'); ?></a></span>
            </li>
        </ul>
    </div>
    <div id="simpleAPIs_options" class="panel_wrapper">
        <!-- simpleAPIs panel -->
        <div id="simpleAPIs_panel" class="panel">
            <br/>

            <div align="center"
                 style="font-weight: bold; background:#F3F6FB; border: 1px solid #D2DFFF; padding: 3px 0;">
                School Location by :
                <select id="location_type">
                    <option selected="selected" value="bycity">City</option>
                    <option value="byzip">Zip Code</option>
                    <option value="bycoord">Latitude/Longitude</option>
                </select>
            </div>
            <table id="bycity" class="bycity" width="320" border="0" cellpadding="4"
                   cellspacing="0">
                <tr>
                    <td colspan="2">
                        <div align="center"><strong> Schools by City </strong></div>
                    </td>
                </tr>
                <tr>
                    <td width="130"> City</td>
                    <td width="190"><input type="text" name="textfield822"
                                           class="city"><span>*</span></td>
                </tr>
                <tr>
                    <td>State</td>
                    <td><?php print states_select('bycity_state', null, 'state'); ?><span>*</span>
                    </td>
                </tr>
            </table>
            <table id="byzip" class="byzip" width="320" border="0" cellpadding="4" cellspacing="0">
                <tr>
                    <td colspan="2">
                        <div align="center"><strong> Schools by Zip Code </strong></div>
                    </td>
                </tr>
                <tr>
                    <td width="130"> City</td>
                    <td width="190"><input type="text" name="textfield82" class="city">
                    </td>
                </tr>
                <tr>
                    <td>State</td>
                    <td><?php print states_select('byzip_state', null, 'state'); ?>
                    </td>
                </tr>
                <tr>
                    <td>Zip Code</td>
                    <td><input type="text" name="textfield322" class="zip"><span>*</span></td>
                </tr>
            </table>
            <table id="bycoord" class="bycoord" width="320" border="0" cellpadding="4"
                   cellspacing="0">
                <tr>
                    <td colspan="2">
                        <div align="center"><strong> Schools by Lat/Lng </strong></div>
                    </td>
                </tr>

                <tr>
                    <td width="130"> Latitude</td>
                    <td width="190"><input type="text" name="textfield8" class="lat"><span>*</span>
                    </td>
                </tr>

                <tr>
                    <td>Longitude</td>
                    <td><input type="text" name="textfield32" class="lng"><span>*</span></td>
                </tr>
            </table>
            <div class="optional_params byzip bycity bycoord">
                <div class="section_title">Optional Parameters</div>
                <table width="320" border="0" cellpadding="4" cellspacing="0">
                    <td width="130">Location Name</td>
                    <td width="190"><input type="text" name="textfield" class="location_title"></td>
                    </tr>
                    <tr id="distance">
                        <td>Search Radius</td>
                        <td><input name="textfield2" type="text" class="distance" value="3">
                            mi
                        </td>
                    </tr>
                    <tr>
                        <td>Group by</td>
                        <td><select name="select" class="groupby">
                                <option value="gradelevel" selected="selected">Grade Level</option>
                                <option value="schooltype">School Type</option>
                                <option value="schooldistrictname">School District</option>
                                <option value="zip">Zip Code</option>
                            </select></td>
                    </tr>
                    <tr>
                        <td>Output</td>
                        <td><select class="output">
                                <option value="table" selected="selected">Table</option>
                                <option value="list">List</option>
                            </select></td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- end simpleAPIs panel -->

        <!-- simpleAPIs panel -->
        <div id="simpleAPIs_panel2" class="panel current">
            <br/>
            <table width="320" border="0" cellpadding="4" cellspacing="0">
                <tr>
                    <td colspan="2">
                        <div id="get_coordinates">
                            <div class="section_title">Enter Location</div>
                            <table id="coord_form" class="bycoord" cellpadding="4" cellspacing="0"
                                   style="width:100%;">
                                <tr>
                                    <td><?php _e("Street Address:", 'simpleGMap_address'); ?></td>
                                    <td><input type="text" name="simpleGMap_address"
                                               id="listings_address" size="30"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label
                                            for="simpleGMap_city"><?php _e("City:", 'simpleGMap_city'); ?></label>
                                    </td>
                                    <td><input type="text" name="simpleGMap_city" id="listings_city"
                                               class="city"
                                               size="30"/></td>
                                </tr>
                                <tr>
                                    <td><label
                                            for="simpleGMap_state"><?php _e("State:", 'simpleGMap_state'); ?></label>
                                    </td>
                                    <td><?php print states_select('simpleGMap_state', 'listings_state', 'state'); ?></td>
                                </tr>
                                <tr>
                                    <td><label
                                            for="simpleGMap_zipcode"><?php _e("Zipcode:", 'simpleGMap_zip'); ?></label>
                                    </td>
                                    <td><input type="text" name="simpleGMap_zipcode"
                                               id="listings_postcode" class="zip"
                                               size="30"/></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input id="srp_get_coord" class="button button-primary"
                                               type="button" name="get_coord" value="Get Lat/Long"/>
                                        &nbsp;&nbsp;<span id="test_geo_link"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td width="130"> Latitude</td>
                    <td width="190"><input type="text" class="lat"><span>*</span></td>
                </tr>
                <tr>
                    <td>Longitude</td>
                    <td><input type="text" class="lng"><span>*</span></td>
                </tr>
            </table>
            <div class="optional_params">
                <!--                <div class="section_title">Optional Parameters</div>-->
                <table id="yelp" width="320" border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td>Radius</td>
                        <td><input type="text" class="radius" value="3"></td>
                    </tr>
                    <tr>
                        <td>Sort by</td>
                        <td>
                            <select name="select" class="sortby">
                                <option value="avg_rating" selected="selected">Average Rating
                                </option>
                                <option value="distance">Distance</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Category</td>
                        <td><select name="select2" size="7" multiple="multiple" class="term">
                                <option value="" selected="selected">All Categories</option>
                                <option value="grocery">Grocery</option>
                                <option value="restaurants">Restaurants</option>
                                <option value="banks">Banks</option>
                                <option value="gas_stations">Gas Stations</option>
                                <option value="golf">Golf</option>
                                <option value="hospitals">Hospitals</option>
                            </select></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>


    <div class="mceActionPanel">
        <input type="hidden" id="listings_latitude" name="lat"/>
        <input type="hidden" id="listings_longitude" name="lng"/>
        <div style="float: left">
            <input type="button" id="cancel" name="cancel"
                   value="<?php _e("Cancel", 'simpleAPIs'); ?>" onclick="tinyMCEPopup.close();"/>
        </div>


        <div style="float: right">
            <input type="submit" id="insert" name="insert"
                   value="<?php _e("Insert", 'simpleAPIs'); ?>" onclick="insertsimpleAPIsLink();"/>
        </div>
    </div>
</form>
</body>
</html>