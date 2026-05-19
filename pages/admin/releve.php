<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole(['admin', 'responsable']);
$pageTitle = 'Relevé collectif';

date_default_timezone_set('Africa/Porto-Novo');

$classes = $pdo->query("SELECT * FROM classes")->fetchAll();
$classeId = (int)($_GET['classe'] ?? ($classes[0]['id'] ?? 0));

$rows = [];
$classeNom = '';
if ($classeId) {
  $stmt = $pdo->prepare("
        SELECT e.id, e.matricule, e.nom, e.prenom,
            ROUND(AVG(n.moyenne), 2) AS moy_gen,
            COUNT(n.id) AS nb_matieres
        FROM etudiants e
        LEFT JOIN notes n ON n.etudiant_id = e.id AND n.validee = 1
        WHERE e.classe_id = ?
        GROUP BY e.id
        ORDER BY moy_gen DESC");
  $stmt->execute([$classeId]);
  $rows = $stmt->fetchAll();

  foreach ($classes as $c) {
    if ($c['id'] == $classeId) $classeNom = $c['nom'];
  }
}

include __DIR__ . '/../../includes/header.php';
?>

<!-- Welcome Banner -->
<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
          <i class="ri-file-list-line text-2xl text-blue-600"></i>
        </div>
        <div>
          <h2 class="text-lg font-bold text-slate-800">Relevé collectif</h2>
          <p class="text-sm text-slate-500">Classement par ordre de mérite</p>
        </div>
      </div>
      <button onclick="window.print()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm flex items-center gap-2 transition">
        <i class="ri-printer-line"></i> Imprimer
      </button>
    </div>
  </div>
</div>

<div class="no-print bg-white rounded-2xl p-5 mb-4 shadow-sm border border-slate-100">
  <form method="GET" class="flex gap-3 items-center">
    <label class="text-sm text-slate-600 font-medium">Classe :</label>
    <select name="classe" onchange="this.form.submit()" class="border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      <?php foreach ($classes as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $c['id'] == $classeId ? 'selected' : '' ?>><?= e($c['nom']) ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
  <div class="text-center mb-6 border-b pb-4">
    <h1 class="text-xl font-bold text-slate-800">INSTITUT ACADÉMIQUE DU BÉNIN</h1>
    <p class="text-sm text-slate-600 mt-1">Relevé de notes collectif par ordre de mérite</p>
    <p class="text-sm font-semibold mt-2 text-blue-700">Classe : <?= e($classeNom) ?> · Année 2024-2025</p>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-100">
        <tr>
          <th class="px-4 py-3 text-left">Rang</th>
          <th class="px-4 py-3 text-left">Matricule</th>
          <th class="px-4 py-3 text-left">Nom & Prénom</th>
          <th class="px-4 py-3 text-center">Matières</th>
          <th class="px-4 py-3 text-center">Moyenne /20</th>
          <th class="px-4 py-3 text-center">Mention</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $i => $r):
          $m = $r['moy_gen'] ?? 0;
          $mention = mention((float)$m);
          $mentionClass = match (true) {
            $m >= 16 => 'text-emerald-700 bg-emerald-50',
            $m >= 14 => 'text-green-700 bg-green-50',
            $m >= 12 => 'text-blue-700 bg-blue-50',
            $m >= 10 => 'text-amber-700 bg-amber-50',
            default => 'text-red-700 bg-red-50'
          };
        ?>
          <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
            <td class="px-4 py-3 font-bold <?= $i < 3 ? 'text-amber-600' : 'text-slate-600' ?>">
              <?= $i + 1 ?>ᵉʳ
            </td>
            <td class="px-4 py-3 font-mono text-xs text-slate-600"><?= e($r['matricule']) ?></td>
            <td class="px-4 py-3 font-medium text-slate-800"><?= e($r['nom'] . ' ' . $r['prenom']) ?></td>
            <td class="px-4 py-3 text-center text-slate-600"><?= $r['nb_matieres'] ?></td>
            <td class="px-4 py-3 text-center font-bold <?= $m >= 10 ? 'text-green-600' : 'text-red-600' ?>">
              <?= $m ?: '—' ?>
            </td>
            <td class="px-4 py-3 text-center">
              <span class="px-2 py-1 rounded-lg text-xs font-medium <?= $mentionClass ?>">
                <?= $mention ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-8 text-right text-xs text-slate-400 border-t pt-4">
    Édité le <?= date('d/m/Y à H:i') ?>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>