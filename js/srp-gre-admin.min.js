//Utilizing GMap API v3 (no API key needed)
function srp_geocode(){
	if(jQuery.trim(jQuery('#listings_address').val()) != '' && jQuery.trim(jQuery('#listings_city').val()) != '' && jQuery.trim(jQuery('#listings_state').val()) != '' && jQuery.trim(jQuery('#listings_postcode').val()) != ''){

			var address = jQuery('#listings_address').val() + ', ' + jQuery('#listings_city').val() + ', ' + jQuery('#listings_state').val() + ' ' + jQuery('#listings_postcode').val();
                        var geocoder;
                        geocoder = new google.maps.Geocoder();
                        if (geocoder) {
                              geocoder.geocode( { 'address': address}, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    var latlng = results[0].geometry.location;

                                    jQuery('#listings_latitude').val(latlng.lat());
                                    jQuery('#listings_longitude').val(latlng.lng());
                                    srp_geocode_test(latlng.lat(), latlng.lng());
                                }else{
                                  alert("Geocode was not successful for the following reason: " + status);
                                }
                              });
                            }

		return false;
		}

}

function srp_geocode_test(lat, lng){
		var test = '<a href="http://maps.google.com/maps?hl=en&q=' + lat + ' ' + lng + '" target="_blank">Preview Location</a>';
		jQuery('#test_geo_link').html(test);
		jQuery("#listings_latitude").triggerHandler("focus");
	}

//Adding Get Coordinates button at the bottom of the listing editor page created by the GRE plugin.
jQuery(document).ready( function() {
	// BEGIN for GRE Plugin

	var geo_button = '<p><input id="srp_get_coord" type="button" name="get_coord" value="Get Lat/Long" /><span id="test_geo_link"></span></p>';

	if(typeof(jQuery('#listings3-div div')) !== 'undefined'){
		jQuery('#listings3-div div').append(geo_button);
	}

	if(jQuery('#listings_latitude').val() != '' && jQuery('#listings_longitude').val() != ''){
		var lat = jQuery('#listings_latitude').val();
		var lng = jQuery('#listings_longitude').val();
		srp_geocode_test(lat, lng);
	}
	jQuery('#srp_get_coord').bind('click', function() {
		srp_geocode();
	});

	// END for GRE Plugin

	//Overriding Thickbox' tb_remove function because it breaks tabs
	window.tb_remove = function() {
		_fixed_tb_remove();
	};

	function _fixed_tb_remove() {
	 	jQuery("#TB_imageOff").unbind("click");
		jQuery("#TB_closeWindowButton").unbind("click");
		jQuery("#TB_window").fadeOut("fast",function(){jQuery('#TB_window,#TB_overlay,#TB_HideSelect').unload("#TB_ajaxContent").unbind().remove();});
		jQuery("#TB_load").remove();
		if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
			jQuery("body","html").css({height: "auto", width: "auto"});
			jQuery("html").css("overflow","");
		}
		jQuery(document).unbind('.thickbox');
		return false;
	}
});