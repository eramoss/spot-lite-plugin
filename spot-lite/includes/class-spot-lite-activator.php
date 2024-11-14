<?php

/**
 * Fired during plugin activation
 *
 * @link       http://lite.acad.univali.br
 * @since      1.0.0
 *
 * @package    Spot_Lite
 * @subpackage Spot_Lite/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Spot_Lite
 * @subpackage Spot_Lite/includes
 */
class Spot_Lite_Activator
{

	/**
	 * Activation function.
	 *
	 * This function is fired during plugin activation.
	 * Today, it only init the database.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		self::init_database();

		spot_lite_log('Spot Lite activated');
		self::populate_database();
	}

	/**
	 * Init the database.
	 *
	 * This function is fired during plugin activation.
	 * It creates the tables needed by the plugin.
	 *
	 * @since    1.0.0
	 */
	private static function init_database()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-database.php';
		$database = Spot_Lite_Database::get_instance();
		$database->create_schema();
	}

	/**
	 * Populate the database.
	 *
	 * This function is fired during plugin activation.
	 * It populates the tables needed by the plugin.
	 *
	 * @since    1.0.0
	 */
	private static function populate_database()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-database.php';
		$database = Spot_Lite_Database::get_instance();
		$database->populate();
	}

}
