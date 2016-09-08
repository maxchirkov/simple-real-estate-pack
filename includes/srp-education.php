<?php
define('EDU_API_URL', 'http://api.education.com/service/service.php');
define('EDU_API_KEY', '65f5fef47d17f7562c88128cae993b11');
define('RESF', 'xml');

function srp_education_get_api_key(){
		return EDU_API_KEY;
}

function _srp_get_query_url($args = array()){
  global $srp_scripts;
  $srp_scripts = true;

	if(is_array($args) && count($args)>0){
		$i = 0;
		$q = NULL;

		//distance parameter is invalid if lat/lng is not setup
		if( !isset($args['latitude']) )
			unset($args['distance']);

        //distance is required if lat/lng is set
        if( isset($args['latitude']) && isset($args['longitude']) )
            $args['distance'] = isset($args['distance']) ? $args['distance'] : 3;

    $query = '';
		foreach($args as $key => $value){
			if(!$value){
				return;
			}
			if($i > 0){
				$q = '&';
			}
			$query .= $q . $key . '=' . str_replace(' ', '+', $value);
			$i++;
		}

		return $query;
	}
}
// Create function_name_output() specific to each function
function srp_get_apiFunction($function_name){
	$api_funcs = array(
		'schoolSearch' => array(
			'required'	=> array(
				'sn'	=> 'sf',
				'f'		=> 'schoolSearch',
				'key'	=> EDU_API_KEY,
				'resf'	=> RESF,
			),
			'optional'	=> array(
				'schoolid', 'districtid', 'zip', 'city', 'state', 'distance', 'latitude', 'longitude', 'minResult',
			),
			'return'	=> array(),
		),


		'districtSearch' => array(
			'required'	=> array(
				'sn'	=> 'sf',
				'f'		=> 'districtSearch',
				'key'	=> EDU_API_KEY,
				'resf'	=> RESF,
			),
			'optional'	=> array(
				'districtid', 'districtname', 'zip', 'city', 'state',
			),
			'return'	=> array(),
		),


		'numberOf' => array(
			'required'	=> array(
				'sn'	=> 'sf',
				'f'		=> 'numberOf',
				'key'	=> EDU_API_KEY,
				'resf'	=> RESF,
				'city'	=> false,
				'state'	=> false,
			),
			'optional'	=> array(),
			'return'	=> array(),
		),

                'gbd' => array(
			'required'	=> array(
				'sn'	=> 'sf',
				'f'	=> 'gbd',
				'key'	=> EDU_API_KEY,
				'resf'	=> RESF,
			),
			'optional'	=> array( 'city', 'state'),
			'return'	=> array(),
		),


	);

	foreach($api_funcs[$function_name]['required'] as $key => $val){
		if(empty($api_funcs[$function_name]['required'][$key])){
			$empty[] = $key;
		}
	}
	if( isset($empty) ){
		$keys = implode(', ', $empty);
		echo '<div class="error">Education API paramenters [' . $keys . '] for function ' . $function_name. '() were not found. Please check the Plugin Settings.</div>';
		die();
	}
	return $api_funcs[$function_name];
}

function srp_run_apiFunction($function_name, $arguments=array()){
	$function = srp_get_apiFunction($function_name);
		$required = array();
		$optional = array();

		//checking required options against arguments
		foreach($arguments as $arg=>$value){

			if(in_array($arg, array_keys($function['required']))){
				$function['required'][$arg] = $value;
			}
		}
		$required = $function['required'];

		//checking optional parameters agains arguments
		foreach($arguments as $arg=>$value){
			if(in_array($arg, $function['optional'])){
				$optional[$arg] = $value;
			}
		}

		if(!empty($required) && !empty($optional)){
                    $url = EDU_API_URL . '?' . _srp_get_query_url($required) . '&' . _srp_get_query_url($optional);

                    if(!$xml = srp_wp_http_xml($url)){
                        return;
                    }
                    return $xml;
               }
}

function srp_groupSchoolsBy($args){
	$location = $args['location'];
	$xml = srp_run_apiFunction('schoolSearch', $location);
	if($xml){
		$schools = array();
		$i=0;
		//pa($xml);
		foreach($xml->record as $item){
			//if($i> 5){ break; }
			$school = $item->school;
			$group = (string)$school->$args['groupby'];
			$group = explode(',', $group);
			$group = array_unique($group);
                        if(is_array($group)){
                            foreach($group as $name){
                                    if($name != ''){
                                            $schools[$name][] = $item;
                                    }
                            }
                            $i++;
                            unset($group);
                        }
		}
                if(!empty($schools))
                    return $schools;

                return;
	}
        return;
}

