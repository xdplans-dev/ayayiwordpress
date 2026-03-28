<?php
/**
 * Plugin Name: Transportadora XD Plugin
 * Plugin URI: https://xdplans.com
 * Description: Método de entrega via transportadora com campos personalizados.
 * Version: 1.1
 * Author: XD Plans
 * Text Domain: transportadora-xd
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action('woocommerce_shipping_init', 'transportadora_xd_shipping_method_init');
function transportadora_xd_shipping_method_init() {
    class WC_Transportadora_XD_Shipping_Method extends WC_Shipping_Method {
        public function __construct() {
            $this->id                 = 'transportadora_xd';
            $this->method_title       = 'Transportadora';
            $this->method_description = 'Entrega via transportadora designada pelo cliente.';
            $this->enabled            = $this->get_option('enabled', 'yes');
            $this->title              = $this->get_option('title', 'Transportador');

            $this->init();
        }

        function init() {
            $this->init_form_fields();
            $this->init_settings();
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Ativar/Desativar',
                    'type'        => 'checkbox',
                    'label'       => 'Ativar método de envio Transportadora',
                    'default'     => 'yes'
                ),
                'title' => array(
                    'title'       => 'Título do método',
                    'type'        => 'text',
                    'description' => 'Título que será exibido para o cliente',
                    'default'     => 'Transportador',
                    'desc_tip'    => true,
                ),
            );
        }

        public function calculate_shipping( $package = array() ) {
            // Só adiciona a taxa se o método estiver ativado
            if ($this->enabled === 'yes') {
                $rate = array(
                    'id'    => $this->id,
                    'label' => $this->title,
                    'cost'  => 0,
                    'calc_tax' => 'per_item'
                );
                $this->add_rate($rate);
            }
        }
    }
}

add_filter('woocommerce_shipping_methods', 'add_transportadora_xd_shipping_method');
function add_transportadora_xd_shipping_method( $methods ) {
    $methods['transportadora_xd'] = 'WC_Transportadora_XD_Shipping_Method';
    return $methods;
}

add_action('woocommerce_after_shipping_rate', 'transportadora_xd_extra_fields', 10, 2);
function transportadora_xd_extra_fields($method, $index) {
    if (!is_object($method) || !isset($method->id) || $method->id !== 'transportadora_xd') return;

    // Verifica se o método está selecionado
    $chosen_method = WC()->session->get('chosen_shipping_methods')[0] ?? '';
    $display = ($chosen_method === 'transportadora_xd') ? '' : 'display:none;';

    echo '<div id="transportadora-xd-fields" class="transportadora-xd-fields" style="margin-top:10px; padding:15px; border:1px solid #ddd; background:#f9f9f9; margin-bottom:20px; border-radius:5px; ' . $display . '">';
    echo '<p style="margin-bottom: 15px; font-size: 14px; color: #666;"><small>Os itens serão recolhidos por um transportador designado pelo cliente. Por favor, insira as informações corretamente.</small></p>';

    echo '<div style="display: flex; flex-wrap: wrap; gap: 15px;">';
    
    echo '<div style="flex: 1; min-width: 250px;">';
    woocommerce_form_field('transportadora_nome', array(
        'type' => 'text',
        'label' => 'Transportadora (nome da empresa)',
        'required' => true,
        'class' => array('form-row-wide'),
        'input_class' => array('input-text'),
        'custom_attributes' => array(
            'style' => 'width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;'
        )
    ));
    echo '</div>';

    echo '<div style="flex: 1; min-width: 200px;">';
    woocommerce_form_field('transportadora_telefone', array(
        'type' => 'text',
        'label' => 'Telefone da transportadora',
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
            .transportadora-xd-fields {
                padding: 10px !important;
            }
            .transportadora-xd-fields > div {
                flex-direction: column !important;
            }
            .transportadora-xd-fields > div > div {
                min-width: 100% !important;
                margin-bottom: 10px;
            }
        }
        .transportadora-xd-fields input[type="text"]:focus {
            border-color: #0073aa !important;
            outline: none;
            box-shadow: 0 0 5px rgba(0,115,170,0.3);
        }
    </style>';

    // Adiciona JS para mostrar/ocultar os campos conforme seleção
    echo '<script>
    (function($){
        function toggleTransportadoraFields() {
            var selected = $("input[name=\"shipping_method[0]\"]:checked").val();
            if(selected === "transportadora_xd") {
                $("#transportadora-xd-fields").show();
            } else {
                $("#transportadora-xd-fields").hide();
            }
        }
        $(document).on("change", "input[name=\"shipping_method[0]\"]", toggleTransportadoraFields);
        $(document).ready(toggleTransportadoraFields);
    })(jQuery);
    </script>';
}

add_action('woocommerce_checkout_update_order_meta', 'transportadora_xd_save_shipping_fields');
function transportadora_xd_save_shipping_fields($order_id) {
    if (isset($_POST['transportadora_nome'])) {
        update_post_meta($order_id, 'Transportadora (nome)', sanitize_text_field($_POST['transportadora_nome']));
    }
    if (isset($_POST['transportadora_telefone'])) {
        update_post_meta($order_id, 'Telefone da transportadora', sanitize_text_field($_POST['transportadora_telefone']));
    }
}

add_action('woocommerce_admin_order_data_after_shipping_address', 'transportadora_xd_display_admin_order_meta', 10, 1);
function transportadora_xd_display_admin_order_meta($order){
    $nome = get_post_meta($order->get_id(), 'Transportadora (nome)', true);
    $tel  = get_post_meta($order->get_id(), 'Telefone da transportadora', true);

    if ($nome || $tel) {
        echo '<p><strong>Transportadora:</strong><br>';
        if ($nome) echo 'Empresa: ' . esc_html($nome) . '<br>';
        if ($tel)  echo 'Telefone: ' . esc_html($tel);
        echo '</p>';
    }
}

// Validação obrigatória dos campos da transportadora
add_action('woocommerce_checkout_process', 'transportadora_xd_validate_shipping_fields');
function transportadora_xd_validate_shipping_fields() {
    $chosen_method = WC()->session->get('chosen_shipping_methods')[0] ?? '';
    if ($chosen_method === 'transportadora_xd') {
        if (empty($_POST['transportadora_nome'])) {
            wc_add_notice(__('Por favor, preencha o nome da transportadora.'), 'error');
        }
        if (empty($_POST['transportadora_telefone'])) {
            wc_add_notice(__('Por favor, preencha o telefone da transportadora.'), 'error');
        }
    }
}

// Exibir informações da transportadora nos detalhes do pedido do cliente
add_action('woocommerce_order_details_after_order_table', 'transportadora_xd_display_order_details_to_customer', 10, 1);
function transportadora_xd_display_order_details_to_customer($order) {
    $nome = get_post_meta($order->get_id(), 'Transportadora (nome)', true);
    $tel  = get_post_meta($order->get_id(), 'Telefone da transportadora', true);

    if ($nome || $tel) {
        echo '<section class="woocommerce-order-details transportadora-xd-order-details" style="margin-top:20px;">';
        echo '<h3>Dados da Transportadora</h3>';
        echo '<p>';
        if ($nome) echo '<strong>Empresa:</strong> ' . esc_html($nome) . '<br>';
        if ($tel)  echo '<strong>Telefone:</strong> ' . esc_html($tel);
        echo '</p>';
        echo '</section>';
    }
}
