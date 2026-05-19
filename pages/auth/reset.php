<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

$msg = null; $err = null;
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$validToken = false;
$emailToken = null;

if ($token) {
  $st = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expire_at > NOW()");
  $st->execute([$token]);
  $row = $st->fetch();
  if ($row) { $validToken = true; $emailToken = $row['email']; }
  else { $err = "Lien invalide ou expiré."; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
  $new = $_POST['password'] ?? '';
  $confirm = $_POST['confirm'] ?? '';
  if (strlen($new) < 6) {
    $err = "Le mot de passe doit faire au moins 6 caractères.";
  } elseif ($new !== $confirm) {
    $err = "Les deux mots de passe ne correspondent pas.";
  } else {
    $h = password_hash($new, PASSWORD_BCRYPT);
    $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE email = ?")->execute([$h, $emailToken]);
    $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?")->execute([$token]);
    $msg = "Mot de passe réinitialisé avec succès.";
    $validToken = false;
  }
}
?>
<!DOCTYPE html>
<html lang="fr"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Réinitialiser le mot de passe</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Nouveau mot de passe</h1>
    <p class="text-sm text-slate-500 mb-6"><?= $emailToken ? 'Compte : ' . e($emailToken) : 'Token requis.' ?></p>

    <?php if ($err): ?><div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm flex items-center gap-2"><i class="ri-error-warning-line"></i><?= e($err) ?></div><?php endif; ?>
    <?php if ($msg): ?>
      <div class="mb-4 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm flex items-center gap-2"><i class="ri-checkbox-circle-line"></i><?= e($msg) ?></div>
      <a href="<?= baseUrl() ?>/pages/login.php" class="block w-full text-center py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-lg font-semibold">Se connecter →</a>
    <?php endif; ?>

    <?php if ($validToken): ?>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <div>
        <label class="text-sm font-medium text-slate-700">Nouveau mot de passe</label>
        <input required type="password" name="password" minlength="6" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
      </div>
      <div>
        <label class="text-sm font-medium text-slate-700">Confirmer</label>
        <input required type="password" name="confirm" minlength="6" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
      </div>
      <button class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-lg hover:opacity-95 transition">Mettre à jour <i class="ri-shield-check-line ml-1"></i></button>
    </form>
    <?php elseif (!$msg): ?>
      <a href="<?= baseUrl() ?>/pages/auth/forgot.php" class="block w-full text-center py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">Demander un nouveau lien</a>
    <?php endif; ?>
    <p class="mt-6 text-center text-sm text-slate-500"><a href="<?= baseUrl() ?>/pages/login.php" class="text-blue-600 hover:underline">← Retour à la connexion</a></p>
  </div>
</body></html>
