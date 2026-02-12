<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEBbuilders - Professional Website Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="css/home.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="contact-section">
    <div class="contact-wrapper">
        
        <h2 class="title">Get in Touch</h2>
        <div class="blue-line"></div>

        <div class="contact-form-container">
            <form>
                <input type="text" placeholder="Name" class="input-field">
                <input type="email" placeholder="Email" class="input-field">
                <input type="text" placeholder="WhatsApp Number" class="input-field">
                <textarea placeholder="Message" class="input-field textarea-field"></textarea>
                
                <div class="captcha-box">
                    <div class="captcha-content">
                        <input type="checkbox" id="robot-check">
                        <label for="robot-check">I'm not a robot</label>
                    </div>
                    <div class="captcha-logo">
                        <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA">
                        <p>reCAPTCHA<br><span>Privacy - Terms</span></p>
                    </div>
                </div>

                <button type="submit" class="send-btn">Send</button>
            </form>
        </div>

        <div class="info-card">
            <div class="info-item">
                <div class="icon-circle"><i class="fas fa-home"></i></div>
                <p>Thaddatheru Junction, kks Rd, Jaffna</p>
            </div>
            <div class="info-item">
                <div class="icon-circle"><i class="fas fa-phone-alt"></i></div>
                <p>+94 76 9988 123<br>+94 74 156 1234</p>
            </div>
            <div class="info-item">
                <div class="icon-circle"><i class="fas fa-envelope"></i></div>
                <p>info@webbuilders.lk<br>chartheepan@outlook.com</p>
            </div>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="script.js"></script>
</body>
</html>
