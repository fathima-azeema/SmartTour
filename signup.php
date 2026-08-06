<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - SmartTour</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --text: #34495e;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --gradient: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .signup-container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            min-height: 700px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            background: white;
        }

        /* Left Side - Branding */
        .signup-left {
            flex: 1;
            background: var(--gradient);
            color: white;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .signup-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1551632811-561732d1e306?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80');
            background-size: cover;
            background-position: center;
            opacity: 0.2;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
            z-index: 1;
        }

        .logo-icon {
            font-size: 2.5rem;
            margin-right: 15px;
            color: white;
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 700;
        }

        .signup-left h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 20px;
            line-height: 1.2;
            z-index: 1;
        }

        .signup-left p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 500px;
            z-index: 1;
        }

        .benefits {
            margin-top: 40px;
            z-index: 1;
        }

        .benefit {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .benefit-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.3rem;
        }

        .benefit-text h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .benefit-text p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Right Side - Signup Form */
        .signup-right {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
            overflow-y: auto;
        }

        .signup-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .signup-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .signup-header p {
            color: var(--text);
            opacity: 0.8;
        }

        .signup-form {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary);
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 10px;
            color: var(--primary);
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 1em;
            padding-right: 40px;
        }

        .terms {
            display: flex;
            align-items: flex-start;
            margin: 25px 0;
        }

        .terms input {
            margin-top: 5px;
            margin-right: 10px;
        }

        .terms label {
            font-size: 0.9rem;
            color: var(--text);
            line-height: 1.5;
        }

        .terms a {
            color: var(--primary);
            text-decoration: none;
        }

        .terms a:hover {
            text-decoration: underline;
        }

        .signup-btn {
            width: 100%;
            padding: 16px;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }

        .signup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(52, 152, 219, 0.3);
        }

        .login-link {
            text-align: center;
            color: var(--text);
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .password-strength {
            margin-top: 5px;
            font-size: 0.85rem;
        }

        .strength-weak { color: #e74c3c; }
        .strength-medium { color: #f39c12; }
        .strength-strong { color: #27ae60; }

        /* Responsive Design */
        @media (max-width: 992px) {
            .signup-container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .signup-left {
                padding: 40px 30px;
            }
            
            .signup-left h1 {
                font-size: 2.5rem;
            }
            
            .signup-right {
                padding: 40px 30px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            
            .signup-left {
                padding: 30px 20px;
            }
            
            .signup-left h1 {
                font-size: 2rem;
            }
            
            .logo-text {
                font-size: 1.8rem;
            }
            
            .signup-right {
                padding: 30px 20px;
            }
            
            .signup-header h2 {
                font-size: 2rem;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .signup-left, .signup-right {
            animation: fadeIn 0.8s ease-out;
        }

        .signup-right {
            animation-delay: 0.2s;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <!-- Left Side - Branding -->
        <div class="signup-left">
            <div class="logo">
                <i class="fas fa-map-marked-alt logo-icon"></i>
                <span class="logo-text">SmartTour</span>
            </div>
            
            <h1>Start Your Journey Today</h1>
            <p>Join thousands of travelers who trust SmartTour for their adventures. Create your account to unlock personalized travel experiences.</p>
            
            <div class="benefits">
                <div class="benefit">
                    <div class="benefit-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="benefit-text">
                        <h4>Personalized Recommendations</h4>
                        <p>Get tour suggestions based on your preferences</p>
                    </div>
                </div>
                
                <div class="benefit">
                    <div class="benefit-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="benefit-text">
                        <h4>Quick Booking</h4>
                        <p>Book hotels, restaurants, and tours in seconds</p>
                    </div>
                </div>
                
                <div class="benefit">
                    <div class="benefit-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="benefit-text">
                        <h4>Secure Account</h4>
                        <p>Your personal data is protected with encryption</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Signup Form -->
        <div class="signup-right">
            <div class="signup-header">
                <h2>Create Account</h2>
                <p>Join SmartTour and start exploring the world</p>
            </div>

            <form class="signup-form" action="register.php" method="POST" id="signupForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="first_name">
                            <i class="fas fa-user"></i> First Name
                        </label>
                        <input type="text" id="first_name" name="first_name" class="form-control" placeholder="John" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="last_name">
                            <i class="fas fa-user"></i> Last Name
                        </label>
                        <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Doe" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">
                        <i class="fas fa-phone"></i> Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="+94 77 123 4567" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="user_type">
                        <i class="fas fa-user-tag"></i> I am a
                    </label>
                    <select id="user_type" name="user_type" class="form-control" required>
                        <option value="" disabled selected>Select your role</option>
                        <option value="tourist">Tourist</option>
                        <option value="student">Tourism Student</option>
                        <option value="guide">Tour Guide</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a strong password" required>
                    <div id="passwordStrength" class="password-strength"></div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">
                        <i class="fas fa-lock"></i> Confirm Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter your password" required>
                    <div id="passwordMatch" class="password-strength"></div>
                </div>

                <div class="terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        I agree to the <a href="terms.php">Terms & Conditions</a> and <a href="privacy.php">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="signup-btn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>

                <div class="login-link">
                    Already have an account? 
                    <a href="login.php">Sign In</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthDiv.textContent = '';
                return;
            }
            
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength++;
            
            // Contains lowercase
            if (/[a-z]/.test(password)) strength++;
            
            // Contains uppercase
            if (/[A-Z]/.test(password)) strength++;
            
            // Contains numbers
            if (/[0-9]/.test(password)) strength++;
            
            // Contains special characters
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            let strengthText = '';
            let strengthClass = '';
            
            if (strength <= 2) {
                strengthText = 'Weak password';
                strengthClass = 'strength-weak';
            } else if (strength <= 4) {
                strengthText = 'Medium password';
                strengthClass = 'strength-medium';
            } else {
                strengthText = 'Strong password ✓';
                strengthClass = 'strength-strong';
            }
            
            strengthDiv.textContent = strengthText;
            strengthDiv.className = 'password-strength ' + strengthClass;
        });
        
        // Password match checker
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchDiv.textContent = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchDiv.textContent = 'Passwords match ✓';
                matchDiv.className = 'password-strength strength-strong';
            } else {
                matchDiv.textContent = 'Passwords do not match';
                matchDiv.className = 'password-strength strength-weak';
            }
        });
        
        // Form validation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const terms = document.getElementById('terms').checked;
            
            // Password match validation
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            // Password strength validation
            if (password.length < 8) {
                alert('Password must be at least 8 characters long');
                return;
            }
            
            // Terms agreement validation
            if (!terms) {
                alert('You must agree to the Terms & Conditions');
                return;
            }
            
            // If all validations pass, submit the form
            this.submit();
        });
    </script>
</body>
</html>