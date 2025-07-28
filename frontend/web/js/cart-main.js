/**
 * FREUDELADEN.DE Cart JavaScript
 * SEO-friendly external script for cart functionality
 */

$(document).ready(function() {
    console.log('Cart page loaded');
    
    // Update cart totals
    function updateCartTotals() {
        let subtotal = 0;
        $('.cart-item').each(function() {
            const price = parseFloat($(this).find('.price').text().replace('€', '').replace(',', '.'));
            const quantity = parseInt($(this).find('.quantity-input').val());
            const itemTotal = price * quantity;
            
            $(this).find('.subtotal').text('€' + itemTotal.toFixed(2).replace('.', ','));
            subtotal += itemTotal;
        });
        
        const tax = subtotal * 0.19;
        const shipping = 5.99;
        const total = subtotal + tax + shipping;
        
        $('#cart-subtotal').text('€' + subtotal.toFixed(2).replace('.', ','));
        $('#cart-tax').text('€' + tax.toFixed(2).replace('.', ','));
        $('#cart-total').text('€' + total.toFixed(2).replace('.', ','));
    }

    // Remove item function
    function removeFromCart(removeButton) {
        const itemId = removeButton.data('id');
        console.log('Removing item with ID:', itemId);
        
        if (!itemId) {
            alert('Item ID nicht gefunden');
            return;
        }
        
        // Get CSRF data from meta tags
        const csrfParam = $('meta[name="csrf-param"]').attr('content');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        const postData = {
            item_id: itemId
        };
        postData[csrfParam] = csrfToken;
        
        $.ajax({
            url: '/cart/remove',
            type: 'POST',
            data: postData,
            success: function(response) {
                console.log('Remove response:', response);
                if (response.success) {
                    removeButton.closest('.cart-item').fadeOut(300, function() {
                        $(this).remove();
                        updateCartTotals();
                        
                        // Check if cart is empty
                        if ($('.cart-item').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    alert(response.message || 'Fehler beim Entfernen des Artikels');
                }
            },
            error: function(xhr, status, error) {
                console.error('Remove error:', xhr.responseText);
                alert('Fehler beim Entfernen des Artikels');
            }
        });
    }

    // Update cart quantity function
    function updateCartQuantity(cartItem, newQuantity) {
        const itemId = cartItem.find('.remove-item').data('id');
        console.log('Updating quantity for item:', itemId, 'to:', newQuantity);
        
        if (!itemId) {
            alert('Item ID nicht gefunden');
            return;
        }
        
        // Get CSRF data from meta tags
        const csrfParam = $('meta[name="csrf-param"]').attr('content');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        const postData = {
            item_id: itemId,
            quantity: newQuantity
        };
        postData[csrfParam] = csrfToken;
        
        $.ajax({
            url: '/cart/update',
            type: 'POST',
            data: postData,
            success: function(response) {
                console.log('Update response:', response);
                if (response.success) {
                    updateCartTotals();
                } else {
                    alert(response.message || 'Fehler beim Aktualisieren der Menge');
                    // Revert the quantity change
                    cartItem.find('.quantity-input').val(response.currentQuantity || 1);
                    updateCartTotals();
                }
            },
            error: function(xhr, status, error) {
                console.error('Update error:', xhr.responseText);
                alert('Fehler beim Aktualisieren der Menge');
                // Revert to previous value
                updateCartTotals();
            }
        });
    }

    // Quantity controls
    $('.quantity-increase').click(function() {
        const input = $(this).siblings('.quantity-input');
        const cartItem = $(this).closest('.cart-item');
        const current = parseInt(input.val());
        if (current < 99) {
            const newQuantity = current + 1;
            input.val(newQuantity);
            updateCartQuantity(cartItem, newQuantity);
        }
    });

    $('.quantity-decrease').click(function() {
        const input = $(this).siblings('.quantity-input');
        const cartItem = $(this).closest('.cart-item');
        const current = parseInt(input.val());
        if (current > 1) {
            const newQuantity = current - 1;
            input.val(newQuantity);
            updateCartQuantity(cartItem, newQuantity);
            updateCartQuantity(cartItem, newQuantity);
        }
    });

    $('.quantity-input').on('input', function() {
        const cartItem = $(this).closest('.cart-item');
        const value = parseInt($(this).val());
        if (value >= 1 && value <= 99) {
            updateCartQuantity(cartItem, value);
        }
    });

    // Remove item click event
    $('.remove-item').click(function() {
        console.log('Remove button clicked');
        const itemId = $(this).data('id');
        console.log('Item ID:', itemId);
        
        if (confirm('Sind Sie sicher, dass Sie diesen Artikel entfernen möchten?')) {
            removeFromCart($(this));
        }
    });

    // Initialize totals
    updateCartTotals();
});
