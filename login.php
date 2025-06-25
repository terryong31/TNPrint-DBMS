<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];

    // Connect to database
    $conn = new mysqli("localhost", "root", "", "sanko");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT password, name FROM admin WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword, $name);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            header("Location: sanko_database.php");
            exit();
        } else {
            $error = "Invalid ID or password.";
        }
    } else {
        $error = "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanko DB Management - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
            --shadow-color: rgba(31, 38, 135, 0.37);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s ease-in-out infinite;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float-shapes 15s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: -5s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: -10s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 10%;
            right: 20%;
            animation-delay: -7s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @keyframes float-shapes {
            0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
            33% { transform: translateY(-30px) translateX(30px) rotate(120deg); }
            66% { transform: translateY(30px) translateX(-30px) rotate(240deg); }
        }

        /* Glass Card */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 8px 32px var(--shadow-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px var(--shadow-color);
        }

        /* Input Styles */
        .glass-input {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 15px 20px;
            color: #1a202c;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .glass-input::placeholder {
            color: rgba(26, 32, 44, 0.6);
        }

        .glass-input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.6);
            background: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            color: #1a202c;
        }

        /* Button Styles */
        .glass-button {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 15px 30px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .glass-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .glass-button:hover::before {
            left: 100%;
        }

        .glass-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.2));
        }

        /* Icon Styles */
        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Error Message */
        .error-message {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 12px 20px;
            color: #fee;
            text-align: center;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Title Styles */
        .title-gradient {
            background: linear-gradient(135deg, #fff, #f8fafc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 300;
            margin-bottom: 40px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .glass-card {
                margin: 20px;
                border-radius: 15px;
            }
            
            .icon-wrapper {
                width: 60px;
                height: 60px;
                margin-bottom: 20px;
            }
            
            .glass-input, .glass-button {
                padding: 12px 16px;
                font-size: 14px;
            }
        }

        /* Input Focus Effects */
        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(26, 32, 44, 0.7);
            z-index: 2;
            transition: all 0.3s ease;
        }

        .input-group .glass-input {
            padding-left: 55px;
        }

        .input-group .glass-input:focus + i {
            color: #1a202c;
            transform: translateY(-50%) scale(1.1);
        }

        /* Footer */
        .footer-text {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation"></div>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="glass-card w-full max-w-md p-8">
            <!-- Logo/Icon -->
            <div class="icon-wrapper">
                <i class="bi bi-shield-lock-fill text-white" style="font-size: 2rem;"></i>
            </div>

            <!-- Title -->
            <div class="text-center mb-8">
                <h1 class="title-gradient text-3xl font-bold mb-2">Welcome Back</h1>
                <p class="subtitle">Sanko Database Management System</p>
            </div>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="login.php">
                <!-- Error Message -->
                <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <!-- User ID Input -->
                <div class="input-group">
                    <input type="text" 
                           name="id" 
                           class="glass-input w-full" 
                           placeholder="Enter your User ID" 
                           required>
                    <i class="bi bi-person-fill"></i>
                </div>

                <!-- Password Input -->
                <div class="input-group">
                    <input type="password" 
                           name="password" 
                           class="glass-input w-full" 
                           placeholder="Enter your Password" 
                           required>
                    <i class="bi bi-lock-fill"></i>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="glass-button w-full">
                    <span class="button-text">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Sign In
                    </span>
                    <div class="loading" style="display: none;"></div>
                </button>
            </form>

            <!-- Additional Info -->
            <div class="text-center mt-6">
                <p class="text-white text-opacity-60 text-sm">
                    <i class="bi bi-info-circle me-1"></i>
                    Secure access to your database management system
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-text">
        <i class="bi bi-shield-check me-1"></i>
        Powered by Sanko Database Management System
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const inputs = document.querySelectorAll('.glass-input');
            const submitButton = form.querySelector('button[type="submit"]');
            const buttonText = submitButton.querySelector('.button-text');
            const loading = submitButton.querySelector('.loading');

            // Add focus/blur animations to inputs
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });

                // Add typing animation
                input.addEventListener('input', function() {
                    if (this.value.length > 0) {
                        this.style.borderColor = 'rgba(255, 255, 255, 0.6)';
                        this.style.background = 'rgba(255, 255, 255, 0.3)';
                    } else {
                        this.style.borderColor = 'rgba(255, 255, 255, 0.3)';
                        this.style.background = 'rgba(255, 255, 255, 0.2)';
                    }
                });
            });

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                // Show loading state
                buttonText.style.display = 'none';
                loading.style.display = 'inline-block';
                submitButton.disabled = true;
                
                // Add a small delay to show the loading animation
                setTimeout(() => {
                    // The form will submit naturally after this
                }, 500);
            });

            // Add particle effect on click
            document.addEventListener('click', function(e) {
                createParticle(e.clientX, e.clientY);
            });

            function createParticle(x, y) {
                const particle = document.createElement('div');
                particle.style.position = 'fixed';
                particle.style.left = x + 'px';
                particle.style.top = y + 'px';
                particle.style.width = '4px';
                particle.style.height = '4px';
                particle.style.background = 'rgba(255, 255, 255, 0.7)';
                particle.style.borderRadius = '50%';
                particle.style.pointerEvents = 'none';
                particle.style.zIndex = '9999';
                particle.style.animation = 'particle-fade 0.8s ease-out forwards';
                
                document.body.appendChild(particle);
                
                setTimeout(() => {
                    particle.remove();
                }, 800);
            }

            // Add particle animation CSS
            const style = document.createElement('style');
            style.textContent = `
                @keyframes particle-fade {
                    0% {
                        transform: scale(1) translateY(0);
                        opacity: 1;
                    }
                    100% {
                        transform: scale(0) translateY(-20px);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);

            // Prevent form resubmission on page refresh
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        });
    </script>
</body>
</html>