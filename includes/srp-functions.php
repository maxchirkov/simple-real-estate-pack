<?php
//TODO: for some reason Currency JS was removed from previous version. See if it doesn't need to be there.

include 'srp-widgets.php';
include 'srp-education.php';
include 'srp-yelp.php';
include 'srp-tinymce-widgets.php';
include 'srp-profile.php';
include 'srp-shortcodes.php';

define('SRP_DEBUG', true);
define('SRP_DEBUG_DATA', false);

function srp_debug($message = '', $data = null){

    if(SRP_DEBUG){
        if($message){
            echo "<div class='error'>{$message}</div>";
        }
        if(SRP_DEBUG_DATA && $data){
            pa($data);
        }
    }
}
//Heleper for debugging :)
function pa($x){
        print '<textarea cols="80" rows="10">';
	//print '<pre>';
	print_r($x);
	//print '</pre>';
        print '</textarea>';
}

/*
if( !class_exists( 'WP_Http' ) ){
    //this class was introduced in 2.7.0
    // in v 3.0 file was renamed.
    if ( version_compare($wp_version, 3.0, '>=') ){
        include_once( ABSPATH . WPINC. '/class-http.php' );
    }else{
        include_once( ABSPATH . WPINC. '/http.php' );
    }
}*/

function srp_get_states($key = NULL){
	$states = array (
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'DC' => 'District of Columbia',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming',
	);

        //Currently Canada is not supported by Education.com
        $provinces = array(
                'ON' => 'Ontario',
                'QC' => 'Quebec',
                'BC' => 'British Columbia',
                'AB' => 'Alberta',
                'MB' => 'Manitoba',
                'SK' => 'Saskatchewan',
                'NS' => 'Nova Scotia',
                'NB' => 'New Brunswick',
                'NL' => 'Newfoundland and Labrador',
                'PE' => 'Prince Edward Island',
                'NT' => 'Northwest Territories',
                'YT' => 'Yukon',
                'NU' => 'Nunavut',
        );

        $opt = get_option('srp_general_options');
        if( isset($opt['content']['srp_canadian']) ){
            $states = $provinces;
        }

	if($key){
		return $states[$key];
	}else{
		return $states;
	}
}


/*
 * Turns numerical string into a phone number format
 */
function srp_format_phone($phone){
	$phone = preg_replace("/[^0-9]/", "", $phone);
	if(strlen($phone) == 7)
		return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
	elseif(strlen($phone) == 10)
		return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
	elseif(strlen($phone) == 11)
		return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1 ($2) $3-$4", $phone);
	else
		return $phone;
}

/**
* @param
*
*/
function srp_map($lat, $lng, $html=null, $width = NULL, $height = NULL) {
    global $srp_scripts;
    wp_enqueue_script( 'google-maps-api-v3' );

	   if($width){
           //if metrics (% or px) is not indicated - fallback to px by default.
           //this is not explainded in the settings, but rather instructed to provide numeric values, so everything defaults to px.
           $width = ( !strstr($width, '%') && !stristr($width, 'px') ) ? "width:{$width}px;" : "width:{$width};";
       }
	   if($height){
           $height = ( !strstr($height, '%') && !stristr($height, 'px') ) ? "height:{$height}px;" : "height:{$height};";
       }
        $srp_gmap_options = get_option('srp_gmap');
	$output = '<div id="map">
	  <div id="map_area" style="' . $width . $height . '">
   		<div id="gre_map_canvas" style="' . $width . $height . '"></div>';

		if (get_option('srp_yelp_api_key') && $srp_gmap_options['yelp']){
			$output .= srp_yelp_select();
		}

	$output .= '<input id="srp_gre_prop_coord" type="hidden" value="' . $lat .',' . $lng . '" />
	   </div>
	   <div class="srp_gre_legend">';
        if( isset($srp_gmap_options['mainmarker']) ){
            $output .= '<span><img src="//www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png" /> - ' . $srp_gmap_options['mainmarker_label'] . '</span>';
        }
        $output .= '</div>
	</div>';

    //make sure our JavaScripts get loaded on the current page
    $srp_scripts = true;

	return $output;
}

