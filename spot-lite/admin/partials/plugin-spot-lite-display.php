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
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php

require_once plugin_dir_path(__DIR__) . '../includes/class-spot-lite-custom-tables.php';

$list_table = new Spot_Lite_Reports_Table();
$list_table->prepare_items();

?>

<div class="wrap">
  <h1 class="wp-heading-inline">Relatórios</h1>
  <form method="post">
    <?php
    $list_table->search_box('Pesquisar', 'search_id');
    $list_table->display();
    ?>
  </form>
</div>