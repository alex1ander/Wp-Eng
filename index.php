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


$context['title'] = get_the_title();
$context['wp_head'] = $wp_head;
$context['wp_footer'] = $wp_footer;


// Рендерим шаблон index.twig и передаем контекст
Timber::render('index.twig', $context);
