<?php

/* Global array of shorcode attributes
  /*keys are the actual shorcode names */
$shortcode_atts = array(
    'mortgage' => array(
        'required' => array(
            "price_of_home" => '',
            "interest_rate" => '',
        ),
        'optional' => array(
            "title" => "Mortgage Calculator",
            "before_title" => "<h3>",
            "after_title" => "</h3>",
            "down_payment" => 0,
            "mortgage_term" => 30,
            "width" => false,
            "return" => true,
        ),
    ),
    'mortgage_rates' => array(
        'required' => array(),
        'optional' => array(
            "title" => "Mortgage Rates",
            "before_title" => "<h3>",
            "after_title" => "</h3>",
            "width" => false,
            "return" => true,
        ),
    ),
    'affordability' => array(
        'required' => array(
            "interest_rate" => '',
        ),
        'optional' => array(
            "title" => "Affordability Calculator",
            "before_title" => "<h3>",
            "after_title" => "</h3>",
            "width" => false,
            "return" => true,
        ),
    ),
    'closingcosts' => array(
        'required' => array(),
        'optional' => array(
            "title" => "Closing Cost Estimator",
            "before_title" => "<h3>",
            "after_title" => "</h3>",
            "loan_amount" => false,
            "width" => false,
            "return" => true,
        ),
    ),
    'schoolsearch' => array(
        'required' => array(),
        'optional' => array(
            "title" => "Schools",
            "location_title" => false,
            "city" => false,
            "state" => false,
            "zip" => false,
            "lat" => false,
            "lng" => false,
            "distance" => srp_get_radius('schools'),
            "groupby" => "gradelevel",
            "output" => "table",
        ),
        'description' => '<p><strong>Important:</strong> One of the location attributes has to be defined, for example zip code OR lattitude and longitude. Also, attribute distance will only work with lat and lng coordinates.</p><p>Attribute <em>groupby</em> can have the following values: zip, gradelevel(default), schooltype, schooldistrictname.<br />
Attribute <em>output</em> can have the following values: table(default), list.</p>',
    ),
    'yelp' => array(
        'required' => array(
            "lat"       => null,
            "lng"       => null,
            "location"  => null,
        ),
        'optional' => array(
            "title"         => "Yelp/Nearby Businesses",
            "radius"        => srp_get_radius('yelp'),
            "output"        => "table",
            "sortby"        => 'distance',
            "term"          => null,
            "num_biz_requested" => NULL,
            "ajax"          => NULL,
        ),
        'description' => '<p>Attribute <em>output</em> can have the following values: table(default), list.<br />Attribute <em>sortby</em> can have the following values: distance(default), name, avg_rating.<br />
Attribute <em>term</em> can have the following values: ' . implode(', ', array_keys($yelp_categories)) . '.</p>',
    ),
    'srpmap' => array(
        'required' => array(
            "lat" => NULL,
            "lng" => NULL,
        ),
        'optional' => array(
            "title" => "Google Map",
            "width" => NULL,
            "height" => NULL,
            "address" => NULL,
            "city" => NULL,
            "state" => NULL,
            "zip_code" => NULL,
            "extended" => false,
        ),
    ),
    'walkscore' => array(
        'required' => array(
            "ws_wsid" => get_option('srp_walkscore_api_key'),
            "ws_address" => NULL,
        ),
        'optional' => array(
            "title" => "Walkscore",
            "ws_width" => 500,
            "ws_height" => 286,
            "ws_layout" => 'horizontal',
        ),
    ),
    'srp_profile' => array(
        'required' => array(
            'lat' => null,
            'lng' => null,
            'address' => null,
            'city' => null,
            'state' => null,
            'zip_code' => null,
        ),
        'optional' => array(
            'title' => 'Neighborhood Profile',
            'listing_price' => null,
            'bedrooms' => null,
            'bathrooms' => null,
            'html' => null,
        ),
    ),
);

function srp_merge_atts($shortcode_name) {
  global $shortcode_atts;
  $new_atts = array();
  $new_atts = array_merge($shortcode_atts[$shortcode_name]['required'], $shortcode_atts[$shortcode_name]['optional']);
  return $new_atts;
}

/*
 * * Widget Shortcodes
 */

function srp_MortgageCalc_shortcode($atts=array()) {
  $args = shortcode_atts(srp_merge_atts('mortgage'), $atts);
  $instance = $args;
  $sb = new srp_MortgageCalc();
  $sb->number = rand(100, 999);
  return $sb->widget($args, $instance);
}

function srp_AffordabilityCalc_shortcode($atts=array()) {
  $args = shortcode_atts(srp_merge_atts('affordability'), $atts);
  $instance = $args;
  $sb = new srp_AffordabilityCalc();
  $sb->number = rand(100, 999);
  return $sb->widget($args, $instance);
}

function srp_ClosingCosts_shortcode($atts=array()) {
  $args = shortcode_atts(srp_merge_atts('closingcosts'), $atts);
  $instance = $args;
  $sb = new srp_ClosingCosts();
  $sb->number = rand(100, 999);
  return $sb->widget($args, $instance);
}

