<!-- header.php -->
<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the website header.

// Include universal configuration for timezone and other settings
require_once dirname(__DIR__, 3) . '/private/gws-universal-config.php';

// Load editable content variables
// Universal session start
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
// Universal logged-in check for use in other includes
$logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Include development content highlighter (temporarily disabled)
// include_once __DIR__ . '/content_highlighter.php';

?>
<!-- Skip to main content link for accessibility -->
<a href="#main" class="skip-link">Skip to main content</a>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Dynamic Brand Colors injected from settings -->
<?php
// Universal public settings include
if (file_exists(__DIR__ . '/settings/public_settings.php')) {
  include_once __DIR__ . '/settings/public_settings.php';
}
?>
<style id="dynamic-brand-colors">
  :root {
    --brand-primary:
      <?php echo isset($brand_primary_color) ? htmlspecialchars($brand_primary_color) : '#124265'; ?>
    ;
    --brand-secondary:
      <?php echo isset($brand_secondary_color) ? htmlspecialchars($brand_secondary_color) : '#2487ce'; ?>
    ;
    --brand-font-family:
      <?php echo isset($brand_font_family) ? htmlspecialchars($brand_font_family) : 'inherit'; ?>
    ;
  }

  .accent-background {
    background: var(--brand-primary) !important;
    color: #fff !important;
  }

  .accent-color {
    color: var(--brand-primary) !important;
  }

  .secondary-color {
    color: var(--brand-secondary) !important;
  }

  .brand-font {
    font-family: var(--brand-font-family), Arial, sans-serif !important;
  }
  
  .navmenu ul li a:hover,
  .navmenu ul li a:focus {
    color: var(--brand-primary, #124265) !important;
    font-weight: bold !important;
    text-decoration: none !important;
  }
  .navmenu ul li a.active {
    color: var(--brand-secondary, #2487ce) !important;
    font-weight: bold !important;
    text-decoration: none !important;
  }
</style>
<!-- Header Section -->

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">


      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <?php if (!empty($business_logo)): ?>
          <img src="<?php echo htmlspecialchars($business_logo); ?>"
            alt="<?php echo htmlspecialchars($business_name); ?> Logo" style="max-height:48px; margin-right:10px;">
        <?php endif; ?>
        <h1 class="sitename"><?php echo htmlspecialchars($business_name); ?></h1>
      </a>


      <nav id="navmenu" class="navmenu" role="navigation" aria-label="Main navigation">
        <ul role="menubar">
          <?php
          // Universal navigation menu logic (remains unchanged)
          if (!empty($header_menu) && is_array($header_menu)) {
            $active = true;
            foreach ($header_menu as $label => $url) {
              echo '<li role="none">';
              echo '<a role="menuitem" href="' . htmlspecialchars($url) . '"';
              if ($active) {
                echo ' class="active"';
                $active = false;
              }
              echo '>' . htmlspecialchars($label) . '</a>';
              echo '</li>';
              if (strtolower($label) === 'contact') {
                echo '<li role="none"><a role="menuitem" href="blog.php">Blog</a></li>';
                echo '<li role="none"><a role="menuitem" href="shop.php">Shop</a></li>';
              }
            }
          }
          // Log In/Log Out menu item as last item
          if ($logged_in) {
            echo '<li role="none"><a role="menuitem" href="logout.php">Log Out</a></li>';
          } else {
            echo '<li role="none"><a role="menuitem" href="auth.php?tab=login">Login / Register</a></li>';
          }
          ?>
        
        </ul>
        
        <button class="mobile-nav-toggle bi bi-list" aria-label="Open main menu" aria-controls="navmenu"
          aria-expanded="false" style="color: var(--brand-secondary, #2487ce); background: #fff; border: none; width:35px; height:35px; font-size:35px; font-weight:bold; display:flex; align-items:center; justify-content:center;"></button>
      </nav>
   </div>
  </header>
  <main id="main" class="main"></main>
  <style>
    /* Hide menu by default for all screens */
    .navmenu ul {
      display: none;
      width: 320px !important;
      max-width: 80vw !important;
      position: absolute !important;
      top: 60px !important;
      right: 20px !important;
      left: auto !important;
      margin: 0 !important;
      border-radius: 8px !important;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
      max-height: 75vh;
      overflow-y: auto;
      z-index: 9900 !important; /* Lower than secondary navigation dropdowns (10050) */
    }

    /* Show menu when toggled on mobile */
    @media (max-width: 991.98px) {
      .navmenu.menu-open ul,
      body.mobile-nav-active .navmenu ul {
        display: flex !important;
        flex-direction: column !important;
        background: #fff !important;
        width: 240px !important;
        max-width: 60vw !important;
        position: absolute !important;
        top: 60px !important;
        right: 20px !important;
        left: auto !important;
        margin: 0 !important;
        border-radius: 8px !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        max-height: 75vh;
        overflow-y: auto;
        z-index: 10000 !important;
        padding: 0 !important;
        align-items: flex-start !important;
      }

      .navmenu.menu-open ul li,
      body.mobile-nav-active .navmenu ul li {
        width: 100%;
        margin: 0 !important;
        padding: 0 !important;
        text-align: left !important;
      }

      .navmenu.menu-open ul li a,
      body.mobile-nav-active .navmenu ul li a {
        width: 100%;
        padding: 12px 24px !important;
        margin: 0 !important;
        text-align: left !important;
        display: block !important;
        color: #333 !important;
        border-bottom: 1px solid #eee !important;
      }
      
      .navmenu.menu-open ul li a:hover,
      body.mobile-nav-active .navmenu ul li a:hover {
        color: var(--brand-primary, #124265) !important;
        font-weight: bold !important;
        background: #f8f9fa !important;
      }
      
      /* Override any inline styles that might be set by JavaScript */
      .navmenu.menu-open ul {
        display: flex !important;
      }
      
      body.mobile-nav-active .navmenu ul {
        display: flex !important;
      }
    }
    /* Desktop hamburger menu: hide menu by default, show vertical dropdown when toggled */
    @media (min-width: 992px) {
      .navmenu.menu-open ul,
      body.mobile-nav-active .navmenu ul {
        display: flex !important;
        flex-direction: column !important;
        background: #fff !important;
        width: 240px !important;
        max-width: 60vw !important;
        position: absolute !important;
        top: 60px !important;
        right: 20px !important;
        left: auto !important;
        margin: 0 !important;
        border-radius: 8px !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        max-height: 75vh;
        overflow-y: auto;
        z-index: 10000 !important;
        padding: 0 !important;
        align-items: flex-start !important;
      }

      .navmenu.menu-open ul li,
      body.mobile-nav-active .navmenu ul li {
        width: 100%;
        margin: 0 !important;
        padding: 0 !important;
        text-align: left !important;
      }

      .navmenu.menu-open ul li a,
      body.mobile-nav-active .navmenu ul li a {
        width: 100%;
        padding: 12px 24px !important;
        margin: 0 !important;
        text-align: left !important;
        display: block !important;
      }
    }
  .navmenu ul li a.active {
    color: var(--brand-secondary, #2487ce) !important;
    font-weight: bold !important;
    text-decoration: none !important;
  }
 

      .navmenu .dropdown ul {
        position: static !important;
        display: none !important;
        width: 100% !important;
        background: none !important;
        box-shadow: none !important;
        border: none !important;
        margin-left: 20px !important;
        padding-left: 10px !important;
        opacity: 1 !important;
        visibility: visible !important;
        transition: none !important;
      }

      .navmenu .dropdown.dropdown-active>ul {
        display: block !important;
      }

      .navmenu .dropdown .dropdown ul {
        margin-left: 40px !important;
      }

      .navmenu .dropdown>a .toggle-dropdown {
        transition: transform 0.2s;
      }

      .navmenu .dropdown.dropdown-active>a .toggle-dropdown {
        transform: rotate(90deg);
      }
    
  
    /* Visually hidden skip link, visible on focus */
    .skip-link {
      position: absolute;
      left: -999px;
      top: 10px;
      background: #124265;
      color: #fff;
      padding: 8px 16px;
      z-index: 10000;
      border-radius: 4px;
      font-weight: bold;
      text-decoration: none;
      transition: left 0.2s;
    }

    .skip-link:focus {
      left: 10px;
      outline: 2px solid #2487ce;
    }
 
  </style>
   
  <script>
    // Hamburger menu toggle for all screens, checks screen size on every click
    document.addEventListener('DOMContentLoaded', function() {
      var navmenu = document.getElementById('navmenu');
      var toggleBtn = document.querySelector('.mobile-nav-toggle');
      function toggleMenu(e) {
        e.stopPropagation();
        var expanded = toggleBtn.getAttribute('aria-expanded') === 'true';
        toggleBtn.setAttribute('aria-expanded', !expanded);
        navmenu.classList.toggle('menu-open');
        document.body.classList.toggle('mobile-nav-active');
        
        // Force menu <ul> to display: flex for all screen sizes when menu is open
        var menuList = navmenu.querySelector('ul');
        if (!expanded) {
          menuList.style.display = 'flex';
        } else {
          menuList.style.display = '';
        }
        
        // Toggle hamburger/X icon
        if (!expanded) {
          toggleBtn.classList.remove('bi-list');
          toggleBtn.classList.add('bi-x');
        } else {
          toggleBtn.classList.remove('bi-x');
          toggleBtn.classList.add('bi-list');
        }
      }
      toggleBtn.addEventListener('click', toggleMenu);
      // Close menu when clicking outside
      document.addEventListener('click', function(e) {
        if (!navmenu.contains(e.target) && !toggleBtn.contains(e.target) && navmenu.classList.contains('menu-open')) {
          navmenu.classList.remove('menu-open');
          document.body.classList.remove('mobile-nav-active');
          toggleBtn.setAttribute('aria-expanded', false);
          toggleBtn.classList.remove('bi-x');
          toggleBtn.classList.add('bi-list');
          // Reset forced display style
          var menuList = navmenu.querySelector('ul');
          if (menuList) {
            menuList.style.display = '';
          }
        }
      });
    });
    </script>
  <!-- End Header Section Begins Main Section -->
   