<?php
/**
 * Plugin Name: Dude Facebook feed
 * Plugin URI: https://github.com/digitoimistodude/dude-facebook-feed
 * Description: Fetches the latest posts from Facebook profile or page.
 * Version: 0.1.2
 * Author: Digitoimisto Dude Oy, Timi Wahalahti
 * Author URI: https://www.dude.fi
 * Requires at least: 4.4.2
 * Tested up to: 5.2
 *
 * Text Domain: dude-facebook-feed
 * Domain Path: /languages
 */

if( !defined( 'ABSPATH' )  )
	exit();

Class Dude_Facebook_Feed {
  private static $_instance = null;

  /**
   * Construct everything and begin the magic!
   *
   * @since   0.1.0
   * @version 0.1.0
   */
  public function __construct() {
    // Add actions to make magic happen
    add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
  } // end function __construct

  /**
   *  Prevent cloning
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dude-facebook-feed' ) );
	} // end function __clone

  /**
   *  Prevent unserializing instances of this class
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function __wakeup() {
    _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dude-facebook-feed' ) );
  } // end function __wakeup

  /**
   *  Ensure that only one instance of this class is loaded and can be loaded
   *
   *  @since   0.1.0
   *  @version 0.1.0
	 *  @return  Main instance
   */
  public static function instance() {
    if( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }

    return self::$_instance;
  } // end function instance

  /**
   *  Load plugin localisation
   *
   *  @since   0.1.0
   *  @version 0.1.0
   */
  public function load_plugin_textdomain() {
    load_plugin_textdomain( 'dude-facebook-feed', false, dirname( plugin_basename( __FILE__ ) ).'/languages/' );
  } // end function load_plugin_textdomain

	public function get_posts( $fbid = '' ) {
		if( empty( $fbid ) )
			return;

		$transient_name = apply_filters( 'dude-facebook-feed/posts_transient', 'dude-facebook-'.$fbid, $fbid );
		$posts = get_transient( $transient_name );
	  if( !empty( $posts ) || false != $posts )
	    return $posts;

		$parameters = array(
			'access_token'	=> apply_filters( 'dude-facebook-feed/parameters/access_token', '' ),
      'locale'        => apply_filters( 'dude-facebook-feed/parameters/locale', 'fi_FI' ),
      'since'         => apply_filters( 'dude-facebook-feed/parameters/since', date('Y-m-d', strtotime( '-1 year' ) ) ),
      'limit'					=> apply_filters( 'dude-facebook-feed/parameters/limit', '10' ),
      'fields'        => implode( ',', apply_filters( 'dude-facebook-feed/parameters/fields', array( 'id', 'created_time', 'type', 'message', 'story', 'full_picture', 'link', 'permalink_url', 'status_type' ) ) ),
		);

		$response = self::_call_api( $fbid, apply_filters( 'dude-facebook-feed/api_call_parameters', $parameters ) );
		if( $response === FALSE ) {
      			set_transient( $transient_name, $response, apply_filters( 'dude-facebook-feed/posts_transient_lifetime', '600' ) / 2 );
			return;
    		}

		$response = json_decode( $response['body'], true );

		// FB graph API 2.x => 3.3 backwards comp
		if ( isset( $val['status_type'] ) && isset( $val['permalink_url'] ) ) {
		    foreach ( $response as $key => $val ) {
		     	$response['data'][ $key ]['type'] = $val['status_type'];
      			$response['data'][ $key ]['link'] = $val['permalink_url'];
		    }
		}

		$response = apply_filters( 'dude-facebook-feed/posts', $response );
		set_transient( $transient_name, $response, apply_filters( 'dude-facebook-feed/posts_transient_lifetime', '600' ) );

		return $response;
	} // end function get_posts

	private function _call_api( $fbid = '', $parameters = array() ) {
		if( empty( $fbid ) )
			return false;

		if( empty( $parameters ) )
			return false;

    $parameters = http_build_query( $parameters );
		$response = wp_remote_get( 'https://graph.facebook.com/'.$fbid.'/feed/?'.$parameters );

		if( is_wp_error( $response ) || $response['response']['code'] !== 200 ) {
			self::_write_log( 'response status code not 200 OK, fbid: '.$fbid );
			return false;
		}

		return $response;
	} // end function _call_api

	private function _write_log ( $log )  {
    if( true === WP_DEBUG ) {
      if( is_array( $log ) || is_object( $log ) ) {
        error_log( print_r( $log, true ) );
      } else {
        error_log( $log );
      }
    }
  } // end _write_log
} // end class Dude_Facebook_Feed

function dude_facebook_feed() {
  return new Dude_Facebook_Feed();
} // end function dude_facebook_feed
