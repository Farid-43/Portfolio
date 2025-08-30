<?php
require_once __DIR__ . '/includes/auth.php';

$auth = new Auth();
$error = '';

$cookieOptions = [
    'expires' => time() + (86400 * 30),
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
];

if (!$auth->isLoggedIn() && isset($_COOKIE['remember_token'])) {
    if ($auth->loginWithToken($_COOKIE['remember_token'])) {
        header('Location: admin/');
        exit();
    } else {

        setcookie('remember_token', '', time() - 3600, '/');
    }
}

if ($auth->isLoggedIn()) {
    header('Location: admin/');
    exit();
}

if ($_POST && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);
    
    if ($auth->login($username, $password)) {
        if ($rememberMe) {

            $token = bin2hex(random_bytes(32));
            $auth->setRememberToken($token);
            setcookie('remember_token', $token, $cookieOptions);
        }
        header('Location: admin/');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portfolio</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #666;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .error {
            background: #dc3545;
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .back-home {
            text-align: center;
            margin-top: 1rem;
        }
        
        .back-home a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-home a:hover {
            text-decoration: underline;
        }
        
        .checkbox-group {
            margin: 1rem 0;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 0.9rem;
            color: #666;
        }
        
        .checkbox-label input[type="checkbox"] {
            margin-right: 0.5rem;
            transform: scale(1.2);
            accent-color: #667eea;
        }
        
        .checkbox-label:hover {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-lock"></i> Admin Login</h1>
            <p>Access the portfolio admin panel</p>
        </div>
        
        <?php if ($error): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group checkbox-group">
                <label for="remember_me" class="checkbox-label">
                    <input type="checkbox" id="remember_me" name="remember_me">
                    <span class="checkmark"></span>
                    Remember me for 30 days
                </label>
            </div>
            
            <button type="submit" name="login" class="btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="back-home">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Portfolio</a>
        </div>
    </div>
</body>
</html>
