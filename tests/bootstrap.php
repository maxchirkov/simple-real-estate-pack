<?php
namespace hji\membership\tests;

new TestsBootstrap();

class TestsBootstrap
{
    public static $dir;


	function __construct()
	{
        self::$dir = dirname(__FILE__);

		$_tests_dir = getenv('WP_TESTS_DIR');

		if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

		require_once $_tests_dir . '/includes/functions.php';

		tests_add_filter( 'muplugins_loaded', array($this, '_manually_load_plugin' ));

		require_once $_tests_dir . '/includes/bootstrap.php';
	}


	function _manually_load_plugin() 
	{
//		require_once dirname( __FILE__ ) . '/../srp.php';
	}

}