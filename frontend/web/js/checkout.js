/**
 * FREUDELADEN.DE - Checkout Form Functionality
 * External JavaScript for better SEO and security
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Checkout page loaded');
    
    // Toggle billing address section
    initBillingAddressToggle();
    
    // Initialize form validation
    initCheckoutFormValidation();
    
    // Initialize payment method selection
    initPaymentMethodSelection();
    
    // Initialize real-time validation
    initRealTimeValidation();
});

/**
 * Initialize billing address toggle functionality
 */
function initBillingAddressToggle() {
    const sameBillingCheckbox = document.getElementById('same-billing-address');
    const billingSection = document.getElementById('billing-address-section');
    const billingTextarea = document.querySelector('textarea[name="Order[billing_address]"]');
    
    if (sameBillingCheckbox) {
        sameBillingCheckbox.addEventListener('change', function() {
            if (this.checked) {
                if (billingSection) billingSection.style.display = 'none';
                if (billingTextarea) billingTextarea.removeAttribute('required');
            } else {
                if (billingSection) billingSection.style.display = 'block';
                if (billingTextarea) billingTextarea.setAttribute('required', 'required');
            }
        });
    }
}

/**
 * Initialize checkout form validation
 */
function initCheckoutFormValidation() {
    const checkoutForm = document.getElementById('checkout-form');
    if (!checkoutForm) return;
    
    checkoutForm.addEventListener('submit', function(e) {
        const form = this;
        
        // Check form validity
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
            return false;
        }
        
        // Check terms acceptance
        const acceptTerms = document.getElementById('accept-terms');
        if (!acceptTerms || !acceptTerms.checked) {
            e.preventDefault();
            showErrorMessage('Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen.');
            return false;
        }
        
        // Show loading state
        showFormLoadingState(form);
        
        form.classList.add('was-validated');
        return true; // Allow normal form submission
    });
}

/**
 * Initialize payment method selection
 */
function initPaymentMethodSelection() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    
    paymentMethods.forEach(function(method) {
        method.addEventListener('change', function() {
            console.log('Payment method changed to:', this.value);
            // Update UI based on selected payment method
            updatePaymentMethodDisplay(this.value);
        });
    });
}

/**
 * Show form loading state
 * @param {HTMLFormElement} form 
 */
function showFormLoadingState(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
        const spinner = document.createElement('i');
        spinner.className = 'fas fa-spinner fa-spin';
        submitButton.innerHTML = '';
        submitButton.appendChild(spinner);
        submitButton.appendChild(document.createTextNode(' Verarbeitung...'));
        submitButton.disabled = true;
    }
}

/**
 * Show error message to user
 * @param {string} message 
 */
function showErrorMessage(message) {
    // Create or update error alert
    let errorAlert = document.getElementById('checkout-error-alert');
    if (!errorAlert) {
        errorAlert = document.createElement('div');
        errorAlert.id = 'checkout-error-alert';
        errorAlert.className = 'alert alert-danger alert-dismissible fade show';
        errorAlert.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="checkout-error-message"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const form = document.getElementById('checkout-form');
        if (form) {
            form.insertBefore(errorAlert, form.firstChild);
        }
    }
    
    const messageElement = document.getElementById('checkout-error-message');
    if (messageElement) {
        messageElement.textContent = message;
    }
    
    // Scroll to error
    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

/**
 * Update payment method display
 * @param {string} method 
 */
function updatePaymentMethodDisplay(method) {
    // Remove previous selection styles
    document.querySelectorAll('.payment-methods .form-check').forEach(function(check) {
        check.classList.remove('selected');
    });
    
    // Add selection style to current method
    const selectedMethod = document.getElementById('payment_' + method);
    if (selectedMethod) {
        const parentCheck = selectedMethod.closest('.form-check');
        if (parentCheck) {
            parentCheck.classList.add('selected');
        }
    }
    
    // Show payment-specific information
    showPaymentMethodInfo(method);
}

/**
 * Show payment method specific information
 * @param {string} method 
 */
function showPaymentMethodInfo(method) {
    // Hide all payment info sections
    document.querySelectorAll('.payment-info').forEach(function(info) {
        info.style.display = 'none';
    });
    
    // Show relevant payment info
    const paymentInfo = document.getElementById('payment-info-' + method);
    if (paymentInfo) {
        paymentInfo.style.display = 'block';
    }
}

/**
 * Validate form fields in real-time
 */
function initRealTimeValidation() {
    const form = document.getElementById('checkout-form');
    if (!form) return;
    
    const fields = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    fields.forEach(function(field) {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
}

/**
 * Validate individual field
 * @param {HTMLElement} field 
 */
function validateField(field) {
    const isValid = field.checkValidity();
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
    }
    
    return isValid;
}
            
            form.classList.add('was-validated');
            return true; // Allow normal form submission
        });
    }
});
