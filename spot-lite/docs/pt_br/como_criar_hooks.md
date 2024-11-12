# Como criar um hook no projeto

Hooks são funções que permitem que você "ligue" o seu código a um determinado ponto de execução do Spot. Eles são muito úteis para adicionar funcionalidades extras ao seu projeto, como por exemplo, adicionar um novo tipo de autenticação, ou adicionar um novo tipo de validação de dados.

De uma olhada na propria documentação do wordpress para alguns hooks que você pode usar: [https://developer.wordpress.org/reference/hooks/](https://developer.wordpress.org/reference/hooks/)

Para criar um hook no seu projeto, você precisa seguir os seguintes passos:

Se for um hook para admin, ou seja, que só será executado no painel de administração, você deve criar uma função em `admin/class-spot-lite-admin.php` e adicionar o hook na class Spot_Lite em `includes/class-spot-lite.php` dentro da função `define_admin_hooks`.

### Exemplo de hook para admin

```php
// admin/class-spot-lite-admin.php
	public function add_plugin_admin_menu()
	{
		add_menu_page(
			'Spot Lite',
			'Regitros',
			'manage_options',
			plugin_dir_path(__FILE__) . '/partials/plugin-spot-lite-display.php',
			null,
			ROOT_PLUGIN_URI . 'public/img/icon-light.png',
			6
		);
	}
```

```php
// includes/class-spot-lite.php
  public function define_admin_hooks()
  {
    $plugin_admin = new Spot_Lite_Admin($this->get_plugin_name(), $this->get_version());
    $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
  }
```

prontinho :) Agora você tem um hook no seu projeto.