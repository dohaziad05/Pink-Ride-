<?php
// الشبك مع القاعدة
require_once 'db_connect.php';
if (isset($_GET['ride_id'])) {
    $ride_id = $_GET['ride_id'];
    
    session_start();
// التأكد من إنها مسجلة دخول قبل ما تحجز
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('يرجى تسجيل الدخول أولاً لتتمكني من الحجز 🚗✨'); window.location.href='login.php';</script>";
    exit();
}
// أخذ رقم البنت الحقيقي من النظام
$passenger_id = $_SESSION['user_id'];
    $check_sql = "SELECT available_seats FROM rides WHERE id = $ride_id";
    $result = mysqli_query($conn, $check_sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['available_seats'] > 0) {
        // 2. ننقص عدد المقاعد بمقدار 1
        $update_sql = "UPDATE rides SET available_seats = available_seats - 1 WHERE id = $ride_id";
        mysqli_query($conn, $update_sql);

        // 3. نسجل الحجز بالجدول
        $insert_sql = "INSERT INTO bookings (ride_id, passenger_id) VALUES ($ride_id, $passenger_id)";
        mysqli_query($conn, $insert_sql);

        // 4. نطلع رسالة نجاح ونرجع لصفحة البحث
        echo "<script>
                alert('تم الحجز بنجاح! جهزي حالك للرحلة 🚗✨'); 
                window.location.href='search.php';
              </script>";
    } else {
        // لو المقاعد صفر
        echo "<script>
                alert('عذراً، المقاعد ممتلئة لهي الرحلة 😔'); 
                window.location.href='search.php';
              </script>";
    }
}
?>