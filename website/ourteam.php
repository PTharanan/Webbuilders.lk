<html lang="ta">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEBbuilders - Professional Website Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/ourteam.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

<script>
const openBtn = document.getElementById("openMenu");
const closeBtn = document.getElementById("closeMenu");
const mobileMenu = document.getElementById("mobileOverlay");

// Open Menu
openBtn.addEventListener("click", function () {
    mobileMenu.style.display = "block";
    setTimeout(() => {
        mobileMenu.classList.add("active");
    }, 10);
});

// Close Menu
closeBtn.addEventListener("click", function () {
    mobileMenu.classList.remove("active");
    setTimeout(() => {
        mobileMenu.style.display = "none";
    }, 400);
});

// ✅ NEW ADDITION – Pages & Services Dropdown Toggle
document.querySelectorAll(".mobile-dropdown-btn").forEach(function(btn){
    btn.addEventListener("click", function(){
        this.parentElement.classList.toggle("active");
    });
});
</script>   

<section class="join-squad">
    <div class="squad-container">
        <div class="squad-overlay">
            <a href="/jobs/" class="animated-headline">
                <span class="static-text">Join Our</span>
                <span class="dynamic-wrapper">
                    <span class="dynamic-text">TEAM</span>
                    <span class="dynamic-text">SQUARD</span>
                    <span class="dynamic-text">FAMILY</span>
                </span>
            </a>
        </div>
    </div>
</section>

<section class="team-section">
    <div class="team-grid">
        
        <div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-4.png" alt="Ulaganathan Chartheepan">
            </div>
            <div class="member-info">
                <h3>Ulaganathan Chartheepan</h3>
                <p>MANAGING DIRECTOR ( MD )</p>
            </div>
        </div>

        <div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-5.png" alt="A.E.Anisto">
            </div>
            <div class="member-info">
                <h3>A.E.Anisto</h3>
                <p>GENERAL MANAGER ( GM )</p>
            </div>
        </div>
<div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-6.png" alt="R.A.Regino">
            </div>
            <div class="member-info">
                <h3>R.A.Regino</h3>
                <p>Trainee Manager ( TM )</p>
            </div>
        </div>
<div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-45.png" alt="Thishangar">
            </div>
            <div class="member-info">
                <h3>Thishangar</h3>
                <p>Team Leader ( TL )</p>
            </div>
        </div>
<div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-22.png" alt="Singarasa Joyalraj">
            </div>
            <div class="member-info">
                <h3>Singarasa Joyalraj</h3>
                <p>TEAM LEADER ( TL )</p>
            </div>
        </div>
<div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-8.png" alt="Stalin Joel">
            </div>
            <div class="member-info">
                <h3>Stalin Joel</h3>
                <p>TEAM LEADER ( TL )</p>
            </div>
        </div>
<div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-14.png" alt="Pirathena Sivapatham">
            </div>
            <div class="member-info">
                <h3>Pirathena Sivapatham</h3>
                <p>Acting Administrator ( AA )</p>
            </div>
        </div>
<div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-25.png" alt="Sayanutha Uthayakumar">
            </div>
            <div class="member-info">
                <h3>Sayanutha Uthayakumar</h3>
                <p>Acting Administrator ( AA )</p>
            </div>
        </div>
        <div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-7.png" alt="Jeyanthakumar Arul">
            </div>
            <div class="member-info">
                <h3>Jeyanthakumar Arul</h3>
                <p>Trainee Team Leader ( TTL )</p>
            </div>
        </div>
        <div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-11.png" alt="Jegannathan Keyuran">
            </div>
            <div class="member-info">
                <h3>Jegannathan Keyuran</h3>
                <p>wordpress developer</p>
            </div>
        </div>
<div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-9.png" alt="Kajageshan">
            </div>
            <div class="member-info">
                <h3>Kajageshan</h3>
                <p>wordpress developer</p>
            </div>
        </div>

        <div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-26.png" alt="">Jeyakandan Kathikan
            </div>
            <div class="member-info">
                <h3>Jeyakandan Kathikan</h3>
                <p>graphic designer</p>
            </div>
        </div>

        <div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-36.png" alt="Anantharajah Thanushanth">
            </div>
            <div class="member-info">
                <h3>Anantharajah Thanushanth</h3>
                <p>Web Developer</p>
            </div>
        </div>

        <div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-31.png" alt="Edwin Andrw">
            </div>
            <div class="member-info">
                <h3>Edwin Andrw</h3>
                <p>Web Developer</p>
            </div>
        </div>

        <div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-27.png" alt="Nagarajah Vithusan">
            </div>
            <div class="member-info">
                <h3>Nagarajah Vithusan</h3>
                <p>Trainee Web Developer</p>
            </div>
        </div>

    <div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-43.png" alt="S.Sanjeevan">
            </div>
            <div class="member-info">
                <h3>S.Sanjeevan</h3>
                <p>Web Developer</p>
            </div>

        </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-15.png" alt="Kandasamy Kajeevan">
            </div>
            <div class="member-info">
                <h3>Kandasamy Kajeevan</h3>
                <p>Web designer</p>
            </div>

        </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-46.png" alt="Ramesh Sumirthan">
            </div>
            <div class="member-info">
                <h3>Ramesh Sumirthan</h3>
                <p>Web Designer</p>
            </div>

        </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-10.png" alt="Jeyachandran Reshvigan">
            </div>
            <div class="member-info">
                <h3>Jeyachandran Reshvigan</h3>
                <p>trainee Web designer</p>
            </div>
