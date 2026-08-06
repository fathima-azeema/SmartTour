<?php
// dashboard.php - ENHANCED MODERN UI/UX VERSION
require_once 'config.php';
require_once 'session-check.php';

// Check if user is logged in
checkLogin();

// Get current user data
$user = getCurrentUser();

// If user not found (shouldn't happen, but just in case)
if (!$user) {
    session_destroy();
    redirect('login.php?error=user_not_found');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/footer.css">
    
    <style>
        /* ==========================================================================
           CSS Variables & Reset
           ========================================================================== */
        :root {
            /* Color Palette */
            --primary: #4361ee;
            --primary-light: #4895ef;
            --primary-dark: #3a0ca3;
            --secondary: #7209b7;
            --accent: #f72585;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #f94144;
            --info: #43aa8b;
            
            /* Neutral Colors */
            --light: #f8f9fa;
            --dark: #1e293b;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            
            /* Shadows */
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.12);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-inner: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
            
            /* Border Radius */
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;
            
            /* Transitions */
            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-normal: 250ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 350ms cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Spacing */
            --space-xs: 0.5rem;
            --space-sm: 1rem;
            --space-md: 1.5rem;
            --space-lg: 2rem;
            --space-xl: 3rem;
            
            /* Typography */
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Poppins', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            font-size: 16px;
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-body);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-200);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-400);
            border-radius: 4px;
            transition: background var(--transition-normal);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-500);
        }

        /* Selection */
        ::selection {
            background-color: var(--primary);
            color: white;
        }

        /* Container */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 var(--space-md);
        }

        /* ==========================================================================
           Header & Navigation
           ========================================================================== */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--gray-200);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-normal);
        }

        .header.scrolled {
            box-shadow: var(--shadow-md);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--space-sm) 0;
            position: relative;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: var(--space-xs);
            padding: var(--space-xs);
            border-radius: var(--radius-md);
            transition: all var(--transition-normal);
            background: rgba(67, 97, 238, 0.05);
            border: 1px solid rgba(67, 97, 238, 0.1);
        }

        .logo:hover {
            transform: translateY(-2px);
            background: rgba(67, 97, 238, 0.1);
            box-shadow: var(--shadow-sm);
        }

        .logo i {
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            filter: drop-shadow(0 2px 4px rgba(67, 97, 238, 0.2));
        }

        .logo-text {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }

        /* Navigation */
        .nav-container {
            display: flex;
            align-items: center;
            gap: var(--space-xl);
        }

        .nav-links {
            display: flex;
            gap: var(--space-md);
            align-items: center;
        }

        .nav-link {
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 500;
            font-size: 0.95rem;
            padding: var(--space-xs) var(--space-sm);
            border-radius: var(--radius-sm);
            transition: all var(--transition-normal);
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            color: var(--primary);
            background: rgba(67, 97, 238, 0.05);
            transform: translateY(-1px);
        }

        .nav-link.active {
            color: var(--primary);
            background: rgba(67, 97, 238, 0.1);
            font-weight: 600;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 24px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            border-radius: 2px;
        }

        /* User Menu */
        .user-menu {
            display: flex;
            align-items: center;
            gap: var(--space-md);
            position: relative;
        }

        .notification-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition-normal);
            color: var(--gray-600);
        }

        .notification-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }

        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 18px;
            height: 18px;
            background: linear-gradient(135deg, var(--accent), #ff3d8b);
            color: white;
            border-radius: var(--radius-full);
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(247, 37, 133, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(247, 37, 133, 0); }
            100% { box-shadow: 0 0 0 0 rgba(247, 37, 133, 0); }
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            padding: var(--space-xs) var(--space-sm);
            border-radius: var(--radius-md);
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            cursor: pointer;
            transition: all var(--transition-normal);
            position: relative;
        }

        .user-profile:hover {
            background: white;
            border-color: var(--primary);
            box-shadow: var(--shadow-sm);
            transform: translateY(-1px);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.2);
            transition: all var(--transition-normal);
        }

        .user-profile:hover .user-avatar {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(67, 97, 238, 0.3);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--gray-800);
            white-space: nowrap;
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        .logout-btn {
            padding: var(--space-xs) var(--space-sm);
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(67, 97, 238, 0.2);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }

        /* ==========================================================================
           Main Content
           ========================================================================== */
        .main-content {
            padding: var(--space-xl) 0;
            min-height: calc(100vh - 200px);
        }

        /* Welcome Hero Section */
        .welcome-hero {
            position: relative;
            background: linear-gradient(135deg, 
                rgba(67, 97, 238, 0.1) 0%,
                rgba(114, 9, 183, 0.05) 100%);
            border-radius: var(--radius-lg);
            padding: var(--space-xl);
            margin-bottom: var(--space-xl);
            overflow: hidden;
            border: 1px solid rgba(67, 97, 238, 0.1);
            box-shadow: var(--shadow-md);
        }

        .welcome-hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, 
                rgba(67, 97, 238, 0.05) 0%,
                rgba(114, 9, 183, 0.02) 100%);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }

        .welcome-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .greeting {
            font-family: var(--font-heading);
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: var(--space-sm);
            background: linear-gradient(135deg, var(--gray-800), var(--gray-900));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1.2;
        }

        .greeting .highlight {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            position: relative;
        }

        .greeting .highlight::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            border-radius: 2px;
            opacity: 0.5;
        }

        .greeting-emoji {
            font-size: 2.5rem;
            vertical-align: middle;
            margin-left: var(--space-xs);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .welcome-text {
            font-size: 1.1rem;
            color: var(--gray-600);
            margin-bottom: var(--space-lg);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
        }

        .user-badge-container {
            display: flex;
            gap: var(--space-sm);
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: var(--space-lg);
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--space-xs);
            padding: var(--space-xs) var(--space-md);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-full);
            font-weight: 600;
            color: var(--gray-700);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-normal);
        }

        .user-badge:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
        }

        .user-badge.primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
        }

        .badge-icon {
            font-size: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: var(--space-sm);
            justify-content: center;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: var(--space-sm) var(--space-lg);
            border-radius: var(--radius-md);
            font-weight: 600;
            text-decoration: none;
            transition: all var(--transition-normal);
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            border: 2px solid transparent;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.2);
        }

        .action-btn.primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
            border-color: var(--primary);
        }

        .action-btn.secondary {
            background: white;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
        }

        .action-btn.secondary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Messages/Alerts */
        .alert-container {
            margin-bottom: var(--space-xl);
        }

        .alert {
            padding: var(--space-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--space-sm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: slideDown 0.3s ease;
            border-left: 4px solid;
            background: white;
            box-shadow: var(--shadow-sm);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            border-left-color: var(--success);
            background: linear-gradient(90deg, 
                rgba(76, 201, 240, 0.1) 0%,
                rgba(76, 201, 240, 0.05) 100%);
        }

        .alert-error {
            border-left-color: var(--danger);
            background: linear-gradient(90deg, 
                rgba(249, 65, 68, 0.1) 0%,
                rgba(249, 65, 68, 0.05) 100%);
        }

        .alert-content {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
        }

        .alert-icon {
            font-size: 1.5rem;
        }

        .alert-success .alert-icon {
            color: var(--success);
        }

        .alert-error .alert-icon {
            color: var(--danger);
        }

        .alert-close {
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            font-size: 1.2rem;
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-fast);
        }

        .alert-close:hover {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        /* Dashboard Cards Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmin(300px, 1fr));
            gap: var(--space-lg);
            margin-bottom: var(--space-xl);
        }

        @media (min-width: 768px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .dashboard-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: var(--space-lg);
            box-shadow: var(--shadow-md);
            transition: all var(--transition-normal);
            border: 1px solid var(--gray-200);
            position: relative;
            overflow: hidden;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            opacity: 0;
            transition: opacity var(--transition-normal);
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary);
        }

        .dashboard-card:hover::before {
            opacity: 1;
        }

        .card-icon-container {
            width: 70px;
            height: 70px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--space-md);
            background: linear-gradient(135deg, 
                rgba(67, 97, 238, 0.1) 0%,
                rgba(67, 97, 238, 0.05) 100%);
            color: var(--primary);
            font-size: 1.8rem;
            transition: all var(--transition-normal);
        }

        .dashboard-card:hover .card-icon-container {
            transform: scale(1.1) rotate(5deg);
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
        }

        .dashboard-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: var(--space-sm);
            color: var(--gray-800);
            font-family: var(--font-heading);
        }

        .card-description {
            color: var(--gray-600);
            margin-bottom: var(--space-lg);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .card-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-xs);
            padding: var(--space-sm) var(--space-md);
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            transition: all var(--transition-normal);
            border: none;
            cursor: pointer;
            width: 100%;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }

        .card-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }

        .card-btn i {
            font-size: 0.9rem;
            transition: transform var(--transition-normal);
        }

        .card-btn:hover i {
            transform: translateX(4px);
        }

        /* Quick Stats */
        .quick-stats-section {
            background: white;
            border-radius: var(--radius-lg);
            padding: var(--space-xl);
            box-shadow: var(--shadow-md);
            margin-bottom: var(--space-xl);
            border: 1px solid var(--gray-200);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--space-lg);
        }

        .section-title {
            font-family: var(--font-heading);
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--gray-800);
            position: relative;
            padding-bottom: var(--space-sm);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            border-radius: 2px;
        }

        .view-all-btn {
            padding: var(--space-xs) var(--space-md);
            background: var(--gray-100);
            color: var(--gray-700);
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-weight: 500;
            font-size: 0.9rem;
            transition: all var(--transition-normal);
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            border: 1px solid var(--gray-300);
        }

        .view-all-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateX(4px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: var(--space-md);
        }

        .stat-card {
            background: linear-gradient(135deg, 
                rgba(67, 97, 238, 0.05) 0%,
                rgba(114, 9, 183, 0.02) 100%);
            border-radius: var(--radius-md);
            padding: var(--space-lg);
            border: 1px solid rgba(67, 97, 238, 0.1);
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary);
            background: linear-gradient(135deg, 
                rgba(67, 97, 238, 0.1) 0%,
                rgba(114, 9, 183, 0.05) 100%);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, 
                transparent 0%,
                rgba(255, 255, 255, 0.3) 50%,
                transparent 100%);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .stat-card:hover::before {
            transform: translateX(100%);
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            margin-bottom: var(--space-md);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            color: var(--primary);
            font-size: 1.2rem;
            box-shadow: var(--shadow-sm);
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-800);
            line-height: 1;
            margin-bottom: var(--space-xs);
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: var(--space-xs);
        }

        .stat-trend.up {
            background: rgba(76, 201, 240, 0.1);
            color: var(--success);
        }

        .stat-trend.new {
            background: rgba(247, 37, 133, 0.1);
            color: var(--accent);
        }

        /* ==========================================================================
           Footer
           ========================================================================== */
        .footer {
            background: linear-gradient(135deg, var(--gray-900), var(--gray-800));
            color: white;
            padding: var(--space-xl) 0 var(--space-lg);
            margin-top: var(--space-xl);
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--space-xl);
            margin-bottom: var(--space-xl);
        }

        .footer-column {
            animation: fadeInUp 0.6s ease;
            animation-fill-mode: both;
        }

        .footer-column:nth-child(1) { animation-delay: 0.1s; }
        .footer-column:nth-child(2) { animation-delay: 0.2s; }
        .footer-column:nth-child(3) { animation-delay: 0.3s; }
        .footer-column:nth-child(4) { animation-delay: 0.4s; }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            margin-bottom: var(--space-md);
            text-decoration: none;
        }

        .footer-logo i {
            font-size: 1.5rem;
            color: var(--primary-light);
        }

        .footer-logo-text {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 1.3rem;
            color: white;
        }

        .footer-description {
            color: var(--gray-400);
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: var(--space-md);
        }

        .footer-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: var(--space-md);
            color: white;
            position: relative;
            padding-bottom: var(--space-xs);
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--primary);
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: var(--space-xs);
        }

        .footer-link {
            color: var(--gray-400);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all var(--transition-normal);
            padding: 4px 0;
            display: flex;
            align-items: center;
            gap: var(--space-xs);
        }

        .footer-link:hover {
            color: var(--primary-light);
            transform: translateX(8px);
        }

        .footer-link i {
            font-size: 0.8rem;
            width: 16px;
        }

        .social-links {
            display: flex;
            gap: var(--space-sm);
            margin-top: var(--space-md);
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all var(--transition-normal);
        }

        .social-link:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .footer-bottom {
            padding-top: var(--space-lg);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            color: var(--gray-400);
            font-size: 0.9rem;
        }

        /* ==========================================================================
           Responsive Design
           ========================================================================== */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: var(--space-md);
                padding: var(--space-sm) 0;
            }

            .nav-container {
                width: 100%;
                justify-content: space-between;
            }

            .nav-links {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
                background: none;
                border: none;
                color: var(--gray-600);
                font-size: 1.5rem;
                cursor: pointer;
            }

            .mobile-menu {
                position: fixed;
                top: 0;
                right: -100%;
                width: 80%;
                max-width: 400px;
                height: 100vh;
                background: white;
                box-shadow: var(--shadow-xl);
                transition: right var(--transition-normal);
                z-index: 1001;
                padding: var(--space-xl);
                overflow-y: auto;
            }

            .mobile-menu.active {
                right: 0;
            }

            .mobile-menu-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: var(--space-xl);
            }

            .mobile-nav-links {
                display: flex;
                flex-direction: column;
                gap: var(--space-sm);
            }

            .mobile-nav-link {
                padding: var(--space-sm);
                border-radius: var(--radius-md);
                text-decoration: none;
                color: var(--gray-700);
                font-weight: 500;
                transition: all var(--transition-normal);
            }

            .mobile-nav-link:hover,
            .mobile-nav-link.active {
                background: rgba(67, 97, 238, 0.1);
                color: var(--primary);
            }

            .greeting {
                font-size: 2rem;
            }

            .welcome-hero {
                padding: var(--space-lg);
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .section-header {
                flex-direction: column;
                gap: var(--space-md);
                align-items: flex-start;
            }
        }

        @media (min-width: 769px) {
            .mobile-menu-btn,
            .mobile-menu {
                display: none;
            }
        }

        /* ==========================================================================
           Utility Classes & Animations
           ========================================================================== */
        .fade-in {
            animation: fadeIn 0.5s ease;
        }

        .slide-up {
            animation: slideUp 0.5s ease;
        }

        .scale-in {
            animation: scaleIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header fade-in">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="logo-text">SmartTour</span>
                </a>

                <!-- Desktop Navigation -->
                <div class="nav-container">


                    <!-- Mobile Menu Button -->
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>

                <div class="user-menu">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    
                    <div class="user-profile" onclick="toggleUserMenu()">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($user['first_name']); ?></span>
                            <span class="user-role"><?php echo ucfirst($user['user_type']); ?></span>
                        </div>
                    </div>
                    
                    <button class="logout-btn" onclick="window.location.href='logout.php'">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu-header">
                <a href="index.php" class="logo">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="logo-text">SmartTour</span>
                </a>
                <button class="mobile-menu-close" onclick="closeMobileMenu()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <nav class="mobile-nav-links">
                <a href="dashboard.php" class="mobile-nav-link active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="view-bookings.php" class="mobile-nav-link">
                    <i class="fas fa-calendar-check"></i> My Bookings
                </a>
                <a href="search-tours.php" class="mobile-nav-link">
                    <i class="fas fa-map-marked-alt"></i> Book Tours
                </a>
                <a href="search-hotels.php" class="mobile-nav-link">
                    <i class="fas fa-hotel"></i> Book Hotels
                </a>
                <a href="profile.php" class="mobile-nav-link">
                    <i class="fas fa-user"></i> My Profile
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Welcome Hero Section -->
            <div class="welcome-hero slide-up">
                <div class="welcome-content">
                    <h1 class="greeting">
                        <span id="greetingText">Welcome back</span>, 
                        <span class="highlight"><?php echo htmlspecialchars($user['first_name']); ?></span>
                        <span class="greeting-emoji" id="greetingEmoji">👋</span>
                    </h1>
                    
                    <p class="welcome-text">
                        We're excited to have you back. Here's what's happening with your SmartTour account. 
                        Ready to explore amazing experiences?
                    </p>
                    
                    <div class="user-badge-container">
                        <div class="user-badge primary">
                            <i class="fas fa-user-tag badge-icon"></i>
                            <?php echo ucfirst($user['user_type']); ?> Account
                        </div>
                        <div class="user-badge">
                            <i class="fas fa-calendar-alt badge-icon"></i>
                            Member since <?php echo date('M Y', strtotime($user['created_at'])); ?>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="search-tours.php" class="action-btn primary">
                            <i class="fas fa-search"></i> Explore Tours
                        </a>
                        <a href="profile.php" class="action-btn secondary">
                            <i class="fas fa-cog"></i> Account Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alert Container -->
            <div class="alert-container">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success fade-in">
                        <div class="alert-content">
                            <i class="fas fa-check-circle alert-icon"></i>
                            <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        </div>
                        <button class="alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error fade-in">
                        <div class="alert-content">
                            <i class="fas fa-exclamation-circle alert-icon"></i>
                            <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        </div>
                        <button class="alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Dashboard Cards -->
            <div class="dashboard-grid">
                <?php if ($user['user_type'] == 'tourist'): ?>
                    <!-- Tourist Dashboard -->
                    <div class="dashboard-card scale-in">
                        <div class="card-icon-container">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <h3>Book Hotels</h3>
                        <p class="card-description">
                            Find and book the perfect accommodation for your stay in Sri Lanka. 
                            From luxury resorts to cozy guesthouses.
                        </p>
                        <a href="search-hotels.php" class="card-btn">
                            Browse Hotels <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="dashboard-card scale-in" style="animation-delay: 0.1s">
                        <div class="card-icon-container">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h3>Book Tours</h3>
                        <p class="card-description">
                            Explore Sri Lanka with our curated tour experiences and expert guides. 
                            Cultural, adventure, or relaxation tours available.
                        </p>
                        <a href="search-tours.php" class="card-btn">
                            Browse Tours <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="dashboard-card scale-in" style="animation-delay: 0.2s">
                        <div class="card-icon-container">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <h3>My Bookings</h3>
                        <p class="card-description">
                            View and manage all your hotel and tour reservations in one place. 
                            Easy cancellation and modification options available.
                        </p>
                        <a href="view-bookings.php" class="card-btn">
                            View Bookings <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                <?php elseif ($user['user_type'] == 'student'): ?>
                    <!-- Student Dashboard -->
                    <div class="dashboard-card scale-in">
                        <div class="card-icon-container">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3>Job Opportunities</h3>
                        <p class="card-description">
                            Find internships and job openings in the tourism industry. 
                            Connect with employers and build your career.
                        </p>
                        <a href="student-jobs.php" class="card-btn">
                            Browse Jobs <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="dashboard-card scale-in" style="animation-delay: 0.1s">
                        <div class="card-icon-container">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Learning Resources</h3>
                        <p class="card-description">
                            Access tutorials, e-books, and courses to boost your tourism career. 
                            Learn from industry experts and professionals.
                        </p>
                        <a href="student-courses.php" class="card-btn">
                            Start Learning <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                                        
                    <div class="dashboard-card scale-in" style="animation-delay: 0.1s">
                        <div class="card-icon-container">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Student details</h3>
                        <p class="card-description">
                            Access tutorials, e-books, and courses to boost your tourism career. 
                            Learn from industry experts and professionals.
                        </p>
                        <a href="student-dashboard.php" class="card-btn">
                            Student dashboard  <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                <?php elseif ($user['user_type'] == 'guide'): ?>
                    <!-- Guide Dashboard -->
                    <div class="dashboard-card scale-in">
                        <div class="card-icon-container">
                            <i class="fas fa-map-signs"></i>
                        </div>
                        <h3>My Tours</h3>
                        <p class="card-description">
                            Create and manage your tour packages and schedules. 
                            Update availability and manage bookings efficiently.
                        </p>
                        <a href="my-tours.php" class="card-btn">
                            Manage Tours <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="dashboard-card scale-in" style="animation-delay: 0.1s">
                        <div class="card-icon-container">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3>Bookings</h3>
                        <p class="card-description">
                            View and manage tour bookings from travelers. 
                            Confirm bookings and communicate with clients.
                        </p>
                        <a href="guide-bookings.php" class="card-btn">
                            View Bookings <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="dashboard-card scale-in" style="animation-delay: 0.2s">
                        <div class="card-icon-container">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Earnings</h3>
                        <p class="card-description">
                            Track your earnings and view detailed reports. 
                            Manage payments and withdrawal methods.
                        </p>
                        <a href="guide-earnings.php" class="card-btn">
                            View Earnings <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endif; ?>
                
                <!-- Common for all users -->
                <div class="dashboard-card scale-in" style="animation-delay: 0.3s">
                    <div class="card-icon-container">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h3>My Profile</h3>
                    <p class="card-description">
                        Update your personal information, preferences, and account settings. 
                        Manage your privacy and security options.
                    </p>
                    <a href="profile.php" class="card-btn">
                        Edit Profile <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="dashboard-card scale-in" style="animation-delay: 0.4s">
                    <div class="card-icon-container">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Help & Support</h3>
                    <p class="card-description">
                        Need assistance? Get help with bookings, technical issues, or account questions. 
                        24/7 customer support available.
                    </p>
                    <a href="help.php" class="card-btn">
                        Get Help <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="dashboard-card scale-in" style="animation-delay: 0.5s">
                    <div class="card-icon-container">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h3>Special Offers</h3>
                    <p class="card-description">
                        Exclusive deals and discounts available for you. 
                        Save on tours, hotels, and experiences.
                    </p>
                    <a href="offers.php" class="card-btn">
                        View Offers <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="quick-stats-section fade-in">
                <div class="section-header">
                    <h2 class="section-title">Your SmartTour Journey</h2>
                    <a href="activity.php" class="view-all-btn">
                        View All Activity <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card scale-in" style="animation-delay: 0.1s">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">0</div>
                                <div class="stat-label">Total Bookings</div>
                            </div>
                        </div>
                        <span class="stat-trend new">
                            <i class="fas fa-plus"></i> Start exploring!
                        </span>
                    </div>
                    
                    <div class="stat-card scale-in" style="animation-delay: 0.2s">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="fas fa-plane"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">0</div>
                                <div class="stat-label">Upcoming Trips</div>
                            </div>
                        </div>
                        <span class="stat-trend new">
                            <i class="fas fa-plus"></i> Plan your next trip!
                        </span>
                    </div>
                    
                    <div class="stat-card scale-in" style="animation-delay: 0.3s">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">0</div>
                                <div class="stat-label">Reviews Given</div>
                            </div>
                        </div>
                        <span class="stat-trend new">
                            <i class="fas fa-plus"></i> Share your experiences!
                        </span>
                    </div>
                    
                    <div class="stat-card scale-in" style="animation-delay: 0.4s">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">
                                    <?php 
                                    $created = new DateTime($user['created_at']);
                                    $now = new DateTime();
                                    $interval = $created->diff($now);
                                    echo $interval->days;
                                    ?>
                                </div>
                                <div class="stat-label">Days with SmartTour</div>
                            </div>
                        </div>
                        <span class="stat-trend up">
                            <i class="fas fa-arrow-up"></i> Thank you for staying!
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </main>

<!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">Smart<span>Tour</span></div>
                    <p>Your trusted partner for unforgettable Sri Lankan adventures. Discover amazing destinations with our curated tours and expert guides.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="about-us.php"><i class="fas fa-info-circle"></i> About Us</a></li>
                        <li><a href="tours.php"><i class="fas fa-map-marked-alt"></i> Tour Packages</a></li>
                        <li><a href="hotels.php"><i class="fas fa-hotel"></i> Hotels</a></li>
                        <li><a href="restaurants.php"><i class="fas fa-utensils"></i> Restaurants</a></li>
                        <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact Us</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Contact Info</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <div>123 Galle Road, Colombo 03, Sri Lanka</div>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <div>+94 11 234 5678</div>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <div>info@smarttour.lk</div>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <div>Mon - Sun: 24/7 Customer Support</div>
                        </li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for the latest travel deals and destination guides.</p>
                    <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
                        <input type="email" class="newsletter-input" placeholder="Your email address" required>
                        <button type="submit" class="newsletter-btn"><i class="fas fa-paper-plane"></i></button>
                    </form>
                    <div class="payment-methods">
                        <i class="fab fa-cc-visa" title="Visa"></i>
                        <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                        <i class="fab fa-cc-amex" title="American Express"></i>
                        <i class="fab fa-cc-paypal" title="PayPal"></i>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="copyright">
                    &copy; 2023 SmartTour. All rights reserved.
                </div>
                <div>
                    <a href="privacy.php">Privacy Policy</a>
                    <a href="terms.php">Terms of Service</a>
                    <a href="sitemap.php">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>
// in-line codes
    <script>
        // greeting;mrng;nyt;
        function updateGreeting() {
            const hour = new Date().getHours();
            const greetingText = document.getElementById('greetingText');
            const greetingEmoji = document.getElementById('greetingEmoji');
            
            if (hour < 12) {
                greetingText.textContent = 'Good morning';
                greetingEmoji.textContent = '☀️';
            } else if (hour < 18) {
                greetingText.textContent = 'Good afternoon';
                greetingEmoji.textContent = '🌤️';
            } else {
                greetingText.textContent = 'Good evening';
                greetingEmoji.textContent = '🌙';
            }
        }

        // support mobile
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('active');
        }

        function closeMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.remove('active');
        }

        // Header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Auto-remove alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            });
        }, 5000);

        // Add hover effects to cards
        document.querySelectorAll('.dashboard-card, .stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = this.classList.contains('dashboard-card') ? 
                    'translateY(-8px)' : 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Notification bell click
        document.querySelector('.notification-btn').addEventListener('click', function() {
            alert('You have 3 new notifications:\n• New tour available\n• Special offer on hotels\n• Booking confirmation');
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateGreeting();
            
            // Mobile menu event listeners
            document.getElementById('mobileMenuBtn').addEventListener('click', toggleMobileMenu);
            
            // Close mobile menu when clicking on links
            document.querySelectorAll('.mobile-nav-link').forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });

            // Add animation to elements as they come into view
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe animated elements
            document.querySelectorAll('.scale-in, .slide-up, .fade-in').forEach(el => {
                observer.observe(el);
            });
        });

        // Simulate loading of stats (in real app, this would be from API)
        setTimeout(() => {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach((stat, index) => {
                if (stat.textContent === '0') {
                    let target = 0;
                    if (index === 0) target = 3; // Total Bookings
                    else if (index === 1) target = 1; // Upcoming Trips
                    else if (index === 2) target = 5; // Reviews Given
                    
                    if (target > 0) {
                        animateCounter(stat, 0, target, 1000);
                    }
                }
            });
        }, 1000);

        function animateCounter(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.textContent = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }
    </script>
</body>
</html>