<?php
$yelp_categories = array(
    'grocery'      => array(
        'name'     => 'Grocery Stores',
        'category' => 'grocery',
        'term'     => 'grocery',
    ),
    'restaurants'  => array(
        'name'     => 'Restaurants',
        'category' => 'restaurants',
        'term'     => 'restaurants',
    ),
    'banks'        => array(
        'name'     => 'Banks',
        'category' => 'banks',
        'term'     => 'banks',
    ),
    'gas_stations' => array(
        'name'     => 'Gas Stations',
        'category' => 'servicestations',
        'term'     => 'gas_stations',
    ),
    'golf'         => array(
        'name'     => 'Golf Courses',
        'category' => 'golf',
        'term'     => 'golf',
    ),
    'hospitals'    => array(
        'name'     => 'Hospitals',
        'category' => 'hospitals',
        'term'     => 'hospitals',
    ),
    'education'    => array(
        'name'     => 'Schools',
        'category' => 'elementaryschools,highschools,preschools',
        'term'     => 'education',
    )
);

function srp_getYelp(array $parameters)
{
    global $yelp_categories, $srp_scripts;

    $location = null;
    $lat = null;
    $lng = null;
    $radius = 5;
    $output = 'table';
    $sortby = 'avg_rating';
    $term = null;
    $num_biz_requested = null;
    $ajax = null;

    extract($parameters);

    if ($term && isset($yelp_categories[$term]))
    {
        $_categories = array($term => $yelp_categories[$term]);
    }
    elseif ($term && $terms = explode(',', $term))
    {
        foreach ($terms as $t)
        {
            $_categories[$t] = $yelp_categories[$t];
        }
    }
    else
    {
        $_categories = $yelp_categories;
    }

    $tabs           = false;
    $content_output = false;
    $ajax_output    = null;

    foreach ($_categories as $cat)
    {
        $args = array(
            'term'            => $cat['term'],
            'category_filter' => $cat['category'],
            'limit'           => (intval($num_biz_requested) <= 20) ? $num_biz_requested : 20,
            'cll'             => "{$lat},{$lng}",
            'radius_filter'   => $radius / 0.621371 * 1000, // converting to meters
            'sort'            => ($sortby == 'distance') ? 1 : 2,
        );

        $wrap_open = $wrap_close = '';

        if (count($_categories) > 1)
        {
            $wrap_open  = '<div id="tabs-' . $cat['term'] . '">'
                . '<h3>' . __($cat['name']) . '</h3>';
            $wrap_close = '</div>';
        }

        $args = array_filter($args);

        require_once(SRP_LIB . '/yelp/YelpApi.php');
        $yelpApi = new \srp\yelp\YelpApi();

        $request_result = $yelpApi->search($cat['term'], $location, $args);
//print '<pre>';
//        echo $request_result;
//print '</pre>';die();
        $result   = json_decode($request_result, true);
        $phparray = $result;

        if (count($phparray['businesses']) < 1)
        {
            $message = '<p class="no-businesses-found">There are no ' . $cat['name'] . ' within ' . $radius . ' miles radius from this property.</p>';
            if ($ajax)
            {
                return json_encode(array('message' => $message));
            }
        }

        if (count($_categories) > 1)
        {
            $tabs .= '<li><a href="#tabs-' . $cat['term'] . '"  title="' . __($cat['name'], "simplerealestatepack") . '" ><span>'
                . __($cat['name'], "simplerealestatepack")
                . '</span></a></li>' . "\n";
        }
        $x           = 0;
        $coordinates = array();
        $table       = null;

        if (!empty($phparray['businesses']))
        {
            foreach ($phparray['businesses'] as $item)
            {
                $biz = $item;
                $x++;
                if ($x % 2)
                {
                    $even_odd = "even";
                }
                else
                {
                    $even_odd = "odd";
                }

                if (isset($biz['location']['coordinate']))
                {
                    $coordinates[$cat['term']][$biz['id']]['lat'] = $biz['location']['coordinate']['latitude'];
                    $coordinates[$cat['term']][$biz['id']]['lng'] = $biz['location']['coordinate']['longitude'];
                }

                $phone = (isset($biz['display_phone'])) ? '<br />Phone: ' . srp_format_phone($biz['display_phone']) : '';

                $image100 = $image40 = '';
                if (isset($biz['image_url']))
                {
                    $image100 = '<img src="' . $biz['image_url'] . '" width="100" height="100" class="yelp_photo" style="float:right" />';
                    $image40  = '<img src="' . $biz['image_url'] . '" class="yelp_photo" width="40" height="40" align="left"/>';
                }
                else
                {
                    $image100 = '<svg version="1.1" id="Layer_1" class="yelp_photo" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="100px" height="100px" viewBox="0 0 612 792" enable-background="new 0 0 612 792" xml:space="preserve">
<path fill="#ABABAB" d="M43.475,382.668c-11.798,18.911-16.779,78.433-12.658,117.998c1.411,13.012,3.767,23.904,7.202,30.372
	c4.723,8.974,12.664,14.377,21.705,14.632c5.788,0.326,9.428-0.654,118.44-35.736c0,0,48.493-15.464,48.696-15.562
	c12.054-3.086,20.218-14.174,20.975-28.378c0.807-14.569-6.747-27.406-19.08-32.044c0,0-34.166-13.884-34.243-13.884
	C77.315,371.672,72.056,369.778,66.168,369.731C57.098,369.337,49.06,373.922,43.475,382.668"/>
<path fill="#ABABAB" d="M305.652,742.824c1.895-5.509,2.121-9.246,2.423-123.875c0,0,0.258-50.588,0.332-51.119
	c0.733-12.353-7.252-23.706-20.444-28.73c-13.518-5.207-28.076-2.024-36.264,8.187c0,0-23.882,28.355-23.958,28.355
	c-82.176,96.427-85.564,100.9-87.579,106.609c-1.191,3.365-1.698,6.897-1.291,10.514c0.505,5.078,2.801,10.185,6.642,14.835
	c19.035,22.69,110.358,56.273,139.519,51.295C295.162,757.028,302.47,751.314,305.652,742.824"/>
<path fill="#ABABAB" d="M490.805,702.949c27.516-10.995,87.579-87.383,91.803-116.771c1.467-10.235-1.743-19.027-8.768-24.688
	c-4.626-3.441-8.187-4.852-117.128-40.588c0,0-47.857-15.768-48.438-16.122c-11.555-4.446-24.77-0.302-33.662,10.666
	c-9.199,11.244-10.61,26.029-3.259,37.297l19.276,31.286c64.666,105.072,69.594,112.499,74.296,116.166
	C472.101,705.827,481.274,706.763,490.805,702.949"/>
<path fill="#ABABAB" d="M434.526,432.438c123.872-29.958,128.622-31.562,133.55-34.777c7.454-5.062,11.244-13.506,10.563-23.827
	c0-0.342,0.076-0.646,0-1.034c-3.183-30.389-56.452-109.507-82.71-122.346c-9.299-4.445-18.622-4.144-26.281,0.989
	c-4.822,3.184-8.311,7.858-74.619,98.552c0,0-29.995,40.809-30.373,41.198c-7.885,9.578-7.985,23.364-0.255,35.137
	c7.965,12.268,21.482,18.249,33.864,14.785c0,0-0.505,0.849-0.657,1.024C403.725,439.865,414.667,437.146,434.526,432.438"/>
<path fill="#ABABAB" d="M310.152,330.183L310.152,330.183c-2.15-49.091-16.885-267.671-18.627-277.791
	c-2.529-9.185-9.631-15.732-19.962-18.298C239.925,26.237,119.112,60.137,96.697,83.22c-7.178,7.502-9.833,16.713-7.683,24.914
	c3.538,7.241,55.035,88.179,92.742,147.312l60.491,95.478c22.159,35.899,40.23,30.325,46.12,28.495
	C294.227,377.589,312.197,372.038,310.152,330.183"/>
</svg>';
                    $image40 = str_replace('width="100px" height="100px"', 'width="40px" height="40px"', $image100);
                }

                $distance = (isset($biz['distance'])) ? round((floatval($biz['distance'])/1000*0.621371), 2) . ' miles' : ' - ';

                /*
                 * Every single element needs to have inline styls with their corresponding widths
                 * so JS can calculate the total width and height of the InfoWindow correctly
                 * otherwise it calculates dimensions of the content being stacked as is.
                 */
                $coordinates[$cat['term']][$biz['id']]['html'] = '
				<div class="srp_infoWindow clearfix" style="width: 315px; font-size: 12px;line-height: normal;">
				<img src="' . $biz['rating_img_url'] . '" width="84" height="17" class="yelp_rating" style="float:left" /><a href="' . $biz['url'] . '" target="_blank" title="Read Reviews">' . $biz['review_count'] . ' Reviews</a>
				' . $image100 . '
				<div class="yelp_text" style="width: 200px">
				<span class="school_name">
				<a href="' . $biz['url'] . '" target="_blank">' . $biz['name'] . '</a>
				</span>' . $phone
                    . '</div>
				</div>
				<div id="yelp_attribution" style="float:none; width: 315px; text-align: right;">
				<a href="http://www.yelp.com">
				<img src="' . SRP_IMG . '/branding/reviewsFromYelpWHT.gif" width="115" height="25" alt="Reviews from Yelp.com" />
				</a>
				</div>';

                $table .= '<tr class="' . $even_odd . '">
					<td style="vertical-align: middle;">' . $image40 . '</td>
                        <td style="vertical-align: middle;"><div class="yelp_text"><span class="school_name"><a href="' . $biz['url'] . '" target="_blank">' . $biz['name'] . '</a></span>' . $phone . '<br />' . implode(', ', $biz['location']['display_address']) . '</div></td>
                    <td style="vertical-align: middle;">
						<div class="yelp_distance">' . $distance . '</div>
					</td>
					<td style="vertical-align: middle;" class="yelp_rating">
                        <img src="' . $biz['rating_img_url_small'] . '" /><br /><a href="' . $biz['url'] . '" target="_blank" title="Read Reviews">' . $biz['review_count'] . ' Reviews</a>
					</td>
				  </tr>';
            }
        }
        else
        {
            $table = $message;
        }


        //$_SESSION['srp_coordinates'] = $coordinates;
        if ($ajax && $coordinates)
        {
            $ajax_output .= json_encode($coordinates);
        }
        elseif ($table)
        {
            $content_output .= $wrap_open;
            $content_output .= '<table class="srp_table tableStyle">' . $table . '</table>';
            $content_output .= $wrap_close;
        }

    }

    if ($ajax_output)
    {
        $srp_scripts = true;

        return $ajax_output;
    }
    elseif (isset($content_output))
    {
        $srp_scripts = true;

        return '<div class="srp-tabs"><ul class="clearfix">' . $tabs . '</ul><div style="clear:both;"></div>' . $content_output . '</div><div id="yelp_attribution"><a href="http://www.yelp.com"><img src="' . SRP_IMG . '/branding/reviewsFromYelpWHT.gif" width="115" height="25" alt="Reviews from Yelp.com"/></a></div>';
    }
    else
    {
        return;
    }
}

function srp_yelp_select()
{
    global $yelp_categories;

    $output = '<div id="yelp_select">';
    $output .= apply_filters('_add_to_yelpselect', '');

    foreach ($yelp_categories as $cat)
    {
        $output .= '<input id="yelp_cat_' . $cat['term'] . '" name="' . $cat['term'] . '" type="checkbox"><label for="' . $cat['term'] . '">' . $cat['name'] . '</label><br />' . "\n";
    }

    $output .= '</div>';

    return $output;
}

function srp_getYelp_ajax()
{
    $args = array(
        'ajax' => true
    );

    if ($result = srp_getYelp(array_merge($args, $_POST)))
    {
        die($result);
    }
}

add_action('wp_ajax_srp_getYelp_ajax', 'srp_getYelp_ajax');
add_action('wp_ajax_nopriv_srp_getYelp_ajax', 'srp_getYelp_ajax');