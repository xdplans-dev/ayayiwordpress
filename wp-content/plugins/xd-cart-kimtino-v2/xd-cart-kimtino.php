<?php
/**
 * Plugin Name: XD Cart - Carrinho Customizado Kimtino
 * Description: Carrinho com layout moderno, verificação de pedido mínimo, entrega apenas no checkout e configurações personalizadas.
 * Version: 1.6
 * Author: David Xavier - XD Plans
 */

if (!defined('ABSPATH')) exit;

// Menu XD Cart no admin
add_action('admin_menu', 'xd_cart_menu');
function xd_cart_menu() {
    add_menu_page(
        'XD Cart',
        'XD Cart',
        'manage_options',
        'xd-cart',
        'xd_cart_config_page',
        'dashicons-cart',
        56
    );
}

function xd_cart_config_page() {
    ?>
    <div class="wrap">
        <h1>Configurações do XD Cart</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('xd_cart_settings');
                do_settings_sections('xd-cart');
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Configurações
add_action('admin_init', 'xd_cart_register_settings');
function xd_cart_register_settings() {
    register_setting('xd_cart_settings', 'xd_cart_ativo');
    add_settings_section('xd_cart_main_section', '', null, 'xd-cart');
    add_settings_field('xd_cart_ativo', 'Ativar carrinho customizado?', 'xd_cart_ativo_callback', 'xd-cart', 'xd_cart_main_section');
}

function xd_cart_ativo_callback() {
    $ativo = get_option('xd_cart_ativo');
    echo '<input type="checkbox" name="xd_cart_ativo" value="1" ' . checked(1, $ativo, false) . '> Ativo';
}

// Scripts e estilos
add_action('wp_enqueue_scripts', 'xd_cart_enqueue_assets');
function xd_cart_enqueue_assets() {
    if (!is_cart() || !get_option('xd_cart_ativo')) return;

    wp_enqueue_style('xd-cart-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('xd-cart-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);

    wp_localize_script('xd-cart-script', 'xd_cart', array(
        'min_order' => 1000,
    ));
}

// Alterar texto do botão de finalização para 'Finalizar' corretamente
add_filter('woocommerce_proceed_to_checkout', function() {
    remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);
    echo '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="checkout-button button alt wc-forward">Finalizar</a>';
});

// Ocultar métodos de entrega e título no carrinho
add_filter('woocommerce_cart_needs_shipping_address', 'xd_cart_hide_shipping');
function xd_cart_hide_shipping($needs_shipping) {
    if (is_cart() && get_option('xd_cart_ativo')) return false;
    return $needs_shipping;
}

add_filter('woocommerce_cart_totals_shipping_html', 'xd_cart_hide_shipping_methods');
function xd_cart_hide_shipping_methods($html) {
    if (is_cart() && get_option('xd_cart_ativo')) return '';
    return $html;
}

add_filter('woocommerce_cart_totals_shipping_method', '__return_empty_string');
add_filter('woocommerce_cart_totals_shipping', '__return_empty_string');
add_filter('woocommerce_cart_totals_shipping_label', '__return_empty_string');

// Remover todos os notices padrão do WooCommerce, inclusive pedido mínimo
add_filter('woocommerce_show_notice', function($show, $notice) {
    if (is_cart() && get_option('xd_cart_ativo')) return false;
    return $show;
}, 10, 2);
