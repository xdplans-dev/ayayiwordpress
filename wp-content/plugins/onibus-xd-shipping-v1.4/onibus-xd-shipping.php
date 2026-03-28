<?php
/*
Plugin Name: Ônibus XD - Método de Entrega
Description: Adiciona o método de entrega via ônibus ao WooCommerce com campos personalizados.
Version: 1.4
Author: XD Plans
*/

if (!defined('ABSPATH')) {
    exit;
}

function onibus_xd_init_shipping_method() {
    if (!class_exists('WC_Shipping_Method')) return;

    class WC_Onibus_XD_Shipping_Method extends WC_Shipping_Method {
        public function __construct() {
            $this->id = 'onibus_xd';
            $this->method_title = 'Ônibus';
            $this->method_description = 'Entrega via ônibus local.';
            $this->enabled = 'yes';
            $this->title = 'Ônibus';

            $this->init();
        }

        public function init() {
            $this->init_form_fields();
            $this->init_settings();

            $this->enabled = $this->get_option('enabled');
            $this->title = $this->get_option('title');
            $this->cost = $this->get_option('cost');

            add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
        }

        public function init_form_fields() {
            $this->form_fields = [
                'enabled' => [
                    'title'       => 'Ativar',
                    'type'        => 'checkbox',
                    'label'       => 'Ativar método de entrega Ônibus',
                    'default'     => 'yes',
                ],
                'title' => [
                    'title'       => 'Título',
                    'type'        => 'text',
                    'description' => 'Nome exibido para os clientes no checkout.',
                    'default'     => 'Ônibus',
                    'desc_tip'    => true,
                ],
                'cost' => [
                    'title'       => 'Custo fixo',
                    'type'        => 'price',
                    'description' => 'Valor cobrado pelo envio via ônibus.',
                    'default'     => '30',
                    'desc_tip'    => true,
                ],
            ];
        }

        public function calculate_shipping($package = []) {
            $rate = [
                'id'    => $this->id,
                'label' => $this->title,
                'cost'  => $this->cost,
                'calc_tax' => 'per_order',
            ];
            $this->add_rate($rate);
        }
    }
}

add_action('woocommerce_shipping_init', 'onibus_xd_init_shipping_method');

function onibus_xd_add_shipping_method($methods) {
    $methods['onibus_xd'] = 'WC_Onibus_XD_Shipping_Method';
    return $methods;
}

add_filter('woocommerce_shipping_methods', 'onibus_xd_add_shipping_method');

// Campos personalizados após a seleção do método de envio
add_action('woocommerce_after_shipping_rate', 'onibus_xd_extra_fields', 10, 2);
function onibus_xd_extra_fields($method, $index) {
    if (!is_object($method) || !isset($method->id) || $method->id !== 'onibus_xd') return;

    // Verifica se o método está selecionado
    $chosen_method = WC()->session->get('chosen_shipping_methods')[0] ?? '';
    $display = ($chosen_method === 'onibus_xd') ? '' : 'display:none;';

    echo '<div id="onibus-xd-fields" class="onibus-xd-fields" style="margin-top:10px; padding:15px; border:1px solid #ddd; background:#f9f9f9; margin-bottom:20px; border-radius:5px; ' . $display . '">';
    echo '<p style="margin-bottom: 15px; font-size: 14px; color: #666;"><small>Os itens serão enviados para um ônibus designado pelo cliente. É de responsabilidade do cliente inserir as informações corretamente.</small></p>';

    echo '<div style="display: flex; flex-wrap: wrap; gap: 15px;">';
    
    echo '<div style="flex: 1; min-width: 250px;">';
    woocommerce_form_field('onibus_local', array(
        'type' => 'text',
        'label' => 'Local de entrega',
        'required' => true,
        'class' => array('form-row-wide'),
        'input_class' => array('input-text'),
        'custom_attributes' => array(
            'style' => 'width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;'
        )
    ));
    echo '</div>';

    echo '<div style="flex: 1; min-width: 200px;">';
    woocommerce_form_field('onibus_linha', array(
        'type' => 'text',
        'label' => 'Nome/linha do ônibus',
        'required' => true,
        'class' => array('form-row-wide'),
        'input_class' => array('input-text'),
        'custom_attributes' => array(
            'style' => 'width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;'
        )
    ));
    echo '</div>';
    
    echo '</div>';

    echo '<div style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px;">';
    
    echo '<div style="flex: 1; min-width: 200px;">';
    woocommerce_form_field('onibus_placa', array(
        'type' => 'text',
        'label' => 'Número/placa do ônibus',
        'required' => true,
        'class' => array('form-row-wide'),
        'input_class' => array('input-text'),
        'custom_attributes' => array(
            'style' => 'width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;'
        )
    ));
    echo '</div>';

    echo '<div style="flex: 1; min-width: 250px;">';
    woocommerce_form_field('onibus_contato', array(
        'type' => 'text',
        'label' => 'Motorista/guia (contato responsável)',
        'required' => true,
        'class' => array('form-row-wide'),
        'input_class' => array('input-text'),
        'custom_attributes' => array(
            'style' => 'width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;'
        )
    ));
    echo '</div>';
    
    echo '</div>';
    echo '</div>';

    // Adiciona CSS responsivo
    echo '<style>
        @media (max-width: 768px) {
            .onibus-xd-fields {
                padding: 10px !important;
            }
            .onibus-xd-fields > div {
                flex-direction: column !important;
            }
            .onibus-xd-fields > div > div {
                min-width: 100% !important;
                margin-bottom: 10px;
            }
        }
        .onibus-xd-fields input[type="text"]:focus {
            border-color: #0073aa !important;
            outline: none;
            box-shadow: 0 0 5px rgba(0,115,170,0.3);
        }
    </style>';

    // Adiciona JS para mostrar/ocultar os campos conforme seleção
    echo '<script>
    (function($){
        function toggleOnibusFields() {
            var selected = $("input[name=\"shipping_method[0]\"]:checked").val();
            if(selected === "onibus_xd") {
                $("#onibus-xd-fields").show();
            } else {
                $("#onibus-xd-fields").hide();
            }
        }
        $(document).on("change", "input[name=\"shipping_method[0]\"]", toggleOnibusFields);
        $(document).ready(toggleOnibusFields);
    })(jQuery);
    </script>';
}

