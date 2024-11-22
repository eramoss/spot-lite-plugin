<?php

/**
 * Singleton Database class to handle all database operations
 *
 * Manages the databse conection under wordpress standards and provides an API to interact with the database. 
 * Dont need to worry about sanitization, escaping or any other security issues, this class will handle it for you.
 * Dont need to worry about the database connection, this class will handle it for you.
 * Since this class is a singleton, you can access it from any part of your code using Spot_Lite_Database::get_instance()
 *
 * Since the wordpress database class is global, we dont need to worry about multiple connections, this class will handle
 *
 * @link       http://lite.acad.univali.br
 * @since      1.0.0
 *
 * @package    Spot_Lite
 * @subpackage Spot_Lite/includes/class-databases
 */
class Spot_Lite_Database
{

  private static $instance = null;
  private $wpdb;

  private function __construct()
  {
    global $wpdb;
    $this->wpdb = $wpdb;
  }

  public static function get_instance()
  {
    if (self::$instance == null) {
      self::$instance = new Spot_Lite_Database();
    }
    return self::$instance;
  }

  static public function get_table_name(TableName $table_name)
  {
    global $wpdb;
    return $wpdb->prefix . "spot_lite_" . $table_name->value;
  }

  protected function create_table_if_not_exists(TableName $table_name, $fields)
  {
    $table_name = self::get_table_name($table_name);
    $charset_collate = $this->wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (";
    foreach ($fields as $field) {
      $sql .= $field . ",";
    }
    $sql = rtrim($sql, ",");
    $sql .= ") $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  private function drop_table_if_exists(TableName $table_name)
  {
    $table_name = self::get_table_name($table_name);
    $this->wpdb->query("DROP TABLE IF EXISTS $table_name");
  }

  private function create_index(TableName $table_name, $index_name, $fields)
  {
    $table_name = self::get_table_name($table_name);
    $index_name = $table_name . "_" . $index_name;

    // Check if the index already exists
    $index_exists = $this->wpdb->get_var(
      $this->wpdb->prepare(
        "SHOW INDEX FROM $table_name WHERE Key_name = %s",
        $index_name
      )
    );

    // If the index does not exist, create it
    if (!$index_exists) {
      $sql = "CREATE INDEX $index_name ON $table_name (";
      if (is_array($fields)) {
        foreach ($fields as $field) {
          $sql .= $field . ",";
        }
      } else {
        $sql .= $fields . ",";
      }
      $sql = rtrim($sql, ",");
      $sql .= ");";
      $this->wpdb->query($sql);
    }
  }

  private function insert(TableName $table_name, $data)
  {
    $table_name = self::get_table_name($table_name);
    $this->wpdb->insert($table_name, $data);
  }

  public function create_schema()
  {
    $this->create_table_if_not_exists(TableName::PROJECTS, [
      'id INT(11) NOT NULL AUTO_INCREMENT',
      'name VARCHAR(255) NOT NULL',
      'description TEXT',
      'start_date DATE',
      'end_date DATE',
      'status VARCHAR(50)',
      'PRIMARY KEY (id)'
    ]);
    $projects_table = self::get_table_name(TableName::PROJECTS);

    $this->create_table_if_not_exists(TableName::PARTICIPANTS, [
      'id INT(11) NOT NULL AUTO_INCREMENT',
      'name VARCHAR(255) NOT NULL',
      'birth_date DATE',
      'school VARCHAR(255)',
      'PRIMARY KEY (id)'
    ]);
    $participants_table = self::get_table_name(TableName::PARTICIPANTS);

    $this->create_table_if_not_exists(TableName::REPORTS, [
      'id INT(11) NOT NULL AUTO_INCREMENT',
      'project_id INT(11) NOT NULL',
      'title VARCHAR(255) NOT NULL',
      'general_event_description TEXT',
      'event_date DATE',
      'author BIGINT UNSIGNED',
      'keywords_for_search TEXT',
      'PRIMARY KEY (id)',
      "FOREIGN KEY (project_id) REFERENCES $projects_table (id) ON DELETE CASCADE",
      'FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE SET NULL'
    ]);
    $reports_table = self::get_table_name(TableName::REPORTS);

    $this->create_table_if_not_exists(TableName::ACTIVITIES, [
      'id INT(11) NOT NULL AUTO_INCREMENT',
      'report_id INT(11) NOT NULL',
      'participant_id INT(11) NOT NULL',
      'description TEXT',
      'PRIMARY KEY (id)',
      "FOREIGN KEY (report_id) REFERENCES $reports_table(id) ON DELETE CASCADE",
      "FOREIGN KEY (participant_id) REFERENCES $participants_table(id) ON DELETE CASCADE"
    ]);

    $this->create_table_if_not_exists(TableName::PHOTOS, [
      'id INT(11) NOT NULL AUTO_INCREMENT',
      'url TEXT',
      'report_id INT(11) NOT NULL',
      'PRIMARY KEY (id)',
      "FOREIGN KEY (report_id) REFERENCES $reports_table(id) ON DELETE CASCADE",
    ]);

    $this->indexes();
    $this->create_full_text_search();
  }

  /**
   * Drop the general schema when deactivating the plugin
   * The deactivation hook is defined in the includes/class-spot-lite-deactivator.php file
   * 
   * @since    1.0.0
   */
  public function drop_schema()
  {
    $this->drop_table_if_exists(TableName::PHOTOS);
    $this->drop_table_if_exists(TableName::ACTIVITIES);
    $this->drop_table_if_exists(TableName::REPORTS);
    $this->drop_table_if_exists(TableName::PARTICIPANTS);
    $this->drop_table_if_exists(TableName::PROJECTS);
  }

  private function indexes()
  {

  }

  private function create_full_text_search()
  {
    $reports_table = self::get_table_name(TableName::REPORTS);
    $column_exists = $this->wpdb->get_var("
      SELECT COUNT(*) 
      FROM information_schema.columns 
      WHERE table_name = '$reports_table' AND column_name = 'fulltext_search'
    ");

    if ($column_exists == 0) {
      $this->wpdb->query("ALTER TABLE $reports_table ADD COLUMN fulltext_search TEXT GENERATED ALWAYS AS (CONCAT(title, ' ', general_event_description, ' ', keywords_for_search)) STORED");
    }


    $index_exists = $this->wpdb->get_var("
      SELECT COUNT(*) 
      FROM information_schema.statistics 
      WHERE table_name = '$reports_table' AND index_name = 'reports_fulltext_search_index'
    ");

    if ($index_exists == 0) {
      $this->wpdb->query("CREATE FULLTEXT INDEX reports_fulltext_search_index ON $reports_table (fulltext_search) WITH PARSER ngram");
    }
  }

  public function insert_project($name, $description, $start_date, $end_date, $status)
  {
    $this->insert(
      TableName::PROJECTS,
      [
        'name' => $name,
        'description' => $description,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'status' => $status
      ]
    );
  }


  public function insert_report($project_id, $title, $general_event_description, $event_date, $author, $keywords_for_search)
  {
    $this->insert(TableName::REPORTS, [
      'project_id' => $project_id,
      'title' => $title,
      'general_event_description' => $general_event_description,
      'event_date' => $event_date,
      'author' => $author,
      'keywords_for_search' => $keywords_for_search
    ]);
  }

  public function insert_activity($report_id, $participant_id, $description)
  {
    $this->insert(TableName::ACTIVITIES, [
      'report_id' => $report_id,
      'participant_id' => $participant_id,
      'description' => $description,
    ]);
  }

  public function insert_participant($name, $birth_date, $school)
  {
    $this->insert(TableName::PARTICIPANTS, [
      'name' => $name,
      'birth_date' => $birth_date,
      'school' => $school
    ]);
  }

  public function insert_photo($url, $report_id)
  {
    $this->insert(TableName::PHOTOS, [
      'url' => $url,
      'report_id' => $report_id
    ]);
  }


  /**
   * Get all values of a table from the database
   * (Optional) Paginate the results
   * (Optional) Select fields
   * 
   * @param TableName $table_name The table name
   * 
   * @param array $args Optional arguments
   * Array with the following keys:
   * - per_page: Number of items per page
   * - current_page: Current page
   * - fields: Array with the fields to select
   * 
   * @return array Array with the reports
   * (if paginated, a count of the total items will be returned in the 'total_items' key)
   * 
   * @example 
   * $db = Spot_Lite_Database::get_instance();
   * $data = $db->get_on_table(TableName::REPORTS,['per_page' => 10, 'current_page' => 1, 'fields' => ['id', 'title']]);
   * 
   * @since    1.0.0
   */
  public function get_on_table(TableName $table_name, $args = [])
  {
    $table_name = self::get_table_name($table_name);
    $fields = isset($args['fields']) ? implode(", ", $args['fields']) : "*";
    $sql = "SELECT $fields FROM $table_name";

    if (isset($args['per_page']) && isset($args['current_page'])) {
      $limit = $this->wpdb->prepare("%d", $args['per_page']);
      $offset = $this->wpdb->prepare("%d", ($args["current_page"] - 1) * $args["per_page"]);
      $sql .= " LIMIT $limit OFFSET $offset";

      $data = $this->wpdb->get_results($sql);

      $total_items = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name");
      return ['data' => $data, 'total_items' => $total_items];
    }
    return $this->wpdb->get_results($sql);
  }


  /**
   * Full text search in the reports table
   * Handles misspelling and partial words
   * @param string $search
   * 
   * @param array $args Optional arguments
   * Array with the following keys:
   * - per_page: Number of items per page
   * - current_page: Current page
   * - fields: Array with the fields to select
   * 
   * @return array Array with the reports extracted from the search
   * 
   * @example
   * $db = Spot_Lite_Database::get_instance();
   * $data = $db->full_text_search_reports('robotic' , ['per_page' => 10, 'current_page' => 1, 'fields' => ['id', 'title']]);
   * 
   * @since    1.0.0
   */
  public function full_text_search_reports($search, $args = [])
  {
    $table_name = self::get_table_name(TableName::REPORTS);
    $fields = isset($args['fields']) ? implode(", ", $args['fields']) : "*";
    $sql = "SELECT $fields FROM $table_name WHERE MATCH(fulltext_search) AGAINST (%s IN NATURAL LANGUAGE MODE)";
    $sql = $this->wpdb->prepare($sql, $search);

    if (isset($args['per_page']) && isset($args['current_page'])) {
      $limit = $this->wpdb->prepare("%d", $args['per_page']);
      $offset = $this->wpdb->prepare("%d", ($args["current_page"] - 1) * $args["per_page"]);

      $sql .= " LIMIT $limit OFFSET $offset";

      $data = $this->wpdb->get_results($sql);
      $total_items = $this->wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE MATCH(fulltext_search) AGAINST (%s IN NATURAL LANGUAGE MODE)", $search);
      return ['data' => $data, 'total_items' => $total_items];
    }

    return $this->wpdb->get_results($sql);
  }

  public function get_author_display_name(int|string $author_id): string
  {
    $author_id = (int) $author_id;
    $author = get_user_by('ID', $author_id);
    if (!$author) {
      spot_lite_log("Author not found: $author_id");
    }
    return $author->display_name;
  }

  public function delete_reports(array $ids): void
  {
    $table_name = self::get_table_name(TableName::REPORTS);
    $sql = "DELETE FROM $table_name WHERE id IN (" . implode(",", $ids) . ")";
    $this->wpdb->query($sql);
  }

  /// DEVELOPMENT ONLY

  public function populate()
  {
    $faker = Faker\Factory::create('pt_BR');

    for ($i = 1; $i <= 3; $i++) {
      $this->insert_project(
        $faker->name(),
        $faker->sentence(6),
        $faker->date('Y-m-d', '2021-01-01'),
        $faker->date('Y-m-d', '2021-12-31'),
        $faker->randomElement(['Em andamento', 'Concluído', 'Cancelado'])
      );
    }

    for ($i = 1; $i <= 3; $i++) {
      $this->insert_participant(
        $faker->name(),
        $faker->date('Y-m-d', '2005-01-01'),
        "Escola " . $faker->word
      );
    }

    for ($i = 1; $i <= 10; $i++) {
      $this->insert_report(
        $faker->numberBetween(1, 3),
        $faker->sentence(6),
        $faker->paragraph(2),
        $faker->date('Y-m-d', '2021-12-31'),
        1,
        $faker->randomElement(['marcenaria', 'robotica', 'programacao', 'eletronica', 'quimica', 'fisica', 'matematica', 'biologia', 'geografia', 'historia'])
      );
    }

    for ($i = 1; $i <= 3; $i++) {
      $this->insert_activity(
        $faker->numberBetween(1, 10),
        $faker->numberBetween(1, 3),
        $faker->paragraph(2),
      );
    }

    for ($i = 1; $i <= 2; $i++) {
      $this->insert_photo($faker->imageUrl(150, 150), $faker->numberBetween(1, 10));
    }
  }


  public function clear_all()
  {
    $this->drop_schema();
    $this->create_schema();
  }
}

enum TableName: string
{
  case PROJECTS = "projects";
  case REPORTS = "reports";
  case ACTIVITIES = "activities";
  case PARTICIPANTS = "participants";
  case PHOTOS = "photos";
}