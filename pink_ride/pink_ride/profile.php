<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
require_once 'lang.php';

$user_id = $_SESSION['user_id'];
$error_message = "";
$success_message = "";
$info_message = "";

// جلب بيانات المستخدم الحالية (الكود الأصلي)
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user_data = mysqli_fetch_assoc($result);

// --- بداية الإضافة (الجزء الأول) ---
// جلب حجوزات الراكبة (إذا كانت راكبة)
$bookings_result = null;
if ($user_data['role'] == 'passenger') {
    $sql_bookings = "SELECT rides.*, users.full_name AS driver_name, bookings.booking_time 
                     FROM bookings 
                     JOIN rides ON bookings.ride_id = rides.id 
                     JOIN users ON rides.driver_id = users.id
                     WHERE bookings.passenger_id = ? 
                     ORDER BY bookings.booking_time DESC";
    $stmt_bookings = $conn->prepare($sql_bookings);
    $stmt_bookings->bind_param("i", $user_id);
    $stmt_bookings->execute();
    $bookings_result = $stmt_bookings->get_result();
}
// --- نهاية الإضافة (الجزء الأول) ---


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (كل كود التحديث الأصلي يبقى كما هو بدون أي تغيير)
    $phone_input = isset($_POST['phone_number']) ? $_POST['phone_number'] : $user_data['phone_number'];
    $role_input = isset($_POST['role']) ? $_POST['role'] : $user_data['role'];
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';

    $new_phone = mysqli_real_escape_string($conn, $phone_input);
    $new_role = mysqli_real_escape_string($conn, $role_input);

    $changes_made = false;
    $update_queries = [];

    if ($new_phone !== $user_data['phone_number']) {
        if (!preg_match('/^07[789][0-9]{7}$/', $new_phone)) {
            $error_message = "Error: Invalid Jordanian phone number!";
        } else {
            $update_queries[] = "phone_number = '$new_phone'";
            $changes_made = true;
        }
    }

    if ($new_role !== $user_data['role']) {
        $update_queries[] = "role = '$new_role'";
        $changes_made = true;
        $_SESSION['user_role'] = $new_role; 
    }

    if (!empty($new_password)) {
        if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password) || !preg_match('/[\W_]/', $new_password)) {
            $error_message = "Error: Password is too weak!";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_queries[] = "PASSWORD = '$hashed_password'";
            $changes_made = true;
        }
    }

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024;
        $file_type = $_FILES['profile_pic']['type'];
        $file_size = $_FILES['profile_pic']['size'];
        if (!in_array($file_type, $allowed_types)) {
            $error_message = __('invalid_image');
        } elseif ($file_size > $max_size) {
            $error_message = __('large_image');
        } else {
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            $extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $new_image_name = uniqid('profile_', true) . '.' . $extension;
            $upload_path = 'uploads/' . $new_image_name;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
                $update_queries[] = "profile_pic = '$new_image_name'";
                $changes_made = true;
                $user_data['profile_pic'] = $new_image_name;
            }
        }
    }

    if (empty($error_message)) {
        if ($changes_made) {
            $set_clause = implode(", ", $update_queries);
            $update_sql = "UPDATE users SET $set_clause WHERE id = $user_id";
            if (mysqli_query($conn, $update_sql)) {
                $success_message = "Profile updated successfully!";
                $user_data['phone_number'] = $new_phone;
                $user_data['role'] = $new_role;
            } else {
                $error_message = "Error: Could not update profile!";
            }
        } else {
            $info_message = __('no_changes');
        }
    }
}

