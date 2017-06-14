<?php

/**
 * @title TinyMCE V3 Button Integration (for Wp2.5)
 * @author Alex Rabe
 */

class add_SRP_button {
	
	var $pluginname;
	var $editor_js;
	var $space_before;
	var $space_after;
	
	function __construct()  {
		// Modify the version when tinyMCE plugins are changed.
		add_filter('tiny_mce_version', array (&$this, 'change_tinymce_version') );
		
		// init process for button control
		add_action('init', array (&$this, 'addbuttons') );
	}

	function addbuttons() {
	
		// Don't bother doing this stuff if the current user lacks permissions
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) return;
		
		// Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {
		 
		// add the button for wp2.5 in a new way
			add_filter("mce_external_plugins", array (&$this, "add_tinymce_plugin" ), 5);
			add_filter('mce_buttons', array (&$this, 'register_button' ), 5);
		}
	}
	
	// used to insert button in wordpress 2.5x editor
	function register_button($buttons) {
	
		//array_push($buttons, "", $this->pluginname );
		array_unshift($buttons, $this->space_before, $this->pluginname, $this->space_after);
	
		return $buttons;
	}
	
	// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
	function add_tinymce_plugin($plugin_array) {    

		$plugin_array[$this->pluginname] =  WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ) . '/' . $this->editor_js;

		return $plugin_array;
	}
	
	function change_tinymce_version($version) {
		return ++$version;
	}
	
}

// Call it now 'srp_TruliaStats', 'editor_plugin.js'
/**/
$trulia_tinymce_button = new add_SRP_button();
$trulia_tinymce_button->space_after = "|";
$trulia_tinymce_button->pluginname = 'srp_TruliaStats';
$trulia_tinymce_button->editor_js = 'editor_plugin.js';

$altos_tinymce_button = new add_SRP_button();
$altos_tinymce_button->pluginname = 'srp_AltosStats';
$altos_tinymce_button->editor_js = 'altos_editor_plugin.js';

$gmap_tinymce_button = new add_SRP_button();
$gmap_tinymce_button->pluginname = 'srp_GMap';
$gmap_tinymce_button->editor_js = 'gmap_editor_plugin.js';

$calcs_tinymce_button = new add_SRP_button();
$calcs_tinymce_button->pluginname = 'srp_Calcs';
$calcs_tinymce_button->editor_js = 'calcs_editor_plugin.js';

$apis_tinymce_button = new add_SRP_button();
$apis_tinymce_button->pluginname = 'srp_APIs';
$apis_tinymce_button->editor_js = 'apis_editor_plugin.js';