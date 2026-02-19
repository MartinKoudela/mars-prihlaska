<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihláška na Mars</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(ellipse at top, #0f172a 0%, #020617 50%, #000000 100%);
        }
        .star {
            position: fixed;
            border-radius: 50%;
            background: white;
            animation: twinkle 3s infinite alternate;
        }
        @keyframes twinkle {
            0% { opacity: 0.2; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body class="min-h-screen text-slate-100">

    <!-- Stars background -->
    <script>
        for (let i = 0; i < 80; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.width = Math.random() * 3 + 'px';
            star.style.height = star.style.width;
            star.style.top = Math.random() * 100 + '%';
            star.style.left = Math.random() * 100 + '%';
            star.style.animationDelay = Math.random() * 3 + 's';
            document.body.appendChild(star);
        }
    </script>

    <div class="relative z-10 max-w-2xl mx-auto px-4 py-12">

        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-orange-400 mb-2">🚀 Přihláška na Mars</h1>
            <p class="text-slate-400 text-lg">Kolonizační program 2035 — Registrace nových členů posádky</p>
        </div>

        <!-- Error messages -->
        <?php if (!empty($_GET['errors'])): ?>
            <div class="bg-red-900/50 border border-red-500 rounded-xl p-4 mb-6">
                <p class="font-semibold text-red-300 mb-2">Formulář obsahuje chyby:</p>
                <ul class="list-disc list-inside text-red-200 text-sm space-y-1">
                    <?php foreach (explode('|', $_GET['errors']) as $error): ?>
                        <li><?= htmlspecialchars(urldecode($error)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="process.php" method="POST" class="space-y-6 bg-slate-900/60 backdrop-blur-sm border border-slate-700 rounded-2xl p-8">

            <!-- Jméno + Příjmení -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jmeno" class="block text-sm font-medium text-slate-300 mb-1">Jméno *</label>
                    <input type="text" id="jmeno" name="jmeno" required
                           placeholder="Např. Elon"
                           class="w-full rounded-lg bg-slate-800 border border-slate-600 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                <div>
                    <label for="prijmeni" class="block text-sm font-medium text-slate-300 mb-1">Příjmení *</label>
                    <input type="text" id="prijmeni" name="prijmeni" required
                           placeholder="Např. Musk"
                           class="w-full rounded-lg bg-slate-800 border border-slate-600 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-1">E-mail *</label>
                <input type="email" id="email" name="email" required
                       placeholder="vas@email.cz"
                       class="w-full rounded-lg bg-slate-800 border border-slate-600 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <!-- Telefon -->
            <div>
                <label for="telefon" class="block text-sm font-medium text-slate-300 mb-1">Telefon</label>
                <input type="tel" id="telefon" name="telefon"
                       placeholder="+420 123 456 789"
                       class="w-full rounded-lg bg-slate-800 border border-slate-600 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <!-- Datum narození -->
            <div>
                <label for="datum_narozeni" class="block text-sm font-medium text-slate-300 mb-1">Datum narození *</label>
                <input type="date" id="datum_narozeni" name="datum_narozeni" required
                       class="w-full rounded-lg bg-slate-800 border border-slate-600 px-4 py-2.5 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <!-- Pohlaví (radio) -->
            <div>
                <p class="text-sm font-medium text-slate-300 mb-2">Pohlaví *</p>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="pohlavi" value="muz" required
                               class="w-4 h-4 accent-orange-500">
                        <span class="text-slate-200">Muž</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="pohlavi" value="zena"
                               class="w-4 h-4 accent-orange-500">
                        <span class="text-slate-200">Žena</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="pohlavi" value="jine"
                               class="w-4 h-4 accent-orange-500">
                        <span class="text-slate-200">Jiné</span>
                    </label>
                </div>
            </div>

            <!-- Krevní skupina (select) -->
            <div>
                <label for="krevni_skupina" class="block text-sm font-medium text-slate-300 mb-1">Krevní skupina *</label>
                <select id="krevni_skupina" name="krevni_skupina" required
                        class="w-full rounded-lg bg-slate-800 border border-slate-600 px-4 py-2.5 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="" disabled selected>Vyberte krevní skupinu</option>
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

            <!-- Dovednosti (checkbox) -->
            <div>
                <p class="text-sm font-medium text-slate-300 mb-2">Dovednosti (vyberte alespoň jednu) *</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="dovednosti[]" value="programovani"
                               class="w-4 h-4 accent-orange-500 rounded">
                        <span class="text-slate-200">Programování</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="dovednosti[]" value="mechanika"
                               class="w-4 h-4 accent-orange-500 rounded">
                        <span class="text-slate-200">Mechanika</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="dovednosti[]" value="medicina"
                               class="w-4 h-4 accent-orange-500 rounded">
                        <span class="text-slate-200">Medicína</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="dovednosti[]" value="botanika"
                               class="w-4 h-4 accent-orange-500 rounded">
                        <span class="text-slate-200">Botanika</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="dovednosti[]" value="geologie"
                               class="w-4 h-4 accent-orange-500 rounded">
                        <span class="text-slate-200">Geologie</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="dovednosti[]" value="pilotovani"
                               class="w-4 h-4 accent-orange-500 rounded">
                        <span class="text-slate-200">Pilotování</span>
                    </label>
                </div>
            </div>

            <!-- Preferovaná role (select) -->
            <div>
                <label for="preferovana_role" class="block text-sm font-medium text-slate-300 mb-1">Preferovaná role na Marsu *</label>
                <select id="preferovana_role" name="preferovana_role" required
                        class="w-full rounded-lg bg-slate-800 border border-slate-600 px-4 py-2.5 text-slate-100 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="" disabled selected>Vyberte roli</option>
                    <option value="pilot">🛩️ Pilot</option>
                    <option value="inzenyr">⚙️ Inženýr</option>
                    <option value="vedec">🔬 Vědec</option>
                    <option value="medik">🩺 Medik</option>
                    <option value="botanik">🌱 Botanik</option>
                </select>
            </div>

            <!-- Fyzická kondice (range) -->
            <div>
                <label for="fyzicka_kondice" class="block text-sm font-medium text-slate-300 mb-1">
                    Fyzická kondice: <span id="kondice_hodnota" class="text-orange-400 font-bold">5</span>/10
                </label>
                <input type="range" id="fyzicka_kondice" name="fyzicka_kondice" min="1" max="10" value="5"
                       oninput="document.getElementById('kondice_hodnota').textContent = this.value"
                       class="w-full h-2 rounded-lg appearance-none cursor-pointer accent-orange-500 bg-slate-700">
            </div>

            <!-- Motivace (textarea) -->
            <div>
                <label for="motivace" class="block text-sm font-medium text-slate-300 mb-1">Proč chcete letět na Mars? *</label>
                <textarea id="motivace" name="motivace" rows="4" required
                          placeholder="Popište svou motivaci pro cestu na Mars..."
                          class="w-full rounded-lg bg-slate-800 border border-slate-600 px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"></textarea>
            </div>

            <!-- Souhlas (checkbox) -->
            <div>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="souhlas" value="1" required
                           class="w-4 h-4 mt-0.5 accent-orange-500 rounded">
                    <span class="text-sm text-slate-300">
                        Souhlasím s tím, že cesta na Mars je jednosměrná a že Mars Colonial Authority
                        neručí za mé přežití. *
                    </span>
                </label>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-6 rounded-xl transition-colors duration-200 text-lg cursor-pointer">
                🚀 Odeslat přihlášku
            </button>

        </form>

        <p class="text-center text-slate-600 text-sm mt-6">Mars Colonial Authority © 2035</p>
    </div>

</body>
</html>
