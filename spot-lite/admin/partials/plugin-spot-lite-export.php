<?php

if (isset($_GET["ids"])) {
  require_once ROOT_PLUGIN_PATH . 'includes/LibExporter/class-html-exporter.php';
  require_once ROOT_PLUGIN_PATH . 'includes/class-database.php';
  $db = Spot_Lite_Database::get_instance();

  $reports = $db->get_report_by_ids((array) $_GET["ids"], ['mode' => ARRAY_A]);
  $exporter = new Spot_Lite_HTML_Exporter();

  foreach ($reports as $report) {
    $photos = $db->get_photos_by_report_id($report['id'], ['mode' => ARRAY_A]);
    $photos = array_map(function ($photo) {
      return $photo['url'];
    }, $photos);

    $activities = $db->get_activities_by_report_id($report['id'], ['mode' => ARRAY_A]);

    $activities = array_map(function ($activity) use ($db) {
      $participant = $db->get_participant_by_id($activity['participant_id'], ['mode' => ARRAY_A]);
      $activity['participant_name'] = $participant['name'];
      $timestamp = strtotime($participant['birth_date']);
      $age_in_years = floor((time() - $timestamp) / 31556926);
      $activity['participant_age'] = $age_in_years;
      return $activity;
    }, $activities);

    $html = $exporter->mount(file_get_contents(ROOT_PLUGIN_PATH . 'includes/LibExporter/templates/class-html-template.html'), $report['title'], $report['general_event_description'], $photos, $activities);

    echo "<script>
            const w$report[id] = window.open('$report[id]', '_blank', 'width=800,height=600');
            if (w$report[id]) {
                w$report[id].document.open();
                w$report[id].document.write(" . json_encode($html) . ");
                w$report[id].document.close();
            } else {
                alert('Pop-up blocked! Please allow pop-ups for this website.');
            }
        </script>";
  }
}



?>