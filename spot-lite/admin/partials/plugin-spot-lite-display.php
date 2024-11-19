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
$data = $db->full_text_search_reports('biolpgia');
$highlight = 'robotica';
$string_data_highlighting = '';

function highlight($text, $words)
{
  $words = explode(' ', $words);
  foreach ($words as $word) {
    $text = preg_replace('/' . $word . '/', '<span style="background-color: yellow;">' . $word . '</span>', $text);
  }
  return $text;
}

foreach ($data as $row) {
  $string_data_highlighting .= '<tr>';
  $string_data_highlighting .= '<td>' . $row->id . '</td>';
  $string_data_highlighting .= '<td>' . highlight($row->title, $highlight) . '</td>';
  $string_data_highlighting .= '<td>' . highlight($row->general_event_description, $highlight) . '</td>';
  $string_data_highlighting .= '<td>' . highlight($row->fulltext_search, $highlight) . '</td>';
  $string_data_highlighting .= '</tr>';
}

echo '<table>';
echo '<tr>';
echo '<th>ID</th>';
echo '<th>Title</th>';
echo '<th>Description</th>';
echo '<th>Search</th>';
echo '</tr>';
echo $string_data_highlighting;
echo '</table>';
echo '<br>';
echo '';

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<h1>alooo</h1>