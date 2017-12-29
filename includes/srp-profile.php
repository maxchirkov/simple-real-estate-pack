<?php

/* ---------------------------------------------*
 * * Simple Realty Pack support functions
 * * to work with Great Real Estate Plugin
 * * Author: Max Chirkov
 * * WebSite: www.PhoenixHomes.com
 * *--------------------------------------------- */

$srp_widgets = new srpWidgets();

function srp_prepare_widgets_object() {
  global $srp_widgets;

  $init = array();
  $init = apply_filters('srp_prepare_widgets_object', $init);

  if (!is_array($init))
    return;

  foreach ($init as $atts) {
    $srp_widgets->add($atts);
  }
}

if (isset($_POST['srp_listing_values'])) {
  $srp_property_values = $_POST['srp_listing_values'];
}

function srp_listing_content() {
  do_action('srp_listing_content');
}

function _check_required_values() {
  global $srp_property_values;

  if (!is_array($srp_property_values))
    return;

  $requierd = array('lat', 'lng', 'address', 'city', 'state', 'zip_code');
  $keys = array_keys($srp_property_values);
  foreach ($requierd as $k) {
    if (!in_array($k, $keys))
      return false;
  }
  return true;
}

if (!$srp_ext_gre_options = get_option('srp_ext_gre_options'))
  return;




$srp_ext_gre_content = array_keys($srp_ext_gre_options['content']);


$srp_ext_gre_tabs = $srp_ext_gre_options['tabs'];
$srp_general_options = get_option('srp_general_options');

function srp_get_radius($type, $echo = false) {
    global $srp_ext_gre_options;
    if ( isset($srp_ext_gre_options['radius'][$type]) )
    {
        $radius = $srp_ext_gre_options['radius'][$type];
    }
    else
    {
        $radius = 3;
    }

    if (!$echo)
        return $radius;

  echo $radius;
}

function srp_get_chart_size($side) {
  global $srp_ext_gre_options;
  if ( !isset($srp_ext_gre_options['chart-dimensions'][$side]) ) {
    if ($side == 'width') {
      $size = 500;
    } elseif ($side == 'height') {
      $size = 300;
    }
  }else{
    $size = $srp_ext_gre_options['chart-dimensions'][$side];
  }
  return $size;
}

function srp_get_map_size($side) {
  global $srp_ext_gre_options;
  if ( !isset($srp_ext_gre_options['map-dimensions'][$side]) ) {
    if ($side == 'width') {
      $size = 500;
    } elseif ($side == 'height') {
      $size = 300;
    }
  }else{
    $size = $srp_ext_gre_options['map-dimensions'][$side];
  }
  return $size;
}

function srp_gre_content_init($init) {
  global $srp_widgets;

  $gre_functions = array(
      //'Description' => 'the_listing_description_content',
      'Photos' => 'the_listing_gallery_content',
      'Video' => 'the_listing_video_content',
      'Panorama' => 'the_listing_panorama_content',
      'Downloads' => 'the_listing_downloads_content',
      'Community' => 'the_listing_community_content',
  );
  foreach ($gre_functions as $tab_name => $callback) {
    //if(srp_buffer($callback)){
    if (function_exists($callback) && srp_buffer($callback)) {
      $init[] = array(
          'name' => strtolower($tab_name),
          'title' => NULL, //GRE already provides H2 headings
          'tab_name' => $tab_name,
          'callback_function' => $callback,
          'ajax' => false,
          'save_to_buffer' => true,
      );
    }
  }
  return $init;
}

if (function_exists('greatrealestate_init')) {
  add_filter('srp_prepare_widgets_object', 'srp_gre_content_init', 2);
}

/*
 * @args - ajax, tabs = bool
 */

