<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المستخدمين والعضويات</title>
    <!-- Bootstrap RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- خط مناسب للعربية -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        .hero-section {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        .feature-box {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
            background-color: white;
        }
        .feature-box:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 40px;
            margin-bottom: 20px;
            color: #007bff;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 30px 0;
            margin-top: 50px;
        }
        .btn-cta {
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 30px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<!-- القائمة العلوية -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-users-cog text-primary me-2"></i>
            نظام إدارة المستخدمين
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#features">المميزات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#pricing">الأسعار</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">اتصل بنا</a>
                </li>
            </ul>
            <div class="d-flex">
                @if (Route::has('login'))
                    <div>
                        @auth
                            <a href="{{ url('/home') }}" class="btn btn-outline-primary me-2">لوحة التحكم</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">تسجيل الدخول</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">إنشاء حساب</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </div>
</nav>

<!-- قسم الترحيب الرئيسي -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">نظام إدارة المستخدمين والعضويات المتكامل</h1>
        <p class="lead mb-5">منصة متكاملة لإدارة المستخدمين، الاشتراكات، والتراخيص بكل سهولة وأمان</p>
        <div>
            <a href="{{ route('register') }}" class="btn btn-light btn-cta me-3">ابدأ الآن مجاناً</a>
            <a href="#features" class="btn btn-outline-light btn-cta">استكشف المميزات</a>
        </div>
    </div>
</section>

<!-- قسم المميزات -->
<section id="features" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">مميزات النظام</h2>
            <p class="text-muted">اكتشف ما يميز نظامنا عن غيره من الأنظمة</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4>إدارة المستخدمين</h4>
                    <p>إدارة كاملة للمستخدمين مع إمكانية تحديد الأدوار والصلاحيات المختلفة</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h4>إدارة الاشتراكات</h4>
                    <p>إدارة مرنة للاشتراكات مع دعم لخطط متعددة (3 أشهر، 6 أشهر، سنة)</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <h4>نظام HWID المتقدم</h4>
                    <p>حماية متقدمة عبر ربط الحسابات بأجهزة المستخدمين مع إمكانية إعادة التعيين</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h4>نظام الكريدت</h4>
                    <p>نظام كريدت متكامل مع إمكانية الشحن والسحب وتتبع المعاملات</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <h4>إشعارات تلغرام</h4>
                    <p>إشعارات فورية عبر تلغرام لجميع أنشطة النظام والتحذيرات الأمنية</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>تقارير وإحصائيات</h4>
                    <p>تقارير تفصيلية وإحصائيات دقيقة لمراقبة أداء النظام والمستخدمين</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم الأسعار -->
<section id="pricing" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">باقات الاشتراك</h2>
            <p class="text-muted">اختر الباقة المناسبة لاحتياجاتك</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h4 class="fw-bold mb-0">باقة أساسية</h4>
                    </div>
                    <div class="card-body text-center">
                        <h2 class="card-title fw-bold">$29<small class="text-muted fw-light">/شهرياً</small></h2>
                        <ul class="list-unstyled mt-4 mb-5">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>حتى 100 مستخدم</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>إدارة الاشتراكات الأساسية</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>نظام HWID</li>
                            <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>إشعارات تلغرام</li>
                            <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>نظام الكريدت</li>
                            <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>التقارير المتقدمة</li>
                        </ul>
                        <a href="#" class="btn btn-outline-primary btn-lg d-block">اختر الباقة</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 shadow border-primary">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h4 class="fw-bold mb-0">باقة احترافية</h4>
                        <span class="badge bg-warning mt-2">الأكثر شعبية</span>
                    </div>
                    <div class="card-body text-center">
                        <h2 class="card-title fw-bold">$59<small class="text-muted fw-light">/شهرياً</small></h2>
                        <ul class="list-unstyled mt-4 mb-5">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>حتى 500 مستخدم</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>إدارة الاشتراكات المتقدمة</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>نظام HWID المتقدم</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>إشعارات تلغرام</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>نظام الكريدت</li>
                            <li class="mb-2"><i class="fas fa-times text-danger me-2"></i>التقارير المتقدمة</li>
                        </ul>
                        <a href="#" class="btn btn-primary btn-lg d-block">اختر الباقة</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h4 class="fw-bold mb-0">باقة المؤسسات</h4>
                    </div>
                    <div class="card-body text-center">
                        <h2 class="card-title fw-bold">$99<small class="text-muted fw-light">/شهرياً</small></h2>
                        <ul class="list-unstyled mt-4 mb-5">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>مستخدمين غير محدودين</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>إدارة الاشتراكات الاحترافية</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>نظام HWID المتقدم</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>إشعارات تلغرام</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>نظام الكريدت المتقدم</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>التقارير والإحصائيات المتقدمة</li>
                        </ul>
                        <a href="#" class="btn btn-outline-primary btn-lg d-block">اختر الباقة</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- قسم الاتصال -->
<section id="contact" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">تواصل معنا</h2>
                <p class="mb-4">نحن هنا للإجابة على أي استفسار. يمكنك التواصل معنا من خلال النموذج أو عبر وسائل الاتصال المتاحة.</p>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3 text-primary" style="font-size: 24px;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">البريد الإلكتروني</h5>
                        <p class="mb-0">info@example.com</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3 text-primary" style="font-size: 24px;">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">الهاتف</h5>
                        <p class="mb-0">+123 456 7890</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3 text-primary" style="font-size: 24px;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">العنوان</h5>
                        <p class="mb-0">123 شارع الرئيسي، المدينة</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">أرسل استفسارك</h4>
                        <form>
                            <div class="mb-3">
                                <label for="name" class="form-label">الاسم</label>
                                <input type="text" class="form-control" id="name" placeholder="ادخل اسمك">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="email" placeholder="ادخل بريدك الإلكتروني">
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">الرسالة</label>
                                <textarea class="form-control" id="message" rows="4" placeholder="اكتب رسالتك هنا"></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">إرسال الرسالة</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="mb-3">نظام إدارة المستخدمين</h5>
                <p>نظام متكامل لإدارة المستخدمين والعضويات بطريقة آمنة وسهلة.</p>
                <div class="mt-3">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="col-md-2 mb-4 mb-md-0">
                <h5 class="mb-3">روابط سريعة</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">الرئيسية</a></li>
                    <li class="mb-2"><a href="#features" class="text-white text-decoration-none">المميزات</a></li>
                    <li class="mb-2"><a href="#pricing" class="text-white text-decoration-none">الأسعار</a></li>
                    <li class="mb-2"><a href="#contact" class="text-white text-decoration-none">اتصل بنا</a></li>
                </ul>
            </div>
            
            <div class="col-md-2 mb-4 mb-md-0">
                <h5 class="mb-3">المساعدة</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">الأسئلة الشائعة</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">الدعم الفني</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">سياسة الخصوصية</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">شروط الاستخدام</a></li>
                </ul>
            </div>
            
            <div class="col-md-4">
                <h5 class="mb-3">النشرة الإخبارية</h5>
                <p>اشترك في نشرتنا الإخبارية للحصول على آخر التحديثات والعروض.</p>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="بريدك الإلكتروني" aria-label="بريدك الإلكتروني">
                    <button class="btn btn-light" type="button">اشتراك</button>
                </div>
            </div>
        </div>
        
        <hr class="mt-4 mb-4 bg-light">
        
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">&copy; {{ date('Y') }} نظام إدارة المستخدمين. جميع الحقوق محفوظة.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">تم التطوير بواسطة <a href="#" class="text-white">فريق التطوير</a></p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>