/*
** CSS and JS initialization
*/
function srp_ajax_vars(){
  $vars = array(
      'srp_url'       => SRP_URL,
      'srp_inc'       => SRP_URL .'/includes',
      'srp_wp_admin'  => ADMIN_URL,
      'ajaxurl'       => admin_url('admin-ajax.php')
  );
  return $vars;
}
function srp_admin_scripts(){
    if( !isset($_GET['page']) )
        return;

    if (  strstr($_GET['page'], 'simple-real-estate-pack') || strstr($_GET['page'], 'srp_') ){
        wp_enqueue_script('postbox');
        wp_enqueue_script('dashboard');
        wp_enqueue_style('dashboard');
        wp_enqueue_style('global');
        wp_enqueue_style('wp-admin');
        wp_enqueue_style('blogicons-admin-css', SRP_URL . '/settings/settings.css');
    }

    wp_enqueue_script('jquery');
    $googlepath = "//maps.google.com/maps/api/js?sensor=true";
    wp_enqueue_script( 'google-maps-api-v3', $googlepath, FALSE, false, false );
    $srp_gre_admin = SRP_URL.'/js/srp-gre-admin.js';
    wp_enqueue_script('srp-gre-admin', $srp_gre_admin, false, false, false);
}

function srp_default_headScripts(){

	wp_enqueue_script('jquery');
  add_thickbox();
  $googlepath = "//maps.google.com/maps/api/js?sensor=true";
	wp_register_script( 'google-maps-api-v3', $googlepath, FALSE, false, false );
    if(function_exists('greatrealestate_init')){
        remove_action( 'wp_enqueue_scripts', 'greatrealestate_add_javascript' );
    }

    wp_register_script('srp-jsmin', SRP_URL . '/js/jsmin.js', array('jquery'), '1.0', true);
    wp_register_script('srp', SRP_URL . '/js/srp.min.js', array('jquery'), '1.0', true);
    wp_register_script('srp-calcs', SRP_URL . '/js/srp-MortgageCalc.min.js', array('jquery', 'srp', 'srp-currency'), '1.0', true);
    wp_register_script('srp-currency', SRP_URL . '/js/jquery.formatCurrency-1.0.0.min.js', array('jquery'), '1.0', true);
    //Pass JS vars so they can be used in a global scope
    wp_localize_script( 'srp', 'srp', srp_ajax_vars() );
}

add_action('wp_print_styles', 'srp_register_header_styles');
function srp_register_header_styles(){
  global $srp_scripts, $wp_query;
  $myStyleUrl		= SRP_URL . '/css/srp.css';
  $myStyleFile	= SRP_DIR . '/css/srp.css';
  if ( file_exists($myStyleFile) ) {
      wp_register_style('srp', $myStyleUrl, array(), null, 'screen');
      wp_enqueue_style('srp');
  }

  $uitabsStyle	= SRP_URL . '/css/ui.tabs.css';
	$uitabsFile		= SRP_DIR . '/css/ui.tabs.css';
	$srp_general_options = get_option('srp_general_options');
	$srp_ext_gre_options = get_option('srp_ext_gre_options');
	if($srp_general_options['content']['srp_gre_css'] || $srp_ext_gre_options['content']['srp_gre_css']  && file_exists($uitabsFile)){
            wp_register_style('srp_uitabs', $uitabsStyle, array(), null, 'screen');
            wp_enqueue_style('srp_uitabs');
	}

}

function srp_head_scripts(){
		echo "\n" . '<script type="text/javascript">
/*<![CDATA[ */' ."\n"
. "\t" . 'tb_pathToImage = "' . get_option('siteurl') . '/wp-includes/js/thickbox/loadingAnimation.gif";'."\n"
. "\t" . 'tb_closeImage = "' . get_option('siteurl') . '/wp-includes/js/thickbox/tb-close.png";'. "\n"
.'/* ]]> */
' . "\n" . '</script>' . "\n";

}

function srp_footer_scripts(){
	global $srp_scripts;

    if( !$srp_scripts )
        return;

    $srp_general_options = get_option('srp_general_options');
    if($srp_general_options['content']['srp_profile_tabs']){
      wp_print_scripts('jquery-ui');
      wp_print_scripts('jquery-ui-tabs');
    }
    wp_print_scripts('srp-jsmin');
    wp_print_scripts('srp');
    wp_print_scripts('srp-calcs');
    wp_print_scripts('srp-currency');
}

add_action('admin_print_scripts', 'srp_admin_scripts');
add_action('init', 'srp_default_headScripts', 1);
add_action('wp_head', 'srp_head_scripts');
add_action('wp_footer', 'srp_footer_scripts');

