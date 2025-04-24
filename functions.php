<?php

function my_theme_enqueue_styles() {
    // Подключаем скомпилированный файл стилей
    wp_enqueue_style('my-theme-styles', get_template_directory_uri() . '/assets/css/style.min.css');
    wp_enqueue_script('my-custom-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), null, true);

    wp_enqueue_script(
        'lead-form-script', 
        get_template_directory_uri() . '/assets/js/contact-form.js', 
        array('jquery'), 
        null, 
        true
    );

    // Локализуем скрипт с параметрами для AJAX-запроса
    wp_localize_script('lead-form-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'), // Правильный URL для AJAX-запросов
        'nonce'    => wp_create_nonce('save_lead_ajax_action'), // Создаем nonce для безопасности
    ));

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


function create_custom_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'contact_form_data'; // Название таблицы
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        service varchar(255) DEFAULT '' NOT NULL,
        name varchar(255) NOT NULL,
        phone varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        message text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('after_setup_theme', 'create_custom_table');



function save_form_data() {
    // Проверка nonce для безопасности
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'save_lead_ajax_action')) {
        wp_send_json_error(array('message' => 'Неверный nonce. Запрос не прошел проверку безопасности.'));
        wp_die();
    }
    // Проверяем, что данные отправляются методом POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        global $wpdb;

        // Получаем данные из формы
        $service = isset($_POST['service']) ? sanitize_text_field($_POST['service']) : '';
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';


        // Вставляем данные в базу
        $table_name = $wpdb->prefix . 'contact_form_data';
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'service' => $service,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'message' => $message,
            )
        );

        // Если вставка прошла успешно
        if ($inserted) {
            wp_send_json_success(array('message' => 'Данные успешно сохранены.'));
        } else {
            wp_send_json_error(array('message' => 'Произошла ошибка при сохранении данных.'));
        }

        wp_die(); // Завершаем выполнение
    }
}

// Добавляем обработчики для AJAX-запросов
add_action('wp_ajax_save_lead_ajax', 'save_form_data');
add_action('wp_ajax_nopriv_save_lead_ajax', 'save_form_data');




function create_admin_menu() {
    add_menu_page(
        'Данные формы',          // Название страницы
        'Данные формы',          // Название меню
        'manage_options',        // Права доступа
        'contact_form_data',     // Слаг
        'display_form_data',     // Функция вывода данных
        'dashicons-forms',       // Иконка
        30                       // Позиция
    );
}

add_action('admin_menu', 'create_admin_menu');

function display_form_data() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'contact_form_data';
    
    // Получаем данные из базы
    $results = $wpdb->get_results("SELECT * FROM $table_name");
    
    echo '<div class="wrap">';
    echo '<h1>Сохраненные данные формы</h1>';
    
    if ($results) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Сервис</th><th>Имя</th><th>Телефон</th><th>Email</th><th>Сообщение</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . $row->id . '</td>';
            echo '<td>' . $row->service . '</td>';
            echo '<td>' . $row->name . '</td>';
            echo '<td>' . $row->phone . '</td>';
            echo '<td>' . $row->email . '</td>';
            echo '<td>' . $row->message . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Нет данных для отображения.</p>';
    }
    
    echo '</div>';
}



function register_my_menu() {
  register_nav_menu('main-menu', 'Основное меню');  // main-menu — это название location
}
add_action('after_setup_theme', 'register_my_menu');






// Подключаем файл с полями ACF
require_once get_template_directory() . '/acf.php';



