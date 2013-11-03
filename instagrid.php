<?php
/*
	Plugin Name: InstaGrid
	Plugin URI: http://instagrid.ahansson.com
	Description: InstaGrid is a responsive image grid that pulls images from Instagram
	Version: 1.4
	Author: Aleksander Hansson
	Author URI: http://ahansson.com
	v3: true
*/

class ah_Instagrid_Plugin {

	function __construct() {
		add_action( 'init', array( &$this, 'ah_updater_init' ) );
	}

	/**
	 * Load and Activate Plugin Updater Class.
	 * @since 0.1.0
	 */
	function ah_updater_init() {

		/* Load Plugin Updater */
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/plugin-updater.php' );

		/* Updater Config */
		$config = array(
			'base'      => plugin_basename( __FILE__ ), //required
			'repo_uri'  => 'http://shop.ahansson.com',  //required
			'repo_slug' => 'instagrid',  //required
		);

		/* Load Updater Class */
		new AH_Instagrid_Plugin_Updater( $config );
	}

}

new ah_Instagrid_Plugin;