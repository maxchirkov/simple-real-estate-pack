<?php

define('TRULIA_VER', 163);

$graph_types = array(
	'qma_median_sales_price'	=> 'Median Sales Price',
	'qma_sales_volume'			=> 'Sales Volume',
	'average_listing_price'		=> 'Average Listing Price',
	'listing_volume'			=> 'Number of Properties',
	'qma_price_per_sqft'		=> 'Average Price Per Square Foot',
);

function srp_get_trulia_stats($atts=array()){
	global $graph_types, $srp_property_values;
	$args = shortcode_atts(array(
		"width"		=> srp_get_chart_size('width'),
		"height"	=> srp_get_chart_size('height'),
		"type"		=> 'qma_median_sales_price',
		"city"		=> $srp_property_values['city'],
		"state"		=> $srp_property_values['state'],
		"zipcode"	=> $srp_property_values['zip_code'],
		"period"	=> 'all',
		"exclude"	=> false,
	), $atts);
	$args = array_filter($args);
	$city_state = urlencode($args['city'] . '-' . srp_get_states($args['state']));
	$url = 'http://graphs.trulia.com/real_estate/' . $city_state . '/graph.png?version=' . TRULIA_VER;

  $query = '';
	foreach($args as $k => $v){
		$query .= '&' . $k . '=' . urlencode($v);
	}
	$url = $url . $query;
        //if(function_exists('GetImageSize') && !$size = @GetImageSize($url)){
        if(!srp_is_ImgExists($url)){
            $url = SRP_URL . '/images/stats-n-a.png';
        }

	$img = '<img src="'.$url.'" alt="'.$graph_types[$args['type']].'" width="'.$args['width'].'px" height="'.$args['height'].'px"/>';
	//TODO: Make chart images clickable, so largere images open up in Thickbox.

	return $img;
}


/*
* Altos Charts
*/
$metrics = array(
	'median'				=> 'Price',
	'mean_dom'				=> 'Avg Days on Market',
	'median_market_heat'	=> 'Market Action Index',
	'inventory'				=> 'Inventory',
);

$pricerangequartile = array(
	't'	=> 'Top',
	'u'	=> 'Upper-Middle',
	'l'	=> 'Lower-Middle',
	'b'	=> 'Bottom',
	'a'	=> 'All quartiles combined',
);

$rollingaverage = array(
	'a'	=> '7-Day',
	'c'	=> '90-Day',
);

function srp_get_altos_stats($atts=array()){
	global $graph_types, $metrics;
	$args = shortcode_atts(array(
		"width"		=> null,
		"type"		=> 'median',
		"city"		=> null,
		"state"		=> null,
		"zipcode"	=> null,
	), $atts);
	$args = array_filter($args);

	$url = 'http://charts.altosresearch.com/altos/app?s='.$args['type'].':l,&ra=c&st='.$args['state'].'&c='.$args['city'].'&z='.$args['zipcode'].'&sz=l&service=chart&pai=55179304';
	//if (!@fclose(@fopen($url, "r"))) {
        //if(function_exists('GetImageSize') && !$size = @GetImageSize($url)){
        if(!srp_is_ImgExists($url)){
            $url = SRP_URL . '/images/stats-n-a.png';
        }

  $alt_text = ( isset($metrics[$args['type']]) ) ? $metrics[$args['type']] . ' in ' : 'Real Estate Statistics for ';
  $alt_text .= ( isset($args['city']) ) ? $args['city'] . ', ' : '';
  $alt_text .= ( isset($args['state']) ) ? $args['state'] . ' ' : '';
  $alt_text .= ( isset($args['zipcode']) ) ? $args['zipcode'] : '';
  
	$img = '<img src="'.$url.'" alt="'. $alt_text .'" width="'.$args['width'].'"/>';

	return $img;
}

/*
* GMap
*/

if(get_option('greatrealestate_googleAPIkey')){
	define("GMAP_API", get_option('greatrealestate_googleAPIkey'));
}elseif(get_option('srp_gmap_api_key')){
	define("GMAP_API", get_option('srp_gmap_api_key'));
}