<?php
require_once 'db_connect.php';

$result = $conn->query("SELECT COUNT(*) as count FROM applications");
$accepted = $result->fetch_assoc()['count'];

$capacity = 259;
$spots_left = max(0, $capacity - $accepted);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MARS COLONIAL AUTHORITY — Application</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        space: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        mars: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Space Grotesk', sans-serif; }

        body {
            background: #000;
            overflow-x: hidden;
        }

        /* Animated starfield */
        #starfield {
            position: fixed;
            inset: 0;
            z-index: 0;
        }
        #starfield canvas { width: 100%; height: 100%; }

        /* Mars horizon glow */
        .mars-glow {
            position: fixed;
            bottom: -30%;
            left: 50%;
            transform: translateX(-50%);
            width: 140%;
            height: 60%;
            background: radial-gradient(ellipse at center bottom, rgba(234, 88, 12, 0.15) 0%, rgba(234, 88, 12, 0.05) 30%, transparent 70%);
            pointer-events: none;
            z-index: 1;
        }

        /* Scan line effect */
        .scanlines {
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0, 0, 0, 0.03) 2px,
                rgba(0, 0, 0, 0.03) 4px
            );
            pointer-events: none;
            z-index: 2;
        }

        /* Glowing input focus */
        .input-glow:focus {
            box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.3), 0 0 20px rgba(249, 115, 22, 0.1);
        }

        /* Custom range slider */
        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            height: 6px;
            border-radius: 3px;
            background: linear-gradient(to right, #1e293b, #334155);
            outline: none;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316, #ea580c);
            cursor: pointer;
            box-shadow: 0 0 12px rgba(249, 115, 22, 0.5);
            transition: box-shadow 0.2s;
        }
        input[type="range"]::-webkit-slider-thumb:hover {
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.8);
        }

        /* Custom radio & checkbox */
        input[type="radio"], input[type="checkbox"] {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #475569;
            background: #0f172a;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        input[type="radio"] { border-radius: 50%; }
        input[type="checkbox"] { border-radius: 4px; }
        input[type="radio"]:checked, input[type="checkbox"]:checked {
            border-color: #f97316;
            background: #f97316;
            box-shadow: 0 0 8px rgba(249, 115, 22, 0.4);
        }
        input[type="radio"]:checked::after {
            content: '';
            display: block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
            margin: 3px auto;
        }
        input[type="checkbox"]:checked::after {
            content: '\2713';
            display: block;
            text-align: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
            line-height: 14px;
        }

        /* Skill card hover */
        .skill-card {
            transition: all 0.2s;
        }
        .skill-card:hover {
            background: rgba(249, 115, 22, 0.08);
            border-color: rgba(249, 115, 22, 0.3);
        }
        .skill-card:has(input:checked) {
            background: rgba(249, 115, 22, 0.1);
            border-color: rgba(249, 115, 22, 0.5);
        }

        /* Submit button animation */
        .btn-launch {
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }
        .btn-launch::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        .btn-launch:hover::before {
            left: 100%;
        }
        .btn-launch:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(249, 115, 22, 0.4);
        }

        /* Section labels */
        .section-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        /* Fade in animation */
        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s ease forwards;
        }
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Status bar blink */
        .status-dot {
            animation: blink 2s infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 antialiased">

    <!-- Starfield canvas -->
    <div id="starfield">
        <canvas id="stars"></canvas>
    </div>

    <!-- Mars glow -->
    <div class="mars-glow"></div>

    <!-- Scanlines -->
    <div class="scanlines"></div>

    <!-- Main content -->
    <div class="relative z-10 max-w-2xl mx-auto px-4 py-8 sm:py-16">

        <!-- Top status bar -->
        <div class="fade-up flex items-center justify-between text-xs font-mono text-slate-500 mb-8 px-1" style="animation-delay: 0s">
            <div class="flex items-center gap-2">
                <span class="status-dot inline-block w-2 h-2 rounded-full bg-green-500"></span>
                <span>SYSTEM ONLINE</span>
            </div>
            <span>MCA-REG-2035-<?= str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) ?></span>
        </div>

        <!-- Header -->
        <div class="fade-up text-center mb-12" style="animation-delay: 0.1s">
            <!-- Logo mark -->
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full border border-mars-500/30 bg-mars-500/5 mb-6">
                <svg viewBox="0 0 100 100" class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="50" cy="50" r="35" stroke="#f97316" stroke-width="2" fill="none"/>
                    <path d="M30 45 Q50 20 70 45" stroke="#f97316" stroke-width="1.5" fill="none"/>
                    <path d="M35 55 Q50 70 65 55" stroke="#f97316" stroke-width="1" fill="none" opacity="0.5"/>
                    <line x1="50" y1="10" x2="50" y2="5" stroke="#f97316" stroke-width="2"/>
                    <polygon points="45,5 50,0 55,5" fill="#f97316"/>
                </svg>
            </div>

            <p class="section-label text-mars-500/70 mb-3">Mars Colonial Authority</p>
            <h1 class="text-4xl sm:text-5xl font-bold text-white tracking-tight mb-3">
                Crew Registration
            </h1>
            <p class="text-slate-400 text-base max-w-md mx-auto leading-relaxed">
                Colonization Program 2035 — New crew member application for the Mars mission
            </p>

            <!-- Mission stats -->
            <div class="flex justify-center gap-6 mt-6">
                <div class="text-center">
                    <p class="text-2xl font-bold text-white font-mono"><?= $accepted ?></p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Accepted</p>
                </div>
                <div class="w-px bg-slate-800"></div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-white font-mono <?= $spots_left <= 10 ? 'text-red-400' : '' ?>"><?= $spots_left ?></p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Spots Left</p>
                </div>
                <div class="w-px bg-slate-800"></div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-white font-mono" id="countdown">---</p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Days to Launch</p>
                </div>
            </div>
        </div>

        <!-- Error messages -->
        <?php if (!empty($_GET['errors'])): ?>
            <div class="fade-up bg-red-950/60 border border-red-500/40 rounded-xl p-5 mb-8 backdrop-blur-sm">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-semibold text-red-300 text-sm">Registration denied — please fix the following:</p>
                </div>
                <ul class="space-y-1 ml-7">
                    <?php foreach (explode('|', $_GET['errors']) as $error): ?>
                        <li class="text-red-200/80 text-sm">— <?= htmlspecialchars(urldecode($error)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="process.php" method="POST" class="space-y-8">

            <!-- Section: Personal Info -->
            <div class="fade-up bg-white/[0.02] border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-5 backdrop-blur-sm" style="animation-delay: 0.2s">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-mars-500/10 border border-mars-500/20 flex items-center justify-center">
                        <span class="text-mars-400 text-sm font-mono font-bold">01</span>
                    </div>
                    <h2 class="text-lg font-semibold text-white">Personal Information</h2>
                </div>

                <!-- First name + Last name -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-slate-400 mb-1.5">First Name <span class="text-mars-500">*</span></label>
                        <input type="text" id="first_name" name="first_name" required placeholder="Enter your first name"
                               class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:border-mars-500 transition-all">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-400 mb-1.5">Last Name <span class="text-mars-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Enter your last name"
                               class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:border-mars-500 transition-all">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-400 mb-1.5">E-mail <span class="text-mars-500">*</span></label>
                    <input type="email" id="email" name="email" required placeholder="you@email.com"
                           class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:border-mars-500 transition-all">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-400 mb-1.5">Phone</label>
                    <input type="tel" id="phone" name="phone" placeholder="+1 234 567 890"
                           class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:border-mars-500 transition-all">
                </div>

                <!-- Date of birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-slate-400 mb-1.5">Date of Birth <span class="text-mars-500">*</span></label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required
                           class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 focus:outline-none focus:border-mars-500 transition-all">
                </div>

                <!-- Gender (radio) -->
                <div>
                    <p class="text-sm font-medium text-slate-400 mb-2.5">Gender <span class="text-mars-500">*</span></p>
                    <div class="flex flex-wrap gap-3">
                        <label class="flex items-center gap-2.5 cursor-pointer px-4 py-2 rounded-lg border border-slate-800 hover:border-slate-700 transition-all">
                            <input type="radio" name="gender" value="male" required>
                            <span class="text-slate-300 text-sm">Male</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer px-4 py-2 rounded-lg border border-slate-800 hover:border-slate-700 transition-all">
                            <input type="radio" name="gender" value="female">
                            <span class="text-slate-300 text-sm">Female</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer px-4 py-2 rounded-lg border border-slate-800 hover:border-slate-700 transition-all">
                            <input type="radio" name="gender" value="other">
                            <span class="text-slate-300 text-sm">Other</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section: Medical -->
            <div class="fade-up bg-white/[0.02] border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-5 backdrop-blur-sm" style="animation-delay: 0.3s">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-mars-500/10 border border-mars-500/20 flex items-center justify-center">
                        <span class="text-mars-400 text-sm font-mono font-bold">02</span>
                    </div>
                    <h2 class="text-lg font-semibold text-white">Medical Profile</h2>
                </div>

                <!-- Blood type (select) -->
                <div>
                    <label for="blood_type" class="block text-sm font-medium text-slate-400 mb-1.5">Blood Type <span class="text-mars-500">*</span></label>
                    <select id="blood_type" name="blood_type" required
                            class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 focus:outline-none focus:border-mars-500 transition-all">
                        <option value="" disabled selected class="text-slate-600">Select blood type</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="0+">0+</option>
                        <option value="0-">0-</option>
                    </select>
                </div>

                <!-- Physical fitness (range) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="physical_fitness" class="text-sm font-medium text-slate-400">Physical Fitness <span class="text-mars-500">*</span></label>
                        <span class="font-mono text-mars-400 font-bold text-lg" id="fitness_value">5</span>
                    </div>
                    <input type="range" id="physical_fitness" name="physical_fitness" min="1" max="10" value="5"
                           oninput="document.getElementById('fitness_value').textContent = this.value"
                           class="w-full cursor-pointer">
                    <div class="flex justify-between text-xs text-slate-600 mt-1 font-mono">
                        <span>1 — Poor</span>
                        <span>5 — Average</span>
                        <span>10 — Excellent</span>
                    </div>
                </div>
            </div>

            <!-- Section: Education & Experience -->
            <div class="fade-up bg-white/[0.02] border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-5 backdrop-blur-sm" style="animation-delay: 0.35s">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-mars-500/10 border border-mars-500/20 flex items-center justify-center">
                        <span class="text-mars-400 text-sm font-mono font-bold">03</span>
                    </div>
                    <h2 class="text-lg font-semibold text-white">Education & Experience</h2>
                </div>

                <!-- Education level (select) -->
                <div>
                    <label for="education" class="block text-sm font-medium text-slate-400 mb-1.5">Highest Education Level <span class="text-mars-500">*</span></label>
                    <select id="education" name="education" required
                            class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 focus:outline-none focus:border-mars-500 transition-all">
                        <option value="" disabled selected class="text-slate-600">Select education level</option>
                        <option value="elementary">Elementary</option>
                        <option value="high_school">High School</option>
                        <option value="associate">Associate Degree</option>
                        <option value="bachelors">Bachelor's Degree</option>
                        <option value="masters">Master's Degree</option>
                        <option value="doctorate">Doctorate (Ph.D.)</option>
                    </select>
                </div>

                <!-- Field of study (text) -->
                <div>
                    <label for="field_of_study" class="block text-sm font-medium text-slate-400 mb-1.5">Field of Study <span class="text-mars-500">*</span></label>
                    <input type="text" id="field_of_study" name="field_of_study" required placeholder="e.g. Astrophysics, Mechanical Engineering, Computer Science..."
                           class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:border-mars-500 transition-all">
                </div>

                <!-- Years of experience (number) -->
                <div>
                    <label for="years_of_experience" class="block text-sm font-medium text-slate-400 mb-1.5">Years of Experience <span class="text-mars-500">*</span></label>
                    <input type="number" id="years_of_experience" name="years_of_experience" required min="0" max="50" placeholder="0"
                           class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:border-mars-500 transition-all">
                </div>

                <!-- Current / last position (text) -->
                <div>
                    <label for="current_position" class="block text-sm font-medium text-slate-400 mb-1.5">Current / Last Position</label>
                    <input type="text" id="current_position" name="current_position" placeholder="e.g. Research Engineer at ESA..."
                           class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 placeholder-slate-600 focus:outline-none focus:border-mars-500 transition-all">
                </div>
            </div>

            <!-- Section: Skills & Role -->
            <div class="fade-up bg-white/[0.02] border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-5 backdrop-blur-sm" style="animation-delay: 0.45s">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-mars-500/10 border border-mars-500/20 flex items-center justify-center">
                        <span class="text-mars-400 text-sm font-mono font-bold">04</span>
                    </div>
                    <h2 class="text-lg font-semibold text-white">Skills & Role</h2>
                </div>

                <!-- Skills (checkbox cards) -->
                <div>
                    <p class="text-sm font-medium text-slate-400 mb-3">Skills <span class="text-mars-500">*</span> <span class="text-slate-600 text-xs ml-1">— select at least one</span></p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <label class="skill-card flex items-center gap-3 cursor-pointer px-4 py-3 rounded-xl border border-slate-800 bg-slate-900/40">
                            <input type="checkbox" name="skills[]" value="programming">
                            <div>
                                <span class="text-slate-200 text-sm font-medium">Programming</span>
                                <p class="text-slate-600 text-xs">Software & systems</p>
                            </div>
                        </label>
                        <label class="skill-card flex items-center gap-3 cursor-pointer px-4 py-3 rounded-xl border border-slate-800 bg-slate-900/40">
                            <input type="checkbox" name="skills[]" value="mechanics">
                            <div>
                                <span class="text-slate-200 text-sm font-medium">Mechanics</span>
                                <p class="text-slate-600 text-xs">Maintenance & repair</p>
                            </div>
                        </label>
                        <label class="skill-card flex items-center gap-3 cursor-pointer px-4 py-3 rounded-xl border border-slate-800 bg-slate-900/40">
                            <input type="checkbox" name="skills[]" value="medicine">
                            <div>
                                <span class="text-slate-200 text-sm font-medium">Medicine</span>
                                <p class="text-slate-600 text-xs">Healthcare & first aid</p>
                            </div>
                        </label>
                        <label class="skill-card flex items-center gap-3 cursor-pointer px-4 py-3 rounded-xl border border-slate-800 bg-slate-900/40">
                            <input type="checkbox" name="skills[]" value="botany">
                            <div>
                                <span class="text-slate-200 text-sm font-medium">Botany</span>
                                <p class="text-slate-600 text-xs">Farming & ecosystems</p>
                            </div>
                        </label>
                        <label class="skill-card flex items-center gap-3 cursor-pointer px-4 py-3 rounded-xl border border-slate-800 bg-slate-900/40">
                            <input type="checkbox" name="skills[]" value="geology">
                            <div>
                                <span class="text-slate-200 text-sm font-medium">Geology</span>
                                <p class="text-slate-600 text-xs">Terrain & minerals</p>
                            </div>
                        </label>
                        <label class="skill-card flex items-center gap-3 cursor-pointer px-4 py-3 rounded-xl border border-slate-800 bg-slate-900/40">
                            <input type="checkbox" name="skills[]" value="piloting">
                            <div>
                                <span class="text-slate-200 text-sm font-medium">Piloting</span>
                                <p class="text-slate-600 text-xs">Spacecraft operation</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Preferred role -->
                <div>
                    <label for="preferred_role" class="block text-sm font-medium text-slate-400 mb-1.5">Preferred Role <span class="text-mars-500">*</span></label>
                    <select id="preferred_role" name="preferred_role" required
                            class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-2.5 text-slate-100 focus:outline-none focus:border-mars-500 transition-all">
                        <option value="" disabled selected class="text-slate-600">Select crew role</option>
                        <option value="pilot">Pilot — Flight control & landing</option>
                        <option value="engineer">Engineer — Base construction & maintenance</option>
                        <option value="scientist">Scientist — Research & analysis</option>
                        <option value="medic">Medic — Crew healthcare</option>
                        <option value="botanist">Botanist — Food production</option>
                    </select>
                </div>
            </div>

            <!-- Section: Motivation -->
            <div class="fade-up bg-white/[0.02] border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-5 backdrop-blur-sm" style="animation-delay: 0.55s">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-mars-500/10 border border-mars-500/20 flex items-center justify-center">
                        <span class="text-mars-400 text-sm font-mono font-bold">05</span>
                    </div>
                    <h2 class="text-lg font-semibold text-white">Motivation</h2>
                </div>

                <div>
                    <label for="motivation" class="block text-sm font-medium text-slate-400 mb-1.5">Why do you want to join the mission? <span class="text-mars-500">*</span></label>
                    <textarea id="motivation" name="motivation" rows="5" required
                              placeholder="Describe your motivation, experience, and what you can offer to the crew..."
                              class="input-glow w-full rounded-lg bg-slate-900/80 border border-slate-700 px-4 py-3 text-slate-100 placeholder-slate-600 focus:outline-none focus:border-mars-500 transition-all resize-none leading-relaxed"></textarea>
                    <p class="text-xs text-slate-600 mt-1.5 font-mono">Min. 20 characters</p>
                </div>
            </div>

            <!-- Agreement & Submit -->
            <div class="fade-up space-y-5" style="animation-delay: 0.6s">

                <!-- Agreement -->
                <label class="flex items-start gap-3.5 cursor-pointer px-5 py-4 rounded-xl border border-slate-800 bg-white/[0.02] hover:border-slate-700 transition-all">
                    <input type="checkbox" name="agreement" value="1" required class="mt-0.5">
                    <span class="text-sm text-slate-400 leading-relaxed">
                        I agree to the mission terms — I acknowledge that the journey to Mars is one-way
                        and that Mars Colonial Authority bears no responsibility for risks associated with
                        interplanetary travel.
                        <span class="text-mars-500">*</span>
                    </span>
                </label>

                <!-- Submit -->
                <button type="submit"
                        class="btn-launch w-full bg-gradient-to-r from-mars-600 to-mars-700 hover:from-mars-500 hover:to-mars-600 text-white font-bold py-4 px-6 rounded-xl text-lg cursor-pointer border border-mars-500/20">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        Submit Application
                    </span>
                </button>

                <p class="text-center text-slate-700 text-xs font-mono tracking-wide">
                    MARS COLONIAL AUTHORITY — REG FORM v2.1 — CLASSIFIED
                </p>
            </div>

        </form>
    </div>

    <!-- Scripts -->
    <script>
        // Animated starfield
        const canvas = document.getElementById('stars');
        const ctx = canvas.getContext('2d');
        let stars = [];

        function initStars() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            stars = [];
            const count = Math.floor((canvas.width * canvas.height) / 8000);
            for (let i = 0; i < count; i++) {
                stars.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    size: Math.random() * 1.5 + 0.5,
                    speed: Math.random() * 0.3 + 0.05,
                    opacity: Math.random() * 0.8 + 0.2,
                    pulse: Math.random() * Math.PI * 2
                });
            }
        }

        function drawStars() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            const time = Date.now() * 0.001;
            stars.forEach(star => {
                const flicker = Math.sin(time * star.speed * 3 + star.pulse) * 0.3 + 0.7;
                ctx.beginPath();
                ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(255, 255, 255, ${star.opacity * flicker})`;
                ctx.fill();

                if (star.size > 1.2) {
                    ctx.beginPath();
                    ctx.arc(star.x, star.y, star.size * 3, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(255, 255, 255, ${star.opacity * flicker * 0.05})`;
                    ctx.fill();
                }
            });
            requestAnimationFrame(drawStars);
        }

        initStars();
        drawStars();
        window.addEventListener('resize', initStars);

        // Countdown to launch (Jan 1, 2036)
        function updateCountdown() {
            const launch = new Date('2036-01-01');
            const now = new Date();
            const days = Math.ceil((launch - now) / (1000 * 60 * 60 * 24));
            document.getElementById('countdown').textContent = days;
        }
        updateCountdown();

        // Fade-in on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.fade-up').forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });
    </script>

</body>
</html>
