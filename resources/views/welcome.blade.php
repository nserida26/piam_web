<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PIAM — Plateforme Intégrée de suivi des Accès à l'eau et à l'assainissement en Mauritanie">
    <title>PIAM — Plateforme de suivi Eau & Assainissement</title>
    <link href="{{ asset('assets/admin/imgs/logo.png') }}" rel="icon" type="image/png">

    <!-- Cairo Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'piam-red':      '#D01C1F',
                        'piam-red-dk':   '#a81518',
                        'piam-green':    '#00A95C',
                        'piam-green-dk': '#008248',
                        'piam-gold':     '#FFD700',
                        'piam-gold-dk':  '#e6c200',
                        'piam-dark':     '#1c2a38',
                        'piam-dark2':    '#243447',
                    },
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        * { font-family: 'Cairo', sans-serif; }

        html[dir="rtl"] .ltr-only  { display: none !important; }
        html[dir="ltr"] .rtl-only  { display: none !important; }
        html[dir="rtl"] .rtl-flip  { transform: scaleX(-1); }

        html { scroll-behavior: smooth; }

        .hero-bg {
            background: linear-gradient(135deg, #1c2a38 0%, #243447 40%, #1a3828 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(0,169,92,0.18) 0%, transparent 60%),
                radial-gradient(circle at 80% 20%, rgba(208,28,31,0.15) 0%, transparent 50%),
                radial-gradient(circle at 60% 80%, rgba(255,215,0,0.08) 0%, transparent 40%);
        }
        .hero-bg > * { position: relative; z-index: 1; }

        .animated-underline {
            position: relative;
            display: inline-block;
        }
        .animated-underline::after {
            content: '';
            position: absolute;
            bottom: -4px; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #00A95C, #FFD700);
            border-radius: 2px;
        }

        .stat-card, .feature-card, .quick-card {
            transition: transform 0.25s, box-shadow 0.25s;
        }
        .stat-card:hover, .feature-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(0,0,0,0.12); }
        .feature-card:hover { border-color: #00A95C; }
        .quick-card:hover   { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,0.14); }

        .nav-link-item { position: relative; }
        .nav-link-item::after {
            content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
            height: 2px; background: #00A95C;
            transform: scaleX(0); transition: transform 0.2s;
        }
        .nav-link-item:hover::after { transform: scaleX(1); }

        .fade-in {
            opacity: 0; transform: translateY(24px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .fade-in.visible { opacity: 1; transform: none; }

        .flag-stripe {
            height: 5px;
            background: linear-gradient(90deg, #D01C1F 0%, #FFD700 50%, #00A95C 100%);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    <!-- FLAG STRIPE -->
    <div class="flag-stripe"></div>

    <!-- ═══════════════ NAVBAR ═══════════════ -->
    <header id="navbar" class="sticky top-0 z-50 bg-white shadow-md transition-shadow duration-300">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-piam-green flex items-center justify-center shadow">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                    </svg>
                </div>
                <span class="text-xl font-extrabold text-piam-dark tracking-wide">PIAM</span>
            </div>

            <div class="hidden md:flex items-center gap-8">
                <a href="#about"    class="nav-link-item text-sm font-semibold text-gray-600 hover:text-piam-green transition-colors"><span class="ltr-only">À propos</span><span class="rtl-only">حول البرنامج</span></a>
                <a href="#features" class="nav-link-item text-sm font-semibold text-gray-600 hover:text-piam-green transition-colors"><span class="ltr-only">Fonctionnalités</span><span class="rtl-only">المزايا</span></a>
                <a href="#stats"    class="nav-link-item text-sm font-semibold text-gray-600 hover:text-piam-green transition-colors"><span class="ltr-only">Chiffres clés</span><span class="rtl-only">أرقام رئيسية</span></a>
                <a href="#access"   class="nav-link-item text-sm font-semibold text-gray-600 hover:text-piam-green transition-colors"><span class="ltr-only">Accès rapide</span><span class="rtl-only">روابط سريعة</span></a>
            </div>

            <div class="flex items-center gap-3">
                <button id="lang-toggle" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border-2 border-piam-green text-piam-green text-xs font-bold hover:bg-piam-green hover:text-white transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                    <span id="lang-label">العربية</span>
                </button>
                <a href="{{ route('admin.showlogin') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-piam-red text-white text-sm font-bold hover:bg-piam-red-dk transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span class="ltr-only">Connexion</span><span class="rtl-only">تسجيل الدخول</span>
                </a>
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 px-4 py-3 space-y-2">
            <a href="#about"    class="block py-2 text-sm font-semibold text-gray-600 hover:text-piam-green"><span class="ltr-only">À propos</span><span class="rtl-only">حول البرنامج</span></a>
            <a href="#features" class="block py-2 text-sm font-semibold text-gray-600 hover:text-piam-green"><span class="ltr-only">Fonctionnalités</span><span class="rtl-only">المزايا</span></a>
            <a href="#stats"    class="block py-2 text-sm font-semibold text-gray-600 hover:text-piam-green"><span class="ltr-only">Chiffres clés</span><span class="rtl-only">أرقام رئيسية</span></a>
            <a href="{{ route('admin.showlogin') }}" class="block mt-2 text-center py-2 rounded-lg bg-piam-red text-white text-sm font-bold">
                <span class="ltr-only">Connexion</span><span class="rtl-only">تسجيل الدخول</span>
            </a>
        </div>
    </header>

    <!-- ═══════════════ HERO ═══════════════ -->
    <section class="hero-bg min-h-screen flex items-center py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="text-white">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/20 text-piam-gold text-xs font-bold mb-6">
                        <span class="w-2 h-2 rounded-full bg-piam-green animate-pulse"></span>
                        <span class="ltr-only">République Islamique de Mauritanie</span>
                        <span class="rtl-only">الجمهورية الإسلامية الموريتانية</span>
                    </div>
                    <h1 class="text-5xl sm:text-6xl font-black leading-tight mb-4">
                        <span class="animated-underline">PIAM</span>
                    </h1>
                    <p class="text-lg font-semibold text-white/90 mb-3">
                        <span class="ltr-only">Plateforme Intégrée de suivi des Accès à l'eau potable et à l'assainissement en Mauritanie</span>
                        <span class="rtl-only">المنصة المتكاملة لرصد الوصول إلى المياه الصالحة للشرب والصرف الصحي في موريتانيا</span>
                    </p>
                    <p class="text-base text-white/60 mb-8 leading-relaxed max-w-lg">
                        <span class="ltr-only">Outil de pilotage national pour la collecte, la validation et l'analyse des données d'intervention en eau et assainissement sur l'ensemble du territoire mauritanien.</span>
                        <span class="rtl-only">أداة وطنية لإدارة جمع البيانات والتحقق منها وتحليلها المتعلقة بتدخلات المياه والصرف الصحي على كامل التراب الموريتاني.</span>
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('admin.showlogin') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-piam-green text-white font-bold text-sm shadow-lg hover:bg-piam-green-dk transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span class="ltr-only">Accéder à la plateforme</span>
                            <span class="rtl-only">الدخول إلى المنصة</span>
                        </a>
                        <a href="#about" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-white/30 text-white font-bold text-sm hover:bg-white/10 transition-all">
                            <span class="ltr-only">En savoir plus</span>
                            <span class="rtl-only">اعرف أكثر</span>
                            <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        <a href="{{ route('download.apk') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-purple-500 text-white font-bold text-sm shadow-lg hover:bg-purple-600 transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16"/>
                            </svg>
                            <span class="ltr-only">Télécharger l'application (APK)</span>
                            <span class="rtl-only">تحميل التطبيق (APK)</span>
                        </a>
                    </div>
                </div>

                <div class="hidden lg:flex justify-center items-center">
                    <div class="relative w-80 h-80">
                        <div class="absolute inset-0 rounded-full border-2 border-piam-green/20 animate-ping" style="animation-duration:3s;"></div>
                        <div class="absolute inset-4 rounded-full border-2 border-piam-gold/20 animate-ping" style="animation-duration:3.5s;animation-delay:.5s;"></div>
                        <div class="absolute inset-10 rounded-full bg-piam-green/10 border border-piam-green/30 flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-20 h-20 text-piam-green mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-white font-bold text-sm mt-2">Mauritanie</p>
                                <p class="text-piam-green text-xs font-semibold">15 Wilayas</p>
                            </div>
                        </div>
                        <div class="absolute top-2 left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-piam-red shadow-lg"></div>
                        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 w-3 h-3 rounded-full bg-piam-gold shadow-lg"></div>
                        <div class="absolute left-2 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full bg-piam-green shadow-lg"></div>
                        <div class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full bg-white/40"></div>
                    </div>
                </div>
            </div>

            <!-- Mini stats -->
            <div class="mt-16 grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach([
                    ['num'=>'15',    'fr'=>'Wilayas',             'ar'=>'ولاية'],
                    ['num'=>'56',    'fr'=>'Moughatas',           'ar'=>'مقاطعة'],
                    ['num'=>'3 500+','fr'=>'Localités',           'ar'=>'تجمع سكاني'],
                    ['num'=>'7',     'fr'=>"Types d'opérations",  'ar'=>'أنواع التدخلات'],
                ] as $ms)
                <div class="rounded-xl bg-white/8 border border-white/12 px-5 py-4 text-center">
                    <div class="text-2xl font-black text-piam-gold">{{ $ms['num'] }}</div>
                    <div class="text-xs font-semibold text-white/70 mt-0.5">
                        <span class="ltr-only">{{ $ms['fr'] }}</span>
                        <span class="rtl-only">{{ $ms['ar'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ═══════════════ ABOUT ═══════════════ -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="relative h-72 hidden lg:block fade-in">
                    <div class="absolute inset-0 bg-gradient-to-br from-piam-green/10 to-piam-green/5 rounded-2xl border border-piam-green/20 flex items-center justify-center">
                        <div class="text-center p-8">
                            <div class="w-20 h-20 rounded-2xl bg-piam-dark flex items-center justify-center mx-auto mb-4 shadow-xl">
                                <svg class="w-10 h-10 text-piam-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <p class="font-bold text-piam-dark text-base ltr-only">Suivi intégré des interventions</p>
                            <p class="font-bold text-piam-dark text-base rtl-only">متابعة التدخلات المتكاملة</p>
                            <p class="text-gray-500 text-sm mt-1 ltr-only">Eau & Assainissement — Mauritanie</p>
                            <p class="text-gray-500 text-sm mt-1 rtl-only">المياه والصرف الصحي — موريتانيا</p>
                        </div>
                    </div>
                    <div class="absolute -top-3 -right-3 px-3 py-1.5 rounded-full bg-piam-red text-white text-xs font-bold shadow-md">WASH</div>
                    <div class="absolute -bottom-3 -left-3 px-3 py-1.5 rounded-full bg-piam-green text-white text-xs font-bold shadow-md ltr-only">Mauritanie</div>
                    <div class="absolute -bottom-3 -left-3 px-3 py-1.5 rounded-full bg-piam-green text-white text-xs font-bold shadow-md rtl-only">موريتانيا</div>
                </div>

                <div class="fade-in">
                    <p class="text-piam-green text-sm font-bold uppercase tracking-widest mb-3">
                        <span class="ltr-only">À propos</span><span class="rtl-only">حول البرنامج</span>
                    </p>
                    <h2 class="text-3xl sm:text-4xl font-black text-piam-dark mb-5 leading-tight">
                        <span class="ltr-only">Une plateforme au service de l'accès universel à l'eau</span>
                        <span class="rtl-only">منصة في خدمة الوصول الشامل إلى المياه</span>
                    </h2>
                    <p class="text-gray-600 mb-4 leading-relaxed ltr-only">
                        Le PIAM est un système national de gestion de l'information sur les programmes d'eau potable et d'assainissement en Mauritanie. Il permet le suivi en temps réel des interventions, de la phase de déclenchement jusqu'à la certification finale.
                    </p>
                    <p class="text-gray-600 mb-4 leading-relaxed rtl-only">
                        برنامج PIAM هو نظام وطني لإدارة المعلومات المتعلقة ببرامج المياه الصالحة للشرب والصرف الصحي في موريتانيا. يتيح متابعة التدخلات في الوقت الفعلي، من مرحلة الإطلاق حتى التصديق النهائي.
                    </p>
                    <p class="text-gray-600 mb-6 leading-relaxed ltr-only">
                        Développé pour centraliser les données collectées par les équipes terrain, la plateforme offre une vision consolidée par wilaya, moughata et localité, facilitant la prise de décision stratégique.
                    </p>
                    <p class="text-gray-600 mb-6 leading-relaxed rtl-only">
                        تم تطوير هذه المنصة لمركزة البيانات التي تجمعها الفرق الميدانية، وتقدم رؤية موحدة حسب الولاية والمقاطعة والتجمع السكاني، مما يسهل اتخاذ القرارات الاستراتيجية.
                    </p>
                    <ul class="space-y-3">
                        @foreach([
                            ['fr'=>"Centraliser les données de terrain en temps réel",'ar'=>'تركيز بيانات الميدان في الوقت الفعلي'],
                            ['fr'=>"Suivre les étapes de chaque opération WASH",      'ar'=>'متابعة مراحل كل تدخل في مجال المياه'],
                            ['fr'=>"Faciliter la prise de décision stratégique",       'ar'=>'تسهيل اتخاذ القرارات الاستراتيجية'],
                            ['fr'=>"Assurer la traçabilité et l'audit des données",   'ar'=>'ضمان إمكانية تتبع البيانات ومراجعتها'],
                        ] as $obj)
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-piam-green/15 text-piam-green flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            <span class="text-gray-700 text-sm font-medium">
                                <span class="ltr-only">{{ $obj['fr'] }}</span>
                                <span class="rtl-only">{{ $obj['ar'] }}</span>
                            </span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════ FEATURES ═══════════════ -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14 fade-in">
                <p class="text-piam-green text-sm font-bold uppercase tracking-widest mb-2">
                    <span class="ltr-only">Fonctionnalités</span><span class="rtl-only">المزايا</span>
                </p>
                <h2 class="text-3xl sm:text-4xl font-black text-piam-dark mb-4">
                    <span class="ltr-only">Tout ce dont vous avez besoin</span>
                    <span class="rtl-only">كل ما تحتاجه</span>
                </h2>
                <p class="text-gray-500 max-w-xl mx-auto ltr-only">Une suite complète d'outils pour la gestion, l'analyse et le reporting des programmes d'eau et d'assainissement.</p>
                <p class="text-gray-500 max-w-xl mx-auto rtl-only">مجموعة متكاملة من الأدوات لإدارة وتحليل وإعداد تقارير برامج المياه والصرف الصحي.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    ['icon'=>'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7','bg'=>'bg-blue-500','fr'=>['Cartographie','Visualisation géographique des localités et indicateurs sur une carte interactive.'],'ar'=>['رسم الخرائط','تصور جغرافي للتجمعات السكانية والمؤشرات على خريطة تفاعلية.']],
                    ['icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','bg'=>'bg-piam-green','fr'=>['Reporting avancé','Génération automatique de rapports par territoire, opération et période avec graphiques.'],'ar'=>['التقارير المتقدمة','إعداد تقارير تلقائية حسب المنطقة أو التدخل أو الفترة الزمنية مع رسوم بيانية.']],
                    ['icon'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12','bg'=>'bg-piam-red','fr'=>["Import de données","Import en masse depuis Excel/CSV avec prévisualisation et validation automatique."],'ar'=>['استيراد البيانات','الاستيراد الجماعي من Excel/CSV مع معاينة وتحقق تلقائي واكتشاف الأخطاء.']],
                    ['icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','bg'=>'bg-yellow-500','fr'=>['Validation','Processus structuré avec confirmation, rejet et traçabilité complète des modifications.'],'ar'=>['التحقق من البيانات','عملية تحقق منظمة مع التأكيد والرفض وتتبع التعديلات.']],
                    ['icon'=>'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z','bg'=>'bg-purple-500','fr'=>['Application mobile','Saisie hors ligne avec synchronisation automatique lors du retour en ligne.'],'ar'=>['تطبيق الهاتف','إدخال البيانات بدون إنترنت مع المزامنة التلقائية عند العودة إلى الاتصال.']],
                    ['icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z','bg'=>'bg-indigo-500','fr'=>['Gestion des accès','Contrôle des droits par rôles, wilaya, opération et opérateur avec journal d\'audit.'],'ar'=>['إدارة الصلاحيات','التحكم في الصلاحيات حسب الأدوار والولاية والتدخل والمشغل مع سجل المراجعة.']],
                ] as $i => $f)
                <div class="feature-card bg-white rounded-2xl border border-gray-100 p-6 shadow-sm fade-in" style="transition-delay:{{ $i * 80 }}ms">
                    <div class="w-12 h-12 rounded-xl {{ $f['bg'] }} flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-piam-dark text-base mb-2">
                        <span class="ltr-only">{{ $f['fr'][0] }}</span>
                        <span class="rtl-only">{{ $f['ar'][0] }}</span>
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        <span class="ltr-only">{{ $f['fr'][1] }}</span>
                        <span class="rtl-only">{{ $f['ar'][1] }}</span>
                    </p>
                    @if($f['fr'][0] === 'Application mobile')
                    <a href="{{ route('download.apk') }}" class="inline-flex items-center gap-1.5 mt-4 text-purple-600 hover:text-purple-800 text-xs font-bold transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16"/>
                        </svg>
                        <span class="ltr-only">Télécharger l'APK</span>
                        <span class="rtl-only">تحميل APK</span>
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ═══════════════ STATS ═══════════════ -->
    <section id="stats" class="py-20 bg-piam-dark relative overflow-hidden">
        <div class="absolute top-0 left-0 w-64 h-64 rounded-full bg-piam-green/10 blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 rounded-full bg-piam-red/10 blur-3xl translate-x-1/2 translate-y-1/2"></div>
        <div class="absolute inset-0 opacity-5" style="background-image:radial-gradient(circle,white 1px,transparent 1px);background-size:40px 40px;"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center mb-14 fade-in">
                <p class="text-piam-gold text-sm font-bold uppercase tracking-widest mb-2">
                    <span class="ltr-only">Chiffres clés</span><span class="rtl-only">أرقام رئيسية</span>
                </p>
                <h2 class="text-3xl sm:text-4xl font-black text-white">
                    <span class="ltr-only">La Mauritanie en chiffres</span>
                    <span class="rtl-only">موريتانيا بالأرقام</span>
                </h2>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['num'=>'15',    'border'=>'border-piam-gold', 'fr'=>'Wilayas couvertes',   'ar'=>'ولاية مشمولة',      'icon'=>'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                    ['num'=>'56',    'border'=>'border-piam-green','fr'=>'Moughatas',            'ar'=>'مقاطعة',             'icon'=>'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276'],
                    ['num'=>'3 500+','border'=>'border-piam-red',  'fr'=>'Localités suivies',   'ar'=>'تجمع سكاني متابَع', 'icon'=>'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5'],
                    ['num'=>'7',     'border'=>'border-blue-400',  'fr'=>"Types d'opérations", 'ar'=>'أنواع التدخلات',    'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ] as $i => $s)
                <div class="stat-card bg-white/6 {{ $s['border'] }} border-t-2 border-l-0 border-r-0 border-b-0 rounded-2xl p-6 text-center fade-in" style="transition-delay:{{ $i*100 }}ms">
                    <div class="w-12 h-12 rounded-xl bg-white/8 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $s['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="text-4xl font-black text-white mb-2">{{ $s['num'] }}</div>
                    <div class="text-sm font-semibold text-white/60">
                        <span class="ltr-only">{{ $s['fr'] }}</span>
                        <span class="rtl-only">{{ $s['ar'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ═══════════════ OPERATIONS TIMELINE ═══════════════ -->
    <section class="py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 fade-in">
                <p class="text-piam-green text-sm font-bold uppercase tracking-widest mb-2">
                    <span class="ltr-only">Cycle d'intervention</span><span class="rtl-only">دورة التدخل</span>
                </p>
                <h2 class="text-3xl font-black text-piam-dark">
                    <span class="ltr-only">7 étapes d'un programme WASH</span>
                    <span class="rtl-only">٧ مراحل لبرنامج المياه والصرف الصحي</span>
                </h2>
            </div>
            <div class="relative">
                <div class="absolute top-6 left-6 right-6 h-0.5 bg-gradient-to-r from-piam-red via-piam-gold to-piam-green hidden lg:block"></div>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4">
                    @foreach([
                        ['1','bg-piam-red',    'Déclenchement',  'الإطلاق'],
                        ['2','bg-orange-500',  "État des lieux", 'الجرد الميداني'],
                        ['3','bg-piam-gold',   'Certification',  'شهادة FDAL'],
                        ['4','bg-piam-green',  'Dernier suivi',  'المتابعة الأخيرة'],
                        ['5','bg-blue-500',    'Inventaire',     'الجرد'],
                        ['6','bg-indigo-500',  'Programmation',  'البرمجة'],
                        ['7','bg-purple-500',  'Réalisation',    'التنفيذ'],
                    ] as $i => $op)
                    <div class="fade-in text-center" style="transition-delay:{{ $i*80 }}ms">
                        <div class="relative z-10 w-12 h-12 rounded-full {{ $op[1] }} text-white font-black text-sm flex items-center justify-center mx-auto mb-3 shadow-md">{{ $op[0] }}</div>
                        <p class="text-xs font-semibold text-gray-700 leading-tight">
                            <span class="ltr-only">{{ $op[2] }}</span>
                            <span class="rtl-only">{{ $op[3] }}</span>
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════ QUICK ACCESS ═══════════════ -->
    <section id="access" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 fade-in">
                <p class="text-piam-green text-sm font-bold uppercase tracking-widest mb-2">
                    <span class="ltr-only">Navigation rapide</span><span class="rtl-only">روابط سريعة</span>
                </p>
                <h2 class="text-3xl font-black text-piam-dark">
                    <span class="ltr-only">Accès direct aux modules</span>
                    <span class="rtl-only">وصول مباشر للوحدات</span>
                </h2>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['/admin/home','/admin/home','from-piam-green to-piam-green-dk',false,'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6','Tableau de bord','Indicateurs et KPIs globaux','لوحة المتابعة','عرض شامل للمؤشرات'],
                    ['/admin/reporting','/admin/reporting','from-blue-500 to-blue-700',false,'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','Reporting','Analyses et tableaux de bord','التقارير','تحليلات ولوحات متابعة'],
                    ['/admin/imports/administrations','/admin/imports/administrations','from-piam-red to-piam-red-dk',false,'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12','Importation','Import Excel avec prévisualisation','الاستيراد','استيراد Excel مع معاينة متقدمة'],
                    ['/admin/maps','/admin/maps','from-piam-gold to-piam-gold-dk',true,'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7','Cartographie','Visualisation géospatiale','الخرائط','تصور البيانات الجغرافية'],
                ] as $i => $lk)
                <a href="{{ $lk[0] }}" class="quick-card rounded-2xl bg-gradient-to-br {{ $lk[2] }} p-6 shadow-md group block fade-in" style="transition-delay:{{ $i*80 }}ms">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors">
                        <svg class="w-6 h-6 {{ $lk[3] ? 'text-piam-dark' : 'text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $lk[4] }}"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-base {{ $lk[3] ? 'text-piam-dark' : 'text-white' }} mb-1">
                        <span class="ltr-only">{{ $lk[5] }}</span>
                        <span class="rtl-only">{{ $lk[7] }}</span>
                    </h3>
                    <p class="text-sm {{ $lk[3] ? 'text-piam-dark/70' : 'text-white/70' }}">
                        <span class="ltr-only">{{ $lk[6] }}</span>
                        <span class="rtl-only">{{ $lk[8] }}</span>
                    </p>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ═══════════════ CTA BANNER ═══════════════ -->
    <section class="py-16 bg-gradient-to-r from-piam-green to-piam-green-dk relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle,white 1px,transparent 1px);background-size:30px 30px;"></div>
        <div class="max-w-3xl mx-auto px-4 text-center relative">
            <h2 class="text-3xl font-black text-white mb-4">
                <span class="ltr-only">Prêt à commencer ?</span>
                <span class="rtl-only">هل أنت مستعد للبدء؟</span>
            </h2>
            <p class="text-white/80 mb-8">
                <span class="ltr-only">Connectez-vous à la plateforme pour accéder à vos données et tableaux de bord.</span>
                <span class="rtl-only">سجّل دخولك إلى المنصة للوصول إلى بياناتك ولوحات المتابعة الخاصة بك.</span>
            </p>
            <a href="{{ route('admin.showlogin') }}" class="inline-flex items-center gap-3 px-8 py-4 rounded-xl bg-white text-piam-green font-black text-base shadow-xl hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span class="ltr-only">Accéder à la plateforme</span>
                <span class="rtl-only">الدخول إلى المنصة</span>
            </a>
        </div>
    </section>

    <!-- ═══════════════ FOOTER ═══════════════ -->
    <footer class="bg-piam-dark py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-10 mb-10">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-piam-green flex items-center justify-center shadow">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-black text-white">PIAM</span>
                    </div>
                    <p class="text-white/55 text-sm leading-relaxed ltr-only">Plateforme nationale de suivi des programmes d'eau potable et d'assainissement en Mauritanie.</p>
                    <p class="text-white/55 text-sm leading-relaxed rtl-only">المنصة الوطنية لمتابعة برامج المياه الصالحة للشرب والصرف الصحي في موريتانيا.</p>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm mb-4 uppercase tracking-wider">
                        <span class="ltr-only">Modules</span><span class="rtl-only">الوحدات</span>
                    </h4>
                    <ul class="space-y-2">
                        @foreach([
                            ['/admin/home','Tableau de bord','لوحة المتابعة'],
                            ['/admin/reporting','Reporting','التقارير'],
                            ['/admin/imports/administrations','Importation','الاستيراد'],
                            ['/admin/maps','Cartographie','الخرائط'],
                        ] as $fl)
                        <li>
                            <a href="{{ $fl[0] }}" class="text-white/55 hover:text-piam-gold text-sm transition-colors">
                                <span class="ltr-only">{{ $fl[1] }}</span>
                                <span class="rtl-only">{{ $fl[2] }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm mb-4 uppercase tracking-wider">
                        <span class="ltr-only">Organisation</span><span class="rtl-only">المنظمة</span>
                    </h4>
                    <p class="text-white/55 text-sm leading-relaxed ltr-only">République Islamique de Mauritanie<br>Ministère de l'Hydraulique et de l'Assainissement</p>
                    <p class="text-white/55 text-sm leading-relaxed rtl-only">الجمهورية الإسلامية الموريتانية<br>وزارة الموارد المائية والصرف الصحي</p>
                    <div class="flex gap-2 mt-4">
                        <span class="h-2 w-6 rounded-full bg-piam-red"></span>
                        <span class="h-2 w-6 rounded-full bg-piam-gold"></span>
                        <span class="h-2 w-6 rounded-full bg-piam-green"></span>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-white/40 text-xs">&copy; {{ date('Y') }} PIAM — <span class="ltr-only">Tous droits réservés</span><span class="rtl-only">جميع الحقوق محفوظة</span></p>
                <a href="{{ route('admin.showlogin') }}" class="text-xs text-piam-gold hover:text-piam-gold-dk transition-colors font-semibold">
                    <span class="ltr-only">Connexion</span><span class="rtl-only">تسجيل الدخول</span>
                </a>
            </div>
        </div>
    </footer>

    <script>
    (function () {
        var currentLang = localStorage.getItem('piamLang') || 'fr';

        function applyLang(lang) {
            currentLang = lang;
            localStorage.setItem('piamLang', lang);
            document.documentElement.lang = lang;
            document.documentElement.dir  = lang === 'ar' ? 'rtl' : 'ltr';
            document.getElementById('lang-label').textContent = lang === 'fr' ? 'العربية' : 'Français';
        }

        document.getElementById('lang-toggle').addEventListener('click', function () {
            applyLang(currentLang === 'fr' ? 'ar' : 'fr');
        });

        applyLang(currentLang);

        document.getElementById('mobile-menu-btn').addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); }
            });
        }, { threshold: 0.12 });
        document.querySelectorAll('.fade-in').forEach(function (el) { io.observe(el); });

        window.addEventListener('scroll', function () {
            var nav = document.getElementById('navbar');
            nav.classList.toggle('shadow-xl', window.scrollY > 20);
            nav.classList.toggle('shadow-md', window.scrollY <= 20);
        }, { passive: true });
    })();
    </script>
</body>
</html>
