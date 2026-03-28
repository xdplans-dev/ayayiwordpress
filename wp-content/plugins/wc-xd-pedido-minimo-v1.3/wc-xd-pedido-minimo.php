<?php
/**
 * Plugin Name: XD Pedido Mínimo
 * Plugin URI: https://xdplans.com/
 * Description: Controla pedido mínimo na loja via admin, bloqueando checkout e direcionando ao atacado.
 * Version: 1.3
 * Author: XD Plans
 * Text Domain: xd-pedido-minimo
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class XD_Pedido_Minimo {
    private $options;

    public function __construct(){
        $this->options = get_option('xdpm_settings', array());
        add_action('admin_menu', array($this, 'add_admin_page'));
        add_action('admin_init', array($this, 'register_settings'));

        if ( ! empty($this->options['enabled']) ) {
            add_action('woocommerce_before_cart', array($this, 'check_minimum'));
            add_action('woocommerce_before_cart', array($this, 'prevent_checkout_button'), 20);
        }
    }

    public function add_admin_page(){
        add_menu_page(
            'XD Pedido Mínimo',
            'XD Pedido Mínimo',
            'manage_options',
            'xd-pedido-minimo',
            array($this, 'admin_page'),
            'dashicons-editor-ul',
            56
        );
    }

    public function register_settings(){
        register_setting('xdpm_group', 'xdpm_settings', array($this, 'sanitize'));
        add_settings_section('xdpm_main', 'Configurações do Pedido Mínimo', '', 'xd-pedido-minimo');
        add_settings_field('enabled', 'Habilitado', array($this, 'render_enabled'), 'xd-pedido-minimo', 'xdpm_main');
        add_settings_field('min_amount', 'Valor Mínimo (R$)', array($this, 'render_min_amount'), 'xd-pedido-minimo', 'xdpm_main');
        add_settings_field('redirect_cat', 'Slug Categoria Atacado', array($this, 'render_redirect_cat'), 'xd-pedido-minimo', 'xdpm_main');
    }

    public function sanitize($input){
        $new = array();
        $new['enabled'] = isset($input['enabled']) ? 1 : 0;
        $new['min_amount'] = is_numeric($input['min_amount']) ? floatval($input['min_amount']) : 1000;
        $new['redirect_cat'] = sanitize_text_field($input['redirect_cat']);
        return $new;
    }

    public function render_enabled(){
        printf(
            '<input type="checkbox" name="xdpm_settings[enabled]" value="1" %s />',
            checked(1, isset($this->options['enabled']) ? $this->options['enabled'] : 0, false)
        );
    }

    public function render_min_amount(){
        printf(
            '<input type="number" step="0.01" name="xdpm_settings[min_amount]" value="%s" />',
            esc_attr(isset($this->options['min_amount']) ? $this->options['min_amount'] : 1000)
        );
    }

    public function render_redirect_cat(){
        printf(
            '<input type="text" name="xdpm_settings[redirect_cat]" value="%s" /><p class="description">Slug da categoria para redirecionar "Comprar mais".</p>',
            esc_attr(isset($this->options['redirect_cat']) ? $this->options['redirect_cat'] : '')
        );
    }

    public function admin_page(){
        echo '<div class="wrap"><h1>XD Pedido Mínimo</h1><form method="post" action="options.php">';
        settings_fields('xdpm_group');
        do_settings_sections('xd-pedido-minimo');
        submit_button();
        echo '</form></div>';
    }

    public function check_minimum(){
        if ( ! is_cart() ) return;
        $subtotal = WC()->cart->subtotal;
        $min = floatval($this->options['min_amount']);
        if ( $subtotal < $min ) {
            $faltam = $min - $subtotal;
            wc_add_notice(
                sprintf(
                    'Para atingir o pedido mínimo de R$%s, faltam R$%s. <a class="button" href="%s">Comprar mais</a>',
                    number_format($min,2,',','.'),
                    number_format($faltam,2,',','.'),
                    esc_url(get_term_link($this->options['redirect_cat'], 'product_cat'))
                ),
                'error'
            );
        }
    }

    public function prevent_checkout_button(){
        if ( ! is_cart() ) return;
        $subtotal = WC()->cart->subtotal;
        if ( $subtotal < floatval($this->options['min_amount']) ) {
            remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);
        }
    }
}

new XD_Pedido_Minimo();
