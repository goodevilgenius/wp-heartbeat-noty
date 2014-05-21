<?php
/*
Plugin Name: Wp Heartbeat Noty (beta)
Plugin URI: https://github.com/goodevilgenius/wp-heartbeat-noty
Description: Based on <strong>WordPress 3.6 heartbeat API</strong>, Wp Heartbeat Noty, display a realtime custom message to your visitor each time a new post is published with a link redirecting to it. Still in beta version.
Version: 0.0.1
Author: Dan Jones
Author URI: http://danielrayjones.com/
License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

require_once( 'core/class-my-plugin.php' );
require_once( 'core/class-wp-heartbeat-noty.php' );

global $my_plugin;

// Create the plugin and store it as a global
$my_plugin = new My_Plugin( 
	__FILE__, 
	array(
		'required'	=>	array( 
			'wordpress'	=>	'3.6' // WordPress 3.6 is required
		)
	) 
);

// Instantiate Heartbeat notifications
add_action( 'init', 'initialize_wp_heartbeat_noty' );
function initialize_wp_heartbeat_noty () {

	global $my_plugin;

	new Wp_Heartbeat_Noty( array(
		'context'	=>	array( 'front' ),	// This plugin is supposed to work only on the front end
		'base_url'	=>	$my_plugin->uri		// Set js and css base url
	));
	
}


// Let's hook into Post publication
add_filter ( 'publish_post', 'noty_published_post' );
function noty_published_post( $post_id ) {
	
	global $my_plugin;
	
	// That's it. Easy... isn'it?
	Wp_Heartbeat_Noty::noty( array(
		'title'		=>		__( 'New Article', $my_plugin->textdomain ),
		'content'	=>	 	__( 'There\'s a new post, why don\'t you give a look at', $my_plugin->textdomain ) . 
							' <a href="' . get_permalink( $post_id ) . '">' . get_the_title( $post_id ) . '</a>',
		'type'		=>		'update'
	) );
	
	return $post_id;
	
}
