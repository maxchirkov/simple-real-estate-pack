<?php
function srp_yelp_get_api_key(){
	if(function_exists('get_option')){
		$api_key = get_option('srp_yelp_api_key');
		if($api_key != NULL){
			return $api_key;
		}
	}
}


define('YELP_API_URL', 'http://api.yelp.com/business_review_search');
define('YELP_API_KEY', srp_yelp_get_api_key());
define('YELP_OUTPUT', 'json');

$yelp_categories = array(
	'grocery'       => array(
                                'name'		=> 'Grocery Stores',
                                'category'	=> 'grocery',
                                'term'		=> 'grocery',
                        ),
	'restaurants'   => array(
                                'name'		=> 'Restaurants',
                                'category'	=> 'restaurants',
                                'term'		=> 'restaurants',
                        ),
	'banks'		=> array(
                                'name'		=> 'Banks',
                                'category'	=> 'banks',
                                'term'		=> 'banks',
                        ),
        'gas_stations'  => array(
                                'name'		=> 'Gas Stations',
                                'category'	=> 'servicestations',
                                'term'		=> 'gas_stations',
                        ),
	'golf'		=> array(
                                'name'		=> 'Golf Courses',
                                'category'	=> 'golf',
                                'term'		=> 'golf',
                        ),
	'hospitals'	=> array(
                                'name'		=> 'Hospitals',
                                'category'	=> 'hospitals',
                                'term'		=> 'hospitals',
                        )
);

