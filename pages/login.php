<?php
require_once __DIR__ . '/../includes/auth.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  if (login($email, $pass)) {
    $r = $_SESSION['user']['role'];
    $map = ['admin' => '/pages/admin/dashboard.php', 'enseignant' => '/pages/enseignant/dashboard.php', 'etudiant' => '/pages/etudiant/dashboard.php', 'responsable' => '/pages/admin/dashboard.php'];
    header('Location: ' . baseUrl() . ($map[$r] ?? '/'));
    exit;
  }
  $error = 'Identifiants invalides';
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion · EduNotes</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif
    }
  </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center p-4">
  <div class="w-full max-w-5xl grid md:grid-cols-2 bg-white rounded-3xl shadow-2xl overflow-hidden">
    <!-- Left: branding -->
    <div class="hidden md:flex flex-col justify-between p-10 bg-gradient-to-br from-blue-700 to-indigo-900 text-white">
      <div>
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center"><i class="ri-graduation-cap-fill text-2xl"></i></div>
          <div>
            <div class="font-bold text-xl">Institut-Lumiere</div>
            <div class="text-xs text-blue-200">Institut Académique du Bénin</div>
          </div>
        </div>
        <h2 class="mt-12 text-3xl font-bold leading-tight">Gestion intelligente des notes académiques</h2>
        <p class="mt-3 text-blue-100 text-sm">Saisie, calcul automatique, bulletins, relevés collectifs et statistiques pour Administrateurs, Enseignants et Étudiants.</p>
      </div>
      <div class="space-y-2 text-sm text-blue-100">
        <div class="flex items-center gap-2"><i class="ri-shield-check-line"></i> Authentification sécurisée</div>
        <div class="flex items-center gap-2"><i class="ri-calculator-line"></i> Moyenne = 30% Interro + 70% Devoir</div>
        <div class="flex items-center gap-2"><i class="ri-printer-line"></i> Bulletins & relevés imprimables</div>
      </div>
    </div>
    <!-- Right: form -->
    <div class="p-8 sm:p-12 flex flex-col justify-center">
      <h1 class="text-2xl font-bold text-slate-800">Connexion</h1>
      <p class="text-sm text-slate-500 mt-1">Accédez à votre espace personnel</p>
      <?php if ($error): ?>
        <div class="mt-4 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm flex items-center gap-2"><i class="ri-error-warning-line"></i><?= e($error) ?></div>
      <?php endif; ?>
      <form method="POST" class="mt-6 space-y-4">
        <div>
          <label class="text-sm font-medium text-slate-700">Email</label>
          <div class="mt-1 relative">
            <i class="ri-mail-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input required type="email" name="email" placeholder="exemple@institut.bj"
              class="w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
          </div>
        </div>
        <div>
          <label class="text-sm font-medium text-slate-700">Mot de passe</label>
          <div class="mt-1 relative">
            <i class="ri-lock-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input required type="password" name="password" placeholder="••••••••"
              class="w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
          </div>
        </div>
        <button class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-lg hover:opacity-95 transition shadow-lg shadow-blue-500/30">
          Se connecter <i class="ri-arrow-right-line ml-1"></i>
        </button>
      </form>
      <div class="mt-4 flex justify-between text-sm">
        <a href="<?= baseUrl() ?>/pages/auth/forgot.php" class="text-blue-600 hover:underline">Mot de passe oublié ?</a>
        <a href="<?= baseUrl() ?>/pages/auth/register.php" class="text-blue-600 hover:underline font-semibold">Créer un compte</a>
      </div>
    </div>
  </div>
</body>

</html>
