<?php
/**
 * Plugin Name: Checkout One Step Simples
 * Description: Checkout simplificado com campos essenciais em ordem visual moderna.
 * Version: 1.3
 * Author: XD Plans
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', 'xd_enqueue_checkout_assets_simplificado' );
function xd_enqueue_checkout_assets_simplificado() {
    if (is_checkout()) {
        wp_enqueue_style( 'xd-checkout-style', plugin_dir_url(__FILE__) . 'checkout-style.css' );
    }
}

// Reorganiza campos do checkout
add_filter( 'woocommerce_checkout_fields', 'xd_checkout_fields_simplificado' );
function xd_checkout_fields_simplificado( $fields ) {
    $fields['billing'] = array();

    $fields['billing']['billing_cpf'] = array(
        'label' => 'CPF ou CNPJ',
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 1,
    );

    $fields['billing']['billing_email'] = array(
        'label' => 'Email',
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 2,
    );

    $fields['billing']['billing_whatsapp'] = array(
        'label' => 'WhatsApp',
        'required' => true,
        'class' => array('form-row-wide', 'kimtino-whatsapp-field'),
        'priority' => 2.5,
        'placeholder' => 'Ex: (99) 99999-9999',
    );

    $fields['billing']['billing_first_name'] = array(
        'label' => 'Nome',
        'required' => true,
        'class' => array('form-row-first'),
        'priority' => 3,
    );

    $fields['billing']['billing_last_name'] = array(
        'label' => 'Sobrenome',
        'required' => true,
        'class' => array('form-row-last'),
        'priority' => 4,
    );

    $fields['billing']['billing_birthdate'] = array(
        'label' => 'Data de Nascimento',
        'required' => false,
        'class' => array('form-row-first'),
        'priority' => 5,
    );

    $fields['billing']['billing_gender'] = array(
        'type'    => 'select',
        'label'   => 'Sexo',
        'required'=> false,
        'class'   => array('form-row-last'),
        'options' => array(
            ''       => 'Selecione...',
            'male'   => 'Masculino',
            'female' => 'Feminino',
            'other'  => 'Outro',
        ),
        'priority' => 6,
    );

    $fields['billing']['billing_postcode'] = array(
        'label' => 'CEP',
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 7,
    );

    $fields['billing']['billing_address_1'] = array(
        'label' => 'Endereço',
        'required' => true,
        'class' => array('form-row-wide'),
        'priority' => 8,
    );

    $fields['billing']['billing_neighborhood'] = array(
        'label' => 'Bairro',
        'required' => true,
        'class' => array('form-row-first'),
        'priority' => 10,
    );

    $fields['billing']['billing_city'] = array(
        'label' => 'Cidade',
        'required' => true,
        'class' => array('form-row-first'),
        'priority' => 11,
    );

    $fields['billing']['billing_country'] = array(
        'type'     => 'select',
        'label'    => 'País',
        'required' => true,
        'class'    => array('form-row-wide'),
        'options'  => array('BR' => 'Brasil'),
        'default'  => 'BR',
        'priority' => 0,
    );

    $fields['billing']['billing_state'] = array(
        'type'    => 'select',
        'label'   => 'Estado',
        'required' => true,
        'class'   => array('form-row-wide'),
        'options' => array(
            ''   => 'Selecione...',
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins',
        ),
        'priority' => 12,
    );

    return $fields;
}

// Bloquear edição dos campos principais se logado
add_filter('woocommerce_checkout_get_value', 'xd_checkout_readonly_if_logged', 10, 2);
function xd_checkout_readonly_if_logged($value, $input) {
    if (is_user_logged_in()) {
        $readonly_fields = [
            'billing_cpf',
            'billing_email',
            'billing_first_name',
            'billing_last_name',
        ];
        if (in_array($input, $readonly_fields)) {
            return get_user_meta(get_current_user_id(), $input, true);
        }
    }
    return $value;
}

add_filter('woocommerce_checkout_fields', 'xd_make_fields_readonly_logged', 20);
function xd_make_fields_readonly_logged($fields) {
    if (is_user_logged_in()) {
        $readonly_fields = [
            'billing_cpf',
            'billing_email',
            'billing_first_name',
            'billing_last_name',
        ];
        foreach ($readonly_fields as $field) {
            if (isset($fields['billing'][$field])) {
                $fields['billing'][$field]['custom_attributes']['readonly'] = 'readonly';
            }
        }
    }
    return $fields;
}

// Remover cards de cupom e login do topo
remove_action('woocommerce_before_checkout_form', 'kimtino_cards_row', 2);

// Mensagem de boas-vindas centralizada (apenas se não logado)
add_action('woocommerce_before_checkout_form', function() {
    if (is_user_logged_in()) return;
    echo '<div class="kimtino-checkout-welcome" style="max-width: 540px; margin: 0 auto 28px auto; text-align: center;">
        <h2 style="font-size:2.2em; color:#cc0000; margin-bottom:10px;">🚀 Vamos finalizar sua compra?</h2>
        <p style="font-size:1.15em; color:#222; margin-bottom:8px;">Preencha seus dados abaixo para garantir seus produtos Kimtino com segurança e rapidez.</p>
        <div class="kimtino-login-msg" style="font-size:1.08em; color:#0056b3; font-weight:bold;">
            Já é cliente da Kimtino? <a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" style="color:#ff4d4d; text-decoration:underline;">Clique aqui para fazer login e agilize seu checkout!</a>
        </div>
    </div>';
}, 3);

// Mensagem de segurança/confiança acima do botão
add_action('woocommerce_review_order_before_submit', function() {
    echo '<div class="kimtino-seguro-msg" style="margin-bottom:12px; text-align:center;">
        <span style="color:#27ae60; font-size:1.2em;">🔒 Compra 100% segura | Entrega garantida Kimtino</span>
    </div>';
});

// Personalizar texto do botão de finalizar pedido
add_filter('woocommerce_order_button_text', function() {
    return 'Finalizar minha compra Kimtino';
});

// Adiciona máscara simples para CPF/CNPJ no checkout
add_action('wp_footer', function() {
    if (!is_checkout()) return;
    ?>
    <style>
    .kimtino-checkout-welcome {
        max-width: 540px;
        margin: 0 auto 28px auto;
        text-align: center;
        background: #fff8f0;
        border-radius: 16px;
        box-shadow: 0 2px 12px #0001;
        padding: 24px 18px 18px 18px;
    }
    .woocommerce-billing-fields {
        border: 1px solid #f1f1f1;
        padding: 20px;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 2px 12px #0001;
        margin-bottom: 18px;
    }
    .kimtino-login-msg a {
        color: #ff4d4d;
        text-decoration: underline;
        font-weight: bold;
    }
    .kimtino-seguro-msg {
        margin-bottom:12px;
        text-align:center;
        background: #eafaf1;
        border-radius: 12px;
        padding: 10px 0;
        box-shadow: 0 1px 6px #0001;
    }
    .kimtino-whatsapp-field input {
        padding-left: 36px !important;
        background: url('https://cdn-icons-png.flaticon.com/512/733/733585.png') no-repeat 8px center/22px 22px #fff;
        border-radius: 8px;
        border: 1.5px solid #25d366;
        box-shadow: 0 1px 6px #0001;
    }
    .form-row-wide input, .form-row-first input, .form-row-last input {
        border-radius: 8px;
        border: 1.5px solid #ccc;
        box-shadow: 0 1px 6px #0001;
        padding: 10px;
        font-size: 1.08em;
    }
    label[for*="billing_whatsapp"]:before {
        content: '📱 ';
    }
    label[for*="billing_cpf"]:before {
        content: '🆔 ';
    }
    label[for*="billing_email"]:before {
        content: '✉️ ';
    }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var cpfInput = document.querySelector('input[name="billing_cpf"]');
        if (cpfInput) {
            cpfInput.setAttribute('maxlength', '18');
            cpfInput.addEventListener('input', function(e) {
                let v = cpfInput.value.replace(/\D/g, '');
                if (v.length <= 11) {
                    v = v.replace(/(\d{3})(\d)/, '$1.$2');
                    v = v.replace(/(\d{3})(\d)/, '$1.$2');
                    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                } else {
                    v = v.replace(/(\d{2})(\d)/, '$1.$2');
                    v = v.replace(/(\d{3})(\d)/, '$1.$2');
                    v = v.replace(/(\d{3})(\d)/, '$1/$2');
                    v = v.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
                }
                cpfInput.value = v;
            });
        }
        var whatsappInput = document.querySelector('input[name="billing_whatsapp"]');
        if (whatsappInput) {
            whatsappInput.setAttribute('maxlength', '15');
            whatsappInput.addEventListener('input', function(e) {
                let v = whatsappInput.value.replace(/\D/g, '');
                v = v.replace(/(\d{2})(\d)/, '($1) $2');
                v = v.replace(/(\d{5})(\d)/, '$1-$2');
                whatsappInput.value = v;
            });
        }
    });
    </script>
    <?php
});
