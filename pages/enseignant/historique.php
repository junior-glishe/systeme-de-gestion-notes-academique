<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole(['enseignant', 'admin']);
$pageTitle = 'Historique des validations';
date_default_timezone_set('Africa/Porto-Novo');

$user = currentUser();
$ens = $pdo->prepare("SELECT id FROM enseignants WHERE utilisateur_id=?");
$ens->execute([$user['id']]);
$ens = $ens->fetch();
$ensId = $ens['id'] ?? 0;

// Toutes les notes validées concernant les matières de l'enseignant
$rows = [];
if ($ensId) {
  $stmt = $pdo->prepare("SELECT h.*, m.nom AS matiere, m.code, c.nom AS classe,
        e.matricule, e.nom AS et_nom, e.prenom AS et_prenom
        FROM notes_historique h
        JOIN matieres m ON m.id = h.matiere_id
        JOIN etudiants e ON e.id = h.etudiant_id
        LEFT JOIN classes c ON c.id = m.classe_id
        WHERE m.enseignant_id = ?
        ORDER BY h.validated_at DESC");
  $stmt->execute([$ensId]);
  $rows = $stmt->fetchAll();
}
include __DIR__ . '/../../includes/header.php';
?>
<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center"><i class="ri-history-line text-2xl text-blue-600"></i></div>
      <div>
        <h2 class="text-lg font-bold text-slate-800">Historique des validations</h2>
        <p class="text-sm text-slate-500"><?= count($rows) ?> note(s) validée(s)</p>
      </div>
    </div>
  </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
        <tr>
          <th class="px-4 py-3 text-left">Date</th>
          <th class="px-4 py-3 text-left">Matricule</th>
          <th class="px-4 py-3 text-left">Étudiant</th>
          <th class="px-4 py-3 text-left">Matière / Classe</th>
          <th class="px-4 py-3 text-center">Interro</th>
          <th class="px-4 py-3 text-center">Devoir</th>
          <th class="px-4 py-3 text-center">Moyenne</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $r): $m = (float)$r['moyenne']; ?>
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 text-xs text-slate-500"><?= date('d/m/Y H:i', strtotime($r['validated_at'])) ?></td>
            <td class="px-4 py-3 font-mono text-xs text-slate-600"><?= e($r['matricule']) ?></td>
            <td class="px-4 py-3 font-medium text-slate-800"><?= e($r['et_nom'] . ' ' . $r['et_prenom']) ?></td>
            <td class="px-4 py-3 text-slate-600 text-xs"><?= e($r['matiere']) ?> <span class="text-slate-400">· <?= e($r['classe']) ?></span></td>
            <td class="px-4 py-3 text-center"><?= $r['note_interro'] ?></td>
            <td class="px-4 py-3 text-center"><?= $r['note_devoir'] ?></td>
            <td class="px-4 py-3 text-center font-bold <?= $m >= 10 ? 'text-green-600' : 'text-red-600' ?>"><?= $r['moyenne'] ?>/20</td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7" class="text-center py-12 text-slate-400">
            <i class="ri-history-line text-4xl mb-2 block"></i>Aucune note validée pour le moment.
          </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
