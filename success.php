<?php
require_once 'db_connect.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM applications WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$app = $result->fetch_assoc();

if (!$app) {
    header("Location: index.php");
    exit;
}

$role_labels = [
    'pilot' => 'Pilot',
    'engineer' => 'Engineer',
    'scientist' => 'Scientist',
    'medic' => 'Medic',
    'botanist' => 'Botanist',
];

$edu_labels = [
    'elementary' => 'Elementary',
    'high_school' => 'High School',
    'associate' => 'Associate Degree',
    'bachelors' => "Bachelor's Degree",
    'masters' => "Master's Degree",
    'doctorate' => 'Doctorate (Ph.D.)',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Confirmed — Mars Colonial Authority</title>
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
                            400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Space Grotesk', sans-serif; }
        body { background: #000; overflow-x: hidden; }

        #starfield { position: fixed; inset: 0; z-index: 0; }
        #starfield canvas { width: 100%; height: 100%; }

        .mars-glow {
            position: fixed; bottom: -30%; left: 50%; transform: translateX(-50%);
            width: 140%; height: 60%;
            background: radial-gradient(ellipse at center bottom, rgba(234, 88, 12, 0.15) 0%, rgba(234, 88, 12, 0.05) 30%, transparent 70%);
            pointer-events: none; z-index: 1;
        }

        .fade-up {
            opacity: 0; transform: translateY(20px);
            animation: fadeUp 0.6s ease forwards;
        }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }

        .checkmark-circle {
            animation: scaleIn 0.5s ease forwards;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }

        .section-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.65rem; letter-spacing: 0.15em; text-transform: uppercase;
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 antialiased">

    <div id="starfield"><canvas id="stars"></canvas></div>
    <div class="mars-glow"></div>

    <div class="relative z-10 max-w-2xl mx-auto px-4 py-8 sm:py-16">

        <!-- Success header -->
        <div class="fade-up text-center mb-10" style="animation-delay: 0s">
            <div class="checkmark-circle inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-500/10 border border-green-500/30 mb-6">
                <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <p class="section-label text-green-500/70 mb-3">Application Received</p>
            <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight mb-3">
                Welcome aboard, <?= htmlspecialchars($app['first_name']) ?>
            </h1>
            <p class="text-slate-400 text-base max-w-md mx-auto leading-relaxed">
                Your application has been successfully submitted. Your crew ID is shown below.
            </p>

            <!-- Crew ID badge -->
            <div class="inline-block mt-6 px-6 py-3 rounded-xl bg-white/[0.03] border border-slate-800">
                <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Crew ID</p>
                <p class="text-2xl font-bold font-mono text-mars-400">MCA-2035-<?= str_pad($app['id'], 4, '0', STR_PAD_LEFT) ?></p>
            </div>
        </div>

        <!-- Application summary -->
        <div class="fade-up bg-white/[0.02] border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-6 backdrop-blur-sm" style="animation-delay: 0.2s">
            <h2 class="text-lg font-semibold text-white">Application Summary</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Full Name</p>
                    <p class="text-slate-200"><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">E-mail</p>
                    <p class="text-slate-200"><?= htmlspecialchars($app['email']) ?></p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Date of Birth</p>
                    <p class="text-slate-200"><?= date('M d, Y', strtotime($app['date_of_birth'])) ?></p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Gender</p>
                    <p class="text-slate-200"><?= ucfirst($app['gender']) ?></p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Blood Type</p>
                    <p class="text-slate-200"><?= htmlspecialchars($app['blood_type']) ?></p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Physical Fitness</p>
                    <p class="text-slate-200"><?= $app['physical_fitness'] ?>/10</p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Education</p>
                    <p class="text-slate-200"><?= $edu_labels[$app['education']] ?? $app['education'] ?></p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Field of Study</p>
                    <p class="text-slate-200"><?= htmlspecialchars($app['field_of_study']) ?></p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Experience</p>
                    <p class="text-slate-200"><?= $app['years_of_experience'] ?> years</p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Preferred Role</p>
                    <p class="text-slate-200"><?= $role_labels[$app['preferred_role']] ?? $app['preferred_role'] ?></p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Skills</p>
                    <p class="text-slate-200"><?= htmlspecialchars($app['skills']) ?></p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-slate-500 text-xs uppercase tracking-wider mb-1">Motivation</p>
                    <p class="text-slate-200 leading-relaxed"><?= nl2br(htmlspecialchars($app['motivation'])) ?></p>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-800 text-xs text-slate-600 font-mono">
                Submitted: <?= date('Y-m-d H:i:s', strtotime($app['created_at'])) ?> UTC
            </div>
        </div>

        <!-- Back button -->
        <div class="fade-up mt-8 text-center" style="animation-delay: 0.3s">
            <a href="index.php"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white/[0.03] border border-slate-800 text-slate-400 hover:text-white hover:border-slate-700 transition-all text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Back to Registration
            </a>
        </div>

        <p class="text-center text-slate-700 text-xs font-mono tracking-wide mt-8">
            MARS COLONIAL AUTHORITY — APPLICATION CONFIRMED — CLASSIFIED
        </p>
    </div>

    <script>
        const canvas = document.getElementById('stars');
        const ctx = canvas.getContext('2d');
        let stars = [];
        function initStars() {
            canvas.width = window.innerWidth; canvas.height = window.innerHeight;
            stars = [];
            const count = Math.floor((canvas.width * canvas.height) / 8000);
            for (let i = 0; i < count; i++) {
                stars.push({ x: Math.random()*canvas.width, y: Math.random()*canvas.height,
                    size: Math.random()*1.5+0.5, speed: Math.random()*0.3+0.05,
                    opacity: Math.random()*0.8+0.2, pulse: Math.random()*Math.PI*2 });
            }
        }
        function drawStars() {
            ctx.clearRect(0,0,canvas.width,canvas.height);
            const time = Date.now()*0.001;
            stars.forEach(s => {
                const f = Math.sin(time*s.speed*3+s.pulse)*0.3+0.7;
                ctx.beginPath(); ctx.arc(s.x,s.y,s.size,0,Math.PI*2);
                ctx.fillStyle = `rgba(255,255,255,${s.opacity*f})`; ctx.fill();
            });
            requestAnimationFrame(drawStars);
        }
        initStars(); drawStars();
        window.addEventListener('resize', initStars);
    </script>
</body>
</html>
