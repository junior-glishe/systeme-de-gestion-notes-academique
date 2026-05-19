<?php
require_once __DIR__ . '/../../config/database.php';
$pageTitle = "Gestion des utilisateurs";
$activeMenu = 'utilisateurs';
require_once __DIR__ . '/../../includes/layout_header.php';
requireRole('admin');

date_default_timezone_set('Africa/Porto-Novo');

$msg = null;
$err = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  checkCsrf();

  $action = $_POST['action'] ?? '';

  try {
    if ($action === 'create') {
      $h = password_hash($_POST['password'], PASSWORD_BCRYPT);

      $stmt = $pdo->prepare("
                INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role)
                VALUES (?, ?, ?, ?, ?)
            ");
      $stmt->execute([
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['email'],
        $h,
        $_POST['role']
      ]);

      $uid = $pdo->lastInsertId();

      if ($_POST['role'] === 'etudiant') {
        $pdo->prepare("
                    INSERT INTO etudiants 
                    (utilisateur_id, matricule, sexe, date_naissance, lieu_naissance, classe_id)
                    VALUES (?, ?, ?, ?, ?, ?)
                ")->execute([
          $uid,
          $_POST['matricule'],
          $_POST['sexe'],
          $_POST['date_naissance'],
          $_POST['lieu_naissance'],
          $_POST['classe_id'] ?: null
        ]);
      } elseif ($_POST['role'] === 'enseignant') {
        $pdo->prepare("
                    INSERT INTO enseignants (utilisateur_id, specialite)
                    VALUES (?, ?)
                ")->execute([
          $uid,
          $_POST['specialite'] ?? ''
        ]);
      }

      $msg = "Utilisateur créé avec succès.";
    } elseif ($action === 'delete') {
      if ((int)$_POST['id'] === (int)$user['id']) {
        $err = "Impossible de supprimer votre propre compte.";
      } else {
        $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")
          ->execute([(int)$_POST['id']]);
        $msg = "Utilisateur supprimé.";
      }
    } elseif ($action === 'toggle') {
      $pdo->prepare("UPDATE utilisateurs SET actif = 1 - actif WHERE id = ?")
        ->execute([(int)$_POST['id']]);
      $msg = "Statut mis à jour.";
    }
  } catch (Exception $e) {
    $err = "Erreur : " . $e->getMessage();
  }
}
$users = $pdo->query("SELECT * FROM utilisateurs ORDER BY cree_le DESC")->fetchAll();
$classes = $pdo->query("SELECT * FROM classes")->fetchAll();
$csrf = csrfToken();
?>

<!-- Welcome Banner -->
<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
        <i class="fas fa-users text-2xl text-blue-600"></i>
      </div>
      <div>
        <h2 class="text-lg font-bold text-slate-800">Gestion des utilisateurs</h2>
        <p class="text-sm text-slate-500"><?= count($users) ?> utilisateur(s) dans le système</p>
      </div>
    </div>
  </div>
</div>

<?php if ($msg): ?>
  <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-xl text-sm border border-green-200">
    <i class="fas fa-check-circle mr-2"></i><?= e($msg) ?>
  </div>
<?php endif; ?>
<?php if ($err): ?>
  <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-xl text-sm border border-red-200">
    <i class="fas fa-exclamation-triangle mr-2"></i><?= e($err) ?>
  </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <h3 class="font-semibold mb-4 text-slate-800 flex items-center gap-2">
      <i class="fas fa-user-plus text-blue-600"></i> Nouvel utilisateur
    </h3>
    <form method="POST" class="space-y-4" id="userForm">
      <input type="hidden" name="csrf" value="<?= $csrf ?>">
      <input type="hidden" name="action" value="create">
      <div class="grid grid-cols-2 gap-3">
        <input name="nom" required placeholder="Nom" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input name="prenom" required placeholder="Prénom" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <input type="email" name="email" required placeholder="Email" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input type="password" name="password" required minlength="6" placeholder="Mot de passe (6+ caractères)" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <select name="role" id="role" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="etudiant">Étudiant</option>
        <option value="enseignant">Enseignant</option>
        <option value="admin">Administrateur</option>
      </select>

      <div id="etuFields" class="space-y-3 p-4 bg-slate-50 rounded-xl">
        <input name="matricule" placeholder="Matricule" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="sexe" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="M">Masculin</option>
          <option value="F">Féminin</option>
        </select>
        <input type="date" name="date_naissance" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input name="lieu_naissance" placeholder="Lieu de naissance" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="classe_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">— Choisir une classe —</option>
          <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= e($c['nom']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div id="ensFields" class="hidden p-4 bg-slate-50 rounded-xl">
        <input name="specialite" placeholder="Spécialité" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
        Créer l'utilisateur
      </button>
    </form>
  </div>

  <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-800">Liste des utilisateurs</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="px-5 py-3 text-left">Nom</th>
            <th class="px-5 py-3 text-left">Email</th>
            <th class="px-5 py-3 text-left">Rôle</th>
            <th class="px-5 py-3 text-center">Statut</th>
            <th class="px-5 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($users as $u): ?>
            <tr class="hover:bg-slate-50 transition">
              <td class="px-5 py-3 font-medium text-slate-800"><?= e($u['nom'] . ' ' . $u['prenom']) ?></td>
              <td class="px-5 py-3 text-slate-600"><?= e($u['email']) ?></td>
              <td class="px-5 py-3">
                <span class="px-2 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700"><?= e($u['role']) ?></span>
              </td>
              <td class="px-5 py-3 text-center">
                <span class="px-2 py-1 rounded-lg text-xs font-medium <?= $u['actif'] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' ?>">
                  <?= $u['actif'] ? 'Actif' : 'Inactif' ?>
                </span>
              </td>
              <td class="px-5 py-3 text-center space-x-2">
                <form method="POST" class="inline">
                  <input type="hidden" name="csrf" value="<?= $csrf ?>">
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <button class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs transition" title="Changer statut">
                    <i class="fas fa-power-off"></i>
                  </button>
                </form>
                <form method="POST" class="inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                  <input type="hidden" name="csrf" value="<?= $csrf ?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <button class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs transition" title="Supprimer">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  const role = document.getElementById('role');
  const etu = document.getElementById('etuFields');
  const ens = document.getElementById('ensFields');

  role.addEventListener('change', () => {
    if (role.value === 'etudiant') {
      etu.classList.remove('hidden');
      ens.classList.add('hidden');
    } else if (role.value === 'enseignant') {
      etu.classList.add('hidden');
      ens.classList.remove('hidden');
    } else {
      etu.classList.add('hidden');
      ens.classList.add('hidden');
    }
  });
</script>

<?php require_once __DIR__ . '/../../includes/layout_footer.php'; ?>