function srp_getYelp($lat, $lng, $radius, $output = 'table', $sortby = 'distance', $term = null, $num_biz_requested = null, $ajax = null){
	global $yelp_categories, $srp_scripts;

	if($term && $yelp_categories[$term]) {
		$_categories = array($term => $yelp_categories[$term]);
	}elseif($term && $terms = explode(',', $term)){
		foreach($terms as $t){
			$_categories[$t] = $yelp_categories[$t];
		}
	}else{
		$_categories = $yelp_categories;
	}
	//print_r($_categories);
  $tabs = false;
  $content_output = false;
	foreach($_categories as $cat){
		$args = array(
			'term'				=> $cat['term'],
			'num_biz_requested'	=> $num_biz_requested,
			'lat'				=> $lat,
			'long'				=> $lng,
			'radius'			=> $radius,
			'ywsid'				=> YELP_API_KEY,
			'output'			=> YELP_OUTPUT,
			'category'			=> $cat['category'],
		);

		if(count($_categories) > 1){
			$wrap_open = '<div id="tabs-'.$cat['term'].'">'
				. '<h3>' . __($cat['name']). '</h3>';
			$wrap_close = '</div>';
		}

		$args = array_filter($args);
		$query_arr = array();
		foreach($args as $k => $v){
			$query_arr[] = $k . '=' . $v;
		}
		$query = implode('&', $query_arr);
		$request = YELP_API_URL . '?' . $query;
		//print $request;
		if(!$request_result = @file_get_contents($request))
			return;

		$result = json_decode($request_result, true);
		$phparray = $result;

        if(count($phparray['businesses']) < 1){
            $message = '<p class="no-businesses-found">There are no ' . $cat['name'] . ' within ' . $radius . ' miles radius from this property.</p>';
            if($ajax){
                return json_encode(array('message' => $message));
            }
        }

		if(count($_categories) > 1){
			$tabs .= '<li><a href="#tabs-'.$cat['term'].'"  title="'.__($cat['name'],"simplerealestatepack") . '" ><span>'
			. __($cat['name'],"simplerealestatepack")
			. '</span></a></li>' . "\n";
		}
		$x = 0;
		//pre-sorting
		$businesses = array();
		$coordinates = array();
		$table = null;

		foreach($phparray['businesses'] as $item){
			$businesses[] = array($item[$sortby], 'biz' => $item);
		}
		switch($sortby){
			case 'avg_rating':
				rsort($businesses);
				break;
			case 'distance':
			case 'name':
				sort($businesses);
				break;
		}

		if(!empty($businesses)){
			foreach($businesses as $item){
				$biz = $item['biz'];
				$x++;
				if($x%2){ $even_odd = "even"; } else { $even_odd = "odd"; }
				$coordinates[$cat['term']][$biz['id']]['lat'] = $biz['latitude'];
				$coordinates[$cat['term']][$biz['id']]['lng'] = $biz['longitude'];

				/*
				 * Every single element needs to have inline styls with their corresponding widths
				 * so JS can calculate the total width and height of the InfoWindow correctly
				 * otherwise it calculates dimensions of the content being stacked as is.
				 */
				$coordinates[$cat['term']][$biz['id']]['html'] = '
				<div class="srp_infoWindow clearfix" style="width: 315px; font-size: 12px;line-height: normal;">
				<img src="'.$biz['rating_img_url'].'" width="84" height="17" class="yelp_rating" style="float:left" /><a href="'.$biz['url'].'" target="_blank" title="Read Reviews">'. $biz['review_count'] .' Reviews</a>
				<img src="' . $biz['photo_url'].'" width="100" height="100" class="yelp_photo" style="float:right" />
				<div class="yelp_text" style="width: 200px">
				<span class="school_name">
				<a href="'.$biz['url'].'" target="_blank">'.$biz['name'].'</a>
				</span><br />
				Phone: '. srp_format_phone($biz['phone'])
				.'<br />' . $biz['address1'].', '. $biz['city'].', '.$biz['state_code'].' '. $biz['zip']
				.'</div>
				</div>
				<div id="yelp_attribution" style="float:none; width: 315px; text-align: right;">
				<a href="http://www.yelp.com">
				<img src="'. SRP_IMG .'/branding/reviewsFromYelpWHT.gif" width="115" height="25" alt="Reviews from Yelp.com" />
				</a>
				</div>';

				$table .= '<tr class="' . $even_odd . '">
					<td style="vertical-align: middle;"><img src="' . $biz['photo_url_small'].'" class="yelp_photo" width="40" height="40" align="left"/></td>
                                            <td style="vertical-align: middle;"><div class="yelp_text"><span class="school_name"><a href="'.$biz['url'].'" target="_blank">'.$biz['name'].'</a></span><br />Phone: '. srp_format_phone($biz['phone']) .'<br />' . $biz['address1'].', '. $biz['city'].', '.$biz['state_code'].' '. $biz['zip'] .'</div></td>
					<td style="vertical-align: middle;">
						<div class="yelp_distance">' . round($biz['distance'], 2) . ' miles</div>
					</td>
					<td style="vertical-align: middle;" class="yelp_rating">
                                                <img src="'.$biz['rating_img_url_small'].'" /><br /><a href="'.$biz['url'].'" target="_blank" title="Read Reviews">'. $biz['review_count'] .' Reviews</a>
					</td>
				  </tr>';
			}
		}else{
			$table = $message;
		}


		//$_SESSION['srp_coordinates'] = $coordinates;
		if($ajax && $coordinates)
		{
			$ajax_output .= json_encode($coordinates);
		}elseif($table)
		{
			$content_output .= $wrap_open;
			$content_output .= '<table class="srp_table tableStyle">' . $table . '</table>';
			$content_output .= $wrap_close;
		}

	}
	if( isset($ajax_output) ){
    $srp_scripts = true;
		return $ajax_output;
	}elseif( isset($content_output) ){
    $srp_scripts = true;
		return '<div class="srp-tabs"><ul class="clearfix">' . $tabs . '</ul><div style="clear:both;"></div>' . $content_output . '</div><div id="yelp_attribution"><a href="http://www.yelp.com"><img src="'. SRP_IMG .'/branding/reviewsFromYelpWHT.gif" width="115" height="25" alt="Reviews from Yelp.com"/></a></div>';
	}else{
		return;
	}
}

function srp_yelp_select(){
	global $yelp_categories;
	$output = '<div id="yelp_select">';
	$output .= apply_filters('_add_to_yelpselect', $output);
	foreach($yelp_categories as $cat){
		$output .= '<input id="yelp_cat_'.$cat['term'].'" name="'.$cat['term'].'" type="checkbox"><label for="'.$cat['term'].'">'.$cat['name'].'</label><br />' . "\n";
	}
	//$output .= '<a class="poweredbysrp" href="http://wordpress.org/extend/plugins/simple-real-estate-pack/">Powered by <span>SRP</span></a>';
	$output .= '</div>';

	return $output;
}

function srp_getYelp_ajax(){
	$lat = $_POST['lat'];
	$lng = $_POST['lng'];
	$radius = $_POST['radius'];
	$term = $_POST['term'];
	if( $result = srp_getYelp($lat, $lng, $radius=3, $output = 'table', $sortby = 'distance', $term , $num_biz_requested = null, $ajax = true) ){
		die($result);
	}
}

add_action('wp_ajax_srp_getYelp_ajax', 'srp_getYelp_ajax');
add_action('wp_ajax_nopriv_srp_getYelp_ajax', 'srp_getYelp_ajax');
?>