// --- تعديل بسيط على رابط العودة ليكون ذكياً ---
$home_link = ($user_data['role'] == 'driver') ? 'driver_dashboard.php' : 'search.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang == 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <!-- ... (كل قسم الـ head الأصلي يبقى كما هو) ... -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PinkRide - Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- بداية الإضافة (الجزء الثاني ) --- */
        .ride-item { 
            background: #f8f9fa; 
            border-radius: 12px; 
            padding: 15px; 
            margin-bottom: 10px; 
            border-right: 5px solid var(--burgundy); 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        /* --- نهاية الإضافة (الجزء الثاني) --- */

        .profile-pic-container {
            position: relative; width: 110px; height: 110px; margin: 0 auto 15px; cursor: pointer;
        }
        .profile-pic {
            width: 110px; height: 110px; border-radius: 50%; object-fit: cover;
            border: 4px solid var(--burgundy ); box-shadow: 0 10px 20px rgba(128,0,32,0.15); transition: 0.3s;
        }
        .profile-pic-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); border-radius: 50%; display: flex;
            align-items: center; justify-content: center; color: white; opacity: 0; transition: 0.3s;
        }
        .profile-pic-container:hover .profile-pic-overlay { opacity: 1; }
        #fileInput { display: none; }
        .input-group-custom {
            position: relative; width: 100%; background: #fff;
            border: 1px solid #ced4da; border-radius: 8px; transition: 0.3s;
        }
        .input-group-custom:focus-within {
            border-color: var(--burgundy); box-shadow: 0 0 0 4px rgba(128,0,32,0.1);
        }
        .main-icon {
            position: absolute; top: 50%; transform: translateY(-50%);
            color: var(--burgundy); font-size: 1.1rem; z-index: 5;
        }
        .premium-input {
            width: 100%; border: none !important; outline: none !important;
            padding: 12px 15px; background: transparent; box-shadow: none !important; border-radius: 8px;
        }
        html[dir="rtl"] .main-icon { right: 15px; }
        html[dir="rtl"] .premium-input { padding-right: 45px; }
        html[dir="ltr"] .main-icon { left: 15px; }
        html[dir="ltr"] .premium-input { padding-left: 45px; }
        .password-wrapper { display: flex; align-items: center; gap: 10px; }
        .eye-btn-outside {
            background: #fff; border: 1px solid #ced4da; border-radius: 8px;
            width: 50px; height: 48px; display: flex; align-items: center;
            justify-content: center; color: var(--burgundy); cursor: pointer;
            transition: 0.3s; flex-shrink: 0;
        }
        .eye-btn-outside:hover { background: var(--baby-pink); border-color: var(--burgundy); }
        .instant-error { color: #d81b60; font-size: 0.85rem; font-weight: bold; margin-top: 5px; display: none; }
        .input-error-border { border-color: #d81b60 !important; box-shadow: 0 0 0 4px rgba(216, 27, 96, 0.1) !important; }
        .premium-btn:disabled { background: #d2d6de; cursor: not-allowed; transform: none; box-shadow: none; }
    </style>
</head>
<body>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <a href="?lang=<?php echo $lang == 'ar' ? 'en' : 'ar'; ?>" class="lang-toggle">
        <i class="fa-solid fa-globe me-1"></i> <?php echo $lang == 'ar' ? 'English' : 'عربي'; ?>
    </a>
    <div class="premium-card" style="max-width: 700px;">
        <form method="POST" action="" enctype="multipart/form-data" id="profileForm">
            <!-- ... (كل نموذج تعديل البيانات الأصلي يبقى كما هو) ... -->
            <div class="text-center mb-4">
                <div class="profile-pic-container" onclick="document.getElementById('fileInput').click();">
                    <?php if($user_data['profile_pic'] != 'default.png' && file_exists('uploads/'.$user_data['profile_pic'])): ?>
                        <img src="uploads/<?php echo $user_data['profile_pic']; ?>" class="profile-pic" alt="Profile">
                    <?php else: ?>
                        <div class="profile-pic" style="background: var(--baby-pink); display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-user-astronaut fa-3x" style="color: var(--burgundy);"></i></div>
                    <?php endif; ?>
                    <div class="profile-pic-overlay"><i class="fa-solid fa-camera fa-lg"></i></div>
                </div>
                <input type="file" name="profile_pic" id="fileInput" accept="image/png, image/jpeg, image/jpg">
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: -5px; margin-bottom: 10px; font-weight: bold;"><?php echo __('change_pic'); ?></div>
                <h4 style="color: var(--burgundy); font-weight: 800;"><?php echo $user_data['full_name']; ?></h4>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label fw-bold" style="color: var(--burgundy);"><?php echo __('email'); ?></label><div class="input-group-custom" style="background-color: #f8f9fa; opacity: 0.8;"><i class="fa-solid fa-envelope main-icon"></i><input type="email" class="premium-input" value="<?php echo $user_data['email']; ?>" readonly></div></div>
                <div class="col-md-6 mb-3"><label class="form-label fw-bold" style="color: var(--burgundy);"><?php echo __('phone'); ?></label><div class="input-group-custom" id="phoneGroup"><i class="fa-solid fa-phone-flip main-icon"></i><input type="text" name="phone_number" id="phone" class="premium-input" value="<?php echo $user_data['phone_number']; ?>" required></div><div id="phoneError" class="instant-error"><i class="fa-solid fa-xmark"></i> Invalid Phone!</div></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label fw-bold" style="color: var(--burgundy);"><?php echo __('role'); ?></label><div class="input-group-custom"><i class="fa-solid fa-venus main-icon"></i><select name="role" class="premium-input" required><option value="passenger" <?php echo ($user_data['role'] == 'passenger') ? 'selected' : ''; ?>><?php echo __('passenger'); ?></option><option value="driver" <?php echo ($user_data['role'] == 'driver') ? 'selected' : ''; ?>><?php echo __('driver'); ?></option></select></div></div>
                <div class="col-md-6 mb-4"><label class="form-label fw-bold" style="color: var(--burgundy);">New Password (Optional)</label><div class="password-wrapper"><div class="input-group-custom" id="passGroup"><i class="fa-solid fa-lock main-icon"></i><input type="password" name="new_password" id="newPassword" class="premium-input" placeholder="Leave blank to keep current"></div><button type="button" class="eye-btn-outside" id="togglePasswordBtn"><i class="fa-solid fa-eye" id="eyeIcon"></i></button></div><div id="passError" class="instant-error"><i class="fa-solid fa-xmark"></i> Too weak!</div></div>
            </div>
            <button type="submit" id="submitBtn" class="premium-btn mb-3"><?php echo __('update_btn'); ?> <i class="fa-solid fa-pen-to-square ms-1"></i></button>
        </form>

        <!-- --- بداية الإضافة (الجزء الثاني) --- -->
        <!-- هذا القسم سيظهر فقط إذا كان المستخدم راكبة -->
        <?php if ($user_data['role'] == 'passenger'): ?>
            <hr>
            <div class="mt-4">
                <h5 class="fw-bold mb-3" style="color: var(--burgundy);"><i class="fa-solid fa-clock-rotate-left me-2"></i> رحلاتي المحجوزة</h5>
                <div class="booking-history">
                    <?php if($bookings_result && $bookings_result->num_rows > 0): ?>
                        <?php while($row = $bookings_result->fetch_assoc()): ?>
                            <div class="ride-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1">من: <?php echo htmlspecialchars($row['start_point']); ?> إلى: <?php echo htmlspecialchars($row['destination']); ?></h6>
                                    <small class="text-muted">السائقة: <?php echo htmlspecialchars($row['driver_name']); ?> | تاريخ الحجز: <?php echo date('Y-m-d', strtotime($row['booking_time'])); ?></small>
                                </div>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['price']); ?> JOD</div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-muted p-3">لا يوجد حجوزات حالياً. <a href="search.php">ابحثي عن رحلة الآن!</a></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <!-- --- نهاية الإضافة (الجزء الثاني) --- -->

        <div class="d-flex gap-3 mt-4">
            <a href="<?php echo $home_link; ?>" class="btn w-50" style="background: #f1f2f6; color: #2d3436; font-weight: bold; border-radius: 10px; padding: 10px;"><i class="fa-solid fa-arrow-left"></i> <?php echo __('back_btn'); ?></a>
            <button type="button" onclick="confirmLogout()" class="btn w-50" style="background: #ffeaa7; color: #d81b60; font-weight: bold; border-radius: 10px; padding: 10px; border: 1px solid #d81b60;"><?php echo __('logout_btn'); ?> <i class="fa-solid fa-right-from-bracket"></i></button>
        </div>
    </div>

    <script>
        // ... (كل كود الـ JavaScript الأصلي يبقى كما هو) ...
        document.getElementById('fileInput').addEventListener('change', function(event) {
            if(event.target.files.length > 0){
                var src = URL.createObjectURL(event.target.files[0]);
                var preview = document.querySelector('.profile-pic');
                if(preview.tagName === 'IMG') { preview.src = src; } else {
                    var img = document.createElement('img');
                    img.src = src; img.className = 'profile-pic';
                    preview.parentNode.replaceChild(img, preview);
                }
            }
        });
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passInput = document.getElementById('newPassword');
        const eyeIcon = document.getElementById('eyeIcon');
        togglePasswordBtn.addEventListener('click', function () {
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
        const phoneInput = document.getElementById('phone');
        const submitBtn = document.getElementById('submitBtn');
        function checkValidity() {
            const isPhoneValid = /^07[789][0-9]{7}$/.test(phoneInput.value);
            const passVal = passInput.value;
            const isPassValid = passVal === '' || (passVal.length >= 8 && /[A-Z]/.test(passVal) && /[0-9]/.test(passVal) && /[\W_]/.test(passVal));
            submitBtn.disabled = !(isPhoneValid && isPassValid);
        }
        phoneInput.addEventListener('input', function() {
            if (!/^07[789][0-9]{7}$/.test(this.value)) {
                document.getElementById('phoneError').style.display = 'block';
                document.getElementById('phoneGroup').classList.add('input-error-border');
            } else {
                document.getElementById('phoneError').style.display = 'none';
                document.getElementById('phoneGroup').classList.remove('input-error-border');
            }
            checkValidity();
        });
        passInput.addEventListener('input', function() {
            const val = this.value;
            if (val !== '' && (val.length < 8 || !/[A-Z]/.test(val) || !/[0-9]/.test(val) || !/[\W_]/.test(val))) {
                document.getElementById('passError').style.display = 'block';
                document.getElementById('passGroup').classList.add('input-error-border');
            } else {
                document.getElementById('passError').style.display = 'none';
                document.getElementById('passGroup').classList.remove('input-error-border');
            }
            checkValidity();
        });
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure?', text: "You will be logged out of your account!",
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#d81b60',
                cancelButtonColor: '#636e72', confirmButtonText: 'Yes, logout!'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = 'logout.php'; }
            });
        }
        <?php if(!empty($error_message)): ?>
            Swal.fire({ icon: 'error', title: 'Oops...', text: '<?php echo $error_message; ?>', confirmButtonColor: '#d81b60' });
        <?php endif; ?>
        <?php if(!empty($success_message)): ?>
            Swal.fire({ icon: 'success', title: 'Success!', text: '<?php echo $success_message; ?>', showConfirmButton: false, timer: 2000 });
        <?php endif; ?>
        <?php if(!empty($info_message)): ?>
            Swal.fire({ icon: 'info', title: 'No Changes', text: '<?php echo $info_message; ?>', confirmButtonColor: '#636e72' });
        <?php endif; ?>
    </script>
</body>
</html>
