<?php
session_start();

// Admin credentials (in production, use database with hashed passwords)
$admin_username = 'admin';
$admin_password = 'udara123'; // Change this password

$message = '';
$messageType = '';

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = time();
        header('Location: admin.php');
        exit;
    } else {
        $message = 'Invalid username or password!';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Udara NEWS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .login-header .brand-highlight {
            color: #e74c3c;
        }

        .login-header p {
            color: #7f8c8d;
            font-size: 1rem;
        }

        .lock-icon {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 1rem;
        }

        .message {
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            font-weight: 500;
            text-align: center;
        }

        .message.error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            background: white;
        }

        .form-group .icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            font-size: 1.1rem;
            pointer-events: none;
        }

        .form-group input.with-icon {
            padding-left: 3rem;
        }

        .login-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 2rem;
        }

        .back-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #2980b9;
        }

        .security-note {
            background: rgba(52, 152, 219, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.3);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 2rem;
            text-align: center;
        }

        .security-note p {
            color: #2c3e50;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem;
                margin: 1rem;
            }

            .login-header h1 {
                font-size: 1.8rem;
            }
        }

        /* Loading animation */
        .loading {
            display: none;
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }

        .login-btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="lock-icon">🔐</div>
            <h1>Udara <span class="brand-highlight">NEWS</span></h1>
            <p>Admin Panel Access</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="" id="loginForm">
            <div class="form-group">
                <label for="username">👤 Username</label>
                <input type="text" id="username" name="username" required autocomplete="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">🔑 Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                🚀 Login to Admin Panel
            </button>
        </form>

        <div class="back-link">
            <a href="index.php">← Back to Website</a>
        </div>

        <div class="security-note">
            <p>🛡️ <strong>Secure Access:</strong> This admin panel is protected. Only authorized personnel can access news management features.</p>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.textContent = 'Logging in...';
        });

        // Auto-focus username field
        document.getElementById('username').focus();

        // Simple client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (username === '' || password === '') {
                e.preventDefault();
                alert('Please fill in all fields.');
                return false;
            }
        });
    </script>
</body>
</html>