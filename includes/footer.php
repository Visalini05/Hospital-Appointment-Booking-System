</main>

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-col">
            <h4>🏥 ABC Hospital</h4>
            <p>Compassionate care, experienced specialists, and easy online appointment booking — all in one place.</p>
        </div>
        <div class="footer-col">
            <h4>Quick Links</h4>
            <a href="<?php echo $base; ?>/index.php">Home</a>
            <a href="<?php echo $base; ?>/services.php">Services</a>
            <a href="<?php echo $base; ?>/doctors.php">Doctors</a>
            <a href="<?php echo $base; ?>/appointment.php">Book Appointment</a>
            <a href="<?php echo $base; ?>/patient/dashboard.php">My Appointments</a>
        </div>
        <div class="footer-col">
            <h4>Account</h4>
            <a href="<?php echo $base; ?>/patient/login.php">Patient Login</a>
            <a href="<?php echo $base; ?>/patient/register.php">Create Account</a>
            <a href="<?php echo $base; ?>/about.php#faq">FAQs</a>
            <a href="<?php echo $base; ?>/contact.php">Contact Us</a>
        </div>
        <div class="footer-col">
            <h4>Contact</h4>
            <p>123 Health Street, Coimbatore</p>
            <p>+91 10293 84756</p>
            <p>info@abchospital.com</p>
            <p>Mon - Sat, 8:00 AM - 8:00 PM</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> ABC Hospital. All rights reserved.</p>
        <a href="#" class="back-to-top" id="backToTop">↑ Top</a>
    </div>
</footer>

<script>
(function () {
    var btn = document.getElementById('backToTop');
    if (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();
</script>
<?php if (file_exists(__DIR__ . '/../js/animations.js')): ?>
<script src="<?php echo $base; ?>/js/animations.js"></script>
<?php endif; ?>
</body>
</html>
