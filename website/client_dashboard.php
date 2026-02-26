<?php
require_once 'includes/dbConnect.php';

// Check if client is logged in
if (!isset($_SESSION['client_logged_in']) || $_SESSION['client_logged_in'] !== true) {
    header("Location: client_dashboard_login.php");
    exit;
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
    <style>
        .logout-btn-wrap {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .btn-logout {
            background: #ff4d4d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: #e60000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 77, 77, 0.3);
        }

        /* Modal Styles */
        .logout-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: modalSlide 0.3s ease-out;
        }

        @keyframes modalSlide {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-icon {
            font-size: 50px;
            color: #ff4d4d;
            margin-bottom: 20px;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            justify-content: center;
        }

        .btn-confirm {
            background: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-cancel {
            background: #f1f1f1;
            color: #333;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-confirm:hover { background: #e60000; }
        .btn-cancel:hover { background: #e0e0e0; }

        .orange-dashboard-banner {
            position: relative;
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <section class="orange-dashboard-banner">
        <div class="logout-btn-wrap">
            <button class="btn-logout" onclick="showLogoutModal()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
        <div class="banner-content">
            <h1>Client Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['client_username'] ?? 'Client'); ?></p>
        </div>
    </section>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="logout-modal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <h3>Ready to Leave?</h3>
            <p>Are you sure you want to log out of your dashboard?</p>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeLogoutModal()">Cancel</button>
                <button class="btn-confirm" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>

    <script>
        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'flex';
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function confirmLogout() {
            window.location.href = 'client_logout.php';
        }

        // Close modal if clicked outside
        window.onclick = function(event) {
            var modal = document.getElementById('logoutModal');
            if (event.target == modal) {
                closeLogoutModal();
            }
        }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>

</html>