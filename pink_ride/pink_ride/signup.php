<?php
session_start();
require_once 'db_connect.php';
require_once 'lang.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // تنظيف المدخلات من المسافات الزائدة
    $full_name = trim(mysqli_real_escape_string($conn, $_POST['full_name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];
    $phone_number = trim(mysqli_real_escape_string($conn, $_POST['phone_number']));
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // حساب العمر بناءً على تاريخ الميلاد
    $today = new DateTime();
    $birthdate = new DateTime($dob);
    $age = $today->diff($birthdate)->y;

    // ==========================================
    // ✅ التحققات الصارمة (Boss Level Validations)
    // ==========================================
    if (empty($full_name) || !preg_match('/^[\p{Arabic}a-zA-Z\s]+$/u', $full_name)) {
        $error_message = "Error: Name must contain ONLY letters and spaces!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Error: Invalid email format!";
    } elseif (!preg_match('/^07[789][0-9]{7}$/', $phone_number)) {
        // يقبل فقط 079, 078, 077
        $error_message = "Error: Invalid Jordanian phone number! Must start with 079, 078, or 077.";
    } elseif ($age < 18) {
        // يجب أن يكون العمر 18 فما فوق
        $error_message = "Error: You must be at least 18 years old to register!";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W_]/', $password)) {
        // قوة كلمة المرور (تحديث الـ PHP ليتطابق مع الشروط الجديدة)
        $error_message = "Error: Password is too weak!";
    } else {
        // التحقق من عدم تكرار الإيميل
        $check_email = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($conn, $check_email);
        
        if (mysqli_num_rows($res) > 0) {
            $error_message = "Error: Email already exists in our system!";
        } else {
            // تشفير الباسورد
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (full_name, email, PASSWORD, phone_number, dob, role) 
                    VALUES ('$full_name', '$email', '$hashed_password', '$phone_number', '$dob', '$role')";
            
            if (mysqli_query($conn, $sql)) {
                $new_user_id = mysqli_insert_id($conn);
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_role'] = $role;
                
                if ($role == 'driver') {
                    header("Location: driver_dashboard.php");
                } else {
                    header("Location: search.php");

                }
                exit();
            } else {
                $error_message = "Error: Could not save data to database!";
            }
        }
    }
}

// تحديد أقصى تاريخ ميلاد مسموح (قبل 18 سنة من اليوم)
$max_date = date('Y-m-d', strtotime('-18 years'));
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang == 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PinkRide - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .premium-card { max-width: 550px; padding: 40px; }
        .instant-error {
            color: #d81b60; font-size: 0.85rem; font-weight: bold;
            margin-top: 5px; display: none; animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px  ); } to { opacity: 1; transform: translateY(0); } }
        .input-error { border-color: #d81b60 !important; box-shadow: 0 0 0 4px rgba(216, 27, 96, 0.1) !important; }
        .premium-btn:disabled { background: #d2d6de; cursor: not-allowed; transform: none; box-shadow: none; }

        /* ستايل أيقونة العين */
        .toggle-password {
            position: absolute; top: 50%; transform: translateY(-50%);
            background: transparent; border: none; color: var(--burgundy);
            cursor: pointer; opacity: 0.6; transition: 0.3s; z-index: 10;
        }
        html[dir="rtl"] .toggle-password { left: 15px; }
        html[dir="ltr"] .toggle-password { right: 15px; }
        .toggle-password:hover { opacity: 1; }

        /* ستايل قائمة شروط الباسورد */
        .password-strength {
            list-style: none; padding: 0; margin-top: 10px; font-size: 0.85rem; display: none;
        }
        .password-strength li { color: #636e72; margin-bottom: 5px; transition: 0.3s; }
        .password-strength li.valid { color: #2ecc71; font-weight: bold; }
        .password-strength li i { margin: 0 5px; }
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
            <?php echo __('signup_title'); ?> 🌸
        </h5>

        <?php if(!empty($error_message)): ?>
            <div class="alert alert-danger text-center" style="border-radius: 15px; font-weight: 500;">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="signupForm">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: var(--burgundy);"><?php echo __('full_name'); ?></label>
                    <div class="input-group-custom">
                        <input type="text" name="full_name" id="fullName" class="form-control premium-input" placeholder="<?php echo __('name_placeholder'); ?>" required>
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <div id="nameError" class="instant-error"><i class="fa-solid fa-xmark"></i> Letters & spaces only!</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: var(--burgundy);"><?php echo __('email'); ?></label>
                    <div class="input-group-custom">
                        <input type="email" name="email" id="email" class="form-control premium-input" placeholder="<?php echo __('email_placeholder'); ?>" required>
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <div id="emailError" class="instant-error"><i class="fa-solid fa-xmark"></i> Invalid email format!</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: var(--burgundy);"><?php echo __('phone'); ?></label>
                    <div class="input-group-custom">
                        <input type="text" name="phone_number" id="phone" class="form-control premium-input" placeholder="079xxxxxxx" required>
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div id="phoneError" class="instant-error"><i class="fa-solid fa-xmark"></i> Must be 079/078/077 and 10 digits!</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: var(--burgundy);"><?php echo __('dob'); ?></label>
                    <div class="input-group-custom">
                        <input type="date" name="dob" id="dob" class="form-control premium-input" max="<?php echo $max_date; ?>" required>
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold" style="color: var(--burgundy);"><?php echo __('password'); ?></label>
                    <div class="input-group-custom" style="position: relative;">
                        <input type="password" name="password" id="password" class="form-control premium-input" placeholder="<?php echo __('password_placeholder'); ?>" required>
                        <i class="fa-solid fa-lock"></i>
                        <!-- زر العين -->
                        <button type="button" class="toggle-password" id="togglePasswordBtn">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <!-- قائمة الشروط الذكية -->
                    <ul class="password-strength" id="passwordStrength">
                        <li id="req-length"><i class="fa-regular fa-circle"></i> Min 8 characters</li>
                        <li id="req-capital"><i class="fa-regular fa-circle"></i>  Capital Letters</li>
                        <li id="req-number"><i class="fa-regular fa-circle"></i>  Numbers</li>
                        <li id="req-special"><i class="fa-regular fa-circle"></i>  Special character (@$!%*?&)</li>
                    </ul>
                    <div id="passwordError" class="instant-error"><i class="fa-solid fa-xmark"></i> Password is too weak!</div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold" style="color: var(--burgundy);"><?php echo __('role'); ?></label>
                    <div class="input-group-custom">
                        <select name="role" class="form-control premium-input" style="appearance: none;" required>
                            <option value="passenger"><?php echo __('passenger'); ?></option>
                            <option value="driver"><?php echo __('driver'); ?></option>
                        </select>
                        <i class="fa-solid fa-venus"></i>
                    </div>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="premium-btn" disabled>
                <?php echo __('signup_btn'); ?> <i class="fa-solid fa-user-plus ms-1"></i>
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="login.php" class="text-decoration-none" style="color: var(--burgundy); font-weight: 700; font-size: 1.05rem; transition: 0.3s;">
                <?php echo __('have_account'); ?>
            </a>
        </div>
    </div>

    <script>
        const form = document.getElementById('signupForm');
        const submitBtn = document.getElementById('submitBtn');
        
        // ميزة إظهار/إخفاء كلمة المرور
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePasswordBtn.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });

        function validateInput(input, regex, errorId) {
            const errorDiv = document.getElementById(errorId);
            if (input.value.length > 0 && !regex.test(input.value)) {
                errorDiv.style.display = 'block';
                input.classList.add('input-error');
                return false;
            } else {
                errorDiv.style.display = 'none';
                input.classList.remove('input-error');
                return true;
            }
        }

        function checkFormValidity() {
            const isNameValid = document.getElementById('fullName').value !== '' && /^[\u0600-\u06FFa-zA-Z\s]+$/.test(document.getElementById('fullName').value);
            const isEmailValid = document.getElementById('email').value !== '' && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(document.getElementById('email').value);
            const isPhoneValid = document.getElementById('phone').value !== '' && /^07[789][0-9]{7}$/.test(document.getElementById('phone').value);
            
            // التحقق من قوة الباسورد
            const passVal = document.getElementById('password').value;
            const isPassValid = passVal.length >= 8 && /[A-Z]/.test(passVal) && /[0-9]/.test(passVal) && /[\W_]/.test(passVal);

            if (isNameValid && isEmailValid && isPhoneValid && isPassValid) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }

        document.getElementById('fullName').addEventListener('input', function() {
            validateInput(this, /^[\u0600-\u06FFa-zA-Z\s]+$/, 'nameError');
            checkFormValidity();
        });

        document.getElementById('email').addEventListener('input', function() {
            validateInput(this, /^[^\s@]+@[^\s@]+\.[^\s@]+$/, 'emailError');
            checkFormValidity();
        });

        document.getElementById('phone').addEventListener('input', function() {
            validateInput(this, /^07[789][0-9]{7}$/, 'phoneError');
            checkFormValidity();
        });

        // التحقق الذكي من الباسورد (تلوين الشروط بالأخضر)
        const reqLength = document.getElementById('req-length');
        const reqCapital = document.getElementById('req-capital');
        const reqNumber = document.getElementById('req-number');
        const reqSpecial = document.getElementById('req-special');
        const passwordStrengthBox = document.getElementById('passwordStrength');
        const passwordError = document.getElementById('passwordError');

        function toggleRequirement(element, isValid) {
            const icon = element.querySelector('i');
            if (isValid) {
                element.classList.add('valid');
                icon.classList.remove('fa-circle');
                icon.classList.add('fa-circle-check');
            } else {
                element.classList.remove('valid');
                icon.classList.remove('fa-circle-check');
                icon.classList.add('fa-circle');
            }
        }

        document.getElementById('password').addEventListener('input', function() {
            const val = this.value;
            
            // إظهار القائمة عند بدء الكتابة
            if (val.length > 0) {
                passwordStrengthBox.style.display = 'block';
            } else {
                passwordStrengthBox.style.display = 'none';
            }

            // فحص الشروط
            const isLengthValid = val.length >= 8;
            const isCapitalValid = /[A-Z]/.test(val);
            const isNumberValid = /[0-9]/.test(val);
            const isSpecialValid = /[\W_]/.test(val);

            toggleRequirement(reqLength, isLengthValid);
            toggleRequirement(reqCapital, isCapitalValid);
            toggleRequirement(reqNumber, isNumberValid);
            toggleRequirement(reqSpecial, isSpecialValid);

            // إظهار الإيرور الأحمر إذا الشروط مش مكتملة
            if (val.length > 0 && !(isLengthValid && isCapitalValid && isNumberValid && isSpecialValid)) {
                passwordError.style.display = 'block';
                this.classList.add('input-error');
            } else {
                passwordError.style.display = 'none';
                this.classList.remove('input-error');
            }
            
            checkFormValidity();
        });
    </script>

</body>
</html>
