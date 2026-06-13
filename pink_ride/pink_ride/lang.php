<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// منطق تحديد اللغة (لا تغيير هنا)
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar'])) {
    $_SESSION['lang'] = $_GET['lang'];
    setcookie('lang', $_GET['lang'], time() + (86400 * 30), "/");
} 
elseif (!isset($_SESSION['lang']) && isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], ['en', 'ar'])) {
    $_SESSION['lang'] = $_COOKIE['lang'];
} 
elseif (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar';
}
$lang = $_SESSION['lang'];

// --- قاموس الترجمة الشامل والنهائي لكل المشروع ---
$translations = [
    'en' => [
        // === Part 1: Auth & Profile ===
        'login_title' => 'Welcome Back',
        'email' => 'Email Address',
        'password' => 'Password',
        'login_btn' => 'Login Now',
        'no_account' => "Don't have an account? Sign up",
        'signup_title' => 'Create New Account',
        'full_name' => 'Full Name',
        'phone' => 'Phone Number',
        'role' => 'Register As',
        'driver' => 'Driver 🚗',
        'passenger' => 'Passenger 👩',
        'signup_btn' => 'Create Account',
        'have_account' => 'Already have an account? Login',
        'profile_title' => 'My Profile',
        'update_btn' => 'Update Profile',
        'logout_btn' => 'Logout',
        'back_btn' => 'Back',
        'no_changes' => 'No changes were made to your profile.',
        'change_pic' => 'Click to change picture',
        'new_password_optional' => 'New Password (Optional)',
        'leave_blank_to_keep' => 'Leave blank to keep current',
        'confirm_logout_title' => 'Are you sure?',
        'confirm_logout_text' => 'You will be logged out!',
        'yes_logout' => 'Yes, logout!',
        'cancel' => 'Cancel',

        // === Part 2: Driver Dashboard (Your Part) ===
        'driver_dashboard_title' => 'Driver Dashboard',
        'welcome_driver' => 'Welcome,',
        'add_new_ride' => 'Add a New Ride',
        'start_point' => 'Start Point',
        'destination' => 'Destination',
        'price' => 'Price (JOD)',
        'available_seats' => 'Available Seats',
        'add_ride_btn' => 'Add Ride',
        'my_rides' => 'My Current Rides',
        'status' => 'Status',
        'actions' => 'Actions',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'no_rides_yet' => 'You have not added any rides yet.',
        'edit_ride_title' => 'Edit Ride',
        'save_changes' => 'Save Changes',
        'confirm_delete_ride' => 'Are you sure you want to delete this ride?',

        // === Part 3: Search & Booking ===
        'search_page_title' => 'Find Your Next Ride',
        'search_page_subtitle' => 'Set your destination and budget, and we will find the best ride for you!',
        'search_placeholder_dest' => 'Where are you going?',
        'search_placeholder_price' => 'Max Price (JOD)',
        'search_btn' => 'Search',
        'driver_name' => 'Driver',
        'from' => 'From',
        'to' => 'To',
        'available' => 'Available',
        'book_now_btn' => 'Book Now',
        'full_btn' => 'Full',
        'no_rides_found' => 'Sorry, no rides match your search currently.',
        'view_all_rides' => 'View All Rides',
        'my_bookings' => 'My Booked Rides',
        'no_bookings_yet' => 'You have no bookings yet.',
        'find_ride_now' => 'Find a ride now!',
        'booking_date' => 'Booking Date',
        'home_page' => 'Home',
        'my_account' => 'My Account',
    ],
    'ar' => [
        // === Part 1: Auth & Profile ===
        'login_title' => 'أهلاً بكِ من جديد',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'login_btn' => 'تسجيل الدخول',
        'no_account' => 'ليس لديكِ حساب؟ إنشاء حساب',
        'signup_title' => 'إنشاء حساب جديد',
        'full_name' => 'الاسم الكامل',
        'phone' => 'رقم الهاتف',
        'role' => 'تسجيل كـ',
        'driver' => 'سائقة 🚗',
        'passenger' => 'راكبة 👩',
        'signup_btn' => 'إنشاء الحساب',
        'have_account' => 'لديكِ حساب بالفعل؟ تسجيل الدخول',
        'profile_title' => 'ملفي الشخصي',
        'update_btn' => 'تحديث البيانات',
        'logout_btn' => 'تسجيل الخروج',
        'back_btn' => 'عودة',
        'no_changes' => 'لم تقومي بإجراء أي تعديلات على بياناتك.',
        'change_pic' => 'اضغطي لتغيير الصورة',
        'new_password_optional' => 'كلمة مرور جديدة (اختياري)',
        'leave_blank_to_keep' => 'اترك الحقل فارغاً لعدم التغيير',
        'confirm_logout_title' => 'هل أنتِ متأكدة؟',
        'confirm_logout_text' => 'سيتم تسجيل خروجك!',
        'yes_logout' => 'نعم، خروج!',
        'cancel' => 'إلغاء',

        // === Part 2: Driver Dashboard (Your Part) ===
        'driver_dashboard_title' => 'لوحة تحكم السائقة',
        'welcome_driver' => 'مرحباً،',
        'add_new_ride' => 'أضيفي رحلة جديدة',
        'start_point' => 'نقطة الانطلاق',
        'destination' => 'الوجهة',
        'price' => 'السعر (دينار)',
        'available_seats' => 'المقاعد المتاحة',
        'add_ride_btn' => 'أضف الرحلة',
        'my_rides' => 'رحلاتي الحالية',
        'status' => 'الحالة',
        'actions' => 'الإجراءات',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'no_rides_yet' => 'لم تقومي بإضافة أي رحلات بعد.',
        'edit_ride_title' => 'تعديل الرحلة',
        'save_changes' => 'حفظ التعديلات',
        'confirm_delete_ride' => 'هل أنتِ متأكدة من حذف هذه الرحلة؟',

        // === Part 3: Search & Booking ===
        'search_page_title' => 'ابحثي عن رحلتك القادمة',
        'search_page_subtitle' => 'حددي وجهتك والميزانية المناسبة لكِ، ونجد لكِ التوصيلة الأفضل!',
        'search_placeholder_dest' => 'لوين رايحة؟',
        'search_placeholder_price' => 'أقصى سعر (دينار)',
        'search_btn' => 'بحث',
        'driver_name' => 'السائقة',
        'from' => 'من',
        'to' => 'إلى',
        'available' => 'المتاح',
        'book_now_btn' => 'احجزي الآن',
        'full_btn' => 'ممتلئة',
        'no_rides_found' => 'عذراً، لا يوجد رحلات مطابقة لبحثك حالياً.',
        'view_all_rides' => 'عرض كل الرحلات',
        'my_bookings' => 'رحلاتي المحجوزة',
        'no_bookings_yet' => 'لا يوجد حجوزات حالياً.',
        'find_ride_now' => 'ابحثي عن رحلة الآن!',
        'booking_date' => 'تاريخ الحجز',
        'home_page' => 'الرئيسية',
        'my_account' => 'حسابي',
    ]
];

function __(string $key): string {
    global $translations, $lang;
    return $translations[$lang][$key] ?? $key;
}
?>
