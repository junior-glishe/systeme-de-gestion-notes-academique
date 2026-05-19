<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('etudiant');
$pageTitle = 'Mon bulletin';

date_default_timezone_set('Africa/Porto-Novo');

$user = currentUser();
$et = $pdo->prepare("SELECT e.*, c.nom AS classe, c.niveau FROM etudiants e LEFT JOIN classes c ON e.classe_id = c.id WHERE e.utilisateur_id = ?");
$et->execute([$user['id']]);
$et = $et->fetch();

$notes = [];
$moyG = 0;
$totalCoef = 0;
$sommeP = 0;

if ($et) {
  $stmt = $pdo->prepare("SELECT n.*, m.nom AS matiere, m.code, m.coefficient FROM notes n JOIN matieres m ON n.matiere_id = m.id WHERE n.etudiant_id = ? AND n.validee = 1 ORDER BY m.nom");
  $stmt->execute([$et['id']]);
  $notes = $stmt->fetchAll();

  foreach ($notes as $n) {
    $sommeP += $n['moyenne'] * $n['coefficient'];
    $totalCoef += $n['coefficient'];
  }
  $moyG = $totalCoef ? round($sommeP / $totalCoef, 2) : 0;
}

include __DIR__ . '/../../includes/header.php';
?>

<!-- Bouton impression -->
<div class="no-print mb-4 flex flex-col items-start gap-3">
  <div class="text-xs text-slate-500">
    Pour imprimer un bulletin propre, décochez « En-têtes et pieds de page » dans les options d'impression de votre navigateur.
  </div>
  <button onclick="window.print()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm flex items-center gap-2 transition shadow-sm">
    <i class="ri-printer-line"></i> Imprimer / PDF
  </button>
</div>

