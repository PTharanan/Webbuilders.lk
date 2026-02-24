<!DOCTYPE html>
<html lang="ta">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEBbuilders - Professional Website Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <section class="hero-section">
        <div class="central-wrapper">
            <div class="hero-container">

                <div class="hero-content">
                    <h1 id="header-text">Build your <br>Dream Website</h1>
                    <p class="sub-text">We don't simply construct websites, <br> we build websites that SELL</p>
                    <div class="cta-box">
                        <a href="#" class="btn-contact">CONTACT NOW</a>
                    </div>
                </div>

                <div class="hero-image">
                    <img src="images/image1.png" alt="Hero Illustration">
                </div>

            </div>
        </div>
    </section>

    <section id="domain-finder" class="about-exact-section">
        <div class="about-exact-container">

            <div class="char-illustration side-image">
                <img src="images/image2.png" alt="Character Left">
            </div>

            <div class="about-exact-content">
                <h2 class="exact-title">About WEBbuilders.lk</h2>

                <p class="exact-para">WEBbuilders.lk delivers high-end web solutions to businesses. We provide the most
                    effective class web management service, web designing service, and web development service for your
                    business that helps you to achieve your ventures.</p>

                <p class="exact-para">Our team members have worked with various successful startups and large-scale
                    enterprises to supply the most effective web solutions for several industries. We develop and
                    present new creative website management ideas, web development concepts, web solutions, and
                    approaches for client success. We always focus on critical information, skip irrelevant or
                    unnecessary details, and maintain a top level of professionalism. We believe in dedicated
                    involvement to produce post-implementation support.</p>

                <div class="exact-orange-box">
                    <h3 class="box-label">CHECK YOUR DREAM WEBSITE NAME :</h3>
                    <div class="exact-input-group">
                        <input type="text" id="domainInput" placeholder="Enter your domain name here...">
                        <select id="tldSelect" style="padding: 10px; border: none; font-weight: 600; cursor: pointer;">
                            <option value=".com">.com</option>
                            <option value=".lk">.lk</option>
                            <option value=".net">.net</option>
                            <option value=".org">.org</option>
                            <option value=".co">.co</option>
                            <option value=".info">.info</option>
                            <option value=".biz">.biz</option>
                            <option value=".io">.io</option>
                            <option value=".app">.app</option>
                            <option value=".dev">.dev</option>
                        </select>
                        <button type="button" id="checkButton" onclick="checkDomain()">CHECK NOW</button>
                    </div>

                    <!-- Results Display -->
                    <div id="resultMessage" class="result-message"
                        style="display: none; margin-top: 15px; padding: 15px; border-radius: 8px; text-align: center;">
                        <div id="resultText" style="color: white; font-weight: 600; font-size: 14px;"></div>
                        <div id="resultActions" style="display: none; margin-top: 10px;">
                            <button onclick="openRegisterModal()"
                                style="padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 5px; margin-right: 8px; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-shopping-cart"></i> Register Now
                            </button>
                            <button onclick="checkAnother()"
                                style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-redo"></i> Check Another
                            </button>
                        </div>
                        <div id="suggestions" style="margin-top: 15px;"></div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" style="display: none; text-align: center; margin-top: 15px;">
                        <div
                            style="display: inline-block; width: 30px; height: 30px; border: 3px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 1s linear infinite;">
                        </div>
                        <p style="color: white; margin-top: 10px; font-weight: 500;">Checking domain availability...</p>
                    </div>

                    <div class="blinking-dots-container">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
            </div>

            <div class="char-illustration side-image">
                <img src="images/image3.png" alt="Character Right">
            </div>

        </div>
    </section>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <section class="services-section">
        <div class="service-container">
            <div class="service-content">
                <h2>Website Management</h2>
                <p>WEBbuilders.lk managed website service aims to take the burden of content management, graphic design,
                    web monitoring, and systems maintenance off your shoulders so as that you'll specialize in the more
                    important things like your customers and business. Our seasoned professionals work to take care of a
                    healthy website with 100% aimed uptime, for continued growth and customer satisfaction.</p>
                <div class="service-buttons">
                    <a href="#"><i class="fas fa-desktop"></i> Read More</a>
                    <a href="#"><i class="fas fa-shopping-cart"></i> Get Now</a>
                </div>
            </div>
            <div class="service-image">
                <img src="images/image4.png" alt="Website Management" class="floating-img">
            </div>
        </div>
    </section>

    <section class="services-section reverse">
        <div class="service-container reverse">
            <div class="service-content">
                <h2>Website Design and Development</h2>
                <p>Are you trying to find an wonderful website design with affordable development packages to get
                    designed and developed your website? WEBbuilders.lk has designed 3 differing types of website
                    packages to design and develop custom websites for your business. Our customized website prices or
                    plans are having all the quality features and functionalities to create your web project look good,
                    interacting and beautiful. Need a professional website? Compare our cost-effective website creation
                    packages designed separately for little, medium and enormous size businesses. Get your SEO ready and
                    fully mobile responsive online store now with our web designing and website development packages in
                    India just starting at 34999 LKR.</p>
                <div class="service-buttons">
                    <a href="#"><i class="fas fa-desktop"></i> Read More</a>
                    <a href="#"><i class="fas fa-shopping-cart"></i> Get Now</a>
                </div>
            </div>
            <div class="service-image">
                <img src="images/image5.png" alt="Website Design" class="floating-img">
            </div>
        </div>
    </section>

    <section class="services-section">
        <div class="service-container">
            <div class="service-content">
                <h2>HOSTING AND DOMAIN</h2>
                <p>Our company doesn't have web hosting and domain Package. But Our team can contact the best hosting
                    provider companies in Sri Lanka or foreign countries to get hosting and domains at a cheap price for
                    our customer's reference. And we can immediately fix any error in your hosting side with hosting
                    provide companies and it doesn't have any charge.</p>
                <div class="service-buttons">
                    <a href="#"><i class="fas fa-desktop"></i> Read More</a>
                    <a href="#"><i class="fas fa-shopping-cart"></i> Get Now</a>
                </div>
            </div>
            <div class="service-image">
                <img src="images/image6.png" alt="Hosting and Domain" class="floating-img">
            </div>
        </div>
    </section>

    <section class="services-section reverse">
        <div class="service-container reverse">
            <div class="service-content">
                <h2>CONTENT WRITING</h2>
                <p>WEBbuilders.lk has been providing the simplest content writing services world wide since 2019. As a
                    content agency, we've empowered over 100 clients across industries around the world. At
                    WEBbuilders.lk, we understand that Enters creating marketing content that helps customers to create
                    powerful communication with the audience.
                    We have 2 packages in this service. One is content writing for a full website without blogs. It may
                    cost 35 USD and another one is expected above and includes 3 blogs within a range of 300 to 500
                    words. For this service, our fee was 50 USD only. If you need any additional blogs for your websites
                    it may take you 15 USD for one.</p>

                <div class="service-buttons">
                    <a href="#"><i class="fas fa-desktop"></i> Read More</a>
                    <a href="#"><i class="fas fa-comments"></i> Get a Free Consultation</a>
                </div>
            </div>
            <div class="service-image">
                <img src="images/image7.png" alt="Content Writing" class="floating-img">
            </div>
        </div>
    </section>

    <section class="what-we-do">
        <div class="main-container">
            <div class="title-area">
                <h2>WHAT WE <span>DO IN</span></h2>
                <div class="line-deco">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="long-line"></div>
                </div>
            </div>

            <div class="services-grid">
                <div class="service-card">
                    <div class="card-header">
                        <i class="fa-solid fa-code"></i>
                        <h3>Web Development</h3>
                    </div>
                    <p>We build customized and need-based website solutions for E-Commerce Websites, Web Applications
                        and Dynamic or Static Websites.</p>
                    <a href="#" class="learn-more">LEARN MORE <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>

                <div class="service-card">
                    <div class="card-header">
                        <i class="fa-solid fa-chalkboard-user"></i>
                        <h3>Web Management</h3>
                    </div>
                    <p>we'll manage your content and maintain your systems. So our attention is fine-tuning to growing
                        your business.</p>
                    <a href="#" class="learn-more">LEARN MORE <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>

                <div class="service-card">
                    <div class="card-header">
                        <i class="fa-solid fa-chart-line"></i>
                        <h3>SEO Optimization</h3>
                    </div>
                    <p>Get comprehensive solutions and expert guidance on digital marketing campaigns that support your
                        business needs.</p>
                    <a href="#" class="learn-more">LEARN MORE <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>

                <div class="service-card">
                    <div class="card-header">
                        <i class="fa-solid fa-laptop-code"></i>
                        <h3>Web Creative Design</h3>
                    </div>
                    <p>We can build website like your expected design. And we will do your website will responsive to
                        any system like mobile and tablet and notebook and desktop screen.</p>
                    <a href="#" class="learn-more">LEARN MORE <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>

                <div class="service-card">
                    <div class="card-header">
                        <i class="fa-solid fa-share-nodes"></i>
                        <h3>Social Media Management</h3>
                    </div>
                    <p>We can do social media strategy for your business through platforms like a Facebook and Instagram
                        and etc ... and it bring you more sales and reputation.</p>
                    <a href="#" class="learn-more">LEARN MORE <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>

                <div class="service-card">
                    <div class="card-header">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                        <h3>Web Technical Support</h3>
                    </div>
                    <p>We can fix errors immediately in come with your website. For this we charge 5 usd per hours and
                        we charge for only who doesn't take our website management Package</p>
                    <a href="#" class="learn-more">LEARN MORE <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonial-section">
        <div class="testimonial-container">

            <div class="header-area">
                <h2>WHAT PEOPLE <span>SAY?</span></h2>
                <div class="deco-line">
                    <div class="deco-dot"></div>
                    <div class="deco-dot"></div>
                    <div class="deco-long"></div>
                </div>
            </div>

            <div class="slider-engine">
                <div class="single-slide active">
                    <div class="text-content">
                        <div class="stars-box">★★★★★</div>
                        <p class="review-text">
                            Excellent, working with web designer express was great. Thanks to their knowledge and
                            determination our website looks great and functions really good. I am recommend anyone that
                            is looking for a custom website to give them a call and speak to Gus.
                        </p>
                        <h3 class="name-title">Mr Janathan</h3>
                        <p class="role-subtext">Business Investor</p>
                        <div class="nav-arrows">
                            <i class="fa-solid fa-arrow-left" onclick="moveT(-1)"></i>
                            <i class="fa-solid fa-arrow-right" onclick="moveT(1)"></i>
                        </div>
                    </div>
                    <div class="visual-content">
                        <img src="images/image8.png" alt="Janathan">
                        <div class="social-links">
                            <a href="#" style="background: #3b5998;"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" style="background: #222;"><i class="fab fa-instagram"></i></a>
                            <a href="#" style="background: #0077b5;"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" style="background: #25d366;"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>

                <div class="single-slide">
                    <div class="text-content">
                        <div class="stars-box">★★★★★</div>
                        <p class="review-text">
                            The designers and developers are true professionals. They understand your vision and make it
                            a reality. The layouts and designs are fantastic. The best I have seen. I have nothing but
                            praise and the highest recommendation.
                        </p>
                        <h3 class="name-title">Mr Sangaran</h3>
                        <p class="role-subtext">CEO of Tech Solutions</p>
                        <div class="nav-arrows">
                            <i class="fa-solid fa-arrow-left" onclick="moveT(-1)"></i>
                            <i class="fa-solid fa-arrow-right" onclick="moveT(1)"></i>
                        </div>
                    </div>
                    <div class="visual-content">
                        <img src="images/image8.png" alt="Sangaran">
                        <div class="social-links">
                            <a href="#" style="background: #3b5998;"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" style="background: #222;"><i class="fab fa-instagram"></i></a>
                            <a href="#" style="background: #0077b5;"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" style="background: #25d366;"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>

                <div class="single-slide">
                    <div class="text-content">
                        <div class="stars-box">★★★★★</div>
                        <p class="review-text">
                            Gus, thank you so much for all your help. Since we launched the website we had more than 400
                            hundred people registering for our event. I look forward to working with you guys again on a
                            new project I am thinking about, I will let you know soon. Thanks, You guys are the best.
                        </p>
                        <h3 class="name-title">Mr Pirashanth</h3>
                        <p class="role-subtext">One of the Sri lankan Politician</p>
                        <div class="nav-arrows">
                            <i class="fa-solid fa-arrow-left" onclick="moveT(-1)"></i>
                            <i class="fa-solid fa-arrow-right" onclick="moveT(1)"></i>
                        </div>
                    </div>
                    <div class="visual-content">
                        <img src="images/image8.png" alt="Pirashanth">
                        <div class="social-links">
                            <a href="#" style="background: #3b5998;"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" style="background: #222;"><i class="fab fa-instagram"></i></a>
                            <a href="#" style="background: #0077b5;"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" style="background: #25d366;"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="partner-section">
        <div class="partner-wrapper">
            <div class="partner-card">
                <div class="content-area">
                    <h1>Become our <span>Partner</span></h1>
                    <a href="#" class="sub-heading">A Complete White Label Web Design Service That's Yours to Resell</a>
                    <p class="description-text">
                        Get white label website designs that you can resell to clients. As part of our website design
                        reseller program,
                        our team provides you with expertise on demand. So you can run web development projects at
                        scale.
                    </p>
                </div>
                <button class="try-btn">TRY IT NOW &rarr;</button>
            </div>
        </div>
    </section>

    <section class="join-team-section">
        <div class="partner-wrapper">
            <div class="partner-card">
                <div class="content-area">
                    <h1>Join our <span>Team</span></h1>
                    <a href="#" class="sub-heading">Are You Looking for a Job Opportunities in Jaffna or Freelancing
                        ?</a>
                    <p class="description-text">
                        We don't have the foggiest idea of what sort of vocation you have as a main priority. Be that as
                        it may, we realize you can locate the correct section opportunity with us
                    </p>
                </div>
                <button class="try-btn">JOIN NOW &rarr;</button>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="script.js"></script>

    <script>
        function suggestDomain(domainName) {
            document.getElementById('domainInput').value = domainName;
            checkDomain();
        }

        function checkDomain() {
            const domainInput = document.getElementById('domainInput').value.trim();
            const tldSelect = document.getElementById('tldSelect').value;
            const resultMessage = document.getElementById('resultMessage');
            const resultText = document.getElementById('resultText');
            const resultActions = document.getElementById('resultActions');
            const checkButton = document.getElementById('checkButton');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const suggestions = document.getElementById('suggestions');

            if (!domainInput) {
                showResult('⚠️ Please enter a domain name', 'rgba(251, 191, 36, 0.3)');
                return;
            }

            // Format domain
            let domain = domainInput.toLowerCase().replace(/^https?:\/\//, '').replace(/\/$/, '');

            // Extract domain name without path
            domain = domain.split('/')[0];

            // Remove any existing extension and add selected TLD
            domain = domain.replace(/\.[a-z]+$/, '') + tldSelect;

            // Validate domain format
            const domainRegex = /^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,}$/i;
            if (!domainRegex.test(domain)) {
                showResult('❌ Invalid domain format. Example: example', 'rgba(248, 113, 113, 0.3)');
                return;
            }

            console.log('🔍 Checking domain:', domain);

            // Show loading state
            checkButton.disabled = true;
            checkButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> CHECKING...';
            loadingSpinner.style.display = 'block';
            resultMessage.style.display = 'none';
            resultActions.style.display = 'none';
            suggestions.innerHTML = '';

            // Call PHP backend
            fetch('domain_checker_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ domain: domain })
            })
                .then(response => {
                    console.log('📡 Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('📊 API Response:', data);

                    loadingSpinner.style.display = 'none';
                    resultMessage.style.display = 'block';
                    checkButton.disabled = false;
                    checkButton.innerHTML = 'CHECK NOW';

                    if (data.success) {
                        if (data.available) {
                            resultText.innerHTML = `✅ <strong>${domain}</strong> is available!`;
                            resultMessage.style.backgroundColor = 'rgba(16, 185, 129, 0.3)';
                            resultActions.style.display = 'block';
                        } else {
                            resultText.innerHTML = `❌ <strong>${domain}</strong> is already registered`;
                            resultMessage.style.backgroundColor = 'rgba(239, 68, 68, 0.3)';

                            // Show suggestions for taken domains
                            setTimeout(() => {
                                const domainName = domain.split('.')[0];
                                const suggestionsList = [
                                    `my${domainName}`,
                                    `${domainName}online`,
                                    `the${domainName}`,
                                    `${domainName}web`
                                ];

                                showSuggestions(suggestionsList);
                            }, 500);
                        }
                    } else {
                        resultText.innerHTML = `⚠️ ${data.message || 'Unable to check domain'}`;
                        resultMessage.style.backgroundColor = 'rgba(251, 191, 36, 0.3)';
                    }
                })
                .catch(error => {
                    console.error('❌ Error:', error);

                    loadingSpinner.style.display = 'none';
                    resultMessage.style.display = 'block';
                    checkButton.disabled = false;
                    checkButton.innerHTML = 'CHECK NOW';

                    resultText.textContent = '⚠️ Connection error. Please try again.';
                    resultMessage.style.backgroundColor = 'rgba(251, 191, 36, 0.3)';

                    // Fallback: Use DNS check
                    setTimeout(() => {
                        fallbackDomainCheck(domain);
                    }, 1000);
                });
        }

        function fallbackDomainCheck(domain) {
            const resultText = document.getElementById('resultText');
            const resultMessage = document.getElementById('resultMessage');

            resultText.innerHTML = '🔄 Using fallback check method...';

            // Simple DNS check as fallback
            fetch(`https://dns.google/resolve?name=${domain}&type=A`)
                .then(response => response.json())
                .then(data => {
                    if (data.Answer && data.Answer.length > 0) {
                        // Domain resolves = taken
                        resultText.innerHTML = `❌ <strong>${domain}</strong> appears to be registered (DNS check)`;
                        resultMessage.style.backgroundColor = 'rgba(239, 68, 68, 0.3)';
                    } else {
                        // No DNS record = likely available
                        resultText.innerHTML = `✅ <strong>${domain}</strong> appears to be available (DNS check)`;
                        resultMessage.style.backgroundColor = 'rgba(16, 185, 129, 0.3)';
                    }
                })
                .catch(() => {
                    // If even fallback fails
                    resultText.innerHTML = '⚠️ Please check the domain manually';
                    resultMessage.style.backgroundColor = 'rgba(251, 191, 36, 0.3)';
                });
        }

        function showResult(message, color) {
            const resultMessage = document.getElementById('resultMessage');
            const resultText = document.getElementById('resultText');

            resultText.innerHTML = message;
            resultMessage.style.display = 'block';
            resultMessage.style.backgroundColor = color;
        }

        function showSuggestions(suggestionsList) {
            const suggestions = document.getElementById('suggestions');
            const tldSelect = document.getElementById('tldSelect').value;

            const suggestionsHTML = `
            <p style="color: white; margin-top: 15px; margin-bottom: 10px; font-weight: 600;">💡 Try these alternatives:</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px;">
                ${suggestionsList.map(domainName =>
                `<button onclick="suggestDomain('${domainName}')" style="padding: 8px 12px; background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 5px; cursor: pointer; font-weight: 500; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i class="fas fa-globe"></i> ${domainName}${tldSelect}
                    </button>`
            ).join('')}
            </div>
        `;

            suggestions.innerHTML = suggestionsHTML;
        }

        function checkAnother() {
            document.getElementById('domainInput').value = '';
            document.getElementById('domainInput').focus();
            document.getElementById('resultMessage').style.display = 'none';
            document.getElementById('resultActions').style.display = 'none';
            document.getElementById('suggestions').innerHTML = '';
        }

        function openRegisterModal() {
            const domainInput = document.getElementById('domainInput').value.trim();
            const tldSelect = document.getElementById('tldSelect').value;
            let domain = domainInput.toLowerCase().replace(/^https?:\/\//, '').replace(/\/$/, '');
            domain = domain.split('/')[0];
            domain = domain.replace(/\.[a-z]+$/, '') + tldSelect;

            // Redirect to standalone pricing page
            window.location.href = `pricing_modal.php?domain=${encodeURIComponent(domain)}`;
        }

        // Allow Enter key to check domain
        document.addEventListener('DOMContentLoaded', function () {
            const domainInput = document.getElementById('domainInput');
            if (domainInput) {
                domainInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        checkDomain();
                    }
                });

                // Auto-hide result section when focusing or clicking on input field
                const hideResult = function () {
                    const resultMessage = document.getElementById('resultMessage');
                    const resultActions = document.getElementById('resultActions');
                    const suggestions = document.getElementById('suggestions');

                    if (resultMessage) resultMessage.style.display = 'none';
                    if (resultActions) resultActions.style.display = 'none';
                    if (suggestions) suggestions.innerHTML = '';
                };

                domainInput.addEventListener('focus', hideResult);
                domainInput.addEventListener('click', hideResult);
                domainInput.addEventListener('input', hideResult);
            }
        });
    </script>

</body>

</html>