/*-------------------------------------------------------------------------*
** Helper function that reports back via AJAX
** if a certain function or API exists, in order to execute specifc JS.
**	@param string $name - function or option name
**  @param string $type - 'function' or 'option'
**-------------------------------------------------------------------------*/
function srp_function_exists(){
	$name = $_POST['name'];
	$type = $_POST['type'];

	switch($type){
		case 'function':
			if(function_exists($name)){ die(true); }
			die('0');

		case 'option':
			if($option = get_option($name)){ die(true); }
			die('0');
	}
}

add_action('wp_ajax_srp_function_exists', 'srp_function_exists');
add_action('wp_ajax_nopriv_srp_function_exists', 'srp_function_exists');

function srp_walkscore($ws_wsid, $ws_address, $ws_width=500, $ws_height=286, $ws_layout = 'horizontal') {
	$output .= "
	<script type='text/javascript'>
	var ws_wsid = '{$ws_wsid}';
	var ws_address = '{$ws_address}';var ws_width = '{$ws_width}';var ws_height = '{$ws_height}';var ws_layout = '{$ws_layout}';</script><style type='text/css'>#ws-walkscore-tile{position:relative;text-align:left}#ws-walkscore-tile *{float:none;}#ws-footer a,#ws-footer a:link{font:11px Verdana,Arial,Helvetica,sans-serif;margin-right:6px;white-space:nowrap;padding:0;color:#000;font-weight:bold;text-decoration:none}#ws-footer a:hover{color:#777;text-decoration:none}#ws-footer a:active{color:#b14900}</style><div id='ws-walkscore-tile'><div id='ws-footer' style='position:absolute;top:268px;left:8px;width:488px'><form id='ws-form'><a id='ws-a' href='http://www.walkscore.com/' target='_blank'>Find out your home's Walk Score:</a><input type='text' id='ws-street' style='position:absolute;top:0px;left:225px;width:231px' /><input type='image' id='ws-go' src='http://www2.walkscore.com/images/tile/go-button.gif' height='15' width='22' border='0' alt='get my Walk Score' style='position:absolute;top:0px;right:0px' /></form></div></div><script type='text/javascript' src='http://www.walkscore.com/tile/show-walkscore-tile.php'></script>";
	return $output;
}

function srp_extend_gre_ajax(){
    global $srp_property_values, $srp_widgets;

    $srp_property_values = json_decode(stripslashes($_POST['srp_listing_values']), true);
	_gre_extension_content();
	die($srp_widgets->print_all());
}
add_action('wp_ajax_srp_extend_gre_ajax', 'srp_extend_gre_ajax');
add_action('wp_ajax_nopriv_srp_extend_gre_ajax', 'srp_extend_gre_ajax');

function srp_ajax_call(){
    global $srp_property_values, $srp_widgets;

    $_tmp = _srp_smartstripslashes($_REQUEST['srp_listing_values']);
    $srp_property_values = json_decode($_tmp, true);

    if(is_object($srp_property_values)){ //For PHP below 5.2
        foreach($srp_property_values as $k=>$v){
            $tmp[$k] = $v;
        }
        $srp_property_values = $tmp;
    }

    $init = call_user_func($_REQUEST['callback'], array());

        if(!is_array($init))
            return;

        foreach($init as $atts){
            $srp_widgets->add($atts);
            extract($atts); //expecting $name to be set
            break;
        }

  if( isset($name) )
    die($srp_widgets->print_widget($name));

  die();
}
add_action('wp_ajax_srp_ajax_call', 'srp_ajax_call');
add_action('wp_ajax_nopriv_srp_ajax_call', 'srp_ajax_call');

function srp_buffer($callback_function){
    ob_start();
    if($result = call_user_func($callback_function)){
        return $result;
    }
    $result = ob_get_contents();
    ob_end_clean();
    return $result;
}

function _srp_smartstripslashes($str) {
  $cd1 = substr_count($str, "\"");
  $cd2 = substr_count($str, "\\\"");
  $cs1 = substr_count($str, "'");
  $cs2 = substr_count($str, "\\'");
  $tmp = strtr($str, array("\\\"" => "", "\\'" => ""));
  $cb1 = substr_count($tmp, "\\");
  $cb2 = substr_count($tmp, "\\\\");
  if ($cd1 == $cd2 && $cs1 == $cs2 && $cb1 == 2 * $cb2) {
    return strtr($str, array("\\\"" => "\"", "\\'" => "'", "\\\\" => "\\"));
  }
  return $str;
}


//Disclaimers and terms of use placeholder
function srp_footer_disclaimer(){
    echo '<div id="srp-disclaimers">';
    do_action('srp_footer_disclaimer');
    echo '</div>';
}
add_action('wp_footer', 'srp_footer_disclaimer', 2);