<!-- Bulletin -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 max-w-4xl mx-auto print:shadow-none print:p-4">
  <!-- Header du bulletin -->
  <div class="flex justify-between items-start border-b-4 border-blue-700 pb-4">
    <div>
      <div class="text-xs text-slate-500">RÉPUBLIQUE DU BÉNIN</div>
      <div class="text-lg font-bold text-blue-900">INSTITUT ACADÉMIQUE DU BÉNIN</div>
      <div class="text-xs text-slate-600">Cotonou, Bénin</div>
    </div>
    <div class="text-right">
      <div class="text-xs text-slate-500">Année scolaire</div>
      <div class="font-bold text-slate-800">2025-2026</div>
    </div>
  </div>

  <h1 class="text-center text-xl font-bold mt-4 mb-6 text-slate-800">BULLETIN DE NOTES — SEMESTRE 1</h1>

  <!-- Infos étudiant -->
  <div class="grid grid-cols-2 gap-4 text-sm mb-6 bg-slate-50 p-4 rounded-xl">
    <div><span class="text-slate-500">Matricule :</span> <strong class="text-slate-800"><?= e($et['matricule']) ?></strong></div>
    <div><span class="text-slate-500">Classe :</span> <strong class="text-slate-800"><?= e($et['classe']) ?></strong></div>
    <div><span class="text-slate-500">Nom :</span> <strong class="text-slate-800"><?= e($et['nom'] . ' ' . $et['prenom']) ?></strong></div>
    <div><span class="text-slate-500">Né(e) le :</span> <strong class="text-slate-800"><?= e($et['date_naissance']) ?> à <?= e($et['lieu_naissance']) ?></strong></div>
  </div>

  <!-- Tableau des notes -->
  <table class="w-full text-sm border border-slate-300 rounded-xl overflow-hidden">
    <thead class="bg-blue-700 text-white">
      <tr>
        <th class="px-3 py-2 text-left">Matière</th>
        <th class="px-3 py-2 text-center w-16">Coef</th>
        <th class="px-3 py-2 text-center w-24">Interro (30%)</th>
        <th class="px-3 py-2 text-center w-24">Devoir (70%)</th>
        <th class="px-3 py-2 text-center w-24">Moyenne</th>
        <th class="px-3 py-2 text-center w-24">Total</th>
        <th class="px-3 py-2 text-left">Appréciation</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($notes as $n):
        $m = (float)$n['moyenne'];
        $mentionText = match (true) {
          $m >= 16 => 'Très Bien',
          $m >= 14 => 'Bien',
          $m >= 12 => 'Assez Bien',
          $m >= 10 => 'Passable',
          default => 'Insuffisant'
        };
      ?>
        <tr class="border-b border-slate-200 hover:bg-slate-50">
          <td class="px-3 py-2 font-medium text-slate-800"><?= e($n['matiere']) ?></td>
          <td class="px-3 py-2 text-center"><?= $n['coefficient'] ?></td>
          <td class="px-3 py-2 text-center"><?= $n['note_interro'] ?: '—' ?></td>
          <td class="px-3 py-2 text-center"><?= $n['note_devoir'] ?: '—' ?></td>
          <td class="px-3 py-2 text-center font-bold <?= $m >= 10 ? 'text-green-700' : 'text-red-600' ?>">
            <?= $n['moyenne'] ?>
          </td>
          <td class="px-3 py-2 text-center"><?= number_format($m * $n['coefficient'], 2) ?></td>
          <td class="px-3 py-2 text-xs">
            <span class="px-2 py-0.5 rounded-full <?= $m >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
              <?= $mentionText ?>
            </span>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot class="bg-slate-100 font-bold">
      <tr>
        <td colspan="5" class="px-3 py-2 text-right">MOYENNE GÉNÉRALE</td>
        <td class="px-3 py-2 text-center text-lg <?= $moyG >= 10 ? 'text-green-700' : 'text-red-600' ?>">
          <?= $moyG ?>/20
        </td>
        <td class="px-3 py-2 text-xs">
          <span class="px-2 py-1 rounded-full <?= $moyG >= 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
            <?= match (true) {
              $moyG >= 16 => 'Très Bien',
              $moyG >= 14 => 'Bien',
              $moyG >= 12 => 'Assez Bien',
              $moyG >= 10 => 'Passable',
              default => 'Insuffisant'
            } ?>
          </span>
        </td>
      </tr>
    </tfoot>
  </table>

  <!-- Résumé -->
  <div class="grid grid-cols-4 gap-4 mt-6 text-sm">
    <div class="border border-slate-200 rounded-xl p-3 text-center">
      <div class="text-xs text-slate-500">Total points</div>
      <div class="font-bold text-lg text-slate-800"><?= number_format($sommeP, 2) ?></div>
    </div>
    <div class="border border-slate-200 rounded-xl p-3 text-center">
      <div class="text-xs text-slate-500">Total coef.</div>
      <div class="font-bold text-lg text-slate-800"><?= $totalCoef ?></div>
    </div>
    <div class="border border-slate-200 rounded-xl p-3 text-center">
      <div class="text-xs text-slate-500">Moyenne classe</div>
      <div class="font-bold text-lg text-slate-800">—</div>
    </div>
    <div class="border border-slate-200 rounded-xl p-3 text-center">
      <div class="text-xs text-slate-500">Décision</div>
      <div class="font-bold text-lg <?= $moyG >= 10 ? 'text-green-700' : 'text-red-600' ?>">
        <?= $moyG >= 10 ? 'ADMIS(E)' : 'AJOURNÉ(E)' ?>
      </div>
    </div>
  </div>

  <!-- Signatures -->
  <div class="mt-10 grid grid-cols-2 gap-8 text-sm">
    <div>
      <div class="text-slate-500">Le Directeur des Études</div>
      <div class="mt-12 border-t border-slate-300 pt-2 italic text-xs text-slate-400">Signature & cachet</div>
    </div>
    <div class="text-right">
      <div class="text-slate-500">Fait à Cotonou, le <?= date('d/m/Y') ?></div>
    </div>
  </div>
</div>

<!-- Styles pour l'impression -->
<style media="print">
  @page {
    margin: 15mm;
  }

  .no-print {
    display: none !important;
  }

  body {
    background: white;
  }

  .bg-white {
    background: white !important;
    box-shadow: none !important;
  }

  .rounded-2xl {
    border-radius: 0 !important;
  }

  .shadow-sm {
    box-shadow: none !important;
  }

  .border {
    border-color: #ddd !important;
  }
</style>

<?php include __DIR__ . '/../../includes/footer.php'; ?>