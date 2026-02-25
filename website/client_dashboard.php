<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - WEBbuilders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/client_dashboard.css">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <!-- Dashboard Header (Matches Img 1) -->
    <header class="dashboard-header">
        <div class="container">
            <h1>Client Dashboard</h1>
        </div>
    </header>

    <!-- Sign In Section (Matches Img 2) -->
    <main class="login-section">
        <div class="login-container">
            <h2>Sign In</h2>

            <form action="#" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username or E-mail</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Keep me signed in</label>
                </div>

                <div class="login-btn-container">
                    <button type="submit" class="login-btn">Login</button>
                </div>

                <div class="support-info">
                    <p>If You forgot Your Password Please Contact Our Technical Support Team</p>
                    <a href="contact.php" class="contact-link">Contact now</a>
                </div>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="script.js"></script>

</body>

</html>