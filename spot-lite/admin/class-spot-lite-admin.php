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
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->Spot_Lite, plugin_dir_url(__FILE__) . 'js/spot-lite-admin.js', array('jquery'), $this->version, false);
	}

	public function add_admin_menu()
	{

		$topmenu_slug = plugin_dir_path(__FILE__) . '/partials/plugin-spot-lite-display.php';
		$submenu_slug_projetos = plugin_dir_path(__FILE__) . '/partials/plugin-spot-lite-projects.php';
		$submenu_slug_adicionar_relatorio = plugin_dir_path(__FILE__) . '/partials/plugin-spot-lite-add-report.php';
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
