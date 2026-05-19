<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('etudiant');
$pageTitle = 'Tableau de bord';

date_default_timezone_set('Africa/Porto-Novo');

$user = currentUser();
$et = $pdo->prepare("SELECT e.*, c.nom AS classe FROM etudiants e LEFT JOIN classes c ON e.classe_id = c.id WHERE e.utilisateur_id = ?");
$et->execute([$user['id']]);
$et = $et->fetch();

$moy = 0;
$rang = '—';
$nbNotes = 0;
$meilleureMatiere = '—';
$meilleureNote = 0;

if ($et) {
  $stmt = $pdo->prepare("SELECT ROUND(AVG(moyenne), 2) moy, COUNT(*) nb FROM notes WHERE etudiant_id = ?");
  $stmt->execute([$et['id']]);
  $r = $stmt->fetch();
  $moy = $r['moy'] ?? 0;
  $nbNotes = $r['nb'];

  $stmt = $pdo->prepare("SELECT m.nom, n.moyenne FROM notes n JOIN matieres m ON n.matiere_id = m.id WHERE n.etudiant_id = ? AND n.validee = 1 ORDER BY n.moyenne DESC LIMIT 1");
  $stmt->execute([$et['id']]);
  $best = $stmt->fetch();
  if ($best) {
    $meilleureMatiere = $best['nom'];
    $meilleureNote = $best['moyenne'];
  }

  $stmt = $pdo->prepare("SELECT e.id, AVG(n.moyenne) m FROM etudiants e JOIN notes n ON n.etudiant_id = e.id WHERE e.classe_id = ? GROUP BY e.id ORDER BY m DESC");
  $stmt->execute([$et['classe_id']]);
  $i = 1;
  foreach ($stmt as $row) {
    if ($row['id'] == $et['id']) {
      $rang = $i;
      break;
    }
    $i++;
  }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
          <i class="ri-graduation-cap-line text-3xl"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-white/80 mb-1">Tableau de bord étudiant</div>
          <div class="text-2xl font-bold"><?= e($user['prenom'] . ' ' . $user['nom']) ?></div>
          <div class="text-sm text-white/70 mt-1"><?= e($et['matricule'] ?? '') ?> · <?= e($et['classe'] ?? 'Non assigné') ?> · Année 2025-2026</div>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div class="bg-white/10 rounded-xl px-4 py-2 text-center backdrop-blur-sm">
          <div class="text-3xl font-bold"><?= $moy ?>/20</div>
          <div class="text-xs text-white/70">Moyenne générale</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
  <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm text-slate-500 font-medium">Notes saisies</div>
        <div class="text-2xl font-bold text-slate-800 mt-1"><?= $nbNotes ?></div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
        <i class="ri-survey-line text-xl text-blue-600"></i>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm text-slate-500 font-medium">Mention</div>
        <div class="text-xl font-bold mt-1">
          <?php
          if ($moy >= 16) echo '<span class="text-green-600">Très Bien</span>';
          elseif ($moy >= 14) echo '<span class="text-blue-600">Bien</span>';
          elseif ($moy >= 12) echo '<span class="text-indigo-600">Assez Bien</span>';
          elseif ($moy >= 10) echo '<span class="text-yellow-600">Passable</span>';
          elseif ($moy > 0) echo '<span class="text-red-600">Insuffisant</span>';
          else echo '—';
          ?>
        </div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
        <i class="ri-award-line text-xl text-emerald-600"></i>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm text-slate-500 font-medium">Rang dans la classe</div>
        <div class="text-2xl font-bold text-slate-800 mt-1"><?= $rang ?>ᵉʳ</div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
        <i class="ri-medal-line text-xl text-amber-600"></i>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md transition">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm text-slate-500 font-medium">Statut</div>
        <div class="text-xl font-bold mt-1">
          <?php if ($moy >= 10): ?>
            <span class="text-green-600">Admis</span>
          <?php elseif ($moy > 0): ?>
            <span class="text-red-600">À rattraper</span>
          <?php else: ?>
            <span class="text-slate-400">—</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
        <i class="ri-checkbox-circle-line text-xl text-purple-600"></i>
      </div>
    </div>
  </div>
</div>

<?php if ($meilleureMatiere != '—'): ?>
  <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl p-4 mb-6 border border-amber-200">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
        <i class="ri-star-fill text-amber-500"></i>
      </div>
      <div class="flex-1">
        <p class="text-sm text-amber-800">
          <strong>Votre meilleure performance :</strong>
          <?= e($meilleureMatiere) ?> avec <strong><?= $meilleureNote ?>/20</strong>
        </p>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="flex gap-3">
  <a href="notes.php" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm flex items-center gap-2 transition">
    <i class="ri-file-list-line"></i> Voir mes notes
  </a>
  <a href="bulletin.php" class="px-5 py-2.5 bg-white border border-slate-200 hover:border-blue-300 text-slate-700 rounded-xl text-sm flex items-center gap-2 transition">
    <i class="ri-printer-line"></i> Mon bulletin
  </a>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>