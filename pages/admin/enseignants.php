<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
$pageTitle = 'Enseignants';

date_default_timezone_set('Africa/Porto-Novo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  checkCsrf();
  if (($_POST['action'] ?? '') === 'create') {
    $pdo->prepare("INSERT INTO enseignants (matricule, nom, prenom, specialite, telephone) VALUES (?, ?, ?, ?, ?)")
      ->execute([$_POST['matricule'], $_POST['nom'], $_POST['prenom'], $_POST['specialite'], $_POST['telephone']]);
  } elseif (($_POST['action'] ?? '') === 'delete') {
    $pdo->prepare("DELETE FROM enseignants WHERE id = ?")->execute([$_POST['id']]);
  }
  header('Location: enseignants.php');
  exit;
}

$list = $pdo->query("SELECT * FROM enseignants ORDER BY nom")->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
          <i class="ri-user-star-line text-2xl text-blue-600"></i>
        </div>
        <div>
          <h2 class="text-lg font-bold text-slate-800">Gestion des enseignants</h2>
          <p class="text-sm text-slate-500"><?= count($list) ?> enseignant(s) enregistré(s)</p>
        </div>
      </div>
      <button onclick="document.getElementById('modal').classList.remove('hidden')"
        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm flex items-center gap-2 transition">
        <i class="ri-add-line"></i> Ajouter
      </button>
    </div>
  </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-xs uppercase text-slate-600">
      <tr>
        <th class="px-5 py-3 text-left">Matricule</th>
        <th class="px-5 py-3 text-left">Nom complet</th>
        <th class="px-5 py-3 text-left">Spécialité</th>
        <th class="px-5 py-3 text-left">Téléphone</th>
        <th class="px-5 py-3 text-center">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <?php foreach ($list as $e): ?>
        <tr class="hover:bg-slate-50 transition">
          <td class="px-5 py-3 font-mono text-xs text-slate-600"><?= e($e['matricule']) ?></td>
          <td class="px-5 py-3 font-semibold text-slate-800"><?= e($e['nom'] . ' ' . $e['prenom']) ?></td>
          <td class="px-5 py-3 text-slate-600"><?= e($e['specialite']) ?: '—' ?></td>
          <td class="px-5 py-3 text-slate-600"><?= e($e['telephone']) ?: '—' ?></td>
          <td class="px-5 py-3 text-center">
            <form method="POST" onsubmit="return confirm('Supprimer cet enseignant ?')" class="inline">
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

<div id="modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-semibold text-lg">Nouvel enseignant</h3>
      <button onclick="document.getElementById('modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
        <i class="ri-close-line text-xl"></i>
      </button>
    </div>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="action" value="create">
      <input required name="matricule" placeholder="Matricule" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input required name="nom" placeholder="Nom" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input required name="prenom" placeholder="Prénom" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input name="specialite" placeholder="Spécialité" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input name="telephone" placeholder="Téléphone" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
        Enregistrer
      </button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>