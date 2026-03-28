<?php
/*
Plugin Name: XD Analytics
Plugin URI: https://xdplans.com/
Description: Integração simples com Google Analytics para sites XD Plans.
Version: 1.0
Author: XD Plans
Author URI: https://xdplans.com/
*/

if (!defined('ABSPATH')) {
    exit;
}

// Adiciona o menu no painel lateral
add_action('admin_menu', 'xd_analytics_menu');
function xd_analytics_menu() {
    add_menu_page(
        'XD Analytics',
        'XD Analytics',
        'manage_options',
        'xd-analytics',
        'xd_analytics_page',
        'dashicons-chart-area',
        3
    );
}

// Página de configuração
function xd_analytics_page() {
    if (isset($_POST['xd_analytics_uid'])) {
        update_option('xd_analytics_uid', sanitize_text_field($_POST['xd_analytics_uid']));
        echo '<div class="updated"><p>UID salvo com sucesso!</p></div>';
    }
    $uid = get_option('xd_analytics_uid', '');
    ?>
    <div class="wrap" style="font-family: Arial, sans-serif;">
        <h1 style="display:flex;align-items:center;gap:10px;">
            <img src="https://i.imgur.com/axzj2kK.png" alt="XD Plans" style="height:40px;">
            XD Analytics - Google Analytics
        </h1>
        <form method="post" style="margin-top:20px;">
            <label for="xd_analytics_uid"><strong>Google Analytics Measurement ID (G-XXXXXX):</strong></label>
            <input type="text" id="xd_analytics_uid" name="xd_analytics_uid" value="<?php echo esc_attr($uid); ?>" style="width:300px;margin-left:10px;">
            <button type="submit" class="button button-primary">Salvar</button>
        </form>
    </div>
    <?php
}

// Injeta o código do GA no front-end
add_action('wp_head', 'xd_analytics_inject_code');
function xd_analytics_inject_code() {
    $uid = get_option('xd_analytics_uid', '');
    if (!$uid) return;
    ?>
    <!-- XD Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($uid); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?php echo esc_attr($uid); ?>');
    </script>
    <!-- /XD Analytics -->
    <?php
}
?>
