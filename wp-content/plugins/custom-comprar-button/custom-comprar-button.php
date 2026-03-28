<?php
/**
 * Plugin Name: Botão Comprar Customizado
 * Description: Altera o texto do botão "Adicionar ao carrinho" para "Comprar" com ícone à esquerda.
 * Version: 1.0
 * Author: XD Plans
 */

if (!defined('ABSPATH')) exit;

// Altera o texto do botão
add_filter('woocommerce_product_add_to_cart_text', function() {
    return __('Comprar', 'woocommerce');
});

// Altera o botão na lista de produtos para incluir o ícone
add_filter('woocommerce_loop_add_to_cart_link', function($button, $product, $args) {
    if ($product && $product->is_type('simple')) {
        $url = esc_url($product->add_to_cart_url());
        $label = esc_html($product->add_to_cart_text());
        $icon = '<i class="fas fa-shopping-cart" style="margin-right: 6px;"></i>';
        $button = '<a href="' . $url . '" data-quantity="1" class="button add_to_cart_button ajax_add_to_cart" data-product_id="' . $product->get_id() . '">' . $icon . $label . '</a>';
    }
    return $button;
}, 10, 3);

// Adiciona o Font Awesome no frontend (caso não esteja presente)
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
});