function srp_tabs_byType($args = array(), $ajax = NULL){
	if(empty($args)) { return; }

    //don't display distance radius for city/zip code searches
    if ( isset($args['location']['distance']) &&
            ( isset($args['location']['latitude']) && isset($args['location']['longitude']) )
        )
    {
		$distance = $args['location']['distance'];
    }

	if(!$types = srp_groupSchoolsBy($args)) {
            if($distance){
                $message = "<p class='no-schools-found'>There are no schools within {$distance} miles radius of this location.</p>";
            }else{
                $message = "<p class='no-schools-found'>There are no schools near by this property.</p>";
            }
            if($ajax){
                return serialize(array('message' => $message, 'markers' => array()));
            }
            return $message;
        }
	$html_arr = array();
	$titles = array_keys($types);
	$coordinates = array();

	$i = 0;
    $list = false;
    $table = false;
    $tabs = false;
	foreach($types as $type){
		$groups[$i]	= $titles[$i];
		$totals[$i]	= count($type);
		$tabids[$i]	= 1 + $i;

		$i++;
		$total = count($type);
		$name = $titles[$i-1];
		$$args['output'] .= '<div id="tabs-' . $i . '">' . "\n";
        $location = false;
        $in = false;

        if( !isset($args['location']['location_title']) ){
			if ( isset($args['location']['city']) )
                $location .= $args['location']['city'];

            if ( $location && isset($args['location']['state']) )
                $location .= ', ' . $args['location']['state'];

            if ( isset($args['location']['zip']) )
                $location .= ' ' . $args['location']['zip'];

            if ($location)
                $in = ' in ';

			$td_distance_header = false;
		}elseif($args['location']['location_title']){
			$location = $args['location']['location_title'];
			$in = ' near ';
			$td_distance_header = '<th scope="col" class="school_field_center">Distance (mi)</th>';
		}elseif(!$args['location']['latitude'] && !$args['location']['longitude']){
			$in = ' near ';
			$td_distance_header = '<th scope="col" class="school_field_center">Distance (mi)</th>';
		}
		$$args['output'] .= "<h3>$name Schools $in $location</h3>";
		if(isset($distance)){
			$$args['output'] .= "<p class='schools-found'>$total $name Schools found within $distance miles radius.</p>\n";
		}else{
			$$args['output'] .= "<p class='schools-found'>$total $name Schools found in the area.</p>\n";
		}
        $list .= "\t<ul>\n";
		$table .= '<table class="srp_table tableStyle SchoolsByType">
					  <tr>
						<th scope="col" style="width: 40%">School</th>
						<th scope="col" class="school_field_center" style="width: 20%">Type</th>
						<th scope="col" class="school_field_center" style="width: 20%">Grade Level</th>
						<th scope="col" class="school_field_center" style="width: 20%">Enrollment</th>
						<th scope="col" class="school_field_center" style="width: 20%">Students per Teacher</th>'
						.$td_distance_header.
					'</tr>'."\n";
		$x = 0;
		foreach($type as $item){
			if($x%2){ $even_odd = "even"; } else { $even_odd = "odd"; }
			$school = $item->school;
                        if(!isset($city) && !isset($state) && $school->city){
                            $city = $school->city;
                            $state = $school->state;
                        }
			$list .= "\t\t<li>" . $school->schoolname . ' <br /> Phone: ' . $school->phonenumber . '<br />' . $school->address . ', ' . $school->city . ', ' . $school->state . $school->zip . "</li>\n";

            $td_distance = false;
			if($td_distance_header){
				$td_distance = '<td class="school_field_center">'.round((float)$school->distance, 2).'</td>';
			}

                        $website = null;
                        if($school->website != ""){
                            $website = ' <a href="http://' . $school->website . '" target="_blank">' . $school->website . '</a>';
                        }
			$table .= "<tr class=\"$even_odd\">
						<td class=\"school_address\"><a href=\"$school->url\" rel=\"nofollow\" target=\"_blank\"><span class=\"school_name\">$school->schoolname</span></a><br />Phone: $school->phonenumber<br />$school->address, $school->city, $school->state $school->zip". $website ."</td>
						<td class=\"school_field_center\">$school->schooltype</td>
						<td class=\"school_field_center\">$school->gradesserved</td>
						<td class=\"school_field_center\">$school->enrollment</td>
						<td class=\"school_field_center\">$school->studentteacherratio</td>
						$td_distance
					  </tr>\n";

			//$coordinates[] = '"' . $school->latitude .'|'. $school->longitude . '|<div><span class=\"school_name\">'.$school->schoolname.'</span><br />'.$school->schooltype . ' - ' . $school->gradesserved .'<br />Phone: '.$school->phonenumber.'<br />'.$school->address.', '. $school->city.', '.$school->state.' '. $school->zip.'</div>' . '"';
			$schoolname = (string)$school->schoolname;
			$coordinates[$schoolname]['lat'] = (string)$school->latitude;
			$coordinates[$schoolname]['lng'] = (string)$school->longitude;
			$coordinates[$schoolname]['html'] = '<div style="width: 315px; min-height: 70px; font-size: 12px;line-height: normal;">
                            <span class="school_name">'.$school->schoolname.'</span>
                                <br />'.$school->schooltype . ' - ' . $school->gradesserved
                                .'<br />Phone: '.$school->phonenumber
                                .'<br />'.$school->address.', '
                                . $school->city.', '
                                .$school->state.' '
                                . $school->zip
                                .'</div>';
			$x++;
		}
		$list .= "\t<ul>\n";
		$table .= "</table>\n";
		$$args['output'] .= "</div>\n";

	}

	//$coords = implode(', ', $coordinates);
	if($list || $table){
		//Generating Tabs
		$tabs .= "<ul class=\"clearfix\">\n";
		arsort($totals);

		foreach($totals as $id => $total){

			$tabs .= "\t\t" . '<li><a href="#tabs-' .  $tabids[$id] . '">' .$groups[$id] . ' {' . $total . '} </a></li>' . "\n";
		}
		$tabs .= "</ul>\n";
		$tabs .= '<div style="clear:both;"></div>' . "\n";

		//add disclaimer to the footer
		add_action('srp_footer_disclaimers', 'srp_Education_disclaimer');
		$output = '<div id="srp-tab-wrap" class="srp-tabs">' . srp_Education_attribution($school->city, $school->state) . "\n" . $tabs . $$args['output'] . '</div>' . "\n";
		if($ajax){
			$content = $output;
			return serialize(array('markers' => $coordinates));//, 'content' => $content));
		}else{
			return $output;
		}
	}
}

