// TODO add there action for adding payWithCard discount
var body = $('body');
var check_container = $("#payment_type-contaienr");
var cart_table = $('#cart-table-container');
body.on('keyup', '#card-value', function(e) {if (e.keyCode == 13) discount('check', 'card')});
body.on('keyup', '#coupon-value', function(e) {if (e.keyCode == 13) discount('check', 'coupon')});
body.on('click', '#btn-card', function() {discount('check', 'card')});
body.on('click', '#btn-coupon', function() {discount('check', 'coupon')});
body.on('click', '#btn-remove-card', function() {discount('remove', 'card')});
body.on('click', '#btn-remove-coupon', function() {discount('remove', 'coupon')});
body.on('change', '#startcheckout-card_only', function() {
    ajaxit('post', '/checkout/ajax/refresh-checkout-widget', check_container, {val: $(this).is(':checked')})
        .done(function (data) {updateCheckNCart(data)});
});
body.on('change', '.cart-table__quantity-changer', function() {
    var quantity = parseInt($(this).val()),
        box = $(this).parents('.vm').next();
    ajaxit('post', '/checkout/ajax/change-quantity', check_container,{
        sku: $(this).data('sku'),
        quantity: quantity
    }).done(function (data) {
        check_container.html(data);
        box.next().children('span').text(parseInt(box.children('span').text()) * quantity)
    });
});

function discount(action, type) {
    var data = null;
    if (action === 'check') {
        data = {};
        data[type] = $('#' + type + '-value').val();
    }
    ajaxit('post', '/checkout/ajax/'+ action + '-' + type, check_container, data)
        .done(function (r) {updateCheckNCart(r)});
}

function refreshCheckoutWidget() {
    ajaxit('post', '/checkout/ajax/refresh-checkout-widget', check_container, null)
        .done(function (r) {updateCheckNCart(r)});
}

function updateCheckNCart(data) {
    var obj = JSON.parse(data);
    check_container.html(obj.check);
    cart_table.html(obj.list);
    var card_value = $("#card-value");
    // if (card_value.length > 0) {
    //     card_value.inputmask({"mask":"99999-99999"})
    // }
}
