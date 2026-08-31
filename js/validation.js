// js/validation.js - client-side validation for the appointment booking form.
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('appointmentForm');
    if (!form) return;

    function showError(fieldId, msg) {
        var el = document.getElementById(fieldId + 'Error');
        if (el) { el.textContent = msg; el.style.display = msg ? 'block' : 'none'; }
    }

    function validate() {
        var valid = true;

        var name = document.getElementById('name').value.trim();
        if (name === '' || !/^[A-Za-z ]{2,50}$/.test(name)) {
            showError('name', 'Please enter a valid name (letters only).');
            valid = false;
        } else { showError('name', ''); }

        var age = document.getElementById('age').value;
        if (age === '' || age < 1 || age > 120) {
            showError('age', 'Please enter a valid age (1-120).');
            valid = false;
        } else { showError('age', ''); }

        var gender = form.querySelector('input[name="gender"]:checked');
        if (!gender) {
            showError('gender', 'Please select a gender.');
            valid = false;
        } else { showError('gender', ''); }

        var phone = document.getElementById('phone').value.trim();
        if (!/^[0-9]{10}$/.test(phone)) {
            showError('phone', 'Please enter a valid 10-digit phone number.');
            valid = false;
        } else { showError('phone', ''); }

        var email = document.getElementById('email').value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email', 'Please enter a valid email address.');
            valid = false;
        } else { showError('email', ''); }

        var doctor = document.getElementById('doctor_id').value;
        if (!doctor) {
            showError('doctor_id', 'Please select a doctor.');
            valid = false;
        } else { showError('doctor_id', ''); }

        var date = document.getElementById('appointment_date').value;
        if (!date) {
            showError('appointment_date', 'Please select a date.');
            valid = false;
        } else {
            var today = new Date(); today.setHours(0, 0, 0, 0);
            var chosen = new Date(date);
            if (chosen < today) {
                showError('appointment_date', 'Date cannot be in the past.');
                valid = false;
            } else { showError('appointment_date', ''); }
        }

        var timeChecked = form.querySelector('input[name="appointment_time"]:checked');
        if (!timeChecked) {
            showError('appointment_time', 'Please select a time slot.');
            valid = false;
        } else { showError('appointment_time', ''); }

        return valid;
    }

    form.addEventListener('submit', function (e) {
        if (!validate()) {
            e.preventDefault();
        }
    });

    // Set min date to today on the date input.
    var dateInput = document.getElementById('appointment_date');
    if (dateInput) {
        var todayStr = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', todayStr);
    }
});
