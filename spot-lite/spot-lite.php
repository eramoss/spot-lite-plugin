<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://lite.acad.univali.br
 * @since             1.0.0
 * @package           Spot_Lite
 *
 * @wordpress-plugin
 * Plugin Name:      	Spot Lite
 * Plugin URI:        http://lite.acad.univali.br/spot-lite/
 * Description:       Um criador de relatórios de atividades com inteligencia e automação.
 * Version:           1.0.0
 * Author:            Lite Univali
 * Author URI:        http://lite.acad.univali.br/
 * Text Domain:       spot-lite
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('SPOT_LITE_VERSION', '1.0.0');
define('ROOT_PLUGIN_URI', plugin_dir_url(__FILE__));
define('ROOT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DEBUG_SPOT_LITE', true);
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-spot-lite-activator.php
 */
function activate_spot_lite()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-spot-lite-activator.php';
	Spot_Lite_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-spot-lite-deactivator.php
 */
function deactivate_spot_lite()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-spot-lite-deactivator.php';
	Spot_Lite_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_spot_lite');
register_deactivation_hook(__FILE__, 'deactivate_spot_lite');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-spot-lite.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_spot_lite()
{

	$plugin = new Spot_Lite();
	$plugin->run();

}
run_spot_lite();
