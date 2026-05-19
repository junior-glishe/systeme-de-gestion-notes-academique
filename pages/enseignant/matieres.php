<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole(['enseignant', 'admin']);
$pageTitle = 'Mes matières';

date_default_timezone_set('Africa/Porto-Novo');

$user = currentUser();
$ens = $pdo->prepare("SELECT id FROM enseignants WHERE utilisateur_id=?");
$ens->execute([$user['id']]);
$ens = $ens->fetch();

$matieres = $pdo->prepare("SELECT m.*, c.nom AS classe FROM matieres m LEFT JOIN classes c ON m.classe_id = c.id WHERE m.enseignant_id = ?");
$matieres->execute([$ens['id'] ?? 0]);
$matieres = $matieres->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
        <i class="ri-book-open-line text-2xl text-blue-600"></i>
      </div>
      <div>
        <h2 class="text-lg font-bold text-slate-800">Mes matières</h2>
        <p class="text-sm text-slate-500"><?= count($matieres) ?> matière(s) à enseigner</p>
      </div>
    </div>
  </div>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
  <?php foreach ($matieres as $m): ?>
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition group">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs font-mono text-slate-400 bg-slate-100 inline-block px-2 py-0.5 rounded-lg"><?= e($m['code']) ?></div>
          <div class="font-bold text-slate-800 text-lg mt-2"><?= e($m['nom']) ?></div>
          <div class="text-xs text-slate-500 mt-1">
            <i class="ri-school-line mr-1"></i><?= e($m['classe']) ?> ·
            Coef <span class="font-semibold"><?= $m['coefficient'] ?></span>
          </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition">
          <i class="ri-survey-line text-xl text-blue-600"></i>
        </div>
      </div>
      <a href="notes.php?matiere=<?= $m['id'] ?>" class="mt-4 inline-flex items-center gap-2 text-blue-600 text-sm font-semibold hover:gap-3 transition-all">
        Saisir les notes <i class="ri-arrow-right-line"></i>
      </a>
    </div>
  <?php endforeach; ?>

  <?php if (empty($matieres)): ?>
    <div class="col-span-full bg-white rounded-2xl p-8 text-center border border-slate-100">
      <i class="ri-book-open-line text-5xl text-slate-300 mb-3 block"></i>
      <p class="text-slate-500">Aucune matière assignée</p>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>