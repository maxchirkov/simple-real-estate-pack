<?php
namespace srp\yelp;

require_once dirname(__FILE__) . '/lib/OAuth.php';
require_once SRP_SET . '/yelp.php';

use srp\yelp\lib\OAuthConsumer;
use srp\yelp\lib\OAuthRequest;
use srp\yelp\lib\OAuthSignatureMethod_HMAC_SHA1;
use srp\yelp\lib\OAuthToken;


class YelpApi
{
    static $CONSUMER_KEY    = '';
    static $CONSUMER_SECRET = '';
    static $TOKEN           = '';
    static $TOKEN_SECRET    = '';

    static $API_HOST            = 'api.yelp.com';
    static $DEFAULT_TERM        = 'dinner';
    static $DEFAULT_LOCATION    = 'San Francisco, CA';
    static $SEARCH_LIMIT        = 20;
    static $SEARCH_PATH         = '/v2/search/';
    static $BUSINESS_PATH       = '/v2/business/';


    public function __construct()
    {
        $options = new \srp_YelpSettings();

        self::$CONSUMER_KEY     = $options->consumerKey;
        self::$CONSUMER_SECRET  = $options->consumerSecret;
        self::$TOKEN            = $options->token;
        self::$TOKEN_SECRET     = $options->tokenSecret;
    }


    /**
     * Makes a $this->request to the Yelp API and returns the response
     *
     * @param    $host    The domain host of the API
     * @param    $path    The path of the APi after the domain
     * @return   The JSON response from the $this->request
     */
    function request($host, $path) {
        $unsigned_url = "https://" . $host . $path;

        // Token object built using the OAuth library
        $token = new OAuthToken(self::$TOKEN, self::$TOKEN_SECRET);

        // Consumer object built using the OAuth library
        $consumer = new OAuthConsumer(self::$CONSUMER_KEY, self::$CONSUMER_SECRET);

        // Yelp uses HMAC SHA1 encoding
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

        $oauthrequest = OAuthRequest::from_consumer_and_token(
            $consumer,
            $token,
            'GET',
            $unsigned_url
        );

        // Sign the $this->request
        $oauthrequest->sign_request($signature_method, $consumer, $token);

        // Get the signed URL
        $signed_url = $oauthrequest->to_url();

        // Send Yelp API Call
        try {
            $ch = curl_init($signed_url);
            if (FALSE === $ch)
                throw new \Exception('Failed to initialize');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $data = curl_exec($ch);

            if (FALSE === $data)
                throw new \Exception(curl_error($ch), curl_errno($ch));
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 != $http_status)
                throw new \Exception($data, $http_status);

            curl_close($ch);
        } catch(\Exception $e) {
            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);
        }

        return $data;
    }

    /**
     * Query the Search API by a search term and location
     *
     * @param    $term        The search term passed to the API
     * @param    $location    The search location passed to the API
     * @return   The JSON response from the $this->request
     */
//    function search($term, $location) {
//        $url_params = array();
//
//        $url_params['term'] = $term ?: self::$DEFAULT_TERM;
//        $url_params['location'] = $location?: self::$DEFAULT_LOCATION;
//        $url_params['limit'] = self::$SEARCH_LIMIT;
//        $search_path = self::$SEARCH_PATH . "?" . http_build_query($url_params);
//
//        return $this->request(self::$API_HOST, $search_path);
//    }


    function search($term, $location, $params) {
        $url_params = array();

//        $url_params['term'] = $term ?: self::$DEFAULT_TERM;
        $url_params['location'] = $location ?: self::$DEFAULT_LOCATION;
        $url_params['limit'] = self::$SEARCH_LIMIT;

        $url_params = array_merge($url_params, $params);

        $search_path = self::$SEARCH_PATH . "?" . http_build_query($url_params);

        return $this->request(self::$API_HOST, $search_path);
    }

    /**
     * Query the Business API by business_id
     *
     * @param    $business_id    The ID of the business to query
     * @return   The JSON response from the $this->request
     */
    function get_business($business_id) {
        $business_path = self::$BUSINESS_PATH . urlencode($business_id);

        return $this->request(self::$API_HOST, $business_path);
    }

    /**
     * Queries the API by the input values from the user
     *
     * @param    $term        The search term to query
     * @param    $location    The location of the business to query
     */
    function query_api($term, $location) {
        $response = json_decode(search($term, $location));
        $business_id = $response->businesses[0]->id;

        print sprintf(
            "%d businesses found, querying business info for the top result \"%s\"\n\n",
            count($response->businesses),
            $business_id
        );

        $response = $this->get_business($business_id);

        print sprintf("Result for business \"%s\" found:\n", $business_id);
        print "$response\n";
    }
}