</div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-42 (1).png" alt="Thusiyanthan Kajaluxsan">
            </div>
            <div class="member-info">
                <h3>Thusiyanthan Kajaluxsan</h3>
                <p>Web designer</p>
            </div>

</div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-12.png" alt="S.R.Shalini">
            </div>
            <div class="member-info">
                <h3>S.R.Shalini</h3>
                <p>wordpress developer</p>
            </div>
</div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-16 (1).png" alt="Ilakkiya Rasalingam">
            </div>
            <div class="member-info">
                <h3>Ilakkiya Rasalingam</h3>
                <p>web designer</p>
            </div>

            </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-18.png" alt="T.Dilaxshika">
            </div>
            <div class="member-info">
                <h3>T.Dilaxshika</h3>
                <p>web designer</p>
            </div>

            </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-13 (1).png" alt="Julaxshina Nihaldanstan">
            </div>
            <div class="member-info">
                <h3>Julaxshina Nihaldanstan</h3>
                <p>web designer</p>
            </div>

            </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-17.png" alt="Abija Sannathivel">
            </div>
            <div class="member-info">
                <h3>Abija Sannathivel</h3>
                <p>web designer</p>
            </div>

            </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-29 (1).png" alt="Pirathiga Kukathasan">
            </div>
            <div class="member-info">
                <h3>Pirathiga Kukathasan</h3>
                <p>Trainee web designer</p>
            </div>

            </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-20.png" alt="Kujinsha Paskaran">
            </div>
            <div class="member-info">
                <h3>Kujinsha Paskaran</h3>
                <p>Trainee web designer</p>
            </div>

            </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-34.png" alt="Vanuja Mathiyalagan">
            </div>
            <div class="member-info">
                <h3>Vanuja Mathiyalagan</h3>
                <p>Trainee web designer</p>
            </div>

            </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-30.png" alt="Satheeswaran Sakeersana">
            </div>
            <div class="member-info">
                <h3>Satheeswaran Sakeersana</h3>
                <p>Trainee web designer</p>
            </div>

            </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-28.png" alt="Hamshathvani .R">
            </div>
            <div class="member-info">
                <h3>Hamshathvani .R</h3>
                <p>Trainee web designer</p>
            </div>

            </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-32 (1).png" alt="Mathusa Panchanathan">
            </div>
            <div class="member-info">
                <h3>Mathusa Panchanathan</h3>
                <p>Trainee web designer</p>
            </div>

            </div><div class="member-card">
            <div class="img-box">
                <img src="img/New-Project-33.png" alt="Makenthiran Thiluththiga">
            </div>
            <div class="member-info">
                <h3>Makenthiran Thiluththiga</h3>
                <p>Trainee web designer</p>
            </div>
        </div>
        </div>
</section>

<section class="verification-section">
    <div class="verification-container">
        <div class="form-image-side">
            <div class="form-overlay">
                <h2>EMPLOYEE <br> VERIFICATION <br> FORM</h2>
            </div>
        </div>

        <div class="form-content-side">
            <form action="#">
                <input type="text" placeholder="Company Name" required>
                <input type="text" placeholder="Company Website" required>
                <input type="email" placeholder="Company Email" required>
                <input type="text" placeholder="Employee Full Name" required>
                <input type="text" placeholder="Employee NIC Number" required>
                <textarea placeholder="Purpose of the Verification" rows="3"></textarea>
                
               <div class="captcha-placeholder">
    <div class="captcha-left">
        <input type="checkbox" id="robot-check">
        <label for="robot-check">I'm not a robot</label>
    </div>
    
    <div class="captcha-right">
        <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="captcha">
        <span class="captcha-label">reCAPTCHA</span>
        <div class="captcha-links">Privacy - Terms</div>
    </div>
</div> 
            
                <button type="submit" class="send-btn">Send</button>
            </form>
            <p class="form-note">The verification form is provided by us only for legal purposes and we do not provide our employee details to outsourcers at all times.</p>
        </div>
    </div>
</section>

<script src="script.js"></script>
<?php include 'includes/footer.php'; ?>
</body>
</html>