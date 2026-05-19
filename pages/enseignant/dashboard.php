<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole(['enseignant', 'admin']);
$pageTitle = 'Tableau de bord';

date_default_timezone_set('Africa/Porto-Novo');

$user = currentUser();

$ens = $pdo->prepare("SELECT * FROM enseignants WHERE utilisateur_id = ?");
$ens->execute([$user['id']]);
$ens = $ens->fetch();

$matieres = [];
if ($ens) {
  $stmt = $pdo->prepare("SELECT m.*, c.nom AS classe, COUNT(n.id) AS nb_notes
        FROM matieres m 
        LEFT JOIN classes c ON m.classe_id = c.id
        LEFT JOIN notes n ON n.matiere_id = m.id
        WHERE m.enseignant_id = ? 
        GROUP BY m.id");
  $stmt->execute([$ens['id']]);
  $matieres = $stmt->fetchAll();
}

$nbEtudiants = 0;
$nbNotes = 0;

if ($ens) {
  $stmt = $pdo->prepare("SELECT COUNT(DISTINCT e.id) as nb_etudiants FROM etudiants e 
        JOIN classes c ON e.classe_id = c.id 
        JOIN matieres m ON m.classe_id = c.id 
        WHERE m.enseignant_id = ?");
  $stmt->execute([$ens['id']]);
  $nbEtudiants = $stmt->fetchColumn();

  $stmt = $pdo->prepare("SELECT COUNT(n.id) FROM notes n 
        JOIN matieres m ON n.matiere_id = m.id 
        WHERE m.enseignant_id = ?");
  $stmt->execute([$ens['id']]);
  $nbNotes = $stmt->fetchColumn();
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
          <i class="ri-user-line text-3xl"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-white/80 mb-1">Tableau de bord enseignant</div>
          <div class="text-2xl font-bold"><?= e($user['prenom'] . ' ' . $user['nom']) ?></div>
          <div class="text-sm text-white/70 mt-1"><?= e($ens['specialite'] ?? 'Enseignant') ?> · Année 2025-2026</div>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div class="bg-white/10 rounded-xl px-4 py-2 text-center backdrop-blur-sm">
          <div class="text-2xl font-bold"><?= count($matieres) ?></div>
          <div class="text-xs text-white/70">Matières</div>
        </div>
        <div class="bg-white/10 rounded-xl px-4 py-2 text-center backdrop-blur-sm">
          <div class="text-2xl font-bold"><?= $nbEtudiants ?></div>
          <div class="text-xs text-white/70">Étudiants</div>
        </div>
        <div class="bg-white/10 rounded-xl px-4 py-2 text-center backdrop-blur-sm">
          <div class="text-2xl font-bold"><?= $nbNotes ?></div>
          <div class="text-xs text-white/70">Notes saisies</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
  <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm text-slate-500 font-medium">Total matières</div>
        <div class="text-2xl font-bold text-slate-800 mt-1"><?= count($matieres) ?></div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
        <i class="ri-book-open-line text-xl text-blue-600"></i>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm text-slate-500 font-medium">Total étudiants</div>
        <div class="text-2xl font-bold text-slate-800 mt-1"><?= $nbEtudiants ?></div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
        <i class="ri-graduation-cap-line text-xl text-emerald-600"></i>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm text-slate-500 font-medium">Notes saisies</div>
        <div class="text-2xl font-bold text-slate-800 mt-1"><?= $nbNotes ?></div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
        <i class="ri-survey-line text-xl text-amber-600"></i>
      </div>
    </div>
  </div>
</div>

<div class="flex items-center justify-between mb-4">
  <h2 class="font-semibold text-slate-800 text-lg">
    <i class="ri-book-2-line text-blue-600 mr-2"></i>Mes matières
  </h2>
  <span class="text-xs text-slate-500"><?= count($matieres) ?> matière(s) assignée(s)</span>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
  <?php foreach ($matieres as $m): ?>
    <a href="notes.php?matiere=<?= $m['id'] ?>" class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-lg hover:border-blue-300 transition-all group">
      <div class="flex justify-between items-start">
        <div>
          <div class="text-xs font-mono text-slate-400 bg-slate-100 inline-block px-2 py-0.5 rounded-lg"><?= e($m['code']) ?></div>
          <div class="font-bold text-slate-800 text-lg mt-2"><?= e($m['nom']) ?></div>
          <div class="text-xs text-slate-500 mt-1">
            <i class="ri-school-line mr-1"></i><?= e($m['classe']) ?> ·
            Coef <span class="font-semibold text-blue-600"><?= $m['coefficient'] ?></span>
          </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 transition">
          <i class="ri-book-2-line text-xl"></i>
        </div>
      </div>
      <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-600 flex justify-between items-center">
        <span class="flex items-center gap-1">
          <i class="ri-edit-line"></i> Saisir notes
        </span>
        <span class="text-blue-600 font-semibold flex items-center gap-1">
          <?= $m['nb_notes'] ?> note(s)
          <i class="ri-arrow-right-line group-hover:translate-x-1 transition"></i>
        </span>
      </div>
    </a>
  <?php endforeach; ?>

  <?php if (empty($matieres)): ?>
    <div class="col-span-full bg-white rounded-2xl p-8 text-center border border-slate-100">
      <i class="ri-book-open-line text-5xl text-slate-300 mb-3 block"></i>
      <p class="text-slate-500">Aucune matière assignée pour le moment.</p>
      <p class="text-xs text-slate-400 mt-1">Contactez l'administrateur.</p>
    </div>
  <?php endif; ?>
</div>

<div class="mt-6 bg-blue-50 rounded-2xl p-4 border border-blue-100">
  <div class="flex items-center gap-3">
    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
      <i class="ri-question-line text-blue-600"></i>
    </div>
    <div class="flex-1">
      <p class="text-sm text-blue-800">
        <strong>Astuce :</strong> Cliquez sur une matière pour saisir ou modifier les notes des étudiants.
        La moyenne est automatiquement calculée (30% Interro + 70% Devoir).
      </p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>