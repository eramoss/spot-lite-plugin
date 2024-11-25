<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://lite.acad.univali.br
 * @since      1.0.0
 *
 * @package    Spot_Lite
 * @subpackage Spot_Lite/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Spot_Lite
 * @subpackage Spot_Lite/admin
 */
class Spot_Lite_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $Spot_Lite    The ID of this plugin.
	 */
	private $Spot_Lite;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $Spot_Lite       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($Spot_Lite, $version)
	{
		require_once plugin_dir_path(__DIR__) . 'includes/class-database.php';
		$this->Spot_Lite = $Spot_Lite;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->Spot_Lite, plugin_dir_url(__FILE__) . 'css/spot-lite-admin.css', array(), $this->version, 'all');

		wp_register_style('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
		wp_enqueue_style('prefix_bootstrap');
		if (isset($_GET['page']) && (str_contains($_GET['page'], 'spot-lite-add-edit'))) {
			$this->enqueue_add_edit_styles();
		}
	}
	public function enqueue_add_edit_styles()
	{
		wp_enqueue_style($this->Spot_Lite, plugin_dir_url(__FILE__) . 'css/spot-lite-add-edit.css', array(), $this->version, 'all');
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->Spot_Lite, plugin_dir_url(__FILE__) . 'js/spot-lite-admin.js', array('jquery'), $this->version, false);
		wp_enqueue_media();
		wp_register_script('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js');
		wp_enqueue_script('prefix_bootstrap');

		if (isset($_GET['page']) && (str_contains($_GET['page'], 'spot-lite-add-edit'))) {
			$this->enqueue_add_edit_scripts();
		}
	}

	public function enqueue_add_edit_scripts()
	{
		$db = Spot_Lite_Database::get_instance();
		$existing_participants = $db->get_on_table(TableName::PARTICIPANTS, ['mode' => ARRAY_A]);
		$rest_url = esc_url_raw(rest_url('wp/v2/media'));
		$nonce = wp_create_nonce('wp_rest');

		wp_enqueue_script("spot_lite_add_edit", plugin_dir_url(__FILE__) . 'js/spot-lite-add-edit.js', array('jquery'), $this->version, false);
		wp_localize_script($this->Spot_Lite, 'spot_lite_add_edit', [
			'existing_participants' => $existing_participants,
			'rest_url' => $rest_url,
			'nonce' => $nonce
		]);
	}

	public function add_admin_menu()
	{

		$topmenu_slug = plugin_dir_path(__FILE__) . '/partials/plugin-spot-lite-display.php';
		$submenu_slug_projetos = plugin_dir_path(__FILE__) . '/partials/plugin-spot-lite-projects.php';
		$submenu_slug_adicionar_relatorio = plugin_dir_path(__FILE__) . '/partials/plugin-spot-lite-add-edit-report.php';
		$submenu_slug_configuracoes = plugin_dir_path(__FILE__) . '/partials/plugin-spot-lite-settings.php';
		$submenu_slug_analises = plugin_dir_path(__FILE__) . '/partials/plugin-spot-lite-analysis.php';

		add_menu_page(
			'Spot Lite',
			'SpotLite',
			'spot_lite_pass',
			$topmenu_slug,
			null,
			ROOT_PLUGIN_URI . 'public/img/spot-lite-icon.png',
			6
		);

		add_submenu_page(
			$topmenu_slug,
			'Spot Lite - Registros',
			'Registros',
			'spot_lite_pass',
			$topmenu_slug,
			null
		);

		add_submenu_page(
			$topmenu_slug,
			'Spot Lite - Projetos',
			'Projetos',
			'spot_lite_pass',
			$submenu_slug_projetos,
			null
		);

		add_submenu_page(
			$topmenu_slug,
			'Spot Lite - Adicionar Relatório',
			'Adicionar Relatório',
			'spot_lite_pass',
			$submenu_slug_adicionar_relatorio,
			null
		);

		add_submenu_page(
			$topmenu_slug,
			'Spot Lite - Análises',
			'Análises',
			'spot_lite_pass',
			$submenu_slug_analises,
			null
		);

		add_submenu_page(
			$topmenu_slug,
			'Spot Lite - Configurações',
			'Configurações',
			'spot_lite_pass',
			$submenu_slug_configuracoes,
			null
		);

	}

}
