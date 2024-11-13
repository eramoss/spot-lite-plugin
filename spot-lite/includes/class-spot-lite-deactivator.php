<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://lite.acad.univali.br
 * @since      1.0.0
 *
 * @package    Spot_Lite
 * @subpackage Spot_Lite/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Spot_Lite
 * @subpackage Spot_Lite/includes
 */
class Spot_Lite_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		if (defined('DEBUG_SPOT_LITE') && DEBUG_SPOT_LITE) {
			error_log('Spot Lite deactivated');
			self::clear_db();
		}
	}


	private static function clear_db()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-database.php';
		$database = Spot_Lite_Database::get_instance();
		$database->clear_all();
	}
}
