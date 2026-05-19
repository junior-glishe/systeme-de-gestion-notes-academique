<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('etudiant');
$pageTitle = 'Mes notes';

date_default_timezone_set('Africa/Porto-Novo');

$user = currentUser();
$et = $pdo->prepare("SELECT e.id, e.matricule, c.nom AS classe FROM etudiants e LEFT JOIN classes c ON e.classe_id = c.id WHERE e.utilisateur_id = ?");
$et->execute([$user['id']]);
$et = $et->fetch();

$notes = [];
$moyenneGenerale = 0;
$totalCoef = 0;
$sommePoints = 0;

if ($et) {
  $stmt = $pdo->prepare("SELECT n.*, m.nom AS matiere, m.code, m.coefficient FROM notes n JOIN matieres m ON n.matiere_id = m.id WHERE n.etudiant_id = ? AND n.validee = 1 ORDER BY m.nom");
  $stmt->execute([$et['id']]);
  $notes = $stmt->fetchAll();

  foreach ($notes as $n) {
    $sommePoints += $n['moyenne'] * $n['coefficient'];
    $totalCoef += $n['coefficient'];
  }
  $moyenneGenerale = $totalCoef > 0 ? round($sommePoints / $totalCoef, 2) : 0;
}

include __DIR__ . '/../../includes/header.php';
?>

<!-- Welcome Banner -->
<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
          <i class="ri-file-list-line text-2xl"></i>
        </div>
        <div>
          <h2 class="text-lg font-bold">Mes notes</h2>
          <p class="text-sm text-white/70"><?= e($et['matricule'] ?? '') ?> · <?= e($et['classe'] ?? '') ?></p>
        </div>
      </div>
      <div class="bg-white/10 rounded-xl px-4 py-2 text-center">
        <div class="text-2xl font-bold"><?= $moyenneGenerale ?>/20</div>
        <div class="text-xs text-white/70">Moyenne générale</div>
      </div>
    </div>
  </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
        <tr>
          <th class="px-5 py-3 text-left">Code</th>
          <th class="px-5 py-3 text-left">Matière</th>
          <th class="px-5 py-3 text-center w-16">Coef</th>
          <th class="px-5 py-3 text-center w-24">Interro /20</th>
          <th class="px-5 py-3 text-center w-24">Devoir /20</th>
          <th class="px-5 py-3 text-center w-24">Moyenne /20</th>
          <th class="px-5 py-3 text-center">Mention</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($notes as $n):
          $m = (float)$n['moyenne'];
          $mentionClass = match (true) {
            $m >= 16 => 'text-green-600 bg-green-50',
            $m >= 14 => 'text-blue-600 bg-blue-50',
            $m >= 12 => 'text-indigo-600 bg-indigo-50',
            $m >= 10 => 'text-yellow-600 bg-yellow-50',
            default => 'text-red-600 bg-red-50'
          };
        ?>
          <tr class="hover:bg-slate-50 transition">
            <td class="px-5 py-3 font-mono text-xs text-slate-500"><?= e($n['code']) ?></td>
            <td class="px-5 py-3 font-medium text-slate-800"><?= e($n['matiere']) ?></td>
            <td class="px-5 py-3 text-center font-semibold"><?= $n['coefficient'] ?></td>
            <td class="px-5 py-3 text-center"><?= $n['note_interro'] ?: '—' ?></td>
            <td class="px-5 py-3 text-center"><?= $n['note_devoir'] ?: '—' ?></td>
            <td class="px-5 py-3 text-center font-bold <?= $m >= 10 ? 'text-green-600' : 'text-red-600' ?>">
              <?= $n['moyenne'] ?: '—' ?>
            </td>
            <td class="px-5 py-3 text-center">
              <span class="px-2 py-1 rounded-lg text-xs font-medium <?= $mentionClass ?>">
                <?php
                if ($m >= 16) echo 'Très Bien';
                elseif ($m >= 14) echo 'Bien';
                elseif ($m >= 12) echo 'Assez Bien';
                elseif ($m >= 10) echo 'Passable';
                elseif ($m > 0) echo 'Insuffisant';
                else echo '—';
                ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>

        <?php if (empty($notes)): ?>
          <tr>
            <td colspan="7" class="text-center py-12 text-slate-400">
              <i class="ri-file-list-line text-4xl mb-2 block"></i>
              Aucune note disponible pour le moment.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
      <?php if (!empty($notes)): ?>
        <tfoot class="bg-slate-50">
          <tr class="border-t border-slate-200">
            <td colspan="5" class="px-5 py-3 text-right font-semibold text-slate-700">Moyenne générale :</td>
            <td class="px-5 py-3 text-center font-bold text-lg <?= $moyenneGenerale >= 10 ? 'text-green-600' : 'text-red-600' ?>">
              <?= $moyenneGenerale ?>/20
            </td>
            <td class="px-5 py-3 text-center">
              <span class="px-2 py-1 rounded-lg text-xs font-medium <?= $moyenneGenerale >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= $moyenneGenerale >= 10 ? 'Admis' : 'À rattraper' ?>
              </span>
            </td>
          </tr>
        </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>

<!-- Actions -->
<div class="mt-6 flex justify-end">
  <a href="bulletin.php" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm flex items-center gap-2 transition">
    <i class="ri-printer-line"></i> Voir mon bulletin
  </a>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>