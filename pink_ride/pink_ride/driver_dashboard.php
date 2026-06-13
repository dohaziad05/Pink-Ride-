<?php
session_start();
require_once 'db_connect.php';
require_once 'lang.php';

// 1. حماية الصفحة
// 1. حماية الصفحة والتأكد من الصلاحيات
if (!isset($_SESSION['user_id'])) {
    // إذا لم يكن مسجلاً، وجهه لصفحة الدخول
    header("Location: login.php");
    exit();
} elseif ($_SESSION['user_role'] != 'driver') {
    // إذا كان مسجلاً ولكنه ليس سائقاً (أي راكبة)، وجهها للرئيسية
    header("Location: index.php");
    exit();
}

$driver_id = $_SESSION['user_id'];
$driver_name = $_SESSION['user_name'];

// 2. معالجة إضافة رحلة جديدة
$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_ride'])) {
    $start_point = trim($conn->real_escape_string($_POST['start_point']));
    $destination = trim($conn->real_escape_string($_POST['destination']));
    $price = (float)$_POST['price'];
    $available_seats = (int)$_POST['available_seats'];

    if (empty($start_point) || empty($destination) || $price <= 0 || $available_seats <= 0) {
        $error_message = "الرجاء تعبئة جميع الحقول بشكل صحيح.";
    } else {
        $sql = "INSERT INTO rides (driver_id, start_point, destination, price, available_seats, status) VALUES (?, ?, ?, ?, ?, 'متاحة')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdi", $driver_id, $start_point, $destination, $price, $available_seats);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "تمت إضافة الرحلة بنجاح!";
            header("Location: driver_dashboard.php"); // تحديث الصفحة لتجنب إعادة الإرسال
            exit();
        } else {
            $error_message = "حدث خطأ أثناء إضافة الرحلة: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang == 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('driver_dashboard_title'); ?> - PinkRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            align-items: flex-start;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        .dashboard-card {
            background: rgba(255, 255, 255, 0.85  );
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 30px;
            width: 100%;
            max-width: 850px;
            box-shadow: 0 20px 50px rgba(128, 0, 32, 0.1);
            animation: none;
        }
        .table-custom { background: var(--white); border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .table-custom th { background-color: var(--baby-pink); color: var(--burgundy); font-weight: 700; }
        .action-btn { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; }
        .form-label { color: var(--burgundy); }
        .nav-buttons { position: fixed; top: 20px; right: 20px; z-index: 100; }
        html[dir="ltr"] .nav-buttons { right: auto; left: 20px; }
    </style>
</head>
<body>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="nav-buttons d-flex gap-2">
        <a href="?lang=<?php echo $lang == 'ar' ? 'en' : 'ar'; ?>" class="btn fw-bold" style="background: #fff; color: var(--burgundy);">
            <i class="fa-solid fa-globe"></i> <?php echo $lang == 'ar' ? 'English' : 'عربي'; ?>
        </a>
        <a href="profile.php" class="btn fw-bold" style="background: #fff; color: var(--burgundy);"><i class="fa-solid fa-user-pen me-2"></i><?php echo __('profile_title'); ?></a>
        <a href="logout.php" class="btn fw-bold" style="background: #fff; color: var(--burgundy);"><i class="fa-solid fa-right-from-bracket me-2"></i><?php echo __('logout_btn'); ?></a>
    </div>

    <div class="dashboard-card">
        <h3 class="text-center mb-1" style="color: var(--burgundy); font-weight: 800;"><?php echo __('welcome_driver'); ?> <?php echo htmlspecialchars($driver_name); ?>!</h3>
        <p class="text-center text-muted mb-4">هنا يمكنكِ إدارة جميع رحلاتكِ بكل سهولة.</p>

        <div class="p-4 mb-4" style="background: rgba(255,255,255,0.7); border-radius: 20px; border: 1px solid #eee;">
            <h4 class="mb-3 fw-bold" style="color: var(--burgundy-light);"><i class="fa-solid fa-circle-plus me-2"></i><?php echo __('add_new_ride'); ?></h4>
            <form method="POST" action="driver_dashboard.php">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold"><?php echo __('start_point'); ?>:</label><input type="text" name="start_point" class="form-control premium-input" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold"><?php echo __('destination'); ?>:</label><input type="text" name="destination" class="form-control premium-input" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold"><?php echo __('price'); ?>:</label><input type="number" name="price" step="0.1" min="0.1" class="form-control premium-input" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold"><?php echo __('available_seats'); ?>:</label><input type="number" name="available_seats" min="1" class="form-control premium-input" required></div>
                </div>
                <button type="submit" name="add_ride" class="premium-btn w-100 mt-2"><i class="fa-solid fa-car-side me-2"></i><?php echo __('add_ride_btn'); ?></button>
            </form>
        </div>

        <h4 class="mt-5 mb-3 fw-bold" style="color: var(--burgundy-light);"><i class="fa-solid fa-list-check me-2"></i><?php echo __('my_rides'); ?></h4>
        <div class="table-responsive table-custom">
            <table class="table table-hover text-center align-middle mb-0">
                <thead><tr><th><?php echo __('start_point'); ?></th><th><?php echo __('destination'); ?></th><th><?php echo __('price'); ?></th><th><?php echo __('available_seats'); ?></th><th><?php echo __('status'); ?></th><th><?php echo __('actions'); ?></th></tr></thead>
                <tbody>
                    <?php
                        $sql_rides = "SELECT * FROM rides WHERE driver_id = ? ORDER BY id DESC";
                        $stmt_rides = $conn->prepare($sql_rides);
                        $stmt_rides->bind_param("i", $driver_id);
                        $stmt_rides->execute();
                        $result = $stmt_rides->get_result();

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['start_point']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['price']) . " د.أ</td>";
                                echo "<td>" . htmlspecialchars($row['available_seats']) . "</td>";
                                echo "<td><span class='badge bg-success'>" . htmlspecialchars($row['status']) . "</span></td>";
                                echo "<td>
                                        <a href='edit_ride.php?id=" . $row['id'] . "' class='btn btn-primary action-btn' title='" . __('edit') . "'><i class='fa-solid fa-pen'></i></a>
                                        <a href='#' onclick='confirmDelete(" . $row['id'] . ")' class='btn btn-danger action-btn' title='" . __('delete') . "'><i class='fa-solid fa-trash'></i></a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='p-4'>" . __('no_rides_yet') . "</td></tr>";
                        }
                        $stmt_rides->close();
                        $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete(rideId) {
            Swal.fire({
                title: '<?php echo __("confirm_delete_ride"); ?>',
                text: "لن تتمكني من التراجع عن هذا الإجراء!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<?php echo __("delete"); ?>',
                cancelButtonText: '<?php echo __("cancel"); ?>'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = 'delete_ride.php?id=' + rideId; }
            });
        }

        <?php if(!empty($error_message)): ?>
            Swal.fire({ icon: 'error', title: 'خطأ!', text: '<?php echo $error_message; ?>', confirmButtonColor: '#d81b60' });
        <?php endif; ?>

        <?php
        if (isset($_SESSION['success_message'])) {
            echo "Swal.fire({
                icon: 'success',
                title: 'تم بنجاح!',
                text: '" . $_SESSION['success_message'] . "',
                showConfirmButton: false,
                timer: 1500
            });";
            unset($_SESSION['success_message']);
        }
        ?>
    </script>
</body>
</html>
