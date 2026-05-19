<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

$msg = null; $err = null; $token = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $st = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
  $st->execute([$email]);
  if ($st->fetch()) {
    $token = bin2hex(random_bytes(32));
    $expire = date('Y-m-d H:i:s', time() + 3600);
    $pdo->prepare("INSERT INTO password_resets (email, token, expire_at) VALUES (?, ?, ?)")
        ->execute([$email, $token, $expire]);
    $msg = "Un lien de réinitialisation a été généré. Cliquez ci-dessous (valide 1h).";
  } else {
    $err = "Aucun compte n'est associé à cet email.";
  }
}
?>
<!DOCTYPE html>
<html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mot de passe oublié · EduNotes</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-slate-800">Mot de passe oublié</h1>
      <a href="<?= baseUrl() ?>/pages/login.php" class="text-sm text-blue-600 hover:underline"><i class="ri-arrow-left-line"></i></a>
    </div>
    <p class="text-sm text-slate-500 mb-4">Saisissez votre email pour générer un lien de réinitialisation.</p>

    <?php if ($err): ?><div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm flex items-center gap-2"><i class="ri-error-warning-line"></i><?= e($err) ?></div><?php endif; ?>
    <?php if ($msg): ?>
      <div class="mb-4 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">
        <div class="flex items-center gap-2"><i class="ri-checkbox-circle-line"></i><?= e($msg) ?></div>
        <a href="<?= baseUrl() ?>/pages/auth/reset.php?token=<?= e($token) ?>" class="mt-3 inline-block w-full text-center py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold">Réinitialiser mon mot de passe →</a>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="text-sm font-medium text-slate-700">Email</label>
        <div class="mt-1 relative">
          <i class="ri-mail-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          <input required type="email" name="email" class="w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
      </div>
      <button class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-lg hover:opacity-95 transition">Envoyer le lien <i class="ri-send-plane-line ml-1"></i></button>
    </form>
    <p class="mt-6 text-center text-sm text-slate-500">Pas de compte ? <a href="<?= baseUrl() ?>/pages/auth/register.php" class="text-blue-600 hover:underline font-semibold">Créer un compte</a></p>
  </div>
</body></html>