// Validação obrigatória dos campos do ônibus
add_action('woocommerce_checkout_process', 'onibus_xd_validate_shipping_fields');
function onibus_xd_validate_shipping_fields() {
    $chosen_method = WC()->session->get('chosen_shipping_methods')[0] ?? '';
    if ($chosen_method === 'onibus_xd') {
        if (empty($_POST['onibus_local'])) {
            wc_add_notice(__('Por favor, preencha o local de entrega.'), 'error');
        }
        if (empty($_POST['onibus_linha'])) {
            wc_add_notice(__('Por favor, preencha o nome/linha do ônibus.'), 'error');
        }
        if (empty($_POST['onibus_placa'])) {
            wc_add_notice(__('Por favor, preencha o número/placa do ônibus.'), 'error');
        }
        if (empty($_POST['onibus_contato'])) {
            wc_add_notice(__('Por favor, preencha o motorista/guia responsável.'), 'error');
        }
    }
}

// Salvar os dados do ônibus
add_action('woocommerce_checkout_update_order_meta', 'onibus_xd_save_shipping_fields');
function onibus_xd_save_shipping_fields($order_id) {
    if (isset($_POST['onibus_local'])) {
        update_post_meta($order_id, 'Local de entrega', sanitize_text_field($_POST['onibus_local']));
    }
    if (isset($_POST['onibus_linha'])) {
        update_post_meta($order_id, 'Nome/linha do ônibus', sanitize_text_field($_POST['onibus_linha']));
    }
    if (isset($_POST['onibus_placa'])) {
        update_post_meta($order_id, 'Número/placa do ônibus', sanitize_text_field($_POST['onibus_placa']));
    }
    if (isset($_POST['onibus_contato'])) {
        update_post_meta($order_id, 'Motorista/guia responsável', sanitize_text_field($_POST['onibus_contato']));
    }
}

// Mostrar no painel admin
add_action('woocommerce_admin_order_data_after_shipping_address', 'onibus_xd_display_admin_order_meta', 10, 1);
function onibus_xd_display_admin_order_meta($order) {
    $local = get_post_meta($order->get_id(), 'Local de entrega', true);
    $linha = get_post_meta($order->get_id(), 'Nome/linha do ônibus', true);
    $placa = get_post_meta($order->get_id(), 'Número/placa do ônibus', true);
    $contato = get_post_meta($order->get_id(), 'Motorista/guia responsável', true);

    if ($local || $linha || $placa || $contato) {
        echo '<p><strong>Informações do Ônibus:</strong><br>';
        if ($local) echo 'Local: ' . esc_html($local) . '<br>';
        if ($linha) echo 'Linha: ' . esc_html($linha) . '<br>';
        if ($placa) echo 'Placa: ' . esc_html($placa) . '<br>';
        if ($contato) echo 'Motorista: ' . esc_html($contato);
        echo '</p>';
    }
}

// Exibir informações do ônibus nos detalhes do pedido do cliente
add_action('woocommerce_order_details_after_order_table', 'onibus_xd_display_order_details_to_customer', 10, 1);
function onibus_xd_display_order_details_to_customer($order) {
    $local = get_post_meta($order->get_id(), 'Local de entrega', true);
    $linha = get_post_meta($order->get_id(), 'Nome/linha do ônibus', true);
    $placa = get_post_meta($order->get_id(), 'Número/placa do ônibus', true);
    $contato = get_post_meta($order->get_id(), 'Motorista/guia responsável', true);

    if ($local || $linha || $placa || $contato) {
        echo '<section class="woocommerce-order-details onibus-xd-order-details" style="margin-top:20px;">';
        echo '<h3>Dados do Ônibus</h3>';
        echo '<p>';
        if ($local) echo '<strong>Local de entrega:</strong> ' . esc_html($local) . '<br>';
        if ($linha) echo '<strong>Nome/linha:</strong> ' . esc_html($linha) . '<br>';
        if ($placa) echo '<strong>Número/placa:</strong> ' . esc_html($placa) . '<br>';
        if ($contato) echo '<strong>Motorista/guia:</strong> ' . esc_html($contato);
        echo '</p>';
        echo '</section>';
    }
}
