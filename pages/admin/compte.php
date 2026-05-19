<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole(['admin', 'responsable']);
$pageTitle = 'Gestion des utilisateurs';

date_default_timezone_set('Africa/Porto-Novo');

// Traitement des actions CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  checkCsrf();
  $action = $_POST['action'] ?? '';

  try {
    if ($action === 'create') {
      $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);

      $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
      $stmt->execute([$_POST['nom'], $_POST['prenom'], $_POST['email'], $passwordHash, $_POST['role']]);
      $userId = $pdo->lastInsertId();

      if ($_POST['role'] === 'etudiant') {
        $stmt = $pdo->prepare("INSERT INTO etudiants (utilisateur_id, matricule, date_naissance, lieu_naissance, sexe, classe_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $_POST['matricule'], $_POST['date_naissance'], $_POST['lieu_naissance'], $_POST['sexe'], $_POST['classe_id'] ?: null]);
      } elseif ($_POST['role'] === 'enseignant') {
        $stmt = $pdo->prepare("INSERT INTO enseignants (utilisateur_id, matricule, nom, prenom, specialite, telephone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $_POST['matricule'], $_POST['nom'], $_POST['prenom'], $_POST['specialite'], $_POST['telephone']]);
      }

      $_SESSION['flash'] = ['type' => 'success', 'message' => 'Utilisateur créé avec succès.'];
    } elseif ($action === 'update') {
      $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, role = ? WHERE id = ?");
      $stmt->execute([$_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['role'], $_POST['user_id']]);

      if (!empty($_POST['password'])) {
        $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
        $stmt->execute([$passwordHash, $_POST['user_id']]);
      }

      if ($_POST['role'] === 'etudiant') {
        $stmt = $pdo->prepare("UPDATE etudiants SET matricule = ?, date_naissance = ?, lieu_naissance = ?, sexe = ?, classe_id = ? WHERE utilisateur_id = ?");
        $stmt->execute([$_POST['matricule'], $_POST['date_naissance'], $_POST['lieu_naissance'], $_POST['sexe'], $_POST['classe_id'] ?: null, $_POST['user_id']]);
      } elseif ($_POST['role'] === 'enseignant') {
        $stmt = $pdo->prepare("UPDATE enseignants SET matricule = ?, specialite = ?, telephone = ? WHERE utilisateur_id = ?");
        $stmt->execute([$_POST['matricule'], $_POST['specialite'], $_POST['telephone'], $_POST['user_id']]);
      }

      $_SESSION['flash'] = ['type' => 'success', 'message' => 'Utilisateur mis à jour avec succès.'];
    } elseif ($action === 'delete') {
      $pdo->prepare("DELETE FROM etudiants WHERE utilisateur_id = ?")->execute([$_POST['user_id']]);
      $pdo->prepare("DELETE FROM enseignants WHERE utilisateur_id = ?")->execute([$_POST['user_id']]);
      $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$_POST['user_id']]);
      $_SESSION['flash'] = ['type' => 'success', 'message' => 'Utilisateur supprimé avec succès.'];
    } elseif ($action === 'reset_password') {
      $newPassword = trim($_POST['new_password'] ?? '');
      if ($newPassword === '' || strlen($newPassword) < 6) {
        throw new Exception('Le nouveau mot de passe doit contenir au moins 6 caractères.');
      }
      $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
      $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
      $stmt->execute([$passwordHash, $_POST['user_id']]);
      $_SESSION['flash'] = ['type' => 'success', 'message' => 'Mot de passe réinitialisé avec succès.'];
    }

    header('Location: utilisateurs.php');
    exit;
  } catch (Exception $e) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Erreur : ' . $e->getMessage()];
    header('Location: utilisateurs.php');
    exit;
  }
}

