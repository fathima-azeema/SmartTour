<?php
// login.php
session_start();

// --- If user is already logged in, redirect to their dashboard ---
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
    switch ($_SESSION['user_type']) {
        case 'tourist':
            header("Location: tourist-dashboard.php");
            break;
        case 'student':
            header("Location: student-dashboard.php");
            break;
        case 'guide':
            header("Location: guide-dashboard.php");
            break;
        default:
            header("Location: dashboard.php");
    }
    exit();
}

// --- Database configuration ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tourism_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";
$success_message = "";

// --- Check for messages from other pages ---
if (isset($_GET['message'])) {
    if ($_GET['message'] == 'registered') {
        $success_message = "Registration successful! Please login to continue.";
    } elseif ($_GET['message'] == 'logged_out') {
        $success_message = "You have been successfully logged out.";
    } elseif ($_GET['message'] == 'session_expired') {
        $error_message = "Your session has expired. Please login again.";
    }
}

if (isset($_GET['reset']) && $_GET['reset'] == 'success') {
    $success_message = "Password reset successful! Please login with your new password.";
}

// --- Process login ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        // Check if account is locked
        $check_lock_sql = "SELECT lock_until FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_lock_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 1) {
            $lock_data = $check_result->fetch_assoc();

            if ($lock_data['lock_until'] && strtotime($lock_data['lock_until']) > time()) {
                $unlock_time = date('h:i A', strtotime($lock_data['lock_until']));
                $error_message = "Account temporarily locked. Try again after $unlock_time.";
            } else {
                // Not locked – verify credentials
                $sql = "SELECT id, first_name, email, password, user_type, is_active, failed_attempts 
                        FROM users WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();

                    if ($user['is_active'] == 0) {
                        $error_message = "Your account has been deactivated. Please contact support.";
                    } else {
                        if (password_verify($password, $user['password'])) {
                            // Success – reset attempts
                            $reset_sql = "UPDATE users SET failed_attempts = 0, lock_until = NULL, last_login = NOW() WHERE id = ?";
                            $reset_stmt = $conn->prepare($reset_sql);
                            $reset_stmt->bind_param("i", $user['id']);
                            $reset_stmt->execute();
                            $reset_stmt->close();

                            // Set session
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['email'] = $user['email'];
                            $_SESSION['user_type'] = $user['user_type'];
                            $_SESSION['first_name'] = $user['first_name'];
                            $_SESSION['last_login'] = time();

                            // Remember me
                            if (isset($_POST['remember'])) {
                                $token = bin2hex(random_bytes(32));
                                setcookie('remember_token', $user['id'] . ':' . $token, time() + 86400 * 30, '/');
                                $token_sql = "UPDATE users SET remember_token = ? WHERE id = ?";
                                $token_stmt = $conn->prepare($token_sql);
                                $token_stmt->bind_param("si", $token, $user['id']);
                                $token_stmt->execute();
                                $token_stmt->close();
                            }

                            // ✅ REDIRECT BASED ON USER TYPE
                            switch ($user['user_type']) {
                                case 'tourist':
                                    header("Location: tourist-dashboard.php");
                                    break;
                                case 'student':
                                    header("Location: student-dashboard.php");
                                    break;
                                case 'guide':
                                    header("Location: guide-dashboard.php");
                                    break;
                                default:
                                    header("Location: dashboard.php");
                            }
                            exit();
                        } else {
                            // Wrong password – increment attempts
                            $failed_attempts = $user['failed_attempts'] + 1;
                            $update_sql = "UPDATE users SET failed_attempts = ? WHERE id = ?";
                            $update_stmt = $conn->prepare($update_sql);
                            $update_stmt->bind_param("ii", $failed_attempts, $user['id']);
                            $update_stmt->execute();
                            $update_stmt->close();

                            if ($failed_attempts >= 5) {
                                $lock_time = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                $lock_sql = "UPDATE users SET lock_until = ? WHERE id = ?";
                                $lock_stmt = $conn->prepare($lock_sql);
                                $lock_stmt->bind_param("si", $lock_time, $user['id']);
                                $lock_stmt->execute();
                                $lock_stmt->close();
                                $error_message = "Account locked for 30 minutes due to too many failed attempts.";
                            } else {
                                $attempts_left = 5 - $failed_attempts;
                                $error_message = "Invalid password. {$attempts_left} attempts left.";
                            }
                        }
                    }
                } else {
                    $error_message = "Invalid email or password.";
                }
                $stmt->close();
            }
        } else {
            $error_message = "Invalid email or password.";
        }
        $check_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmartTour</title>
    <!-- Fonts, icons, and styles... -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
    
