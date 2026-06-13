<?php
session_start();
require_once 'db_connect.php';
require_once 'lang.php'; 

// بناء جملة SQL الأساسية والآمنة
$sql = "SELECT rides.*, users.full_name AS driver_name 
        FROM rides 
        JOIN users ON rides.driver_id = users.id 
        WHERE rides.status = 'متاحة' AND rides.available_seats > 0";

// إضافة الفلاتر بشكل آمن
$params = [];
$types = '';

// 1. إضافة فلتر نقطة الانطلاق
if (isset($_GET['start_point']) && !empty($_GET['start_point'])) {
    $sql .= " AND rides.start_point LIKE ?";
    $params[] = "%" . $_GET['start_point'] . "%";
    $types .= 's';
}

// 2. إضافة فلتر الوجهة
if (isset($_GET['destination']) && !empty($_GET['destination'])) {
    $sql .= " AND rides.destination LIKE ?";
    $params[] = "%" . $_GET['destination'] . "%";
    $types .= 's';
}

// 3. إضافة فلتر السعر الأقصى
if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
    $sql .= " AND rides.price <= ?";
    $params[] = (float)$_GET['max_price'];
    $types .= 'd';
}

$sql .= " ORDER BY rides.id DESC";

// تنفيذ الاستعلام بالطريقة الآمنة
$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang == 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo __('search_page_title'); ?> - PinkRide</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #fdf2f8; }
        .navbar-custom { background-color: #ffffff; box-shadow: 0 2px 15px rgba(0,0,0,0.05 ); padding: 15px 0; margin-bottom: 40px;}
        .navbar-brand { color: #d81b60 !important; font-weight: 800; font-size: 1.5rem; }
        .nav-link { color: #555 !important; font-weight: 600; margin: 0 10px; }
        .search-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(216, 27, 96, 0.1); background: white; }
        
        /* تنسيق الحقول الجديد ليكون متناسق وممتاز */
        .border-pink { border: 1px solid #fbcce7; border-radius: 10px; }
        .form-control { padding: 12px; border: 1px solid #fbcce7; box-shadow: none !important; }
        .form-control:focus { border-color: #d81b60; box-shadow: 0 0 0 0.2rem rgba(216, 27, 96, 0.1) !important; }
        .input-group-text { background-color: #fffafc; color: #d81b60; border: 1px solid #fbcce7; }
        
        .btn-pink { background-color: #d81b60; color: white; border: none; border-radius: 10px; font-weight: bold; padding: 12px; transition: 0.3s; }
        .btn-pink:hover { background-color: #ad1457; color: white; transform: translateY(-2px); }
        .ride-card { border: none; border-radius: 15px; transition: all 0.3s ease; border-top: 5px solid #d81b60; background: white; }
        .ride-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
        .driver-name { color: #333; font-weight: 800; }
        .icon-pink { color: #d81b60; width: 25px; text-align: center; }
        .price-tag { background-color: #fbcce7; color: #d81b60; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 1.1rem; }
        .seats-badge { background-color: #e9ecef; color: #495057; padding: 5px 10px; border-radius: 10px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <!-- شريط التنقل -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-car-side me-2"></i>Pink Ride</a>
            <div class="d-flex align-items-center">
                <a href="?lang=<?php echo $lang == 'ar' ? 'en' : 'ar'; ?>" class="nav-link fw-bold">
                    <i class="fa-solid fa-globe"></i> <?php echo $lang == 'ar' ? 'English' : 'عربي'; ?>
                </a>
                <a href="index.php" class="nav-link"><i class="fa-solid fa-house"></i> <?php echo __('home_page'); ?></a>
                <a href="profile.php" class="nav-link" style="color: #d81b60 !important;"><i class="fa-solid fa-user"></i> <?php echo __('my_account'); ?></a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: #d81b60;"><i class="fa-solid fa-map-location-dot me-2"></i> <?php echo __('search_page_title'); ?></h2>
            <p class="text-muted"><?php echo __('search_page_subtitle'); ?></p>
        </div>
        
        <!-- شريط البحث (تم إصلاح المشكلة هنا) -->
        <div class="card search-card p-4 mb-5 mx-auto" style="max-width: 1000px;"> 
          <form method="GET" action="" class="row g-3 align-items-center">
                
                <!-- 1. مربع مكانك الحالي -->
                <div class="col-md-6 col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-location-crosshairs"></i></span>
                        <input type="text" name="start_point" class="form-control" placeholder="مكانك الحالي" value="<?php echo isset($_GET['start_point']) ? htmlspecialchars($_GET['start_point']) : ''; ?>">
                    </div>
                </div>

                <!-- 2. مربع وجهتك -->
                <div class="col-md-6 col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-location-dot"></i></span>
                        <input type="text" name="destination" class="form-control" placeholder="وجهتك" value="<?php echo isset($_GET['destination']) ? htmlspecialchars($_GET['destination']) : ''; ?>">
                    </div>
                </div>
                
                <!-- 3. مربع أقصى سعر -->
                <div class="col-md-6 col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-coins"></i></span>
                        <input type="number" step="0.5" name="max_price" class="form-control" placeholder="الميزانية (دينار)" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                    </div>
                </div>
                
                <!-- 4. زر البحث -->
                <div class="col-md-6 col-lg-3">
                    <button type="submit" class="btn btn-pink w-100"><i class="fa-solid fa-magnifying-glass me-1"></i> <?php echo __('search_btn'); ?></button>
                </div>
            </form>
        </div>

        <!-- عرض نتائج الرحلات -->
        <div class="row g-4">
            <?php if($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card ride-card shadow-sm h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="driver-name m-0"><i class="fa-solid fa-circle-user fs-4 me-2 text-secondary"></i> <?php echo htmlspecialchars($row['driver_name']); ?></h5>
                                <span class="price-tag"><?php echo htmlspecialchars($row['price']); ?> JOD</span>
                            </div>
                            <hr class="text-muted opacity-25">
                            <div class="mb-3">
                                <p class="mb-2"><i class="fa-solid fa-location-arrow icon-pink"></i> <strong><?php echo __('from'); ?>:</strong> <?php echo htmlspecialchars($row['start_point']); ?></p>
                                <p class="mb-0"><i class="fa-solid fa-location-dot icon-pink"></i> <strong><?php echo __('to'); ?>:</strong> <span class="text-dark fw-bold"><?php echo htmlspecialchars($row['destination']); ?></span></p>
                            </div>
                            <div class="d-flex justify-content-between align-items-end mt-auto pt-3">
                                <span class="seats-badge"><i class="fa-solid fa-chair text-muted me-1"></i> <?php echo __('available'); ?>: <strong><?php echo htmlspecialchars($row['available_seats']); ?></strong></span>
                                <?php if($row['available_seats'] <= 0): ?>
                                    <button class="btn btn-secondary" disabled><?php echo __('full_btn'); ?></button>
                                <?php else: ?>
                                    <a href="book.php?ride_id=<?php echo $row['id']; ?>" class="btn btn-pink px-4"><?php echo __('book_now_btn'); ?> <i class="fa-solid fa-chevron-left ms-1 small"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5"><img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" width="120" class="mb-3 opacity-50" alt="no results"><h4 class="text-muted"><?php echo __('no_rides_found'); ?></h4><a href="search.php" class="btn btn-outline-secondary mt-3"><?php echo __('view_all_rides'); ?></a></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>