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
require_once plugin_dir_path(__DIR__) . '../includes/class-database.php';
$db = Spot_Lite_Database::get_instance();

$report = [
  'project_id' => '',
  'author' => '',
  'event_date' => '',
  'title' => '',
  'general_event_description' => '',
  'keywords_for_search' => '',
];

$projects = $db->get_on_table(TableName::PROJECTS, ['fields' => ['id', 'name'], 'mode' => ARRAY_A]);


$activities = [];
$photos = [];
$is_update = false;
if (isset($_GET["id"])) {
  $is_update = true;
  $report_id = $_GET["id"];
  $report = $db->get_report_by_id($report_id, ["mode" => ARRAY_A]);

  foreach ($projects as $project) {
    if ($project["id"] == $report["project_id"]) {
      $report["project_id"] = $project["id"];
    }
  }
  if (!$report) {
    echo '<div class="error">Report not found.</div>';
  }
  $activities = $db->get_activities_by_report_id($report_id, ['mode' => ARRAY_A]);
  $acts = [];
  foreach ($activities as $activity) {
    $participant = $db->get_participant_by_id($activity['participant_id'], ['mode' => ARRAY_A]);
    $activity['participant_name'] = $participant['name'];
    $activity['participant_birth_date'] = $participant['birth_date'];
    $activity['participant_id'] = $participant['id'];
    $acts[] = $activity;
  }
  $activities = $acts;
  $photos = $db->get_photos_by_report_id($report_id, ['mode' => ARRAY_A]);

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $report = [
    'project_id' => $_POST['project_id'],
    'author' => get_current_user_id(),
    'event_date' => $_POST['event_date'],
    'title' => $_POST['title'],
    'general_event_description' => $_POST['general_event_description'],
    'keywords_for_search' => $_POST['keywords_for_search'],
  ];

  if ($is_update) {
    $db->update_report_by_id($report_id, $report);
  } else {
    $report_id = $db->insert_report($report['project_id'], $report['title'], $report['general_event_description'], $report['event_date'], $report['author'], $report['keywords_for_search']);
  }

  $activities = [];
  $photos = [];
  if (isset($_POST['activities'])) {
    $activities = $_POST['activities'];
    $acts = [];
    foreach ($activities as $activity) {
      $act = [];
      $id = $db->exists_or_create_participant($activity['participant_name'], $activity['participant_birth_date'], $activity['participant_school']);
      $act['participant_id'] = $id;
      $act['description'] = $activity['description'];
      $act['report_id'] = $report_id;
      $acts[] = $act;
    }
    spot_lite_log($acts);
    $db->update_activities($report_id, $acts);
  }


  $photos = $_POST['photos'] ? $_POST['photos'] : [];
  $db->update_photos($report_id, $photos);


  echo '<div class="updated">Report saved.</div>';
  echo '<script>setTimeout(function(){window.location.href = "' . admin_url('admin.php?page=spot-lite/admin/partials/plugin-spot-lite-display.php') . '";}, 1000);</script>';
}
?>



<div class="wrap">
  <h1><?php echo $report_id ? 'Editar Relatório' : 'Adicionar novo Relatório'; ?></h1>
  <form method="post">
    <!-- Main Report Fields -->
    <table class="form-table">
      <tr>
        <th scope="row"><label for="project_id">Projeto</label></th>
        <td>
          <select name="project_id" id="project_id" required>
            <option value="">-- Selecione o projeto --</option>
            <?php foreach ($projects as $project): ?>
              <option value="<?php echo esc_attr($project['id']); ?>" <?php selected($report['project_id'], $project['id']); ?>>
                <?php echo esc_html($project['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="title">Titulo</label></th>
        <td><input type="text" name="title" id="title" class="regular-text"
            value="<?php echo esc_attr($report['title']); ?>" required></td>
      </tr>
      <tr>
        <th scope="row"><label for="general_event_description">Descrição</label></th>
        <td>
          <textarea name="general_event_description" id="general_event_description" rows="5"
            class="large-text"><?php echo esc_textarea($report['general_event_description']); ?></textarea>
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="event_date">Data</label></th>
        <td><input type="date" name="event_date" id="event_date" value="<?php echo esc_attr($report['event_date']); ?>">
        </td>
      </tr>
      <tr>
        <th scope="row"><label for="keywords_for_search">Palavras chaves</label></th>
        <td>
          <textarea name="keywords_for_search" id="keywords_for_search" rows="3"
            class="large-text"><?php echo esc_textarea($report['keywords_for_search']); ?></textarea>
        </td>
      </tr>
    </table>

    <!-- Activities Section -->
    <h2>Atividades</h2>
    <div id="activities-section">
      <?php foreach ($activities as $index => $activity): ?>
        <div class="activity-item">
          <input type="hidden" name="activities[<?php echo $index; ?>][id]" value="<?php
             echo esc_attr($activity['id']); ?>">

          <div class='participant-container'>
            <div class="input-group">
              <label for="participant_name">Aluno:</label>
              <input type="text" autocomplete="off" name="activities[<?php echo $index; ?>][participant_name]"
                value="<?php echo esc_attr($activity['participant_name']); ?>" list="participants-list"
                class="participant-input">
            </div>

            <div class="input-group">
              <label for="participant_birth_date">Data de nascimento do aluno:</label>
              <input type="date" name="activities[<?php echo $index; ?>][participant_birth_date]"
                value="<?php echo esc_attr($activity['participant_birth_date']); ?>" class="birth-date-input">
            </div>
          </div>

          <label>Descrição da atividade:</label>
          <textarea
            name="activities[<?php echo $index; ?>][description]"><?php echo esc_textarea($activity['description']); ?></textarea>
          <button type="button" class="remove-activity">Remover</button>
        </div>
      <?php endforeach; ?>
    </div>
    <button type="button" id="add-activity">Adicionar Atividade</button>

    <!-- Photos Section -->

    <h2>Fotos</h2>
    <div id="photos-container">
      <div id="drop-area" class="drop-area">
        <p>Arraste e solte fotos aqui ou clique para fazer upload</p>
        <input type="file" id="file-input" accept="image/*" multiple>
      </div>
      <div id="photos-section">
        <?php foreach ($photos as $index => $photo): ?>
          <div class="photo-item">
            <input type="hidden" name="photos[<?php echo $index; ?>][id]" value="<?php echo esc_attr($photo['id']); ?>">
            <label>Foto URL:</label>
            <input type="text" name="photos[<?php echo $index; ?>][url]" class="photo-url"
              value="<?php echo esc_attr($photo['url']); ?>" readonly>
            <button type="button" class="remove-photo button-link">Remover</button>
          </div>
        <?php endforeach; ?>
      </div>



      <button type="button" id="add-photo" class="button">Adicionar foto</button>
      <ul id="photo-list" class="photo-list"></ul>
    </div>

    <p class="submit">
      <button type="submit"
        class="button-primary"><?php echo $report_id ? 'Atualizar Relatório' : 'Adicionar Relatório'; ?></button>
    </p>
  </form>
</div>