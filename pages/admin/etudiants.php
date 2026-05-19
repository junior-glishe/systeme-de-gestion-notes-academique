<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
$pageTitle = 'Étudiants';

date_default_timezone_set('Africa/Porto-Novo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  checkCsrf();
  $action = $_POST['action'] ?? '';
  if ($action === 'create') {
    $stmt = $pdo->prepare("INSERT INTO etudiants (matricule, nom, prenom, date_naissance, lieu_naissance, sexe, classe_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['matricule'], $_POST['nom'], $_POST['prenom'], $_POST['date_naissance'] ?: null, $_POST['lieu_naissance'], $_POST['sexe'], $_POST['classe_id'] ?: null]);
  } elseif ($action === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM etudiants WHERE id = ?");
    $stmt->execute([$_POST['id']]);
  }
  header('Location: etudiants.php');
  exit;
}

$etudiants = $pdo->query("SELECT e.*, c.nom AS classe FROM etudiants e LEFT JOIN classes c ON e.classe_id = c.id ORDER BY e.nom")->fetchAll();
$classes = $pdo->query("SELECT * FROM classes")->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
          <i class="ri-user-line text-2xl text-blue-600"></i>
        </div>
        <div>
          <h2 class="text-lg font-bold text-slate-800">Gestion des étudiants</h2>
          <p class="text-sm text-slate-500"><?= count($etudiants) ?> étudiant(s) enregistré(s)</p>
        </div>
      </div>
      <button onclick="document.getElementById('modal').classList.remove('hidden')"
        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm flex items-center gap-2 transition">
        <i class="ri-add-line"></i> Nouvel étudiant
      </button>
    </div>
  </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
        <tr>
          <th class="px-5 py-3 text-left">Matricule</th>
          <th class="px-5 py-3 text-left">Nom & Prénom</th>
          <th class="px-5 py-3 text-left">Naissance</th>
          <th class="px-5 py-3 text-left">Sexe</th>
          <th class="px-5 py-3 text-left">Classe</th>
          <th class="px-5 py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($etudiants as $e): ?>
          <tr class="hover:bg-slate-50 transition">
            <td class="px-5 py-3 font-mono text-xs text-slate-600"><?= e($e['matricule']) ?></td>
            <td class="px-5 py-3 font-semibold text-slate-800"><?= e($e['nom'] . ' ' . $e['prenom']) ?></td>
            <td class="px-5 py-3 text-slate-600">
              <?= e($e['date_naissance']) ?>
              <span class="text-slate-400">à <?= e($e['lieu_naissance']) ?></span>
            </td>
            <td class="px-5 py-3">
              <span class="px-2 py-0.5 rounded-lg text-xs font-medium <?= $e['sexe'] === 'F' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' ?>">
                <?= $e['sexe'] === 'F' ? 'Féminin' : 'Masculin' ?>
              </span>
            </td>
            <td class="px-5 py-3">
              <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs"><?= e($e['classe']) ?></span>
            </td>
            <td class="px-5 py-3 text-center">
              <form method="POST" onsubmit="return confirm('Supprimer cet étudiant ?')" class="inline">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $e['id'] ?>">
                <button class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div id="modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-semibold text-lg">Nouvel étudiant</h3>
      <button onclick="document.getElementById('modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
        <i class="ri-close-line text-xl"></i>
      </button>
    </div>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="action" value="create">
      <div class="grid grid-cols-2 gap-3">
        <input required name="matricule" placeholder="Matricule" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="sexe" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="M">Masculin</option>
          <option value="F">Féminin</option>
        </select>
        <input required name="nom" placeholder="Nom" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input required name="prenom" placeholder="Prénom" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="date" name="date_naissance" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input name="lieu_naissance" placeholder="Lieu de naissance" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select name="classe_id" class="border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 col-span-2">
          <option value="">-- Choisir une classe --</option>
          <?php foreach ($classes as $c): ?>
            <option value="<?= $c['id'] ?>"><?= e($c['nom']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
        Enregistrer
      </button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>