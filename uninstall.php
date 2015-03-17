<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();


// Delete our Options
delete_option( 'srp_general_options' );
delete_option( 'srp_ext_gre_options' );
delete_option( 'srp_mortgage_calc_options' );
delete_option( 'srp_walkscore_api_key' );
delete_option( 'srp_mortgage_rates' );
delete_option( 'srp_gmap' );
