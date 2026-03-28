jQuery(document).ready(function($) {
  let subtotalText = $('.cart-subtotal .woocommerce-Price-amount bdi, .order-total .woocommerce-Price-amount bdi').first().text();
  let subtotal = parseFloat(subtotalText.replace(/[^0-9,]/g, '').replace(',', '.'));
  let min = parseFloat(xd_cart.min_order);

  // Agrupar tudo dentro do paper
  $('.woocommerce-cart-form, .cart-collaterals').wrapAll('<div class="kimtino-cart-wrapper"></div>');

  // ...removido aviso duplicado via JS...
});
