<?php
/**
 * WordPress Heartbeat Notifications Class
 */
class Wp_Heartbeat_Noty {
	
	var $args = array(
	
		// Enable Wp_Heartbeat_Noty on admin or theme
		'context'			=>	array( 'admin', 'front' ),
		
		// User needed capability
		'capability'		=>	false,
		
		// Include jQuery or use theme one	
		'native_jquery'		=>	true,	
		
		// Heartbeat rate - Only for testing purposes (default: 15)
		'interval'			=>	'auto',
		
		// Domain - So you'll be able to handle multiple instance
		'domain'			=>	'whn',
		
		// Base url to link js and css (must be set)
		'base_url'			=>	''
		
	);
	
	/**
	 * WordPress Heartbeat Notifications Class Constructor
	 *
	 * @param Array $args Class options
	 */
	function __construct( $args = array() ) {
		
		if ( is_array( $args ) )
			$this->args = array_merge( $this->args, $args );
			
		if ( empty( $args['base_url'] ) )
			trigger_error( 'You have to set the url of the js and css folder' , E_USER_NOTICE );
		
		if ( ! $this->has_needed_capability() || !has_filter('heartbeat_settings') )
			return;
		
		if ( 'auto' != $this->args['interval'] && is_int( $this->args['interval'] ) )
			add_filter( 'heartbeat_settings', array( $this, 'change_hearbeat_rate' ) );
		
		if ( in_array( 'admin', $this->args['context'] ) )
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_my_stuff' ) );
			
		if ( in_array( 'front', $this->args['context'] ) ){
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_my_stuff' ) );
			add_filter( 'heartbeat_nopriv_send', array( $this, 'send_data_to_heartbeat' ), 10, 2 );
		}
		
		add_filter( 'heartbeat_send', array( $this, 'send_data_to_heartbeat' ), 10, 2 );
		
	}
	
	/**
	 * Enqueue all the needed stuff
	 *
	 * Bum, bum... it's aliveeeeee!!!
	 */
	function enqueue_my_stuff() {
		$dependency = ( $this->args['native_jquery'] ) ? 'jquery' : null;
		wp_enqueue_script( 'noty_js', $this->args['base_url'] . '/js/noty/packaged/jquery.noty.packaged.min.js', $dependency, null, false );
		wp_enqueue_script( 'wp_hearbeat_noty_js', $this->args['base_url'] . '/js/wp-heartbeat-noty.min.js', array('heartbeat','noty_js'), null, false );
	}
	
	/**
	 * Check if the user has needed capability
	 *
	 * @return Bool
	 */
	private function has_needed_capability() {
		
		if( !$this->args['capability'] || current_user_can( $this->args['capability'] ) )
			return true;
		
		return false;
		
	}
	
	/**
	 * Add data to the heartbeat ajax call (filter)
	 *
	 * @arg Array $data Original data to be sent to Hearbeat
	 * @arg String $screen_id Admin Screen ID
	 *
	 * @return Array Modified array of data to be sent to Hearbeat
	 */
	function send_data_to_heartbeat( $data, $screen_id ) {
		
		$notifications = get_transient($args['domain'] . "_heartbeat_noty");
		if (empty($notifications)) $notifications = array();

		foreach($notifications as $notification) {
				if ($notification['user'] != md5($current_user->user_login))
						$data['message'][] = $notification;
		}
		delete_transient($args['domain'] . "_heartbeat_noty");
		
		return $data;
	}
	
	/**
	 * Change Hearbeat rate (filter)
	 *
	 * Only for testing purposes
	 * 
	 * @arg Array $settings
	 *
	 * @return Array
	 */
	function change_hearbeat_rate( $settings ) {
		
		$settings['interval'] = $this->args['interval'];
		
		return $settings;
		
	}
	
	/**
	 *	Store the notification in the DB
	 *
	 *	@arg Array $args Options
	 */
	static function noty( array $args ) {
		
		$current_user = wp_get_current_user();
		
		$default_args = array(
			'title'		=>	'',
			'content'	=>	'Notification message not set',
			'type'		=>	'error',
			'domain'	=>	'whn',
			'user'		=>	md5( $current_user->user_login )
		);
		
		$args = array_merge( $default_args, $args );
		$store = get_transient($args['domain'] . "_heartbeat_noty");
		if (empty($store)) $store = array();

		$store[] = $args;

		// If you want/need to change hearbeat rate remember to change transient duration
		set_transient( $args['domain'] . "_heartbeat_noty", $store, 60 );
		
	}

} // Class Wp_Hearbeat_Noty
