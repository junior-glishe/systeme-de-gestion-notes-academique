<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
$pageTitle = 'Classes';

date_default_timezone_set('Africa/Porto-Novo');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  checkCsrf();
  if (($_POST['action'] ?? '') === 'create') {
    $pdo->prepare("INSERT INTO classes (nom, niveau, annee_scolaire) VALUES (?, ?, ?)")
      ->execute([$_POST['nom'], $_POST['niveau'], $_POST['annee']]);
  } elseif (($_POST['action'] ?? '') === 'delete') {
    $pdo->prepare("DELETE FROM classes WHERE id = ?")->execute([$_POST['id']]);
  }
  header('Location: classes.php');
  exit;
}

$list = $pdo->query("SELECT c.*, COUNT(e.id) AS nb FROM classes c LEFT JOIN etudiants e ON e.classe_id = c.id GROUP BY c.id")->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
        <i class="ri-building-line text-2xl text-blue-600"></i>
      </div>
      <div>
        <h2 class="text-lg font-bold text-slate-800">Gestion des classes</h2>
        <p class="text-sm text-slate-500"><?= count($list) ?> classe(s) au total</p>
      </div>
    </div>
  </div>
</div>

<div class="grid md:grid-cols-3 gap-4">
  <?php foreach ($list as $c): ?>
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition">
      <div class="flex justify-between items-start">
        <div>
          <div class="text-lg font-bold text-slate-800"><?= e($c['nom']) ?></div>
          <div class="text-xs text-slate-500"><?= e($c['niveau']) ?></div>
          <div class="text-xs text-slate-400 mt-1"><?= e($c['annee_scolaire']) ?></div>
        </div>
        <form method="POST" onsubmit="return confirm('Supprimer cette classe ?')">
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= $c['id'] ?>">
          <button class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition">
            <i class="ri-delete-bin-line text-lg"></i>
          </button>
        </form>
      </div>
      <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2 text-sm text-slate-600">
        <i class="ri-user-line"></i>
        <span><?= $c['nb'] ?> étudiant(s)</span>
      </div>
    </div>
  <?php endforeach; ?>

  <button onclick="document.getElementById('modal').classList.remove('hidden')"
    class="border-2 border-dashed border-slate-300 rounded-2xl p-5 text-slate-500 hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 transition-all flex items-center justify-center gap-2">
    <i class="ri-add-circle-line text-2xl"></i>
    <span>Nouvelle classe</span>
  </button>
</div>

<div id="modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl">
    <div class="flex justify-between items-center mb-4">
      <h3 class="font-semibold text-lg">Nouvelle classe</h3>
      <button onclick="document.getElementById('modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
        <i class="ri-close-line text-xl"></i>
      </button>
    </div>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <input type="hidden" name="action" value="create">
      <input required name="nom" placeholder="Nom (ex. L1-INFO)" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input required name="niveau" placeholder="Niveau" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input required name="annee" placeholder="Année (2024-2025)" value="2024-2025" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
        Enregistrer
      </button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>