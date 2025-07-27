/**
 * FREUDELADEN.DE Cart JavaScript
 * Handles all cart operations with proper CSRF token support
 */

// Global cart object
window.FreudeladenCart = {
    // Get CSRF token from meta tag
    getCsrfToken: function() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    },
    
    // Get CSRF param name
    getCsrfParam: function() {
        const param = document.querySelector('meta[name="csrf-param"]');
        return param ? param.getAttribute('content') : '_csrf';
    },
    
    // Show notification
    showNotification: function(message, type = 'success') {
        // Remove existing notifications
        const existing = document.querySelectorAll('.cart-notification');
        existing.forEach(el => el.remove());
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} cart-notification position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    },
    
    // Add product to cart
    addToCart: function(productId, variantId = null, quantity = 1) {
        const data = {
            product_id: productId,
            quantity: quantity
        };
        
        if (variantId) {
            data.variant_id = variantId;
        }
        
        // Add CSRF token
        data[this.getCsrfParam()] = this.getCsrfToken();
        
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': this.getCsrfToken()
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Produkt wurde zum Warenkorb hinzugefügt!', 'success');
                this.updateCartBadge(data.cartCount);
                
                // Update cart sidebar if exists
                if (typeof this.updateCartSidebar === 'function') {
                    this.updateCartSidebar();
                }
            } else {
                this.showNotification(data.message || 'Fehler beim Hinzufügen zum Warenkorb', 'danger');
            }
        })
        .catch(error => {
            console.error('Cart error:', error);
            this.showNotification('Ein Fehler ist aufgetreten', 'danger');
        });
    },
    
    // Update cart item quantity
    updateCart: function(productId, variantId = null, quantity) {
        const data = {
            product_id: productId,
            quantity: quantity
        };
        
        if (variantId) {
            data.variant_id = variantId;
        }
        
        // Add CSRF token
        data[this.getCsrfParam()] = this.getCsrfToken();
        
        fetch('/cart/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': this.getCsrfToken()
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Warenkorb wurde aktualisiert', 'info');
                this.updateCartBadge(data.cartCount);
                
                // Reload cart page if we're on it
                if (window.location.pathname === '/cart' || window.location.pathname === '/cart/index') {
                    location.reload();
                }
            } else {
                this.showNotification(data.message || 'Fehler beim Aktualisieren', 'danger');
            }
        })
        .catch(error => {
            console.error('Update error:', error);
            this.showNotification('Ein Fehler ist aufgetreten', 'danger');
        });
    },
    
    // Remove item from cart
    removeFromCart: function(productId, variantId = null) {
        const data = {
            product_id: productId
        };
        
        if (variantId) {
            data.variant_id = variantId;
        }
        
        // Add CSRF token
        data[this.getCsrfParam()] = this.getCsrfToken();
        
        fetch('/cart/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': this.getCsrfToken()
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Artikel wurde entfernt', 'info');
                this.updateCartBadge(data.cartCount);
                
                // Reload cart page if we're on it
                if (window.location.pathname === '/cart' || window.location.pathname === '/cart/index') {
                    location.reload();
                }
            } else {
                this.showNotification(data.message || 'Fehler beim Entfernen', 'danger');
            }
        })
        .catch(error => {
            console.error('Remove error:', error);
            this.showNotification('Ein Fehler ist aufgetreten', 'danger');
        });
    },
    
    // Clear entire cart
    clearCart: function() {
        if (!confirm('Möchten Sie wirklich alle Artikel aus dem Warenkorb entfernen?')) {
            return;
        }
        
        const data = {};
        data[this.getCsrfParam()] = this.getCsrfToken();
        
        fetch('/cart/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': this.getCsrfToken()
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Warenkorb wurde geleert', 'info');
                this.updateCartBadge(0);
                
                // Reload cart page if we're on it
                if (window.location.pathname === '/cart' || window.location.pathname === '/cart/index') {
                    location.reload();
                }
            } else {
                this.showNotification(data.message || 'Fehler beim Leeren des Warenkorbs', 'danger');
            }
        })
        .catch(error => {
            console.error('Clear cart error:', error);
            this.showNotification('Ein Fehler ist aufgetreten', 'danger');
        });
    },
    
    // Update cart badge in navigation
    updateCartBadge: function(count) {
        const badge = document.querySelector('.cart-count-badge');
        const cartIcon = document.querySelector('.cart-icon-count');
        
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline' : 'none';
        }
        
        if (cartIcon) {
            cartIcon.textContent = count;
        }
        
        // Update cart link text if exists
        const cartLinks = document.querySelectorAll('.cart-link-text');
        cartLinks.forEach(link => {
            link.textContent = `Warenkorb (${count})`;
        });
    },
    
    // Initialize cart functionality
    init: function() {
        // Bind add to cart buttons
        document.addEventListener('click', (e) => {
            // Add to cart buttons
            if (e.target.matches('#add-to-cart-btn') || e.target.closest('#add-to-cart-btn')) {
                e.preventDefault();
                const btn = e.target.matches('#add-to-cart-btn') ? e.target : e.target.closest('#add-to-cart-btn');
                const productId = btn.getAttribute('data-product-id');
                const variantId = btn.getAttribute('data-variant-id');
                const quantityInput = document.getElementById('quantity');
                const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
                
                this.addToCart(productId, variantId, quantity);
            }
            
            // Quick add buttons
            if (e.target.matches('.quick-add-btn') || e.target.closest('.quick-add-btn')) {
                e.preventDefault();
                const btn = e.target.matches('.quick-add-btn') ? e.target : e.target.closest('.quick-add-btn');
                const productId = btn.getAttribute('data-product-id');
                const variantId = btn.getAttribute('data-variant-id');
                
                this.addToCart(productId, variantId, 1);
            }
            
            // Remove from cart buttons
            if (e.target.matches('.remove-from-cart') || e.target.closest('.remove-from-cart')) {
                e.preventDefault();
                const btn = e.target.matches('.remove-from-cart') ? e.target : e.target.closest('.remove-from-cart');
                const productId = btn.getAttribute('data-product-id');
                const variantId = btn.getAttribute('data-variant-id');
                
                this.removeFromCart(productId, variantId);
            }
            
            // Clear cart button
            if (e.target.matches('.clear-cart') || e.target.closest('.clear-cart')) {
                e.preventDefault();
                this.clearCart();
            }
        });
        
        // Bind quantity change events
        document.addEventListener('change', (e) => {
            if (e.target.matches('.cart-quantity-input')) {
                const input = e.target;
                const productId = input.getAttribute('data-product-id');
                const variantId = input.getAttribute('data-variant-id');
                const quantity = parseInt(input.value);
                
                if (quantity > 0) {
                    this.updateCart(productId, variantId, quantity);
                } else {
                    this.removeFromCart(productId, variantId);
                }
            }
        });
        
        // Load initial cart count
        this.loadCartCount();
    },
    
    // Load cart count from server
    loadCartCount: function() {
        fetch('/cart/count', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateCartBadge(data.count);
            }
        })
        .catch(error => {
            console.error('Error loading cart count:', error);
        });
    }
};

// Global convenience functions
window.addToCart = function(productId, variantId, quantity) {
    return window.FreudeladenCart.addToCart(productId, variantId, quantity);
};

window.updateCart = function(productId, variantId, quantity) {
    return window.FreudeladenCart.updateCart(productId, variantId, quantity);
};

window.removeFromCart = function(productId, variantId) {
    return window.FreudeladenCart.removeFromCart(productId, variantId);
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.FreudeladenCart.init();
});

// Export for module systems if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.FreudeladenCart;
}
