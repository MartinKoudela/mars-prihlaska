<?php
require_once 'db_connect.php';

$result = $conn->query("SELECT * FROM applications ORDER BY created_at DESC");
$applications = $result->fetch_all(MYSQLI_ASSOC);
$total = count($applications);

$role_labels = [
    'pilot' => 'Pilot', 'engineer' => 'Engineer', 'scientist' => 'Scientist',
    'medic' => 'Medic', 'botanist' => 'Botanist',
];

$edu_labels = [
    'elementary' => 'Elementary', 'high_school' => 'High School', 'associate' => 'Associate',
    'bachelors' => "Bachelor's", 'masters' => "Master's", 'doctorate' => 'Ph.D.',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — Mars Colonial Authority</title>
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
        body { background: #000; }

        .section-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.65rem; letter-spacing: 0.15em; text-transform: uppercase;
        }

        .fade-up {
            opacity: 0; transform: translateY(20px);
            animation: fadeUp 0.6s ease forwards;
        }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen text-slate-100 antialiased">

    <div class="max-w-6xl mx-auto px-4 py-8 sm:py-12">

        <!-- Header -->
        <div class="fade-up flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <p class="section-label text-mars-500/70 mb-1">Mars Colonial Authority</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Admin Panel</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="px-4 py-2 rounded-lg bg-white/[0.03] border border-slate-800">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Total Applications</p>
                    <p class="text-xl font-bold font-mono text-mars-400"><?= $total ?></p>
                </div>
                <a href="index.php"
                   class="px-4 py-2 rounded-lg bg-white/[0.03] border border-slate-800 text-slate-400 hover:text-white hover:border-slate-700 transition-all text-sm">
                    &larr; Back to Form
                </a>
            </div>
        </div>

        <?php if ($total === 0): ?>
            <div class="fade-up text-center py-20" style="animation-delay: 0.1s">
                <p class="text-slate-500 text-lg">No applications yet.</p>
                <p class="text-slate-600 text-sm mt-2">Applications will appear here once submitted.</p>
            </div>
        <?php else: ?>

            <!-- Table -->
            <div class="fade-up overflow-x-auto" style="animation-delay: 0.1s">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 text-left">
                            <th class="py-3 px-3 text-xs text-slate-500 uppercase tracking-wider font-mono">ID</th>
                            <th class="py-3 px-3 text-xs text-slate-500 uppercase tracking-wider font-mono">Name</th>
                            <th class="py-3 px-3 text-xs text-slate-500 uppercase tracking-wider font-mono">E-mail</th>
                            <th class="py-3 px-3 text-xs text-slate-500 uppercase tracking-wider font-mono">Role</th>
                            <th class="py-3 px-3 text-xs text-slate-500 uppercase tracking-wider font-mono">Education</th>
                            <th class="py-3 px-3 text-xs text-slate-500 uppercase tracking-wider font-mono">Fitness</th>
                            <th class="py-3 px-3 text-xs text-slate-500 uppercase tracking-wider font-mono">Submitted</th>
                            <th class="py-3 px-3 text-xs text-slate-500 uppercase tracking-wider font-mono"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $i => $app): ?>
                            <tr class="border-b border-slate-800/50 hover:bg-white/[0.02] transition-colors">
                                <td class="py-3 px-3 font-mono text-mars-400">
                                    MCA-<?= str_pad($app['id'], 4, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td class="py-3 px-3 text-slate-200">
                                    <?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?>
                                </td>
                                <td class="py-3 px-3 text-slate-400">
                                    <?= htmlspecialchars($app['email']) ?>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded-md bg-mars-500/10 text-mars-400 text-xs font-medium">
                                        <?= $role_labels[$app['preferred_role']] ?? $app['preferred_role'] ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-slate-300">
                                    <?= $edu_labels[$app['education']] ?? $app['education'] ?>
                                </td>
                                <td class="py-3 px-3 font-mono text-slate-300">
                                    <?= $app['physical_fitness'] ?>/10
                                </td>
                                <td class="py-3 px-3 text-slate-500 font-mono text-xs">
                                    <?= date('M d, H:i', strtotime($app['created_at'])) ?>
                                </td>
                                <td class="py-3 px-3">
                                    <a href="success.php?id=<?= $app['id'] ?>"
                                       class="text-mars-500 hover:text-mars-400 text-xs font-medium transition-colors">
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

        <p class="text-center text-slate-700 text-xs font-mono tracking-wide mt-12">
            MARS COLONIAL AUTHORITY — ADMIN PANEL v1.0 — RESTRICTED ACCESS
        </p>
    </div>

</body>
</html>
