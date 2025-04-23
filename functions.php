<?php

function my_theme_enqueue_styles() {
    // Подключаем скомпилированный файл стилей
    wp_enqueue_style('my-theme-styles', get_template_directory_uri() . '/assets/css/style.min.css');
    wp_enqueue_script('my-custom-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), null, true);

}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');



// Подключение автозагрузки
require_once __DIR__ . '/vendor/autoload.php';

// Инициализация Timber
if ( class_exists( 'Timber\Timber' ) ) {
    Timber\Timber::init();
    Timber\Timber::$dirname = ['views'];
} else {
    // Сообщение об ошибке, если Timber не установлен
    add_action('admin_notices', function () {
        echo '<div class="error"><p>Timber не найден. Убедитесь, что он установлен через Composer.</p></div>';
    });
}


//sprite svg
add_action('wp_footer', 'load_svg_sprite');
function load_svg_sprite() {
    $sprite_path = get_template_directory() . '/assets/icons/sprite.svg';
    if (file_exists($sprite_path)) {
        echo file_get_contents($sprite_path);
    }
}
