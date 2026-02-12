<!DOCTYPE html>
<html lang="en" class="transition-colors duration-500">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle ?? 'Webbuilders Admin'; ?> | Webbuilders Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <?php if (isset($includeChartJS) && $includeChartJS): ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="icon" href="./assets/titleLogo.png" sizes="32x32" />
  <?php endif; ?>
  
  <script>
    tailwind.config = {
      darkMode: 'class',
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
            }
          },
          fontFamily: { 
            sans: ['Inter','ui-sans-serif','system-ui'],
            serif: ['Playfair Display', 'serif']
          }
        }
      }
    }
  </script>
  
  <style>
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeIn 0.7s forwards;
    }
    @keyframes fadeIn {
      to { opacity: 1; transform: translateY(0); }
    }
    
    .enhanced-card {
      background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
      border-radius: 20px;
      overflow: hidden;
      position: relative;
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      border: 1px solid rgba(255,255,255,0.8);
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
    }
    
    .enhanced-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 6px;
      background: linear-gradient(90deg, #f97316, #ea580c);
    }
    
    .enhanced-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 60px rgba(249, 115, 22, 0.15);
    }
    
    .dark .enhanced-card {
      background: linear-gradient(145deg, #1e293b 0%, #0f1724 100%);
      border: 1px solid rgba(255,255,255,0.05);
    }
    
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
      box-shadow: 0 15px 30px rgba(249,115,22,0.3);
    }
    
    .elegant-underline {
      position: relative;
    }
    .elegant-underline::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -6px;
      width: 60px;
      height: 3px;
      background: linear-gradient(90deg, #f97316, #ea580c);
      border-radius: 2px;
    }
    
    .glass-effect {
      background: rgba(255, 255, 255, 0.85);
      -webkit-backdrop-filter: blur(10px);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.2);
    }
    
    .dark .glass-effect {
      background: rgba(15, 23, 36, 0.85);
      border: 1px solid rgba(255,255,255,0.05);
    }
    
    .sidebar {
      background: #000000;
    }
    
    .dark .sidebar {
      background: #000000;
    }

    /* Independent Sidebar Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
      width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
      background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.3);
    }
    
    .avatar-placeholder {
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
      color: white;
      font-weight: bold;
    }
    
    .progress-container {
      background-color: #e5e7eb;
      border-radius: 10px;
      overflow: hidden;
      height: 10px;
    }
    
    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #f97316, #ea580c);
      border-radius: 10px;
      transition: width 1s ease-in-out;
    }
    
    .dark .progress-container {
      background-color: #374151;
    }
    
    .stat-card {
      background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
      border-radius: 16px;
      padding: 1.5rem;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(255,255,255,0.8);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
    }
    
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, #f97316, #ea580c);
    }
    
    .stat-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 15px 35px rgba(249, 115, 22, 0.15);
    }
    
    .dark .stat-card {
      background: linear-gradient(145deg, #1e293b 0%, #0f1724 100%);
      border: 1px solid rgba(255,255,255,0.05);
    }
    
    .icon-container {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
    }
    
    .stat-card:hover .icon-container {
      transform: scale(1.1) rotate(5deg);
    }

    /* Mobile responsive styles */
    @media (max-width: 1023px) {
      .sidebar {
        position: fixed;
        left: -100%;
        top: 0;
        height: 100%;
        z-index: 50;
        transition: left 0.3s ease;
        width: 280px;
      }
      
      .sidebar.active {
        left: 0;
      }
      
      .mobile-menu-btn {
        display: block;
      }
      
      .main-content {
        width: 100%;
      }
      
      .page-title {
        text-align: center;
        width: 100%;
      }
      
      .mobile-menu-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 40;
        display: none;
      }
      
      .mobile-menu-overlay.active {
        display: block;
      }
      
      .top-navbar-mobile {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.5rem 0;
      }
      
      .mobile-header-content {
        display: flex;
        align-items: center;
        flex: 1;
        min-width: 0;
      }
      
      .mobile-title-container {
        flex: 1;
        min-width: 0;
        text-align: center;
      }
      
      .mobile-title-container h1 {
        font-size: 1.5rem;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0 auto;
      }
      
      .mobile-title-container p {
        font-size: 0.875rem;
        line-height: 1.2;
      }
      
      .mobile-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      
      .mobile-profile-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
    }
    
    @media (max-width: 640px) {
      .mobile-title-container h1 {
        font-size: 1.25rem;
      }
      
      .mobile-title-container p {
        font-size: 0.75rem;
      }
    }
    
    @media (min-width: 1024px) {
      .sidebar {
        position: static;
      }
      
      .mobile-menu-btn {
        display: none;
      }
      
      .mobile-menu-overlay {
        display: none;
      }
      
      .top-navbar-mobile {
        display: none;
      }
    }

    /* Logout modal styles */
    .logout-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 100;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }
    
    .logout-modal.active {
      opacity: 1;
      visibility: visible;
    }
    
    .logout-modal-content {
      background: white;
      border-radius: 16px;
      padding: 2rem;
      max-width: 400px;
      width: 90%;
      transform: translateY(20px);
      transition: transform 0.3s ease;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }
    
    .dark .logout-modal-content {
      background: #1e293b;
    }
    
    .logout-modal.active .logout-modal-content {
      transform: translateY(0);
    }

    <?php if (isset($additionalCSS)): ?>
    <?php echo $additionalCSS; ?>
    <?php endif; ?>
  </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans transition-colors duration-500">

  <!-- Layout Container -->
  <div class="flex h-screen">
    
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-4 lg:p-6 overflow-auto main-content">
      
      <!-- Include Topbar -->
      <?php include 'topbar.php'; ?>

      <!-- Page Content -->
      <?php if (isset($pageContent)): ?>
        <?php echo $pageContent; ?>
      <?php endif; ?>
      
    </div>
  </div>

  <?php if (isset($additionalJS)): ?>
  <script>
    <?php echo $additionalJS; ?>
  </script>
  <?php endif; ?>

</body>
</html>