</head>
<body>

    <!-- Message Container -->
    <?php if ($success_message || $error_message): ?>
    <div class="message-container">
        <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $success_message; ?>
            <button class="close-alert">&times;</button>
        </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error_message; ?>
            <button class="close-alert">&times;</button>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="login-container">
        <!-- Left Side - Branding & Graphics -->
        <div class="login-left">
            <div class="logo">
                <i class="fas fa-map-marked-alt logo-icon"></i>
                <span class="logo-text">SmartTour</span>
            </div>
            <h1>Welcome Back Traveler!</h1>
            <p>Login to continue your journey with SmartTour. Access your bookings, personalized recommendations, and manage your travel experiences all in one place.</p>
            <div class="features">
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="feature-text">
                        <h4>Secure Login</h4>
                        <p>Your information is protected with bank-level security</p>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                    <div class="feature-text">
                        <h4>Instant Access</h4>
                        <p>Quick access to all your bookings and preferences</p>
                    </div>
                </div>
                <div class="feature">
                    <div class="feature-icon"><i class="fas fa-headset"></i></div>
                    <div class="feature-text">
                        <h4>24/7 Support</h4>
                        <p>Get help anytime during your travel journey</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-header">
                <h2>Sign In to SmartTour</h2>
                <p>Enter your credentials to access your account</p>
            </div>

            <form class="login-form" method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="login-btn" id="loginButton">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>

                <div class="divider">
                    <span>Or continue with</span>
                </div>

                <div class="social-login">
                    <button type="button" class="social-btn google">
                        <i class="fab fa-google"></i> Google
                    </button>
                    <button type="button" class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </button>
                </div>

                <div class="signup-link">
                    Don't have an account?
                    <a href="signup.php">Sign Up Now</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Close alert messages
        document.querySelectorAll('.close-alert').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.style.animation = 'slideIn 0.3s ease-out reverse';
                setTimeout(() => {
                    this.parentElement.remove();
                }, 300);
            });
        });

        // Auto-remove alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.animation = 'slideIn 0.3s ease-out reverse';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            });
        }, 5000);

        // Form Validation & Loading State
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginButton');

            if (!email || !password) {
                e.preventDefault();
                showMessage('Please fill in all fields', 'error');
                return;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                showMessage('Please enter a valid email address', 'error');
                return;
            }

            loginBtn.innerHTML = '<i class="fas fa-spinner btn-spinner"></i> Signing In...';
            loginBtn.disabled = true;
        });

        function showMessage(message, type) {
            const container = document.querySelector('.message-container') || createMessageContainer();
            const alert = document.createElement('div');
            alert.className = `alert alert-${type === 'error' ? 'error' : 'success'}`;
            alert.innerHTML = `
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
                ${message}
                <button class="close-alert">&times;</button>
            `;
            container.appendChild(alert);
            alert.querySelector('.close-alert').addEventListener('click', function() {
                alert.style.animation = 'slideIn 0.3s ease-out reverse';
                setTimeout(() => { alert.remove(); }, 300);
            });
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.style.animation = 'slideIn 0.3s ease-out reverse';
                    setTimeout(() => { alert.remove(); }, 300);
                }
            }, 5000);
        }

        function createMessageContainer() {
            const container = document.createElement('div');
            container.className = 'message-container';
            document.body.appendChild(container);
            return container;
        }

        document.querySelectorAll('.social-btn').forEach(button => {
            button.addEventListener('click', function() {
                const platform = this.classList.contains('google') ? 'Google' : 'Facebook';
                showMessage(`Redirecting to ${platform} login...`, 'success');
            });
        });

        document.querySelector('.forgot-link').addEventListener('click', function(e) {
            e.preventDefault();
            const email = prompt('Enter your email to reset password:');
            if (email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailRegex.test(email)) {
                    showMessage(`Password reset instructions sent to ${email}`, 'success');
                } else {
                    showMessage('Please enter a valid email address', 'error');
                }
            }
        });
    </script>
</body>
</html>