function srp_profile($args = array()) {
  global $srp_general_options, $srp_widgets, $srp_property_values, $srp_ext_gre_content, $srp_scripts;

    wp_enqueue_script( 'google-maps-api-v3' );

  if (count($srp_property_values) < 6)
    return;

  $srp_scripts = true;

  if (empty($args)) {
    if( isset($srp_general_options['content']['srp_profile_tabs']) ){
      $args['tabs'] = $srp_general_options['content']['srp_profile_tabs'];
    }
    if( isset($srp_general_options['content']['srp_profile_ajax']) ){
      $args['ajax'] = $srp_general_options['content']['srp_profile_ajax'];
    }
  }

  srp_prepare_widgets_object();
//var_dump($srp_widgets);
  $js_func = 'srp_profile';
  $content = '<div id="srp-tab-wrap" class="srp-tabs">';

  //Load Tabs
  if ( isset($args['tabs']) ) {
    $content .= $srp_widgets->get_tabs();
    $js_func = 'srp_profile_tabs';
  }

  //Load inline map JS since it's the same for AJAX and non-AJAX option
  $ajax_js = '';
  $nonajax_js = "\n" . '<script type="text/javascript">
  var srp_listing_values = {' . "\n";
      $i = 0;
      $n = count($srp_property_values);
      foreach ($srp_property_values as $k => $v) {
        $i++;
        if ($i == $n) {
          $comma = '';
        } else {
          $comma = ',';
        }
        $nonajax_js .= "\t" . $k . ': \'' . $v . '\'' . $comma . "\n";
      }

      $nonajax_js .= "\t" . '};%ajax_js%
  var srp_profile_view = \'' . $js_func .'\';
</script>' . "\n";

  if (!get_option('srp_ext_gre_options')) {
    $content .= '<div style="background:red; color: white; font-weight: bold; padding: 10px;">Please visit the <a href="' . ADMIN_URL . '/admin.php?page=srp_ext_gre">Extension to GRE settings</a> page to complete the installation.</div>';

  } elseif ( isset($args['ajax']) ) {

    $widgets = $srp_widgets->widgets;
    if( is_array($widgets) && !empty($widgets) && !empty($srp_ext_gre_content)){
      foreach ($widgets as $widget) {
        if ($widget->ajax == true) {
          if (in_array($widget->name, $srp_ext_gre_content)) {
            $callbacks[] = '\'' . $widget->init_function . '\'';
          }
        }
      }
    }

    if (isset($callbacks)) {

      //check if any functions exist that use 3rd party APIs and require desclamers in the footer
      /*
       * ToDo Max: add filter to wp_footer to add disclaimers.
       * Don't loop an array, but rather do each instance separately since I have to check for their API keys.
       *
        $apis = array(
        '' => 'srp_zillow_disclaimer',
        '' => 'srp_rentometer_disclaimer',
        '' => 'srp_Education_disclaimer',
        );
       */

      $ajax_js = "\n  " . 'var load_srp_functions = [' . implode(',', $callbacks) . '];';
    }

    $content .= '<div id="srp_extension">' . $srp_widgets->get_all_ajax(false) . '</div>';

  } else {
    $content .= '<div id="srp_extension">';
    $content .= $srp_widgets->get_all();
    $content .= '</div>';
  }

  $content .= '</div>';

  echo str_replace('%ajax_js%', $ajax_js, $nonajax_js);
  echo $content;
}

function srp_listing_values_js() {
    global $srp_property_values;

    //Load inline map JS since it's the same for AJAX and non-AJAX option
    $ajax_js = '';
    $nonajax_js = "\n" . '<script type="text/javascript">
    var srp_listing_values = {' . "\n";
    $i = 0;
    $n = count($srp_property_values);
    foreach ($srp_property_values as $k => $v) {
        $i++;
        if ($i == $n) {
            $comma = '';
        } else {
            $comma = ',';
        }
        $nonajax_js .= "\t" . $k . ': \'' . $v . '\'' . $comma . "\n";
    }
    $nonajax_js .= "\t" . '};%ajax_js%
    </script>' . "\n";

    return $nonajax_js;
}

/* ---------------------------------------------*
 * * Substitute function the_listing_map_content()
 * * to be placed in listingpage.php template
 * * for Great Real Estate Plugin
 * *--------------------------------------------- */

function srp_gre_the_listing_map_content() {
  global $srp_widgets, $srp_ext_gre_content, $srp_ext_gre_tabs, $srp_property_values;
  if (!_check_required_values())
    return;

  $title = 'Location Map';
  $content = '<div class=\"srp-tabs\">';
  $width = srp_get_map_size('width');
  $height = srp_get_map_size('height');
  $content .= srp_map($srp_property_values['lat'], $srp_property_values['lng'], $srp_property_values['html'], $width, $height);
  $content .= '</div>';
  return $content;
  //$srp_widgets->add('map', $title, $content);
}

function srp_map_content_init($init) {
  $array = array(
      'name' => 'map',
      'title' => 'Location Map',
      'tab_name' => 'Map',
      'callback_function' => 'srp_gre_the_listing_map_content',
      'init_function' => __FUNCTION__,
      'ajax' => false,
          //'save_to_buffer' => false,
  );
  $init[] = $array;
  return $init;
}

add_filter('srp_prepare_widgets_object', 'srp_map_content_init', 1);

function srp_gre_the_listing_schools_content() {
  global $srp_widgets, $srp_ext_gre_content, $srp_ext_gre_tabs, $srp_property_values;

  if (!in_array('schools', $srp_ext_gre_content))
    return;
  if (!EDU_API_KEY)
    return;

  if (!isset($srp_property_values['lat']) || !isset($srp_property_values['lng'])) {
    return;
  }

  $address = $srp_property_values['address'] . ' ' . $srp_property_values['city'] . ', ' . $srp_property_values['state'] . ' ' . $srp_property_values['zip_code'];
  if (!($content = srp_schoolSearch_shortcode(array("lat" => $srp_property_values['lat'], "lng" => $srp_property_values['lng'], "distance" => srp_get_radius('schools'), "groupby" => "gradelevel", "output" => "table", "location_title" => $address))))
    return;

  return $content;
}

function srp_schools_content_init($init) {
  global $srp_ext_gre_tabs;
  if (!EDU_API_KEY)
    return $init;
  $array = array(
      'name' => 'schools',
      'title' => 'Local Schools',
      'tab_name' => 'Schools',
      'callback_function' => 'srp_gre_the_listing_schools_content',
      'init_function' => __FUNCTION__,
      'ajax' => true,
  );
  $init[] = $array;
  return $init;
}

add_filter('srp_prepare_widgets_object', 'srp_schools_content_init');
/* -------------------------------------------------------------------------*
 * * Market Trends Tab (by Trulia) to be placed in listingpage.php template
 * *------------------------------------------------------------------------- */

function srp_gre_the_trulia_stats_content() {
  global $graph_types, $srp_widgets, $srp_ext_gre_content, $srp_ext_gre_tabs, $srp_property_values;
  if (!in_array('trulia_stats', $srp_ext_gre_content))
    return;

  if (!function_exists('srp_get_trulia_stats'))
    return;

  $output = '';
  $li = '';
  foreach ($graph_types as $k => $v) {
    $id = str_replace(' ', '_', strtolower($k));
    $output .='<div id="' . $id . '">' . srp_get_trulia_stats(array('type' => $k, 'city' => $srp_property_values['city'], 'state' => $srp_property_values['state'], 'zipcode' => $srp_property_values['zip_code'])) . '</div>';
    $li .= '<li><a href="#' . $id . '">' . $v . '</a></li>' . "\n";
  }
  $content = "<div class=\"srp-tabs\"><ul class=\"clearfix\">\n $li \n </ul>\n";
  $content .= $output . '</div>';

  return $content;
}

function srp_trulia_stats_content_init($init) {
  global $srp_ext_gre_tabs, $srp_ext_gre_content;

  $array = array(
      'name' => 'trulia_stats',
      'title' => 'Market Statistics',
      'tab_name' => 'Market Stats',
      'callback_function' => 'srp_gre_the_trulia_stats_content',
      'init_function' => __FUNCTION__,
      'ajax' => true,
  );
  $init[] = $array;
  return $init;
}

add_filter('srp_prepare_widgets_object', 'srp_trulia_stats_content_init');
/* -------------------------------------------------------------------------*
 * * Market Trends Tab (by ALTOS Research) to be placed in listingpage.php template
 * *------------------------------------------------------------------------- */

function srp_gre_the_altos_stats_content($width = false, $height = false) {
  global $metrics, $pricerangequartile, $rollingaverage, $srp_widgets, $srp_ext_gre_content, $srp_ext_gre_tabs, $srp_property_values;

  if (!$width)
    $width = srp_get_chart_size('width') . 'px';

  if (!$height)
    $height = srp_get_chart_size('height') . 'px';

  if (!function_exists('srp_get_altos_stats'))
    return;

  $output = '';
  $li = '';
  foreach ($metrics as $k => $v) {
    $id = str_replace(' ', '_', strtolower($k));
    $output .='<div id="' . $id . '">' . srp_get_altos_stats(array('type' => $k, 'width' => $width, 'height' => $height, 'city' => $srp_property_values['city'], 'state' => $srp_property_values['state'], 'zipcode' => $srp_property_values['zip_code'])) . '</div>';
    $li .= '<li><a href="#' . $id . '">' . $v . '</a></li>' . "\n";
  }

  $content = "<div class=\"srp-tabs\"><ul class=\"clearfix\">\n $li \n </ul>\n";
  $content .= $output . '</div>';

  return $content;
}

function srp_altos_stats_content_init($init) {
  global $srp_ext_gre_tabs, $srp_ext_gre_content;

  $array = array(
      'name' => 'altos_stats',
      'title' => 'Market Statistics',
      'tab_name' => 'Market Stats',
      'callback_function' => 'srp_gre_the_altos_stats_content',
      'init_function' => __FUNCTION__,
      'ajax' => true,
  );
  $init[] = $array;
  return $init;
}

add_filter('srp_prepare_widgets_object', 'srp_altos_stats_content_init');
/* -------------------------------------------------------------------------*
 * * Mortgage/Financing Tab to be placed in listingpage.php template
 * *------------------------------------------------------------------------- */

function srp_gre_the_mortgage_content() {
  global $srp_widgets, $srp_ext_gre_content, $srp_ext_gre_tabs, $srp_property_values;
  if (!in_array('mortgage_calc', $srp_ext_gre_content) && !in_array('closing_estimator', $srp_ext_gre_content) && !in_array('affordability_calc', $srp_ext_gre_content))
    return;

  if (in_array('mortgage_calc', $srp_ext_gre_content)) {
    $content = '
		<div style="float: left; width: 48%; padding: 0 1%;">'
            . srp_MortgageCalc_shortcode(array('price_of_home' => $srp_property_values['listing_price']))
            . '</div>';
  }

  $content .= '<div style="float:left; width: 48%; padding: 0 1%;">';

  if (in_array('closing_estimator', $srp_ext_gre_content)) {
    $content .= srp_ClosingCosts_shortcode(array('loan_amount' => $srp_property_values['listing_price']));
  }

  $content .= '</div>
		<div style="float:left; width: 48%; padding: 0 1%;">';

  if (in_array('affordability_calc', $srp_ext_gre_content)) {
    $content .= srp_AffordabilityCalc_shortcode();
  }

  $content .= '
		</div>';

  return $content;
}

function srp_mortgage_content_init($init) {
  global $srp_ext_gre_tabs, $srp_ext_gre_content;

  $array = array(
      'name' => 'financial',
      'title' => 'Financial Tools',
      'tab_name' => 'Financing',
      'callback_function' => 'srp_gre_the_mortgage_content',
      'init_function' => __FUNCTION__,
      'ajax' => true,
  );
  $init[] = $array;
  return $init;
}

add_filter('srp_prepare_widgets_object', 'srp_mortgage_content_init');

/* -------------------------------------------------------------------------*
 * * Yelp Tab to be placed in listingpage.php template
 * *------------------------------------------------------------------------- */

function srp_gre_the_yelp_content() {
  global $srp_widgets, $srp_ext_gre_content, $srp_ext_gre_tabs, $srp_property_values;
  if (!in_array('yelp', $srp_ext_gre_content))
    return;

  if (!get_option('srp_yelp_api_key'))
    return;
  if (!function_exists('srp_Yelp_shortcode'))
    return;
  if (!($content = srp_Yelp_shortcode($atts = array("location" => $srp_property_values['zip_code'], "lat" => $srp_property_values['lat'], "lng" => $srp_property_values['lng'], 'radius' => srp_get_radius('yelp'), 'output' => 'table', 'sortby' => 'distance', 'term' => null, 'num_biz_requested' => null, 'ajax' => null))))
    return;

  return $content;
}

function srp_yelp_content_init($init) {
  global $srp_ext_gre_tabs, $srp_ext_gre_content;

  if (!get_option('srp_yelp_api_key'))
    return $init;

  $array = array(
      'name' => 'yelp',
      'title' => 'Businesses in the Neighborhood',
      'tab_name' => 'Nearby Businesses',
      'callback_function' => 'srp_gre_the_yelp_content',
      'init_function' => __FUNCTION__,
      'ajax' => true,
  );
  $init[] = $array;
  return $init;
}

add_filter('srp_prepare_widgets_object', 'srp_yelp_content_init');
/* -------------------------------------------------------------------------*
 * * Walkscore Tab to be placed in listingpage.php template
 * *------------------------------------------------------------------------- */

function srp_gre_the_walkscore_content() {
  global $srp_widgets, $srp_ext_gre_content, $srp_ext_gre_tabs, $srp_property_values;
  if (!in_array('walkscore', $srp_ext_gre_content))
    return;
  if (!$ws_wsid = get_option('srp_walkscore_api_key'))
    return;
  $ws_address = $srp_property_values['address'] . ' ' . $srp_property_values['city'] . ', ' . $srp_property_values['state'] . ' ' . $srp_property_values['zip_code'];

  $content = srp_walkscore($ws_wsid, $ws_address, $ws_width = 500, $ws_height = 286, $ws_layout = 'horizontal');

  return $content; //$ws_wsid . ' ' . $ws_address . ' ' . $ws_width=500 . ' ' . $ws_height=286 . ' ' . $ws_layout = 'horizontal';
}

function srp_walkscore_content_init($init) {
  global $srp_ext_gre_tabs, $srp_ext_gre_content;

  if (!$ws_wsid = get_option('srp_walkscore_api_key'))
    return $init;

  $array = array(
      'name' => 'walkscore',
      'title' => 'Walkability of the Neighborhood',
      'tab_name' => 'Walkability',
      'callback_function' => 'srp_gre_the_walkscore_content',
      'init_function' => __FUNCTION__,
      'ajax' => true,
  );
  $init[] = $array;
  return $init;
}

add_filter('srp_prepare_widgets_object', 'srp_walkscore_content_init');