<?php
session_start();
require_once 'db_connect.php';

// 1. حماية الصفحة والتأكد من وجود رحلة للحذف
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'driver') {
    // إذا لم يكن المستخدم سائقة، أعده لصفحة الدخول
    header("Location: login.php");
    exit();
}

// التأكد من أن هناك 'id' للرحلة في الرابط وأنه رقم
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // إذا لا يوجد ID، أعده للوحة التحكم
    header("Location: driver_dashboard.php");
    exit();
}

$ride_id = (int)$_GET['id'];
$driver_id = $_SESSION['user_id']; // ID السائقة الحالية من الجلسة

// 2. تنفيذ أمر الحذف
// نقوم بحذف الرحلة فقط إذا كانت تحمل نفس ride_id و driver_id
// هذا إجراء أمني مهم لمنع سائقة من حذف رحلات سائقة أخرى عن طريق تخمين الـ ID
$sql = "DELETE FROM rides WHERE id = ? AND driver_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $ride_id, $driver_id);

if ($stmt->execute()) {
    // تم الحذف بنجاح
    // لا داعي لرسالة هنا، سنعود مباشرة للوحة التحكم
    header("Location: driver_dashboard.php?status=deleted");
    exit();
} else {
    // حدث خطأ ما
    // يمكنك عرض رسالة خطأ إذا أردت، لكن الأفضل هو العودة للوحة التحكم
    header("Location: driver_dashboard.php?status=error");
    exit();
}

$stmt->close();
$conn->close();
?>
