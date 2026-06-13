<!DOCTYPE html>
<?php
session_start();

// منطق تغيير اللغة
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';
$dir = ($lang == 'ar') ? 'rtl' : 'ltr';

// قاموس الترجمة البسيط
$t = [
    'ar' => [
        'title' => 'Pink Ride - الصفحة الرئيسية',
        'home' => 'الرئيسية',
        'about' => 'من نحن',
        'login' => 'دخول',
        'register' => 'حساب جديد',
        'profile' => 'حسابي',
        'hero_title' => 'طريقكِ الآمن يبدأ مع <span>Pink Ride</span>',
        'hero_desc' => 'أول منصة توصيل مخصصة للسيدات فقط. سواء كنتِ تبحثين عن توصيلة آمنة لجامعتك، أو ترغبين بمشاركة سيارتك وتقليل التكاليف، نحن هنا من أجلكِ.',
        'btn_passenger' => 'ابحثي عن رحلة (راكبة)',
        'btn_driver' => 'أضيفي رحلة (سائقة)'
    ],
    'en' => [
        'title' => 'Pink Ride - Home',
        'home' => 'Home',
        'about' => 'About Us',
        'login' => 'Login',
        'register' => 'Sign Up',
        'profile' => 'My Profile',
        'hero_title' => 'Your Safe Journey Starts With <span>Pink Ride</span>',
        'hero_desc' => 'The first ride-sharing platform exclusively for women. Whether you are looking for a safe ride to campus or want to share your car, we are here for you.',
        'btn_passenger' => 'Find a Ride (Passenger)',
        'btn_driver' => 'Add a Ride (Driver)'
    ]
];
?>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t[$lang]['title']; ?></title>
    
    <!-- خطوط جوجل (Cairo) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #fdf2f8; /* لون خلفية زهري فاتح جداً */
            overflow-x: hidden;
        }

        /* تصميم شريط التنقل */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        .navbar-brand {
            color: #d81b60 !important;
            font-weight: 800;
            font-size: 1.5rem;
        }
        .nav-link {
            color: #555 !important;
            font-weight: 600;
            margin-left: 15px;
            transition: 0.3s;
        }
        .nav-link:hover {
            color: #d81b60 !important;
        }

        /* الأزرار الوردية */
        .btn-pink {
            background-color: #d81b60;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        .btn-pink:hover {
            background-color: #ad1457;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(216, 27, 96, 0.3);
        }
        .btn-outline-pink {
            border: 2px solid #d81b60;
            color: #d81b60;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 700;
            background: transparent;
            transition: all 0.3s ease;
        }
        .btn-outline-pink:hover {
            background-color: #d81b60;
            color: white;
        }

        /* القسم الترحيبي */
        .hero-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #fdf2f8 0%, #fbcce7 100%);
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            margin-bottom: 50px;
        }
        .hero-title {
            color: #212529;
            font-weight: 800;
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .hero-title span {
            color: #d81b60;
        }

        /* كروت لوحة التحكم */
        .dashboard-card {
            border: none;
            border-radius: 15px;
            transition: 0.3s;
            cursor: pointer;
            height: 100%;
        }
        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        }
        .icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 1.8rem;
        }
        .bg-pink-light { background-color: #fbcce7; color: #d81b60; }
        .bg-purple-light { background-color: #e1bee7; color: #8e24aa; }
        .bg-blue-light { background-color: #bbdefb; color: #1976d2; }
    </style>
</head>
<body>

    <!-- شريط التنقل (Navbar) -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-car-side me-2"></i>Pink Ride</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><?php echo $t[$lang]['home']; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php"><?php echo $t[$lang]['about']; ?></a>
                    </li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <!-- تبديل اللغة -->
                    <?php if($lang == 'ar'): ?>
                        <a href="?lang=en" class="nav-link fw-bold text-dark me-3">English</a>
                    <?php else: ?>
                        <a href="?lang=ar" class="nav-link fw-bold text-dark ms-3">العربية</a>
                    <?php endif; ?>

                    <!-- أزرار الحسابات (شغل الطالبة 1) -->
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="btn btn-pink"><i class="fa-solid fa-user ms-1"></i> <?php echo $t[$lang]['profile']; ?></a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-pink"><i class="fa-solid fa-right-to-bracket ms-1"></i> <?php echo $t[$lang]['login']; ?></a>
<a href="signup.php" class="btn btn-pink"><i class="fa-solid fa-user-plus ms-1"></i> <?php echo $t[$lang]['register']; ?></a>                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- القسم الترحيبي (Hero Section) -->
    <section class="hero-section">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h1 class="hero-title"><?php echo $t[$lang]['hero_title']; ?></h1>
                    <p class="lead text-muted mb-5"><?php echo $t[$lang]['hero_desc']; ?></p>
                    
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="search.php" class="btn btn-pink btn-lg"><i class="fa-solid fa-magnifying-glass ms-2"></i> <?php echo $t[$lang]['btn_passenger']; ?></a>
<a href="driver_dashboard.php" class="btn btn-outline-pink btn-lg"><i class="fa-solid fa-plus ms-2"></i> <?php echo $t[$lang]['btn_driver']; ?></a>                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم الخدمات والأزرار الرئيسية للمشروع -->
    <section class="container mb-5">
        <h3 class="text-center fw-bold mb-4" style="color: #333;">لوحة تحكم النظام (اختصارات سريعة)</h3>
        <p class="text-center text-muted mb-5">جميع متطلبات المشروع متوفرة هنا للوصول السريع</p>

        <div class="row g-4 text-center">
            
            <!-- قسم الراكبة (شغلك) -->
            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm p-4 h-100">
                    <div class="icon-wrapper bg-pink-light">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <h4 class="fw-bold mb-3">نظام الراكبة (Search)</h4>
                    <p class="text-muted small mb-4">البحث عن الرحلات، الفلترة حسب السعر والمكان، وحجز مقعد.</p>
                    <a href="search.php" class="btn btn-outline-pink mt-auto w-100">اذهبي للبحث</a>
                </div>
            </div>

            <!-- قسم السائقة (شغل الطالبة 2 - CRUD) -->
            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm p-4 h-100">
                    <div class="icon-wrapper bg-purple-light">
                        <i class="fa-solid fa-car"></i>
                    </div>
                    <h4 class="fw-bold mb-3">نظام السائقة (CRUD)</h4>
                    <p class="text-muted small mb-4">إضافة رحلات جديدة، تعديل بيانات الرحلة، أو حذفها من النظام.</p>
                    <a href="driver_dashboard.php" class="btn btn-outline-pink mt-auto w-100">لوحة السائقة</a>
                </div>
            </div>

            <!-- قسم الحسابات (شغل الطالبة 1 - Auth) -->
            <div class="col-md-4">
                <div class="card dashboard-card shadow-sm p-4 h-100">
                    <div class="icon-wrapper bg-blue-light">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h4 class="fw-bold mb-3">نظام الحسابات (Auth)</h4>
                    <p class="text-muted small mb-4">تسجيل مستخدم جديد، تسجيل الدخول، وحماية الصفحات.</p>
                    <div class="d-flex gap-2 mt-auto">
                        <a href="login.php" class="btn btn-outline-secondary w-50">دخول</a>
<a href="signup.php" class="btn btn-outline-secondary w-50">تسجيل</a>                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- الفوتر -->
  <!-- الفوتر -->
    <footer class="bg-dark text-white text-center py-4 mt-auto">
        <div class="container">
            <h5 class="fw-bold mb-3" style="color: #fbcce7;">Pink Ride</h5>
            
            <!-- سطر الإيميل -->
            <p class="mb-3" style="color: #fbcce7; font-size: 1rem;">
                <i class="fa-regular fa-envelope me-2"></i>support@pinkride.com
            </p>
            
            <p class="small text-white-50 mb-0">&copy; 2026 جميع الحقوق محفوظة لفريق العمل</p>
        </div>
    </footer>
    <!-- Bootstrap JS (مهم عشان قائمة الموبايل تشتغل) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>