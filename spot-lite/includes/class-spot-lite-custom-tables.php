<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Spot_Lite_Reports_Table extends WP_List_Table
{

    private Spot_Lite_Database $db;
    public function __construct($args = array())
    {
        parent::__construct($args);
        include_once ROOT_PLUGIN_PATH . 'includes/class-database.php';
        $this->db = Spot_Lite_Database::get_instance();
    }


    // Define as colunas da tabela
    function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />',
            'id' => 'ID',
            'author' => 'Autor',
            'event_date' => 'Data',
            'title' => 'Título',
            'general_event_description' => 'Descrição',
            'keywords_for_search' => 'Palavras-chave',
        ];
    }

    function get_bulk_actions()
    {
        return [
            'delete' => 'Deletar',
            'export' => 'Exportar',
        ];
    }

    // Retorna os itens para exibir
    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];

        $this->_column_headers = [$columns, $hidden, $sortable];


        $per_page = 5;
        $current_page = $this->get_pagenum();
        $columns = $this->get_columns();
        unset($columns['cb']);
        $fields = array_keys($columns);
        if (isset($_POST['s']) && strlen($_POST['s']) >= 3) {
            $res = $this->db->full_text_search_reports($_POST['s'], compact('per_page', 'current_page', 'fields'));
        } else {
            $res = $this->db->get_on_table(TableName::REPORTS, compact('per_page', 'current_page', 'fields'));
        }

        $data = $res['data'];
        $total_items = $res['total_items'];


        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
        ]);

        $this->items = array_map(function ($item) {
            $item = (array) $item;
            $item['author'] = $this->db->get_author_display_name($item['author']);
            return $item;
        }, $data);
    }

    // Renderiza uma célula padrão
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    // Checkbox para ações em massa
    function column_cb($item)
    {
        return sprintf('
            <div class="d-flex align-items-center pb-2">
                <input type="checkbox" name="id[]" value="%s" />
                <a href="%s" class="px-4"><i class="dashicons dashicons-edit"></i></a>
        ', $item['id'], $this->get_edit_link($item));
    }

    function handle_bulk_actions()
    {
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : [];
            $this->db->delete_reports($ids);
        }
        if ('export' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : [];
            spot_lite_log("exporting pdf for ids: " . json_encode($ids));
        }
    }

    function get_edit_link($item)
    {
        return admin_url("admin.php?page=spot-lite/admin/partials/plugin-spot-lite-add-report.php&id={$item['id']}");
    }

}
