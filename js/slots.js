// js/slots.js - fetches live slot availability for the selected doctor + date
// on the booking and reschedule forms, and disables already-booked chips.
document.addEventListener('DOMContentLoaded', function () {
    var doctorSelect = document.getElementById('doctor_id');
    var dateInput = document.getElementById('appointment_date');
    var slotGrid = document.getElementById('slotGrid');
    var slotHint = document.getElementById('slotHint');
    var feeNote = document.getElementById('feeNote');

    if (dateInput) {
        var todayStr = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', todayStr);
    }

    if (doctorSelect && feeNote) {
        var updateFee = function () {
            var opt = doctorSelect.options[doctorSelect.selectedIndex];
            var fee = opt ? opt.getAttribute('data-fee') : '';
            feeNote.textContent = fee ? ('Consultation fee: ₹' + fee + ' (payable at the hospital).') : '';
        };
        doctorSelect.addEventListener('change', updateFee);
        updateFee();
    }

    if (!slotGrid) return;

    function refreshSlots() {
        var doctorId = doctorSelect ? doctorSelect.value : '';
        var date = dateInput ? dateInput.value : '';
        var chips = slotGrid.querySelectorAll('.slot-radio');

        chips.forEach(function (radio) {
            radio.disabled = false;
            radio.closest ? null : null;
        });
        slotGrid.querySelectorAll('.slot-chip').forEach(function (chip) {
            chip.classList.remove('slot-taken');
        });

        if (!doctorId || !date) {
            if (slotHint) slotHint.textContent = 'Choose a doctor and date to see live availability.';
            return;
        }
        if (slotHint) slotHint.textContent = 'Checking availability...';

        fetch('slots.php?doctor_id=' + encodeURIComponent(doctorId) + '&date=' + encodeURIComponent(date))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var taken = data.taken || [];
                var anyTaken = false;
                chips.forEach(function (radio) {
                    var label = slotGrid.querySelector('label[for="' + radio.id + '"]');
                    if (taken.indexOf(radio.value) !== -1) {
                        radio.disabled = true;
                        if (radio.checked) radio.checked = false;
                        if (label) label.classList.add('slot-taken');
                        anyTaken = true;
                    }
                });
                if (slotHint) {
                    slotHint.textContent = anyTaken
                        ? 'Greyed-out slots are already booked for this doctor on this date.'
                        : 'All slots are available for this doctor on this date.';
                }
            })
            .catch(function () {
                if (slotHint) slotHint.textContent = 'Choose a slot below (live availability check unavailable).';
            });
    }

    if (doctorSelect) doctorSelect.addEventListener('change', refreshSlots);
    if (dateInput) dateInput.addEventListener('change', refreshSlots);
    refreshSlots();
});
