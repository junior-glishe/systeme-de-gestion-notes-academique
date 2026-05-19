<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole(['enseignant', 'admin']);
$pageTitle = "Mes étudiants";

date_default_timezone_set('Africa/Porto-Novo');

$user = currentUser();
$ens = $pdo->prepare("SELECT id FROM enseignants WHERE utilisateur_id=?");
$ens->execute([$user['id']]);
$ens = $ens->fetch();
$rows = [];
if ($ens) {
  $stmt = $pdo->prepare("SELECT DISTINCT e.id, u.nom, u.prenom, e.matricule, e.sexe, c.nom AS classe FROM etudiants e JOIN utilisateurs u ON u.id=e.utilisateur_id JOIN classes c ON c.id=e.classe_id JOIN matieres m ON m.classe_id=c.id WHERE m.enseignant_id=? ORDER BY u.nom");
  $stmt->execute([$ens['id']]);
  $rows = $stmt->fetchAll();
}

include __DIR__ . '/../../includes/header.php';
?>
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
  <div class="p-6 border-b">
    <h3 class="font-semibold text-slate-800">Étudiants de mes classes (<?= count($rows) ?>)</h3>
  </div>
  <table class="w-full text-sm">
    <thead class="bg-slate-50 text-slate-600">
      <tr>
        <th class="px-6 py-3 text-left">Matricule</th>
        <th class="px-6 py-3 text-left">Nom & Prénom</th>
        <th class="px-6 py-3 text-left">Sexe</th>
        <th class="px-6 py-3 text-left">Classe</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <?php foreach ($rows as $r): ?>
        <tr class="hover:bg-slate-50">
          <td class="px-6 py-3 font-mono text-xs"><?= e($r['matricule']) ?></td>
          <td class="px-6 py-3 font-medium"><?= e($r['nom'] . ' ' . $r['prenom']) ?></td>
          <td class="px-6 py-3"><?= $r['sexe'] === 'F' ? '♀ Féminin' : '♂ Masculin' ?></td>
          <td class="px-6 py-3"><?= e($r['classe']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($rows)): ?><tr>
          <td colspan="4" class="text-center py-10 text-slate-400">Aucun étudiant.</td>
        </tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>