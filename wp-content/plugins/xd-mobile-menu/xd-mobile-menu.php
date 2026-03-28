<?php
/*
Plugin Name: XD Mobile Menu
Description: Menu superior flutuante apenas na home e em dispositivos móveis com botões redondos personalizados.
Version: 1.4.3
Author: XD Plans & David Xavier
Author URI: https://kimtino.com
Text Domain: xd-mobile-menu
*/

if (!defined('ABSPATH')) exit;

// Carrega scripts e estilos apenas no mobile e na home
function xdmm_enqueue_assets() {
    if (wp_is_mobile() && is_front_page()) {
        wp_enqueue_style('xdmm-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
        wp_enqueue_script('xdmm-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), null, true);
    }
}
add_action('wp_enqueue_scripts', 'xdmm_enqueue_assets');

// Inclui página de opções do admin
require_once plugin_dir_path(__FILE__) . 'admin/options-page.php';

// Renderiza o menu abaixo do menu principal (acima do slide) apenas na home em mobile
function xdmm_render_mobile_menu() {
    if (!wp_is_mobile() || !is_front_page()) return;

    $enabled = get_option('xdmm_enabled');
    if ($enabled !== '1') return;

    $buttons = get_option('xdmm_buttons');
    if (!is_array($buttons)) return;

    $valid_buttons = array_filter($buttons, function($btn) {
        return !empty($btn['title']) && !empty($btn['url']);
    });

    if (count($valid_buttons) < 3) return;

    echo '<div id="xdmm-menu-top-wrapper">';
    echo '<div id="xdmm-menu-top">';
    foreach ($valid_buttons as $button) {
        $url = esc_url($button['url']);
        $title = esc_html($button['title']);
        $icon = esc_url($button['icon']);

        echo '<a href="' . $url . '" class="xdmm-button" title="' . $title . '">
                <img src="' . $icon . '" alt="' . $title . '">
                <span class="xdmm-title">' . $title . '</span>
              </a>';
    }
    echo '</div></div>';
}
add_action('wp_body_open', 'xdmm_render_mobile_menu');
?>