//Seann Birkelund @ sbirkelund@education.com
//allowed to nofollow disclaimer links, but not the attribution links within the content from the Education.com
function srp_Education_disclaimer(){
	$content = '<div class="spr_disclaimer srp_education_disclaimer">&copy; <a href="http://www.education.com/" rel="nofollow">Education.com, Inc.</a> 2008.&nbsp; Use is subject to <a href="http://www.education.com/schoolfinder/tools/webservice/terms/" rel="nofollow">Terms of Service</a></div>';
	echo $content;
}

function srp_Education_attribution($city=false, $state=false){
    if($city && $state){
        $xml = srp_run_apiFunction('gbd', array('city' => $city, 'state' => $state));
        $link = '<a href="' . $xml->lsc . '">See more information on ' . $city . ' schools from Education.com </a>';
    }else{
        $link = '<a href="http://www.education.com/schoolfinder/">See more school information from Education.com</a>';
    }
	$content = '<div id="srp_Education_attr">Data provided by <a href="http://www.education.com/schoolfinder/"><img src="'. SRP_IMG .'/branding/edu-logo-75x31.jpg" width="75" height="31" alt="Education.com Logo"></a><br />
'. $link .'</div>';
	return $content;
}

function srp_getSchools_ajax(){
	$lat = $_POST['lat'];
	$lng = $_POST['lng'];

    $radius = srp_get_radius('schools', false);
	if ($radius == false)
    {
        $radius = 3;
    }
	$address = NULL; //$_POST['address'];

	if($result = srp_schoolSearch_shortcode(array("lat"=>$lat,  "lng"=>$lng, "distance"=>$radius, "groupby"=>"gradelevel", "output"=>"table", "location_title"=>$address), $ajax = true)){
		$result = unserialize($result);
		$result = json_encode($result);
		die($result);
	}
}
add_action('wp_ajax_srp_getSchools_ajax', 'srp_getSchools_ajax');
add_action('wp_ajax_nopriv_srp_getSchools_ajax', 'srp_getSchools_ajax');

function _schools_checkbox(){
	return '<input id="schools_select" type="checkbox"><label for="schools_select">Schools</label><br />' . "\n";
}
add_filter('_add_to_yelpselect', '_schools_checkbox');