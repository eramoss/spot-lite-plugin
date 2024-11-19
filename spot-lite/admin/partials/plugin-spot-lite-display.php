<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://lite.acad.univali.br
 * @since      1.0.0
 *
 * @package    Spot_Lite
 * @subpackage Spot_Lite/admin/partials
 */
include_once ROOT_PLUGIN_PATH . 'includes/class-database.php';
$db = Spot_Lite_Database::get_instance();
$data = $db->full_text_search_reports('robps');
var_dump($data);
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<h1>alooo</h1>