function srp_MortgageRates_shortcode($atts=array()) {
  $args = shortcode_atts(srp_merge_atts('mortgage_rates'), $atts);
  $instance = $args;
  $sb = new srp_MortgageRates();
  $sb->number = rand(100, 999);
  return $sb->widget($args, $instance);
}

/*
 * * @groupby = zip, gradelevel, schooltype, schooldistrictname
 */

function srp_schoolSearch_shortcode($atts=array(), $ajax = false) {
  unset($atts['title']);
  $args = shortcode_atts(srp_merge_atts('schoolsearch'), $atts);

  $location = array('city', 'state', 'zip', 'distance', 'lat' => 'latitude', 'lng' => 'longitude', 'location_title');

  if ($args) {
    foreach ($location as $key => $name) {
      $a = null;
      $b = null;
      if (in_array($name, array_keys($args))) {
        $a = $name;
        $b = $name;
      } elseif (in_array($key, array_keys($args))) {
        $a = $name;
        $b = $key;
      }
      if ($a && $b) {
        $args['location'][$a] = $args[$b];
        unset($args[$b]);
      }
      $args['location'] = array_filter($args['location']);
    }
  }
  //print_r($args);
  return srp_tabs_byType($args, $ajax);
}

function srp_Yelp_shortcode($atts=array()) {
  unset($atts['title']);
  $args = shortcode_atts(srp_merge_atts('yelp'), $atts);
  return srp_getYelp($args);
}

function srp_map_shortcode($atts=array(), $content = NULL) {
   global $srp_property_values;
  $args = shortcode_atts(srp_merge_atts('srpmap'), $atts);
  $args['gmap'] = 1;
  return srp_profile_shortcode($args, $content);
}

function srp_walkscore_shortcode($atts=array()) {
  $args = shortcode_atts(srp_merge_atts('walkscore'), $atts);

    if (isset($args['ws_address']) && !empty($args['ws_address']))
    {
        return srp_walkscore($args['ws_wsid'], $args['ws_address'], $args['ws_width'], $args['ws_height'], $args['ws_layout']);
    }
}

function srp_profile_shortcode($atts=array(), $content = NULL) {
  //removing empty attributes
//    $atts = array_filter($atts);

    if (!isset($atts['gmap']))
    {
        $args = shortcode_atts(srp_merge_atts('srp_profile'), $atts);
    }
    else if (isset($atts['gmap']) && $atts['gmap'] == 1 &&
        isset($atts['extended']) && $atts['extended'] == true)
    {
        $args = shortcode_atts(srp_merge_atts('srp_profile'), $atts);

    }
    else
    {
        $args = $atts;
    }

  //add address instead of description if none provided
  if( isset($args['address']) || isset($args['city']) ){
    $address = $args['address'] . ', ' . $args['city'] . ' ' . $args['state'] . ' ' . $args['zip_code'];
  }
  if( !$content ){
    if( isset($address)){
      $content = $address;
    }else{
      //if no content and we have default title "Address" - remove it
    if( 'Address' == $args['title'] )
      unset($args['title']);
    }
  }

  if (isset($args['title']))
  {
      $title = sprintf( '<div class="srp-map-location-title" style="font-size: 1.2em;"><b>%s</b></div>', addslashes($args['title']) );
  }
    else
    {
        $title = null;
    }

  if( $content ){
    // WP 3.3.1 - I'm so tired of WP adding dangling <p> tags into shortcodes
    //remove them all
    $content = str_replace(array( '<p>', '</p>' ), ' ', addslashes($content) );
    //add my own around the content
    $content = '<p>' . $content . '</p>';
    $content = str_replace( array("\r\n", "\n", "\r"), "", '<div class="infoWindow" style="max-width:300px; line-height: normal;">' . $title . $content . '</div>' );
  }
    $args['html'] = $content;

  global $srp_property_values;
  $srp_property_values = $args;

  //check if shortcode is for GMap
  if (isset($args['extended']) && $args['extended'] == false)
  {
      return srp_map($args['lat'], $args['lng'], $args['html'], @$args['width'], @$args['height']) . str_replace('%ajax_js%', '', srp_listing_values_js() );
  }

  $output = srp_buffer('srp_profile');

  return $output;
}

add_shortcode('mortgage', 'srp_MortgageCalc_shortcode');
add_shortcode('affordability', 'srp_AffordabilityCalc_shortcode');
add_shortcode('closingcosts', 'srp_ClosingCosts_shortcode');
add_shortcode('mortgage_rates', 'srp_MortgageRates_shortcode');
//add_shortcode('rentmeter', 'srp_RentMeter_shortcode');
add_shortcode('schoolsearch', 'srp_schoolSearch_shortcode');
add_shortcode('yelp', 'srp_Yelp_shortcode');
add_shortcode('srpmap', 'srp_map_shortcode');
add_shortcode('srp_profile', 'srp_profile_shortcode');
add_shortcode('walkscore', 'srp_walkscore_shortcode');