<?php
session_start();
// منطق تغيير اللغة
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';
$dir = ($lang == 'ar') ? 'rtl' : 'ltr';

// القاموس
$t = [
    'ar' => [
        'title' => 'من نحن - Pink Ride',
        'home' => 'الرئيسية',
        'about' => 'من نحن',
        'about_title' => 'قصة <span>Pink Ride</span>',
        'about_text' => 'نحن منصة أردنية تأسست عام 2026 بهدف توفير بيئة نقل آمنة ومريحة للسيدات وطالبات الجامعات. نؤمن بأن الأمان والموثوقية هما أساس أي رحلة.',
        'vision_title' => 'رؤيتنا',
        'vision_text' => 'تمكين المرأة الأردنية وتوفير حلول نقل ذكية واقتصادية تقلل من الازدحام وتحافظ على البيئة من خلال مشاركة الرحلات.',
        'team_title' => 'عن المشروع',
        'team_text' => 'تم تطوير هذا المشروع كجزء من مادة تطبيقات الويب بإشراف الدكتورة منار مزهر.',
        'devs_title' => 'فريق المبرمجات',
        'dev1' => 'شيماء الحاج',
        'dev2' => 'تقى أبو هرة',
        'dev3' => 'ضحى دبوبش'
    ],
    'en' => [
        'title' => 'About Us - Pink Ride',
        'home' => 'Home',
        'about' => 'About Us',
        'about_title' => 'The Story of <span>Pink Ride</span>',
        'about_text' => 'We are a Jordanian platform founded in 2026 to provide a safe and comfortable transportation environment for women and university students. We believe that safety and reliability are the foundation of any journey.',
        'vision_title' => 'Our Vision',
        'vision_text' => 'Empowering Jordanian women and providing smart, economical transportation solutions that reduce congestion and protect the environment through ride-sharing.',
        'team_title' => 'About the Project',
        'team_text' => 'This project was developed as part of the Web Applications course under the supervision of Dr. Manar Mezher.',
        'devs_title' => 'Development Team',
        'dev1' => 'Shaimaa Hamdan Alhaj',
        'dev2' => 'Doha Ziad Dabobash',
        'dev3' => 'Toqa abu herra'
    ]
];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t[$lang]['title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #fdf2f8; display: flex; flex-direction: column; min-height: 100vh;}
        .navbar-custom { background-color: #ffffff; box-shadow: 0 2px 15px rgba(0,0,0,0.05); padding: 15px 0; }
        .navbar-brand { color: #d81b60 !important; font-weight: 800; font-size: 1.5rem; }
        .nav-link { color: #555 !important; font-weight: 600; margin-left: 15px; transition: 0.3s; }
        .nav-link:hover { color: #d81b60 !important; }
        .about-card { background: white; border-radius: 20px; padding: 50px 40px; box-shadow: 0 10px 30px rgba(216, 27, 96, 0.1); border-top: 5px solid #d81b60; margin-top: 50px; }
        .about-title { font-weight: 800; color: #333; }
        .about-title span { color: #d81b60; }
        .icon-circle { width: 60px; height: 60px; background: #fbcce7; color: #d81b60; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 20px; }
        
        /* تصميم بطاقات المبرمجات */
        .dev-card {
            background: #fffafc;
            border: 2px solid #fbcce7;
            border-radius: 15px;
            padding: 20px 10px;
            transition: all 0.3s ease;
            height: 100%;
        }
        .dev-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 25px rgba(216, 27, 96, 0.15);
            border-color: #d81b60;
        }
        .dev-icon { font-size: 2.5rem; margin-bottom: 10px; }
    </style>
</head>
<body>

    <!-- شريط التنقل -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-car-side me-2"></i>Pink Ride</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><?php echo $t[$lang]['home']; ?></a></li>
                    <li class="nav-item"><a class="nav-link active text-pink" href="about.php"><?php echo $t[$lang]['about']; ?></a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="?lang=<?php echo $lang == 'ar' ? 'en' : 'ar'; ?>" class="btn btn-outline-secondary fw-bold">
                        <i class="fa-solid fa-globe"></i> <?php echo $lang == 'ar' ? 'English' : 'عربي'; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-9"> <!-- عرضناها شوي عشان تسع البطاقات براحتها -->
                <div class="about-card text-center">
                    
                    <!-- القصة -->
                    <h2 class="about-title mb-4"><?php echo $t[$lang]['about_title']; ?></h2>
                    <p class="lead text-muted mb-5"><?php echo $t[$lang]['about_text']; ?></p>
                    
                    <!-- الرؤية والمشروع (عمودين متساويين) -->
                    <div class="row mt-5 text-start">
                        <div class="col-md-6 mb-4">
                            <div class="icon-circle"><i class="fa-solid fa-eye"></i></div>
                            <h4 class="fw-bold" style="color: #d81b60;"><?php echo $t[$lang]['vision_title']; ?></h4>
                            <p class="text-muted"><?php echo $t[$lang]['vision_text']; ?></p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="icon-circle"><i class="fa-solid fa-graduation-cap"></i></div>
                            <h4 class="fw-bold" style="color: #d81b60;"><?php echo $t[$lang]['team_title']; ?></h4>
                            <p class="text-muted"><?php echo $t[$lang]['team_text']; ?></p>
                        </div>
                    </div>

                    <!-- قسم فريق العمل (إنتو) بـ 3 أعمدة -->
                    <hr class="my-5" style="border-color: #fbcce7; opacity: 1;">
                    <h3 class="fw-bold mb-4 text-center" style="color: #333;">
                        <i class="fa-solid fa-code me-2" style="color: #d81b60;"></i><?php echo $t[$lang]['devs_title']; ?>
                    </h3>
                    
                    <div class="row justify-content-center g-3 mt-3">
                        <div class="col-md-4">
                            <div class="dev-card text-center">
                                <div class="dev-icon">👩🏻‍💻</div>
                                <h5 class="fw-bold m-0" style="color: #d81b60;"><?php echo $t[$lang]['dev1']; ?></h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="dev-card text-center">
                                <div class="dev-icon">👩🏻‍💻</div>
                                <h5 class="fw-bold m-0" style="color: #d81b60;"><?php echo $t[$lang]['dev2']; ?></h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="dev-card text-center">
                                <div class="dev-icon">👩🏻‍💻</div>
                                <h5 class="fw-bold m-0" style="color: #d81b60;"><?php echo $t[$lang]['dev3']; ?></h5>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- الفوتر الموحد (اللي اتفقنا عليه) -->
    <footer class="bg-dark text-white text-center py-4 mt-auto">
        <div class="container">
            <h5 class="fw-bold mb-3" style="color: #fbcce7;">Pink Ride</h5>
            <p class="mb-3" style="color: #fbcce7; font-size: 1rem;">
                <i class="fa-regular fa-envelope me-2"></i>support@pinkride.com
            </p>
            <p class="small text-white-50 mb-0">&copy; 2026 جميع الحقوق محفوظة لفريق العمل</p>
        </div>
    </footer>

</body>
</html>