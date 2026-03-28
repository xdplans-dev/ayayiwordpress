<?php
function xdmm_options_page() {
    add_menu_page(
        'XD Mobile Menu',
        'XD Mobile Menu',
        'manage_options',
        'xd-mobile-menu',
        'xdmm_render_admin_page',
        'dashicons-smartphone',
        100
    );
}
add_action('admin_menu', 'xdmm_options_page');

function xdmm_render_admin_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        update_option('xdmm_enabled', isset($_POST['xdmm_enabled']) ? '1' : '0');

        $buttons = [];
        for ($i = 0; $i < 5; $i++) {
            $buttons[] = [
                'title' => sanitize_text_field($_POST['title'][$i]),
                'url' => esc_url_raw($_POST['url'][$i]),
                'icon' => esc_url_raw($_POST['icon'][$i]),
            ];
        }
        update_option('xdmm_buttons', $buttons);
        echo '<div class="updated"><p>Configurações salvas!</p></div>';
    }

    $enabled = get_option('xdmm_enabled', '1');
    $buttons = get_option('xdmm_buttons', []);
    if (!is_array($buttons)) $buttons = [];

    echo '<div class="wrap"><h1>XD Mobile Menu</h1>';
    echo '<form method="post"><label><input type="checkbox" name="xdmm_enabled" value="1"' . checked('1', $enabled, false) . '> Ativar Menu Mobile</label>';
    echo '<table class="form-table"><tbody>';
    for ($i = 0; $i < 5; $i++) {
        $title = isset($buttons[$i]['title']) ? esc_attr($buttons[$i]['title']) : '';
        $url = isset($buttons[$i]['url']) ? esc_url($buttons[$i]['url']) : '';
        $icon = isset($buttons[$i]['icon']) ? esc_url($buttons[$i]['icon']) : '';
        echo '<tr><th>Botão ' . ($i+1) . '</th><td>';
        echo 'Título: <input type="text" name="title[]" value="' . $title . '" style="width:100px" /> ';
        echo 'URL: <input type="url" name="url[]" value="' . $url . '" style="width:300px" /> ';
        echo 'Ícone: <input type="text" class="icon-field" name="icon[]" value="' . $icon . '" style="width:300px" /> ';
        if ($icon) echo '<br><img src="' . $icon . '" style="width:40px;height:40px;margin-top:5px;border-radius:50%;">';
        echo '</td></tr>';
    }
    echo '</tbody></table><p><input type="submit" class="button button-primary" value="Salvar configurações" /></p>';
    echo '<p style="margin-top:20px;">Desenvolvido com ❤️ por XD Plans & David Xavier</p>';
    echo '</form></div>';

    echo "<script>
    document.querySelectorAll('.icon-field').forEach(field => {
        field.addEventListener('click', function() {
            const input = this;
            wp.media.editor.send.attachment = function(props, attachment) {
                input.value = attachment.url;
            };
            wp.media.editor.open();
        });
    });
    </script>";
}
?>
