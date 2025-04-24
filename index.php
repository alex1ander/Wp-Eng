<?php
// Подключаем Timber
use Timber\Timber;

// Получаем контекст страницы
$context = Timber::context();

// Захватываем вывод wp_head()
ob_start();
wp_head();
$wp_head = ob_get_clean();

ob_start();
wp_footer();
$wp_footer = ob_get_clean();


$cards = [];

$args = [
    'post_type'      => 'post', // Или 'courses', если кастомный
    'posts_per_page' => -1,
    'post_status'    => 'publish',
];

$query = new WP_Query($args);

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();

        $categories = get_the_category();
        $category_names = [];

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $category_names[] = $category->name;
            }
        }

        $cards[] = [
            'title'       => get_the_title(),
            'description' => get_the_excerpt(),
            'image'       => get_the_post_thumbnail_url(get_the_ID(), 'large'),
            'categories'  => $category_names,
        ];
    }
    wp_reset_postdata();
}

ob_start();
wp_nav_menu(array(
    'theme_location' => 'main-menu',
    'menu_class' => 'main-menu', 
    'menu_id'        => false,
    'container'      => false,
  ));
$menu = ob_get_clean();

$context['title'] = get_the_title();
$context['descriptiont'] = wp_strip_all_tags(get_the_content());
$context['wp_head'] = $wp_head;
$context['wp_footer'] = $wp_footer;

$context['cards'] = $cards;
$context['menu'] = $menu;

$context['benefits'] = get_field('benefits');
$context['contacts'] = get_field('contacts');
$context['images'] = get_field('images');

// Рендерим шаблон index.twig и передаем контекст
Timber::render('index.twig', $context);