function srp_wp_http_xml($url){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if(!$code){
        preg_match('@^(?:http://)?([^/]+)@i', $url, $matches);
        $host = $matches[1];
        preg_match('/[^.]+\.[^.]+$/', $host, $matches);
        srp_debug(__('Something went wrong. No data is being returned from ' . $matches[0] . '.'), $result);
        return;
    }elseif($code != 200){
        $message = 'Request to URL: "' . $url . '" failed. Response code: ' . $code;
        srp_debug(__($message), $result);
        return;
    }
    $xml = @simplexml_load_string($result);

    return $xml;
}

//checks if image exists or not
function srp_is_ImgExists($url){
    $curlOpt = array(
		CURLOPT_RETURNTRANSFER => true, // Return web page
		CURLOPT_HEADER	 => true, // Return headers
		CURLOPT_FOLLOWLOCATION => false, // Follow redirects
		CURLOPT_ENCODING => '', // Handle all encodings
		CURLOPT_USERAGENT => 'image dimension grabber', // Useragent
		CURLOPT_AUTOREFERER => true, // Set referer on redirect
		CURLOPT_FAILONERROR	 => true, // Fail silently on HTTP error
		CURLOPT_CONNECTTIMEOUT => 2, // Timeout on connect
		CURLOPT_TIMEOUT => 2, // Timeout on response
		CURLOPT_MAXREDIRS => 3, // Stop after x redirects
		CURLOPT_SSL_VERIFYHOST => 0, // Don't verify ssl
                CURLOPT_RANGE => '0-1'
	);
    $ch = curl_init($url);
    curl_setopt_array($ch, $curlOpt);
    curl_exec($ch);
    $type_img = false;
    if(!curl_errno($ch)){
        $info = curl_getinfo($ch);
        if(strstr($info['content_type'], 'image')){
                $type_img = true;
        }
    }
    // Close handle
    curl_close($ch);
    return $type_img;
}

//Include pre-set template for GRE into the default template
//TODO: Add conditional check to see if there is a template selected, otherwise use this by default.
if(function_exists('greatrealestate_init')){
    if ( 'true' == get_option('greatrealestate_genindex') ) {
            // add an action to output the listings summary right
            // after the content is displayed
            remove_action('loop_end', 'greatrealestate_add_listindex');
            add_action('loop_end','srp_gre_add_listindex');
            function srp_gre_add_listindex() {
                    global $post, $listing;
                    if (get_option('greatrealestate_pageforlistings') == $post->ID) {
                        $listing->endloop = true;
                        include (SRP_TPL . '/listings.php');
                    }

            }
    }

    //Listing content page
    if ( 'true' == get_option('greatrealestate_genlistpage') ) {

            remove_action('loop_end', 'greatrealestate_add_listdetails');
            remove_filter('the_content','greatrealestate_add_listcontent');
            add_filter('the_content', 'srp_gre_add_listcontent');
    }
}

function srp_gre_add_listdetails() {
        global $post, $listing, $temp_buffer;
        $page_template = get_post_meta($post->ID,'_wp_page_template',true);
        if ((get_option('greatrealestate_pageforlistings') == $post->post_parent )
            && !isset($listing->endloop) && $page_template == 'default') {
               include(SRP_TPL . '/listing_page.php');
        }
}

function srp_gre_add_listcontent($content){
    global $post;
    global $listing;

    // only filter if this is a single page
    if (! is_listing()) return $content;

    // if do not filter flag set, just pass it on
    // IMPORTANT otherwise it loops foreeeeeeevvvvvveeeeeerrrrrrr
    if ( ! (strpos($content, "grenofilters") === FALSE) )
            return $content;

    // this is the "top of page" case
    // combine the box of data and the filtered "beforemore"
    // as well as the remaining listing details
    $content = get_listing_description_beforemore() . srp_buffer('srp_gre_add_listdetails');

    return $content;
}

function srp_gre_listing_contact(){
    if($phone = get_option('greatrealestate_agentphone')){
        if($agent = get_option('greatrealestate_agent')){
            $output = "Call <span class='srp_agent_name'>{$agent}</span> <span class='srp_agent_phone'>{$phone}</span>";
        }else{
            $output = "Call <span class='srp_agent_phone'>{$phone}</span>";
        }
        return $output;
    }
    return;
}

