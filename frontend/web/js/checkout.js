/**
 * FREUDELADEN.DE Checkout JavaScript
 * SEO-friendly external script for checkout functionality
 */

$(document).ready(function() {
    console.log('Checkout page loaded');

    // Payment method change handler
    $('input[name="payment_method"]').change(function() {
        const method = $(this).val();
        console.log('Payment method changed to:', method);
        
        // Update payment info display
        $('.payment-info').hide();
        $('#payment-info-' + method).show();
    });

    // Form validation
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
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
                alert('Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen.');
                return false;
            }
            
            // Show loading state
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                const spinner = document.createElement('i');
                spinner.className = 'fas fa-spinner fa-spin';
                submitButton.innerHTML = '';
                submitButton.appendChild(spinner);
                submitButton.appendChild(document.createTextNode(' Verarbeitung...'));
                submitButton.disabled = true;
            }
            
            form.classList.add('was-validated');
            return true; // Allow normal form submission
        });
    }
});
