<?php
$pageTitle = 'About Us';
require __DIR__ . '/includes/header.php';
?>
<h1>About ABC Hospital</h1>
<div class="card" style="max-width:700px;">
    <p>ABC Hospital has been serving the community for over 20 years, offering quality healthcare across
    Cardiology, Neurology, Pediatrics, Orthopedics, Dermatology and General Medicine.</p>
    <p>We provide 24x7 emergency services, on-site lab testing, and easy online appointment booking so you can
    consult the right specialist without long waiting times.</p>
    <ul>
        <li>24x7 Emergency Service</li>
        <li>Online Appointment Booking</li>
        <li>Experienced Specialist Doctors</li>
        <li>On-site Diagnostic Lab</li>
        <li>Patient Accounts &amp; Appointment History</li>
        <li>Verified Patient Reviews</li>
    </ul>
</div>

<h2 class="section-title" id="faq" style="margin-top:32px;">Frequently Asked Questions</h2>
<div class="faq-list">
    <div class="faq-item">
        <button class="faq-question" type="button">Do I need an account to book an appointment?</button>
        <div class="faq-answer"><p>No — you can book as a guest with just your name, phone and email. Creating a free account just lets you skip re-entering your details and see your appointment history automatically.</p></div>
    </div>
    <div class="faq-item">
        <button class="faq-question" type="button">Can I change my appointment after booking?</button>
        <div class="faq-answer"><p>Yes. Go to "My Appointments", find your booking, and use Reschedule to pick a new date and time, or Cancel if you no longer need it.</p></div>
    </div>
    <div class="faq-item">
        <button class="faq-question" type="button">How do I find my appointment if I booked as a guest?</button>
        <div class="faq-answer"><p>Visit "My Appointments" and enter the phone number you used when booking — all appointments linked to that number will be shown.</p></div>
    </div>
    <div class="faq-item">
        <button class="faq-question" type="button">Are reviews checked before they're published?</button>
        <div class="faq-answer"><p>Yes. Every review is checked by our team before it appears on a doctor's profile, to keep feedback genuine and useful.</p></div>
    </div>
    <div class="faq-item">
        <button class="faq-question" type="button">What are the consultation fees?</button>
        <div class="faq-answer"><p>Each doctor's profile lists their consultation fee, which is payable at the hospital reception on the day of your visit.</p></div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
