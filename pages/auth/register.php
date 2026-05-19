<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (isLoggedIn()) {
  header('Location: ' . baseUrl() . '/index.php');
  exit;
}

$error = null;
$success = null;
$classes = $pdo->query("SELECT * FROM classes ORDER BY nom")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom = trim($_POST['nom'] ?? '');
  $prenom = trim($_POST['prenom'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $matricule = trim($_POST['matricule'] ?? '');
  $sexe = $_POST['sexe'] ?? 'M';
  $date_naissance = $_POST['date_naissance'] ?? null;
  $lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
  $classe_id = (int)($_POST['classe_id'] ?? 0);
  $password = $_POST['password'] ?? '';
  $confirm = $_POST['confirm'] ?? '';

  if (!$nom || !$prenom || !$email || !$matricule || !$password) {
    $error = "Tous les champs marqués sont obligatoires.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Adresse email invalide.";
  } elseif (strlen($password) < 6) {
    $error = "Le mot de passe doit contenir au moins 6 caractères.";
  } elseif ($password !== $confirm) {
    $error = "Les deux mots de passe ne correspondent pas.";
  } else {
    // Unicité email + matricule
    $st = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $st->execute([$email]);
    if ($st->fetch()) {
      $error = "Un compte existe déjà avec cet email.";
    } else {
      $st = $pdo->prepare("SELECT id FROM etudiants WHERE matricule = ?");
      $st->execute([$matricule]);
      if ($st->fetch()) {
        $error = "Ce matricule est déjà utilisé.";
      } else {
        try {
          $pdo->beginTransaction();
          $hash = password_hash($password, PASSWORD_BCRYPT);
          $st = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, actif) VALUES (?, ?, ?, ?, 'etudiant', 1)");
          $st->execute([$nom, $prenom, $email, $hash]);
          $uid = (int)$pdo->lastInsertId();

          $st = $pdo->prepare("INSERT INTO etudiants (utilisateur_id, matricule, nom, prenom, date_naissance, lieu_naissance, sexe, classe_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
          $st->execute([$uid, $matricule, $nom, $prenom, $date_naissance ?: null, $lieu_naissance ?: null, $sexe, $classe_id ?: null]);
          $pdo->commit();
          $success = "Compte créé avec succès. Vous pouvez vous connecter.";
        } catch (Exception $ex) {
          $pdo->rollBack();
          $error = "Erreur lors de la création : " . $ex->getMessage();
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Créer un compte · EduNotes</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>body{font-family:'Inter',sans-serif}</style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center p-4">
  <div class="w-full max-w-3xl bg-white rounded-3xl shadow-2xl overflow-hidden">
    <div class="p-8 sm:p-10">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-2xl font-bold text-slate-800">Créer un compte étudiant</h1>
          <p class="text-sm text-slate-500 mt-1">Inscrivez-vous pour consulter vos notes</p>
        </div>
        <a href="<?= baseUrl() ?>/pages/login.php" class="text-sm text-blue-600 hover:underline flex items-center gap-1"><i class="ri-arrow-left-line"></i> Connexion</a>
      </div>

      <?php if ($error): ?>
        <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm flex items-center gap-2"><i class="ri-error-warning-line"></i><?= e($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="mb-4 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm flex items-center gap-2">
          <i class="ri-checkbox-circle-line"></i><?= e($success) ?>
          <a href="<?= baseUrl() ?>/pages/login.php" class="ml-auto font-semibold underline">Se connecter →</a>
        </div>
      <?php endif; ?>

      <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium text-slate-700">Nom *</label>
          <input required name="nom" value="<?= e($_POST['nom'] ?? '') ?>" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="text-sm font-medium text-slate-700">Prénom *</label>
          <input required name="prenom" value="<?= e($_POST['prenom'] ?? '') ?>" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="md:col-span-2">
          <label class="text-sm font-medium text-slate-700">Email *</label>
          <input required type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="text-sm font-medium text-slate-700">Matricule *</label>
          <input required name="matricule" value="<?= e($_POST['matricule'] ?? '') ?>" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="text-sm font-medium text-slate-700">Sexe</label>
          <select name="sexe" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="M" <?= ($_POST['sexe'] ?? '') === 'M' ? 'selected' : '' ?>>Masculin</option>
            <option value="F" <?= ($_POST['sexe'] ?? '') === 'F' ? 'selected' : '' ?>>Féminin</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-medium text-slate-700">Date de naissance</label>
          <input type="date" name="date_naissance" value="<?= e($_POST['date_naissance'] ?? '') ?>" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="text-sm font-medium text-slate-700">Lieu de naissance</label>
          <input name="lieu_naissance" value="<?= e($_POST['lieu_naissance'] ?? '') ?>" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="md:col-span-2">
          <label class="text-sm font-medium text-slate-700">Classe</label>
          <select name="classe_id" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">— Sélectionner —</option>
            <?php foreach ($classes as $c): ?>
              <option value="<?= $c['id'] ?>" <?= ($_POST['classe_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                <?= e($c['nom']) ?> (<?= e($c['niveau']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="text-sm font-medium text-slate-700">Mot de passe *</label>
          <input required type="password" name="password" minlength="6" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="text-sm font-medium text-slate-700">Confirmer *</label>
          <input required type="password" name="confirm" minlength="6" class="mt-1 w-full px-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="md:col-span-2">
          <button class="w-full py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-lg hover:opacity-95 transition shadow-lg shadow-blue-500/30">
            Créer mon compte <i class="ri-user-add-line ml-1"></i>
          </button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>
