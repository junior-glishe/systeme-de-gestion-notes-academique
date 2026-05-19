<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('etudiant');
$pageTitle = 'Historique de mes notes';
date_default_timezone_set('Africa/Porto-Novo');

$user = currentUser();
$et = $pdo->prepare("SELECT e.id, e.matricule, c.nom AS classe FROM etudiants e LEFT JOIN classes c ON e.classe_id = c.id WHERE e.utilisateur_id = ?");
$et->execute([$user['id']]);
$et = $et->fetch();

$rows = [];
if ($et) {
  $stmt = $pdo->prepare("SELECT h.*, m.nom AS matiere, m.code, m.coefficient,
        u.nom AS validateur_nom, u.prenom AS validateur_prenom
        FROM notes_historique h
        JOIN matieres m ON m.id = h.matiere_id
        LEFT JOIN utilisateurs u ON u.id = h.validated_by
        WHERE h.etudiant_id = ?
        ORDER BY h.validated_at DESC");
  $stmt->execute([$et['id']]);
  $rows = $stmt->fetchAll();
}
include __DIR__ . '/../../includes/header.php';
?>
<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm"><i class="ri-history-line text-2xl"></i></div>
      <div>
        <h2 class="text-lg font-bold">Historique de mes notes</h2>
        <p class="text-sm text-white/70"><?= count($rows) ?> entrée(s) · Toutes les validations de vos enseignants</p>
      </div>
    </div>
  </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
        <tr>
          <th class="px-5 py-3 text-left">Date</th>
          <th class="px-5 py-3 text-left">Matière</th>
          <th class="px-5 py-3 text-center">Interro</th>
          <th class="px-5 py-3 text-center">Devoir</th>
          <th class="px-5 py-3 text-center">Moyenne</th>
          <th class="px-5 py-3 text-left">Validée par</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($rows as $r): $m = (float)$r['moyenne']; ?>
          <tr class="hover:bg-slate-50">
            <td class="px-5 py-3 text-xs text-slate-500"><?= date('d/m/Y H:i', strtotime($r['validated_at'])) ?></td>
            <td class="px-5 py-3"><span class="font-mono text-xs text-slate-500"><?= e($r['code']) ?></span> · <span class="font-medium text-slate-800"><?= e($r['matiere']) ?></span></td>
            <td class="px-5 py-3 text-center"><?= $r['note_interro'] ?></td>
            <td class="px-5 py-3 text-center"><?= $r['note_devoir'] ?></td>
            <td class="px-5 py-3 text-center font-bold <?= $m >= 10 ? 'text-green-600' : 'text-red-600' ?>"><?= $r['moyenne'] ?>/20</td>
            <td class="px-5 py-3 text-xs text-slate-600"><?= e(trim(($r['validateur_prenom'] ?? '') . ' ' . ($r['validateur_nom'] ?? ''))) ?: '—' ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="6" class="text-center py-12 text-slate-400">
            <i class="ri-history-line text-4xl mb-2 block"></i>Aucun historique pour le moment.
          </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
