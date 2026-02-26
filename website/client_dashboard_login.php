<?php
require_once 'includes/dbConnect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $identity = $_POST['identity'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($identity) || empty($password)) {
        $error = "Please enter both username/email and password.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM client WHERE username = ? OR email = ?");
            $stmt->execute([$identity, $identity]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($client && password_verify($password, $client['password'])) {
                // Login success
                $_SESSION['client_logged_in'] = true;
                $_SESSION['client_id'] = $client['id'] ?? $client['email'];
                $_SESSION['client_email'] = $client['email'];
                $_SESSION['client_username'] = $client['username'];

                header("Location: client_dashboard.php");
                exit;
            } else {
                $error = "Invalid username/email or password.";
            }
        } catch (PDOException $e) {
            $error = "An error occurred. Please try again later.";
        }
    }
}
?>
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
    <link rel="stylesheet" href="css/client_dashboard_login.css">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <section class="orange-dashboard-banner">
        <div class="banner-content">
            <h1>Client Dashboard</h1>
        </div>
    </section>

    <main class="login-section">
        <div class="login-box">
            <h2>Sign In</h2>
            <?php if ($error): ?>
                <div class="error-message"
                    style="color: #ff4d4d; background: rgba(255, 77, 77, 0.1); padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #ff4d4d;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="input-field">
                    <label>Username or E-mail</label>
                    <input type="text" name="identity" required>
                </div>
                <div class="input-field">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="remember-me">
                    <input type="checkbox" id="keep-signed" name="remember">
                    <label for="keep-signed">Keep me signed in</label>
                </div>
                <button type="submit" name="login_submit" class="btn-login">Login</button>
            </form>
            <p class="help-text">If You forgot Your Password Please Contact Our Technical Support Team</p>
            <a href="#" class="contact-link">Contact now</a>
        </div>
    </main>
    <script src="script.js"></script>

</body>
<?php include 'includes/footer.php'; ?>

</html>