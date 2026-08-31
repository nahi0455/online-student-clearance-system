<?php
session_start();
include('connect.php');
error_reporting(0);

if (isset($_POST['btnlogin'])) {
    $username = mysqli_real_escape_string($conn, $_POST['txtusername']);
    $password = mysqli_real_escape_string($conn, $_POST['txtpassword']);

    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password' AND status='Active'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        // ✅ Store all important session data
        $_SESSION['admin-username'] = $row['username'];
        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['department'] = $row['department'];
        $_SESSION['photo'] = $row['photo'];

        if ($row['role'] == 'super_admin') {
            header("Location: ../super_admin/super_admin.php");
        } elseif ($row['role'] == 'department_head') {
            header("Location: ../Admin/department/index.php");
        } elseif ($row['role'] == 'library') {
            header("Location: ../Admin/library/index.php");
        } elseif ($row['role'] == 'library_chief') {
            header("Location: ../Admin/office/index.php");
        } elseif ($row['role'] == 'registrar') {
            header("Location: ../Admin/registrar/registrar.php");
        } elseif ($row['role'] == 'dean') {
            header("Location: ../Admin/dean/dean.php");
        } elseif ($row['role'] == 'police') {
            header("Location: ../Admin/police/police.php");
        } elseif ($row['role'] == 'sport') {
            header("Location: ../Admin/sport/sport.php");
        } elseif ($row['role'] == 'registr') {
            header("Location: ../registr/dashboard.php");
        } elseif ($row['role'] == 'bookstore') {
            header("Location: ../Admin/bookstore/bookstore.php");
        } elseif ($row['role'] == 'cafeteria') {
            header("Location: ../Admin/cafeteria/cafeteria.php");
        } elseif ($row['role'] == 'dormitory') {
            header("Location: ../Admin/dormitory/dormitory.php");
        } else {
            header("");
        }
        exit();
    } else {
        $_SESSION['error'] = 'Incorrect username or password. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - University Management System</title>
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/favicon.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Amazing Professional Admin Login - University Management System */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #8B5A2B;
            --primary-light: #A0522D;
            --primary-dark: #654321;
            --accent-color: #D4AF37;
            --text-primary: #2D3748;
            --text-secondary: #718096;
            --text-light: #A0AEC0;
            --bg-primary: #F7FAFC;
            --bg-secondary: #EDF2F7;
            --white: #FFFFFF;
            --success: #48BB78;
            --error: #F56565;
            --warning: #ED8936;
            --shadow-light: rgba(0, 0, 0, 0.1);
            --shadow-medium: rgba(0, 0, 0, 0.15);
            --shadow-heavy: rgba(0, 0, 0, 0.25);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, 
                var(--primary-color) 0%, 
                var(--primary-light) 25%, 
                var(--accent-color) 50%, 
                var(--primary-dark) 75%, 
                var(--primary-color) 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.7;
            animation: float 6s ease-in-out infinite;
        }

        .orb-1 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, var(--accent-color), transparent);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, var(--primary-light), transparent);
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .orb-3 {
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3), transparent);
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(10px) rotate(240deg); }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
    </div>

    <?php if(isset($_SESSION['error'])): ?>
        <div style="position: fixed; top: 20px; right: 20px; background: rgba(245, 101, 101, 0.95); backdrop-filter: blur(10px); color: white; padding: 15px 20px; border-radius: 12px; box-shadow: 0 8px 32px rgba(245, 101, 101, 0.3); z-index: 1000;">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Main Container -->
    <div style="display: flex; min-height: 100vh; position: relative; z-index: 2;">
        <!-- Brand Panel -->
        <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px; position: relative; overflow: hidden;">
            <div style="text-align: center; color: white; z-index: 2; position: relative;">
                <div style="margin-bottom: 40px;">
                    <div style="position: relative; width: 140px; height: 140px; margin: 0 auto;">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 2px solid transparent; border-top: 2px solid var(--accent-color); border-radius: 50%; animation: rotate 8s linear infinite;"></div>
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 2px solid transparent; border-right: 2px solid rgba(255, 255, 255, 0.5); border-radius: 50%; animation: rotate 12s linear infinite reverse;"></div>
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 2px solid transparent; border-bottom: 2px solid rgba(255, 255, 255, 0.3); border-radius: 50%; animation: rotate 15s linear infinite;"></div>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 100px; height: 100px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--accent-color); overflow: hidden; padding: 10px; border: 3px solid rgba(255, 255, 255, 0.2);">
                            <img src="../images/logo.png" alt="University Logo" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; transition: all 0.3s ease; border: 2px solid rgba(255, 255, 255, 0.3);" onerror="this.style.display='none'">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 50px; height: 50px; display: none;">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                <path d="M2 17l10 5 10-5"/>
                                <path d="M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 20px; line-height: 1.2;">
                    <span style="display: block;">University</span>
                    <span style="display: block;">Management</span>
                    <span style="display: block;">System</span>
                </h1>
                <p style="font-size: 1.2rem; font-weight: 400; opacity: 0.9; margin-bottom: 50px;">Empowering Education Through Technology</p>
                
                <div style="display: flex; flex-direction: column; gap: 20px; max-width: 300px; margin: 0 auto;">
                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px 20px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.2); transition: all 0.3s ease;">
                        <div style="width: 24px; height: 24px; color: var(--accent-color);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4"/>
                                <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                                <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                                <path d="M13 12h3a2 2 0 0 1 2 2v1"/>
                                <path d="M13 12h-3a2 2 0 0 0-2 2v1"/>
                            </svg>
                        </div>
                        <span style="font-weight: 500;">Secure Access</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px 20px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.2); transition: all 0.3s ease;">
                        <div style="width: 24px; height: 24px; color: var(--accent-color);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                            </svg>
                        </div>
                        <span style="font-weight: 500;">Fast Performance</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; padding: 15px 20px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.2); transition: all 0.3s ease;">
                        <div style="width: 24px; height: 24px; color: var(--accent-color);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <span style="font-weight: 500;">Data Protection</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px; position: relative;">
            <div style="width: 100%; max-width: 450px;">
                <div style="background: var(--glass-bg); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-radius: 24px; padding: 50px 40px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.2); position: relative; overflow: hidden;">
                    <div style="text-align: center; margin-bottom: 40px;">
                        <div style="position: relative; width: 80px; height: 80px; margin: 0 auto 30px;">
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 3px solid var(--accent-color); border-radius: 50%; animation: spin 8s linear infinite;"></div>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                        </div>
                        <h2 style="font-size: 2.2rem; font-weight: 700; color: white; margin-bottom: 10px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">Admin Portal</h2>
                        <p style="color: rgba(255, 255, 255, 0.8); font-size: 1rem; font-weight: 400;">Welcome back! Please sign in to continue</p>
                    </div>
            
            <form role="form" method="POST" action="">
                <div style="margin-bottom: 30px; position: relative;">
                    <div style="position: relative; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 16px; transition: all 0.3s ease; overflow: hidden;">
                        <div style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: rgba(255, 255, 255, 0.6); transition: all 0.3s ease; z-index: 2;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <input type="text" name="txtusername" required autocomplete="off" placeholder="Enter your username" style="width: 100%; background: transparent; border: none; padding: 20px 24px; padding-left: 55px; color: white; font-size: 16px; font-weight: 500; outline: none; position: relative; z-index: 2;">
                    </div>
                </div>

                <div style="margin-bottom: 30px; position: relative;">
                    <div style="position: relative; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.2); border-radius: 16px; transition: all 0.3s ease; overflow: hidden; padding-right: 60px;">
                        <div style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: rgba(255, 255, 255, 0.6); transition: all 0.3s ease; z-index: 2;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                        </div>
                        <input type="password" name="txtpassword" required autocomplete="off" placeholder="Enter your password" style="width: 100%; background: transparent; border: none; padding: 20px 24px; padding-left: 55px; color: white; font-size: 16px; font-weight: 500; outline: none; position: relative; z-index: 2;">
                        
                        <button type="button" onclick="togglePassword()" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); width: 40px; height: 40px; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: rgba(255, 255, 255, 0.7); transition: all 0.3s ease; z-index: 3;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 20px;">
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" id="remember" name="remember" style="display: none;">
                        <label for="remember" style="display: flex; align-items: center; gap: 12px; cursor: pointer; user-select: none; color: rgba(255, 255, 255, 0.8); font-size: 14px; font-weight: 500; transition: color 0.3s ease;">
                            <div style="width: 24px; height: 24px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.3); border-radius: 6px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; position: relative; overflow: hidden;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="width: 14px; height: 14px; color: white; opacity: 0; transform: scale(0); transition: all 0.3s ease; position: relative; z-index: 2;">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <span>Remember me</span>
                        </label>
                    </div>
                    <a href="../home/index.php" style="display: flex; align-items: center; gap: 8px; color: rgba(255, 255, 255, 0.8); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.3s ease; padding: 8px 12px; border-radius: 8px; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;">
                            <path d="M19 12H5"/>
                            <path d="M12 19l-7-7 7-7"/>
                        </svg>
                        Back to Home
                    </a>
                </div>

                <button type="submit" name="btnlogin" style="width: 100%; background: linear-gradient(135deg, var(--accent-color), #F4D03F); border: none; border-radius: 16px; padding: 18px 32px; color: var(--primary-dark); font-size: 16px; font-weight: 700; cursor: pointer; position: relative; overflow: hidden; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);">
                    <span style="position: relative; z-index: 2; display: flex; align-items: center; justify-content: center;">
                        <span>Sign In to Admin Portal</span>
                    </span>
                </button>
            </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes rotate { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        input::placeholder { color: rgba(255, 255, 255, 0.7); }
        button:hover { transform: translateY(-3px); box-shadow: 0 12px 35px rgba(212, 175, 55, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2); }
        .input-wrapper:focus-within { border-color: var(--accent-color); box-shadow: 0 0 20px rgba(212, 175, 55, 0.3); transform: translateY(-2px); }
        .input-wrapper:focus-within .input-icon { color: var(--accent-color); transform: translateY(-50%) scale(1.1); }
    </style>

    <script>
        function togglePassword() {
            const passwordInput = document.querySelector('input[name="txtpassword"]');
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
        }

        // Input focus animations
        document.querySelectorAll('.input-wrapper input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>

</body>
</html>