//template function - outputs slideshow or image from NGG.
function srp_gre_slideshow_image($gallery_id, $width = 356, $height = 267){
    //check if ngg gallery exists
    if(class_exists('nggGallery')){
        //if slideshow exists - output it
        $ngg_options = nggGallery::get_option('ngg_options');
        if( $ngg_options['enableIR'] == '1' && $ngg_options['irURL']){
            return nggShowSlideshow($gallery_id, $width, $height);
        }else{
        //else - check if there are images and output the main one
            $ngg_options = nggGallery::get_option('ngg_options');
            //Set sort order value, if not used (upgrade issue)
            $ngg_options['galSort'] = ($ngg_options['galSort']) ? $ngg_options['galSort'] : 'pid';
            $ngg_options['galSortDir'] = ($ngg_options['galSortDir'] == 'DESC') ? 'DESC' : 'ASC';

            if($imgs = nggdb::get_gallery($gallery_id, $ngg_options['galSort'], $ngg_options['galSortDir'])){
                foreach($imgs as $obj){
                    return '<img src="' . $obj->imageURL . '" width="' . $width . '" height="'. $height .'" alt="'. $obj->alttext .'"/>';
                    break;
                }
            }
        }
    }
    //otherwise output dummy image "photo coming soon"
    return '<img src="' . SRP_IMG . '/photo-n-a.png" width="' . $width . '" height="'. $height .'" />';
}

function srp_inquiry_form(){
    $srp_ext_gre_options = get_option('srp_ext_gre_options');

    $form_title = false;
    if( isset($srp_ext_gre_options['form-title']) ){
      $form_title = '<a name="more_info"></a><h2><span>' . strip_tags($srp_ext_gre_options['form-title']) . '</span></h2>';
    }

    if($srp_ext_gre_options['form-shortcode']){
        if(strpos($srp_ext_gre_options['form-shortcode'],'<!--cforms')!==false && function_exists('insert_cform')){
          echo $form_title;
          echo cforms_insert($srp_ext_gre_options['form-shortcode']);
        }else{
          echo $form_title;
          echo do_shortcode($srp_ext_gre_options['form-shortcode']);
        }
    }else{
      echo $form_title;
			if($_POST['sendinquiry'] && (!$_POST['email'] && !$_POST['phone'])){
				echo '<p class="error">Please provide your email address or phone number so we could reply back to you.</p>';
			}elseif($_POST['sendinquiry'] && ($_POST['email'] || $_POST['phone'])){
				$admin_email = get_option('admin_email');
				$headers = 'From: Property Inquiry <' . $admin_email . '>' . "\r\n";
				$message = 'First Name: ' . $_POST['first_name']  . "\r\n";
				$message .= 'Last Name: ' . $_POST['last_name']  . "\r\n";
				$message .= 'Email: ' . $_POST['email']  . "\r\n";
				$message .= 'Phone: ' . $_POST['phone']  . "\r\n";
				$message .= 'Message: ' . $_POST['message']  . "\r\n";
				$message .= 'Referring URL: ' . $_SERVER["REQUEST_URI"]  . "\r\n";

        if(wp_mail($admin_email, $property_address, $message, $headers))
          echo '<p class="srp_success">Thank you for your inquiry! We will get back to you shortly.</p>';
			}
		?>
		<form action="<?php echo $PHP_SELF; ?>#more_info" method="post" class="moreinforequest">
		<fieldset>
		<legend>Interested in this listing?</legend>
			<div><label>First Name</label><input type="text" name="firs_tname" class="srp_firstname" value=""/></div>
			<div><label>Last Name</label><input type="text" name="last_name" class="srp_lastname" value=""/></div>
			<div><label>Email</label><input type="text" name="email" class="srp_email" value=""/></div>
			<div><label>Phone</label><input type="text" name="phone" class="srp_phone" value=""/></div>
			<div><label>Message</label><textarea cols="30" rows="8" name="message" class="srp_message"></textarea></div>
		</fieldset>
		<p><input type="submit" name="sendinquiry" id="sendinquiry" class="sendbutton" value="Send Inquiry"/></p>
		</form>
  <?php
  }
}

//Loads TinyMCE window for a widget via AJAX
function srp_ajax_tinymce(){
    // check for rights
    if ( !current_user_can('edit_pages') && !current_user_can('edit_posts') )
        die(__("You are not allowed to be here"));

    if( !$_REQUEST['plugin'] )
        die();

    include_once( SRP_TMCE . '/' . $_REQUEST['plugin'] . '.php');

    die();
}
add_action( 'wp_ajax_srp_tinymce', 'srp_ajax_tinymce' );