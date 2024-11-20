<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Spot_Lite_Reports_Table extends WP_List_Table{


    // Define as colunas da tabela
    function get_columns() {
        return [
            'cb'       => '<input type="checkbox" />', // Checkbox para ações em massa
            'id'       => 'ID',
            'name'     => 'Nome',
            'date'     => 'Data',
        ];
    }

    // Retorna os itens para exibir
    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = []; // Colunas ocultas
        $sortable = ['name' => ['name', true]]; // Colunas ordenáveis

        $this->_column_headers = [$columns, $hidden, $sortable];

        // Simulação de dados (normalmente viria do banco de dados)
        $data = [
            ['id' => 1, 'name' => 'Relatório A', 'date' => '2024-11-20'],
            ['id' => 2, 'name' => 'Relatório B', 'date' => '2024-11-19'],
            ['id' => 3, 'name' => 'Relatório C', 'date' => '2024-11-18'],
            ['id' => 4, 'name' => 'Relatório D', 'date' => '2024-11-17'],
            ['id' => 5, 'name' => 'Relatório E', 'date' => '2024-11-16'],
            ['id' => 6, 'name' => 'Relatório F', 'date' => '2024-11-15'],
            ['id' => 7, 'name' => 'Relatório G', 'date' => '2024-11-14'],
            ['id' => 8, 'name' => 'Relatório H', 'date' => '2024-11-13'],
            ['id' => 9, 'name' => 'Relatório I', 'date' => '2024-11-12'],
            ['id' => 10, 'name' => 'Relatório J', 'date' => '2024-11-11'],
            ['id' => 11, 'name' => 'Relatório K', 'date' => '2024-11-10'],
            ['id' => 12, 'name' => 'Relatório L', 'date' => '2024-11-09'],
        ];

        // Paginação
        $per_page = 5;
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ]);

        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        $this->items = $data;
    }

    // Renderiza uma célula padrão
    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
            case 'name':
            case 'date':
                return $item[$column_name];
            default:
                return print_r($item, true); // Para debug
        }
    }

    // Checkbox para ações em massa
    function column_cb($item) {
        return sprintf('<input type="checkbox" name="id[]" value="%s" />', $item['id']);
    }
}
