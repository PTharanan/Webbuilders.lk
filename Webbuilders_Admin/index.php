<?php
session_start();
require_once 'dbConnect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            // First check if user exists
            $stmt = $conn->prepare("SELECT id, email, password, name, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Check if password matches (try both hashed and plain text)
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    header('Location: admindash.php');
                    exit();
                } else {
                    $error = 'Invalid password.';
                }
            } else {
                $error = 'User not found.';
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login | Webbuilders — Web Development</title>
  <link rel="icon" href="./assets/titleLogo.png" sizes="32x32" />

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            tc: { 
              DEFAULT: '#f97316', 
              50: '#fff7ed', 
              100: '#ffedd5',
              200: '#fed7aa',
              500: '#f97316',
              600: '#ea580c',
              700: '#c2410c',
              800: '#92400e',
              900: '#78350f'
            },
            navy: '#0f1724',
            muted: '#6b7280',
            surface: '#f8fafc',
            premium: {
              gold: '#d4af37',
              silver: '#c0c0c0'
            }
          },
          boxShadow: {
            'elegant': '0 25px 50px -12px rgba(15,23,36,0.25)',
            'premium': '0 20px 60px rgba(14,165,169,0.15)'
          },
          fontFamily: { 
            sans: ['Inter','ui-sans-serif','system-ui'],
            serif: ['Playfair Display', 'serif']
          },
          backgroundImage: {
            'gradient-premium': 'linear-gradient(135deg, #0ea5a9 0%, #2dd4bf 50%, #0d9488 100%)'
          }
        }
      }
    }
  </script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* Premium card */
    .premium-card {
      background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.2);
    }

    /* Premium button */
    .btn-premium {
      background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
      position: relative;
      overflow: hidden;
      transition: all 0.4s ease;
    }
    .btn-premium::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.7s ease;
    }
    .btn-premium:hover::before {
      left: 100%;
    }
    .btn-premium:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 30px rgba(14,165,169,0.3);
    }

    /* Form styling */
    .form-input {
      transition: all 0.3s ease;
    }
    .form-input:focus {
      box-shadow: 0 0 0 3px rgba(14, 165, 169, 0.1);
      border-color: #0ea5a9;
    }

    /* Custom checkbox */
    .custom-checkbox {
      position: relative;
      cursor: pointer;
    }
    .custom-checkbox input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
      height: 0;
      width: 0;
    }
    .checkmark {
      position: absolute;
      top: 0;
      left: 0;
      height: 20px;
      width: 20px;
      background-color: #fff;
      border: 2px solid #d1d5db;
      border-radius: 4px;
      transition: all 0.3s ease;
    }
    .custom-checkbox:hover input ~ .checkmark {
      border-color: #0ea5a9;
    }
    .custom-checkbox input:checked ~ .checkmark {
      background-color: #0ea5a9;
      border-color: #0ea5a9;
    }
    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }
    .custom-checkbox input:checked ~ .checkmark:after {
      display: block;
    }
    .custom-checkbox .checkmark:after {
      left: 6px;
      top: 2px;
      width: 5px;
      height: 10px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }

    /* Floating animation */
    @keyframes premium-float {
      0%, 100% { 
        transform: translateY(0px) rotate(0deg) scale(1);
      }
      33% { 
        transform: translateY(-15px) rotate(2deg) scale(1.05);
      }
      66% { 
        transform: translateY(-8px) rotate(-1deg) scale(0.98);
      }
    }

    .premium-float-animation {
      animation: premium-float 6s ease-in-out infinite;
    }

    /* Fade-in animation */
    .fade-in {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.8s ease, transform 0.8s ease;
    }
    .fade-in.is-visible {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>

<body class="font-sans antialiased bg-surface text-navy overflow-x-hidden">
  <!-- MAIN CONTENT -->
  <main class="min-h-screen flex items-center justify-center px-4 sm:px-6">
    <!-- Background Elements -->
    <div class="absolute inset-0 bg-gradient-to-br from-tc-50 via-white to-tc-100 z-0"></div>
    <div class="absolute top-10 left-10 w-72 h-72 bg-tc/10 rounded-full blur-3xl -z-10 hidden sm:block"></div>
    <div class="absolute bottom-10 right-10 w-96 h-96 bg-tc-200/30 rounded-full blur-3xl -z-10 hidden sm:block"></div>
    
    <!-- Floating Elements -->
    <div class="absolute top-1/4 left-1/4 w-6 h-6 bg-tc rounded-full opacity-70 premium-float-animation hidden sm:block"></div>
    <div class="absolute top-1/3 right-1/4 w-4 h-4 bg-premium-gold rounded-full opacity-60 premium-float-animation hidden sm:block" style="animation-delay: 1s;"></div>
    <div class="absolute bottom-1/4 left-1/3 w-8 h-8 bg-tc-600 rounded-full opacity-40 premium-float-animation hidden sm:block" style="animation-delay: 2s;"></div>

    <div class="max-w-md w-full mx-auto px-2 sm:px-6 py-8 sm:py-12 relative z-10">
      <!-- Login Card -->
      <div class="premium-card rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-elegant fade-in">
        <div class="text-center mb-6 sm:mb-8">
          <div class="flex items-center justify-center gap-3 mb-4">
              <img src="./assets/titleLogo.png" sizes="32x32" />

            <div class="leading-tight">
              <div class="text-lg sm:text-xl font-bold">Webbuilders</div>
              <div class="text-xs text-muted -mt-0.5 tracking-wider">Web & Software Solutions</div>
            </div>
          </div>
          
          <h1 class="text-2xl sm:text-3xl font-serif font-bold mb-2">Welcome Back</h1>
          <p class="text-muted text-sm sm:text-base">Sign in to your Webbuilders account</p>
        </div>

        <!-- Login Form -->
        <form method="POST" class="space-y-4 sm:space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-navy mb-2">Email Address</label>
            <input 
              type="email" 
              id="email" 
              name="email"
              required 
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              class="form-input block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm focus:ring-2 focus:ring-tc/30 focus:border-tc transition-all duration-300 text-base" 
              placeholder="your.email@webbuilders.com" 
            />
          </div>
          
          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="password" class="block text-sm font-medium text-navy">Password</label>
            </div>
            <input 
              type="password" 
              id="password" 
              name="password"
              required 
              class="form-input block w-full rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm focus:ring-2 focus:ring-tc/30 focus:border-tc transition-all duration-300 text-base" 
              placeholder="Enter your password" 
            />
          </div>
          
          <button 
            type="submit" 
            class="w-full btn-premium px-6 py-3 sm:py-4 rounded-xl text-white font-semibold text-base sm:text-lg flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            Sign In
          </button>
          

        </form>
        
        <!-- Security Badge -->
        <div class="mt-6 sm:mt-8 flex items-center justify-center gap-2 text-xs text-muted">
          <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
          </svg>
          <span>Your data is securely encrypted</span>
        </div>
      </div>
    </div>
  </main>

  <!-- Toast Container -->
  <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

  <!-- SCRIPTS -->
  <script>
    // Toast notification function
    function showToast(message, type = 'success') {
      const toast = document.createElement('div');
      toast.className = `flex items-center p-4 rounded-lg shadow-lg transform translate-x-full transition-all duration-300 max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
      }`;
      
      toast.innerHTML = `
        <div class="flex items-center">
          <i class="fas ${
            type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'
          } mr-3 text-lg"></i>
          <span class="font-medium">${message}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
          <i class="fas fa-times"></i>
        </button>
      `;
      
      document.getElementById('toast-container').appendChild(toast);
      
      // Animate in
      setTimeout(() => {
        toast.classList.remove('translate-x-full');
      }, 100);
      
      // Auto remove after 5 seconds
      setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
      }, 5000);
    }

    // Show toasts based on PHP messages
    <?php if ($error): ?>
    showToast('<?= addslashes($error) ?>', 'error');
    <?php endif; ?>
    
    <?php if ($success): ?>
    showToast('<?= addslashes($success) ?>', 'success');
    <?php endif; ?>

    // Fade-in intersection observer
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) e.target.classList.add('is-visible');
      });
    }, { threshold: 0.15 });
    
    // Observe all fade-in elements
    document.querySelectorAll('.fade-in').forEach(el => io.observe(el));
  </script>
</body>
</html>