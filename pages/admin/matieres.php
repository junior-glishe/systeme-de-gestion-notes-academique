<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
$pageTitle = 'Matières';

date_default_timezone_set('Africa/Porto-Novo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  checkCsrf();
  if (($_POST['action'] ?? '') === 'create') {
    $pdo->prepare("INSERT INTO matieres (code, nom, coefficient, enseignant_id, classe_id) VALUES (?, ?, ?, ?, ?)")
      ->execute([$_POST['code'], $_POST['nom'], $_POST['coef'], $_POST['ens'] ?: null, $_POST['cls'] ?: null]);
  } elseif (($_POST['action'] ?? '') === 'delete') {
    $pdo->prepare("DELETE FROM matieres WHERE id = ?")->execute([$_POST['id']]);
  }
  header('Location: matieres.php');
  exit;
}

$list = $pdo->query("SELECT m.*, c.nom AS classe, CONCAT(en.prenom, ' ', en.nom) AS ens 
    FROM matieres m 
    LEFT JOIN classes c ON m.classe_id = c.id 
    LEFT JOIN enseignants en ON m.enseignant_id = en.id")->fetchAll();
$classes = $pdo->query("SELECT * FROM classes")->fetchAll();
$enseignants = $pdo->query("SELECT * FROM enseignants")->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
          <i class="ri-book-line text-2xl text-blue-600"></i>
        </div>
        <div>
          <h2 class="text-lg font-bold text-slate-800">Gestion des matières</h2>
          <p class="text-sm text-slate-500"><?= count($list) ?> matière(s) au total</p>
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
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
        <tr>
          <th class="px-5 py-3 text-left">Code</th>
          <th class="px-5 py-3 text-left">Nom</th>
          <th class="px-5 py-3 text-left">Coef</th>
          <th class="px-5 py-3 text-left">Classe</th>
          <th class="px-5 py-3 text-left">Enseignant</th>
          <th class="px-5 py-3 text-center">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($list as $m): ?>
          <tr class="hover:bg-slate-50 transition">
            <td class="px-5 py-3 font-mono text-xs text-slate-600"><?= e($m['code']) ?></td>
            <td class="px-5 py-3 font-semibold text-slate-800"><?= e($m['nom']) ?></td>
            <td class="px-5 py-3">
              <span class="px-2 py-1 rounded-lg bg-blue-100 text-blue-700 text-xs font-medium"><?= $m['coefficient'] ?></span>
            </td>
            <td class="px-5 py-3 text-slate-600"><?= e($m['classe']) ?: '—' ?></td>
            <td class="px-5 py-3 text-slate-600"><?= e($m['ens']) ?: 'Non assigné' ?></td>
            <td class="px-5 py-3 text-center">
              <form method="POST" onsubmit="return confirm('Supprimer cette matière ?')" class="inline">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $m['id'] ?>">
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

<div id="modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-semibold text-lg">Nouvelle matière</h3>
      <button onclick="document.getElementById('modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
        <i class="ri-close-line text-xl"></i>
      </button>
    </div>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="action" value="create">
      <input required name="code" placeholder="Code (ex. MATH101)" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input required name="nom" placeholder="Nom de la matière" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input required type="number" name="coef" min="1" max="10" value="1" placeholder="Coefficient" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <select name="cls" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">-- Choisir une classe --</option>
        <?php foreach ($classes as $c): ?>
          <option value="<?= $c['id'] ?>"><?= e($c['nom']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="ens" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">-- Choisir un enseignant --</option>
        <?php foreach ($enseignants as $e): ?>
          <option value="<?= $e['id'] ?>"><?= e($e['nom'] . ' ' . $e['prenom']) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
        Enregistrer
      </button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>