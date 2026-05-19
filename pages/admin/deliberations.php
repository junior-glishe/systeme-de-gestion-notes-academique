<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole('responsable');
$pageTitle = 'Délibérations';
$user = currentUser();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  checkCsrf();
  $class_id = (int)($_POST['class_id'] ?? 0);
  if ($class_id > 0) {
    $stmt = $pdo->prepare("SELECT n.* FROM notes n JOIN etudiants e ON n.etudiant_id = e.id WHERE e.classe_id = ? AND n.validee = 0");
    $stmt->execute([$class_id]);
    $notes = $stmt->fetchAll();
    $count = 0;
    foreach ($notes as $n) {
      $pdo->prepare("UPDATE notes SET validee = 1, validated_at = NOW(), validated_by = ? WHERE id = ?")->execute([$user['id'], $n['id']]);
      $pdo->prepare("INSERT INTO notes_historique (note_id, etudiant_id, matiere_id, note_interro, note_devoir, moyenne, semestre, annee_scolaire, action, validated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'validation', ?)")
        ->execute([$n['id'], $n['etudiant_id'], $n['matiere_id'], $n['note_interro'], $n['note_devoir'], $n['moyenne'], $n['semestre'], $n['annee_scolaire'], $user['id']]);
      $count++;
    }
    $_SESSION['flash'] = 'Délibération validée pour la classe sélectionnée. ' . $count . ' note(s) publiées.';
  } else {
    $_SESSION['flash'] = 'Aucune classe sélectionnée.';
  }
  header('Location: deliberations.php');
  exit;
}
$classes = $pdo->query("SELECT c.id, c.nom, ROUND(AVG(n.moyenne),2) moy, COUNT(n.id) nb_notes, COUNT(DISTINCT e.id) nb_etudiants\n  FROM classes c JOIN etudiants e ON e.classe_id=c.id JOIN notes n ON n.etudiant_id=e.id AND n.validee = 0 GROUP BY c.id HAVING nb_notes > 0")->fetchAll();
$pendingNotes = [];
$classIds = array_column($classes, 'id');
if (!empty($classIds)) {
  $placeholders = implode(',', array_fill(0, count($classIds), '?'));
  $stmt = $pdo->prepare("SELECT c.id AS class_id, e.matricule, u.nom, u.prenom, m.nom AS matiere, m.code, n.note_interro, n.note_devoir, n.moyenne
    FROM notes n
    JOIN etudiants e ON n.etudiant_id = e.id
    JOIN utilisateurs u ON u.id = e.utilisateur_id
    JOIN matieres m ON n.matiere_id = m.id
    JOIN classes c ON e.classe_id = c.id
    WHERE n.validee = 0 AND c.id IN ($placeholders)
    ORDER BY c.nom, u.nom, m.nom");
  $stmt->execute($classIds);
  foreach ($stmt->fetchAll() as $row) {
    $pendingNotes[$row['class_id']][] = $row;
  }
}
include __DIR__ . '/../../includes/header.php';
?>
<?php if (!empty($_SESSION['flash'])): ?><div class="mb-4 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm"><?= e($_SESSION['flash']) ?></div><?php $_SESSION['flash'] = null;
                                                                                                                                                    endif; ?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100">
  <div class="p-5 border-b">
    <h2 class="font-semibold">Validation des délibérations</h2>
    <p class="text-xs text-slate-500 mt-1">Valider une classe publiera TOUTES les notes de TOUS les étudiants (toutes matières) en attente de validation.</p>
  </div>
  <div class="divide-y divide-slate-100">
    <?php foreach ($classes as $c): ?>
      <div class="p-5">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="font-semibold text-slate-800"><?= e($c['nom']) ?></div>
            <div class="text-xs text-slate-500"><?= $c['nb_etudiants'] ?> étudiant(s) · <?= $c['nb_notes'] ?> note(s) en attente · Moyenne classe : <strong><?= $c['moy'] ?: '—' ?></strong></div>
          </div>
          <form method="POST" onsubmit="return confirm('Valider TOUTES les notes en attente de cette classe ? Les étudiants pourront alors voir leurs notes.');">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="class_id" value="<?= (int)$c['id'] ?>">
            <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm flex items-center gap-2"><i class="ri-check-double-line"></i> Valider</button>
          </form>
        </div>

        <details class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <summary class="cursor-pointer font-medium text-slate-800">Voir le détail des notes en attente</summary>
          <?php if (!empty($pendingNotes[$c['id']])): ?>
            <div class="mt-4 overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead>
                  <tr class="text-slate-500 uppercase text-[11px] tracking-wide">
                    <th class="pb-2">Élève</th>
                    <th class="pb-2">Matière</th>
                    <th class="pb-2">Interro</th>
                    <th class="pb-2">Devoir</th>
                    <th class="pb-2">Moyenne</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($pendingNotes[$c['id']] as $note): ?>
                    <tr class="border-t border-slate-200">
                      <td class="py-2"><?= e($note['matricule'] . ' - ' . $note['nom'] . ' ' . $note['prenom']) ?></td>
                      <td class="py-2"><?= e($note['matiere']) ?></td>
                      <td class="py-2"><?= e(number_format($note['note_interro'], 2, ',', '')) ?></td>
                      <td class="py-2"><?= e(number_format($note['note_devoir'], 2, ',', '')) ?></td>
                      <td class="py-2 font-semibold"><?= e(number_format($note['moyenne'], 2, ',', '')) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="mt-4 text-slate-500 text-sm">Aucun détail disponible pour cette classe.</div>
          <?php endif; ?>
        </details>
      </div>
    <?php endforeach; ?>
    <?php if (empty($classes)): ?>
      <div class="p-8 text-center text-slate-400">
        <i class="ri-checkbox-circle-line text-4xl mb-2 block text-green-400"></i>
        <p>Aucune classe en attente de validation.</p>
        <p class="text-xs mt-1">Toutes les notes ont déjà été validées et publiées aux étudiants.</p>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>