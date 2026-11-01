$(document).ready(function() {

    const nameInput = $('#name');
    const emailInput = $('#email');
    const phoneInput = $('#phone');
    const messageInput = $('#message');
    const termsInput = $('#terms');
    const serviceInput = $('#service');
    const dateInput = $('#project_date');

    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const phoneRegex = /^[\+]?[(]?[0-9]{2,3}[)]?[-\s\.]?[0-9]{2,3}[-\s\.]?[0-9]{3,4}[-\s\.]?[0-9]{3,4}$/;

    // Zakljucaj sva polja osim prvog
    function lockFieldsAfter(unlockedInput) {
        const order = [nameInput, emailInput, phoneInput, messageInput, termsInput, serviceInput, dateInput];
        const idx = order.indexOf(unlockedInput);
        order.forEach(function(field, i) {
            if (i > idx) {
                field.prop('disabled', true);
                field.closest('.mb-3, .mb-4').css('opacity', '0.5');
            } else {
                field.prop('disabled', false);
                field.closest('.mb-3, .mb-4').css('opacity', '1');
            }
        });
    }

    lockFieldsAfter(nameInput);

    // IME
    nameInput.on('input', function() { validateName(); });
    nameInput.on('blur', function() {
        if (validateName()) {
            lockFieldsAfter(emailInput);
            emailInput.focus();
        }
    });

    // EMAIL
    emailInput.on('input', function() { validateEmail(); });
    emailInput.on('blur', function() {
        if (validateEmail()) {
            lockFieldsAfter(phoneInput);
        }
    });

    // TELEFON
    phoneInput.on('blur', function() {
        validatePhone();
        lockFieldsAfter(messageInput);
    });

    // PORUKA
    messageInput.on('input', function() { validateMessage(); });
    messageInput.on('blur', function() {
        if (validateMessage()) {
            lockFieldsAfter(termsInput);
        }
    });

    // TERMS
    termsInput.on('change', function() {
        if ($(this).is(':checked')) {
            lockFieldsAfter(serviceInput);
        }
    });

    // SERVICE
    serviceInput.on('change', function() {
        if ($(this).val() !== '') {
            lockFieldsAfter(dateInput);
        }
    });

    // VALIDACIONE FUNKCIJE
    function validateName() {
        const val = nameInput.val().trim();
        if (val === '' || val.length < 3) {
            showError(nameInput, 'Name must be at least 3 characters');
            return false;
        }
        showSuccess(nameInput);
        return true;
    }

    function validateEmail() {
        const val = emailInput.val().trim();
        if (val === '' || !emailRegex.test(val)) {
            showError(emailInput, 'Please enter a valid email address');
            return false;
        }
        showSuccess(emailInput);
        return true;
    }

    function validatePhone() {
        const val = phoneInput.val().trim();
        if (val !== '' && !phoneRegex.test(val)) {
            showError(phoneInput, 'Please enter a valid phone number');
            return false;
        }
        showSuccess(phoneInput);
        return true;
    }

    function validateMessage() {
        const val = messageInput.val().trim();
        if (val === '' || val.length < 10) {
            showError(messageInput, 'Message must be at least 10 characters');
            return false;
        }
        showSuccess(messageInput);
        return true;
    }

    function showError(input, message) {
        input.removeClass('success').addClass('error');
        input.closest('.mb-3, .mb-4').find('.error-message').text(message).addClass('show');
    }

    function showSuccess(input) {
        input.removeClass('error').addClass('success');
        input.closest('.mb-3, .mb-4').find('.error-message').text('').removeClass('show');
    }

});