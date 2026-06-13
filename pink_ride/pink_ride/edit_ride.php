<?php
session_start();
require_once 'db_connect.php';
require_once 'lang.php';

// 1. حماية الصفحة والتأكد من وجود رحلة للتعديل
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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: driver_dashboard.php");
    exit();
}

$ride_id = (int)$_GET['id'];
$driver_id = $_SESSION['user_id'];

// 2. معالجة تحديث البيانات
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_point = trim($conn->real_escape_string($_POST['start_point']));
    $destination = trim($conn->real_escape_string($_POST['destination']));
    $price = (float)$_POST['price'];
    $available_seats = (int)$_POST['available_seats'];
    $status = $conn->real_escape_string($_POST['status']);

    $sql_update = "UPDATE rides SET start_point = ?, destination = ?, price = ?, available_seats = ?, status = ? WHERE id = ? AND driver_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssdisii", $start_point, $destination, $price, $available_seats, $status, $ride_id, $driver_id);
    
    if ($stmt_update->execute()) {
        $_SESSION['success_message'] = 'تم تحديث الرحلة بنجاح!';
        header("Location: driver_dashboard.php");
        exit();
    } else {
        $error_message = "حدث خطأ أثناء تحديث البيانات.";
    }
    $stmt_update->close();
}

// 3. جلب بيانات الرحلة الحالية
$sql_fetch = "SELECT * FROM rides WHERE id = ? AND driver_id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("ii", $ride_id, $driver_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();

if ($result->num_rows == 1) {
    $ride = $result->fetch_assoc();
} else {
    header("Location: driver_dashboard.php");
    exit();
}
$stmt_fetch->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang == 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('edit_ride_title'); ?> - PinkRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body { align-items: center; }
    </style>
</head>
<body>
    <a href="?lang=<?php echo $lang == 'ar' ? 'en' : 'ar'; ?>" class="lang-toggle">
        <i class="fa-solid fa-globe me-1"></i> <?php echo $lang == 'ar' ? 'English' : 'عربي'; ?>
    </a>

    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="premium-card" style="max-width: 600px;">
        <h3 class="text-center mb-4" style="color: var(--burgundy  ); font-weight: 800;">
            <i class="fa-solid fa-pen-to-square me-2"></i><?php echo __('edit_ride_title'); ?>
        </h3>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_ride.php?id=<?php echo $ride_id; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?php echo __('start_point'); ?>:</label>
                    <input type="text" name="start_point" class="form-control premium-input" value="<?php echo htmlspecialchars($ride['start_point']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?php echo __('destination'); ?>:</label>
                    <input type="text" name="destination" class="form-control premium-input" value="<?php echo htmlspecialchars($ride['destination']); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?php echo __('price'); ?>:</label>
                    <input type="number" name="price" step="0.1" min="0.1" class="form-control premium-input" value="<?php echo htmlspecialchars($ride['price']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?php echo __('available_seats'); ?>:</label>
                    <input type="number" name="available_seats" min="0" class="form-control premium-input" value="<?php echo htmlspecialchars($ride['available_seats']); ?>" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold"><?php echo __('status'); ?>:</label>
                <select name="status" class="form-select premium-input">
                    <option value="متاحة" <?php if($ride['status'] == 'متاحة') echo 'selected'; ?>><?php echo __('available'); ?></option>
                    <option value="مكتملة" <?php if($ride['status'] == 'مكتملة') echo 'selected'; ?>>مكتملة</option>
                    <option value="ملغاة" <?php if($ride['status'] == 'ملغاة') echo 'selected'; ?>>ملغاة</option>
                </select>
            </div>
            <div class="d-flex gap-3">
                <button type="submit" name="update_ride" class="premium-btn w-100">
                    <i class="fa-solid fa-floppy-disk me-2"></i><?php echo __('save_changes'); ?>
                </button>
                <a href="driver_dashboard.php" class="btn btn-secondary w-50" style="padding: 16px; border-radius: 18px; font-weight: bold;"><?php echo __('cancel'); ?></a>
            </div>
        </form>
    </div>
</body>
</html>