// Récupération de tous les utilisateurs avec leurs informations
$users = $pdo->query("
    SELECT 
        u.id, u.nom, u.prenom, u.email, u.role,
        e.matricule, e.date_naissance, e.lieu_naissance, e.sexe, c.nom AS classe,
        en.specialite, en.telephone
    FROM utilisateurs u
    LEFT JOIN etudiants e ON e.utilisateur_id = u.id
    LEFT JOIN classes c ON e.classe_id = c.id
    LEFT JOIN enseignants en ON en.utilisateur_id = u.id
    ORDER BY u.id DESC
")->fetchAll();

$classes = $pdo->query("SELECT * FROM classes ORDER BY nom")->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="mb-4 px-4 py-3 rounded-xl text-sm flex items-center gap-2 border <?= $_SESSION['flash']['type'] === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' ?>">
    <i class="<?= $_SESSION['flash']['type'] === 'success' ? 'ri-checkbox-circle-line' : 'ri-error-warning-line' ?> mr-1"></i>
    <?= e($_SESSION['flash']['message']) ?>
  </div>
  <?php $_SESSION['flash'] = null; ?>
<?php endif; ?>

<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
          <i class="ri-user-settings-line text-2xl text-blue-600"></i>
        </div>
        <div>
          <h2 class="text-lg font-bold text-slate-800">Gestion des utilisateurs</h2>
          <p class="text-sm text-slate-500"><?= count($users) ?> utilisateur(s) dans le système</p>
        </div>
      </div>
      <button onclick="document.getElementById('modalCreate').classList.remove('hidden')"
        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm flex items-center gap-2 transition shadow-sm">
        <i class="ri-add-line"></i> Nouvel utilisateur
      </button>
    </div>
  </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
        <tr>
          <th class="px-4 py-3 text-left">Matricule</th>
          <th class="px-4 py-3 text-left">Nom & Prénom</th>
          <th class="px-4 py-3 text-left">Email</th>
          <th class="px-4 py-3 text-left">Naissance</th>
          <th class="px-4 py-3 text-center">Sexe</th>
          <th class="px-4 py-3 text-left">Classe / Spécialité</th>
          <th class="px-4 py-3 text-center">Rôle</th>
          <th class="px-4 py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($users as $user): ?>
          <tr class="hover:bg-slate-50 transition">
            <td class="px-4 py-3 font-mono text-xs text-slate-600">
              <?= e($user['matricule'] ?? '—') ?>
            </td>
            <td class="px-4 py-3">
              <div class="font-semibold text-slate-800"><?= e($user['nom'] . ' ' . $user['prenom']) ?></div>
            </td>
            <td class="px-4 py-3 text-slate-600 text-xs">
              <?= e($user['email']) ?>
            </td>
            <td class="px-4 py-3 text-slate-600 text-xs">
              <?php if ($user['date_naissance']): ?>
                <?= date('d/m/Y', strtotime($user['date_naissance'])) ?><br>
                <span class="text-slate-400">à <?= e($user['lieu_naissance']) ?></span>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
            <td class="px-4 py-3 text-center">
              <?php if ($user['sexe']): ?>
                <span class="px-2 py-0.5 rounded-lg text-xs font-medium <?= $user['sexe'] === 'F' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' ?>">
                  <?= $user['sexe'] === 'F' ? '♀ F' : '♂ M' ?>
                </span>
              <?php else: ?>
                <span class="text-slate-400">—</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3 text-slate-600 text-xs">
              <?= e($user['classe'] ?? $user['specialite'] ?? '—') ?>
            </td>
            <td class="px-4 py-3 text-center">
              <span class="px-2 py-1 rounded-lg text-xs font-medium 
                                <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : ($user['role'] === 'enseignant' ? 'bg-indigo-100 text-indigo-700' : ($user['role'] === 'etudiant' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700')) ?>">
                <?= e($user['role']) ?>
              </span>
            </td>
            <td class="px-4 py-3 text-center space-x-1">
              <button onclick='editUser(<?= json_encode($user) ?>)'
                class="px-2 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg text-xs transition"
                title="Modifier">
                <i class="ri-edit-line"></i>
              </button>
              <button onclick='resetPassword(<?= $user['id'] ?>, "<?= e($user['nom'] . ' ' . $user['prenom']) ?>")'
                class="px-2 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs transition"
                title="Réinitialiser mot de passe">
                <i class="ri-key-2-line"></i>
              </button>
              <form method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement cet utilisateur ?')">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <button class="px-2 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs transition" title="Supprimer">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if (empty($users)): ?>
          <tr>
            <td colspan="8" class="text-center py-12 text-slate-400">
              <i class="ri-user-line text-4xl mb-2 block"></i>
              Aucun utilisateur enregistré
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div id="modalCreate" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-xl">
    <div class="sticky top-0 bg-white p-5 border-b flex justify-between items-center">
      <h3 class="font-semibold text-lg">Nouvel utilisateur</h3>
      <button onclick="document.getElementById('modalCreate').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
        <i class="ri-close-line text-xl"></i>
      </button>
    </div>
    <form method="POST" class="p-5 space-y-4">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="action" value="create">

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nom *</label>
          <input required name="nom" placeholder="Nom" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Prénom *</label>
          <input required name="prenom" placeholder="Prénom" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
          <input type="email" required name="email" placeholder="email@exemple.com" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Mot de passe *</label>
          <input type="password" required name="password" minlength="6" placeholder="6+ caractères" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Rôle *</label>
        <select name="role" id="roleSelect" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="etudiant">Étudiant</option>
          <option value="enseignant">Enseignant</option>
          <option value="admin">Administrateur</option>
        </select>
      </div>

      <!-- Champs Étudiant -->
      <div id="etuFields" class="space-y-4 p-4 bg-slate-50 rounded-xl">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Matricule *</label>
            <input name="matricule" placeholder="ETU001" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Sexe</label>
            <select name="sexe" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="M">Masculin</option>
              <option value="F">Féminin</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Date naissance</label>
            <input type="date" name="date_naissance" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Lieu naissance</label>
            <input name="lieu_naissance" placeholder="Cotonou" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Classe</label>
          <select name="classe_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Choisir une classe --</option>
            <?php foreach ($classes as $c): ?>
              <option value="<?= $c['id'] ?>"><?= e($c['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div id="ensFields" class="hidden space-y-4 p-4 bg-slate-50 rounded-xl">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Matricule *</label>
          <input name="matricule" placeholder="ENS001" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Spécialité</label>
            <input name="specialite" placeholder="Mathématiques" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
            <input name="telephone" placeholder="+229 97XXXXXXXX" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
        </div>
      </div>

      <div id="adminFields" class="hidden p-4 bg-slate-50 rounded-xl text-center text-slate-500 text-sm">
        <i class="ri-information-line"></i> Aucune information supplémentaire requise pour un administrateur.
      </div>

      <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
        Créer l'utilisateur
      </button>
    </form>
  </div>
</div>

<div id="modalEdit" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-xl">
    <div class="sticky top-0 bg-white p-5 border-b flex justify-between items-center">
      <h3 class="font-semibold text-lg">Modifier l'utilisateur</h3>
      <button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
        <i class="ri-close-line text-xl"></i>
      </button>
    </div>
    <form method="POST" class="p-5 space-y-4">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="user_id" id="edit_user_id">

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nom *</label>
          <input required name="nom" id="edit_nom" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Prénom *</label>
          <input required name="prenom" id="edit_prenom" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
          <input type="email" required name="email" id="edit_email" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nouveau mot de passe</label>
          <input type="password" name="password" minlength="6" placeholder="Laisser vide pour ne pas changer" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <p class="text-xs text-slate-400 mt-1">Remplir uniquement pour changer le mot de passe</p>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Rôle *</label>
        <select name="role" id="edit_role" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="etudiant">Étudiant</option>
          <option value="enseignant">Enseignant</option>
          <option value="admin">Administrateur</option>
        </select>
      </div>

      <div id="editEtuFields" class="space-y-4 p-4 bg-slate-50 rounded-xl">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Matricule</label>
            <input name="matricule" id="edit_matricule" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Sexe</label>
            <select name="sexe" id="edit_sexe" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="M">Masculin</option>
              <option value="F">Féminin</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Date naissance</label>
            <input type="date" name="date_naissance" id="edit_date_naissance" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Lieu naissance</label>
            <input name="lieu_naissance" id="edit_lieu_naissance" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Classe</label>
          <select name="classe_id" id="edit_classe_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">-- Choisir une classe --</option>
            <?php foreach ($classes as $c): ?>
              <option value="<?= $c['id'] ?>"><?= e($c['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div id="editEnsFields" class="hidden space-y-4 p-4 bg-slate-50 rounded-xl">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Matricule</label>
          <input name="matricule" id="edit_matricule_ens" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Spécialité</label>
            <input name="specialite" id="edit_specialite" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
            <input name="telephone" id="edit_telephone" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
        </div>
      </div>

      <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
        Mettre à jour
      </button>
    </form>
  </div>
</div>

<div id="modalResetPassword" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md shadow-xl">
    <div class="p-5 border-b flex justify-between items-center">
      <h3 class="font-semibold text-lg">Réinitialiser le mot de passe</h3>
      <button onclick="document.getElementById('modalResetPassword').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
        <i class="ri-close-line text-xl"></i>
      </button>
    </div>
    <form method="POST" class="p-5 space-y-4">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="action" value="reset_password">
      <input type="hidden" name="user_id" id="reset_user_id">

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Utilisateur</label>
        <input type="text" id="reset_user_name" disabled class="w-full border border-slate-200 rounded-xl px-4 py-2.5 bg-slate-50 text-slate-600">
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nouveau mot de passe</label>
        <input type="password" name="new_password" required minlength="6" placeholder="6+ caractères" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <p class="text-xs text-slate-400 mt-1">Choisissez un mot de passe sécurisé d’au moins 6 caractères.</p>
      </div>

      <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
        Réinitialiser le mot de passe
      </button>
    </form>
  </div>
</div>

<script>
  const roleSelect = document.getElementById('roleSelect');
  const etuFields = document.getElementById('etuFields');
  const ensFields = document.getElementById('ensFields');
  const adminFields = document.getElementById('adminFields');

  function toggleFields() {
    if (roleSelect) {
      const role = roleSelect.value;
      if (role === 'etudiant') {
        etuFields.classList.remove('hidden');
        ensFields.classList.add('hidden');
        adminFields.classList.add('hidden');
      } else if (role === 'enseignant') {
        etuFields.classList.add('hidden');
        ensFields.classList.remove('hidden');
        adminFields.classList.add('hidden');
      } else {
        etuFields.classList.add('hidden');
        ensFields.classList.add('hidden');
        adminFields.classList.remove('hidden');
      }
    }
  }

  if (roleSelect) {
    roleSelect.addEventListener('change', toggleFields);
    toggleFields();
  }

  function editUser(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_nom').value = user.nom || '';
    document.getElementById('edit_prenom').value = user.prenom || '';
    document.getElementById('edit_email').value = user.email || '';
    document.getElementById('edit_role').value = user.role || 'etudiant';

    const editRole = user.role;
    const editEtu = document.getElementById('editEtuFields');
    const editEns = document.getElementById('editEnsFields');

    if (editRole === 'etudiant') {
      editEtu.classList.remove('hidden');
      editEns.classList.add('hidden');
      document.getElementById('edit_matricule').value = user.matricule || '';
      document.getElementById('edit_sexe').value = user.sexe || 'M';
      document.getElementById('edit_date_naissance').value = user.date_naissance || '';
      document.getElementById('edit_lieu_naissance').value = user.lieu_naissance || '';
      document.getElementById('edit_classe_id').value = user.classe_id || '';
    } else if (editRole === 'enseignant') {
      editEtu.classList.add('hidden');
      editEns.classList.remove('hidden');
      document.getElementById('edit_matricule_ens').value = user.matricule || '';
      document.getElementById('edit_specialite').value = user.specialite || '';
      document.getElementById('edit_telephone').value = user.telephone || '';
    } else {
      editEtu.classList.add('hidden');
      editEns.classList.add('hidden');
    }

    document.getElementById('modalEdit').classList.remove('hidden');
  }

  function resetPassword(userId, userName) {
    document.getElementById('reset_user_id').value = userId;
    document.getElementById('reset_user_name').value = userName;
    document.getElementById('modalResetPassword').classList.remove('hidden');
  }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>