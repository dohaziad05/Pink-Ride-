<?php
session_start();
require_once 'db_connect.php';
require_once 'lang.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_input = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password_input = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($email_input) && !empty($password_input)) {
        if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Error: Invalid email format!";
        } else {
            $email = mysqli_real_escape_string($conn, $email_input);
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $sql);

            if ($result && mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $db_password = isset($row['PASSWORD']) ? $row['PASSWORD'] : '';

                // التحقق من كلمة المرور المشفرة في قاعدة البيانات
                if (password_verify($password_input, $db_password)) { 
                    // حماية الجلسة
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $row['full_name'];
                    $_SESSION['user_role'] = $row['role'];
                    
                    if($row['role'] == 'driver') {
                        header("Location: driver_dashboard.php");
                    } else {
                        header("Location: passenger_home.php");
                    }
                    exit();
                } else {
                    $error_message = "Error: Invalid email or password!"; 
                }
            } else {
                $error_message = "Error: Invalid email or password!";
            }
        }
    } else {
        $error_message = "Error: Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang == 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PinkRide - Premium Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* =========================================
           تنسيق الحقول الجديد (دقيق جداً وبدون تداخل )
           ========================================= */
        .input-group-text {
            background-color: #fff;
            color: var(--burgundy, #800020);
            border-color: #ced4da;
        }
        
        .premium-input {
            padding-top: 12px;
            padding-bottom: 12px;
            border-left: none;
            border-right: none;
        }

        .premium-input:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        /* تأثير التركيز على المجموعة كاملة */
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .premium-input {
            border-color: var(--burgundy, #800020);
        }

        .toggle-password-btn {
            cursor: pointer;
            transition: 0.3s;
        }
        
        .toggle-password-btn:hover {
            background-color: #f8f9fa;
        }

        /* ========================================= */
        
        .instant-error {
            color: #d81b60; font-size: 0.85rem; font-weight: bold;
            margin-top: 5px; display: none; animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        .input-error { border-color: #d81b60 !important; box-shadow: 0 0 0 4px rgba(216, 27, 96, 0.1) !important; }
        .premium-btn:disabled { background: #d2d6de; cursor: not-allowed; transform: none; box-shadow: none; }
    </style>
</head>
<body>

    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <a href="?lang=<?php echo $lang == 'ar' ? 'en' : 'ar'; ?>" class="lang-toggle">
        <i class="fa-solid fa-globe me-1"></i> <?php echo $lang == 'ar' ? 'English' : 'عربي'; ?>
    </a>

    <div class="premium-card">
        <div class="brand-logo">Pink<span>Ride</span> <i class="fa-solid fa-car-side fa-sm"></i></div>
        <h5 class="text-center mb-4 mt-2" style="color: #636e72; font-weight: 500;">
            <?php echo __('login_title'); ?> ✨
        </h5>

        <?php if(!empty($error_message)): ?>
            <div class="alert alert-danger text-center" style="border-radius: 15px; font-weight: 500;">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm">
            <div class="mb-3">
                <label class="form-label fw-bold" style="color: var(--burgundy, #800020);"><?php echo __('email'); ?></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                    <input type="email" name="email" id="email" class="form-control premium-input" placeholder="<?php echo __('email_placeholder'); ?>" required>
                </div>
                <div id="emailError" class="instant-error"><i class="fa-solid fa-xmark"></i> Invalid email format!</div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold" style="color: var(--burgundy, #800020);"><?php echo __('password'); ?></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control premium-input" placeholder="<?php echo __('password_placeholder'); ?>" required>
                    <span class="input-group-text toggle-password-btn" id="togglePassword">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="premium-btn w-100 mt-2">
                <?php echo __('login_btn'); ?> <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="signup.php" class="text-decoration-none" style="color: var(--burgundy, #800020); font-weight: 700; font-size: 1.05rem; transition: 0.3s;">
                <?php echo __('no_account'); ?>
            </a>
        </div>
    </div>

    <script>
        // ميزة إظهار/إخفاء كلمة المرور بدقة
        const togglePasswordBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePasswordBtn.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // تغيير شكل الأيقونة
            if (type === 'text') {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });

        // التحقق الفوري من الإيميل
        const emailInput = document.getElementById('email');
        const submitBtn = document.getElementById('submitBtn');
        const emailError = document.getElementById('emailError');

        emailInput.addEventListener('input', function() {
            let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value.length > 0 && !regex.test(this.value)) {
                emailError.style.display = 'block';
                this.classList.add('input-error');
                submitBtn.disabled = true;
            } else {
                emailError.style.display = 'none';
                this.classList.remove('input-error');
                submitBtn.disabled = false;
            }
        });
    </script>

</body>
</html>
