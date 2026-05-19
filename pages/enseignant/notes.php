<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole(['enseignant', 'admin']);
$pageTitle = 'Saisir les notes';

date_default_timezone_set('Africa/Porto-Novo');

$user = currentUser();
$ens = $pdo->prepare("SELECT * FROM enseignants WHERE utilisateur_id=?");
$ens->execute([$user['id']]);
$ens = $ens->fetch();
$ensId = $ens['id'] ?? 0;

$matieres = $pdo->prepare("SELECT m.*, c.nom AS classe FROM matieres m LEFT JOIN classes c ON m.classe_id = c.id WHERE m.enseignant_id = ?");
$matieres->execute([$ensId]);
$matieres = $matieres->fetchAll();

$matiereId = (int)($_GET['matiere'] ?? ($matieres[0]['id'] ?? 0));
$matiere = null;
foreach ($matieres as $m) {
  if ($m['id'] == $matiereId) $matiere = $m;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $matiere) {
  checkCsrf();
  $action = $_POST['action'] ?? 'save';

  if ($action === 'save') {
    foreach (($_POST['notes'] ?? []) as $eid => $n) {
      // Ignorer les notes déjà validées (sécurité)
      $check = $pdo->prepare("SELECT validee FROM notes WHERE etudiant_id = ? AND matiere_id = ?");
      $check->execute([(int)$eid, $matiereId]);
      $row = $check->fetch();
      if ($row && (int)$row['validee'] === 1) continue;

      $i = max(0, min(20, (float)($n['interro'] ?? 0)));
      $d = max(0, min(20, (float)($n['devoir'] ?? 0)));
      $stmt = $pdo->prepare("INSERT INTO notes (etudiant_id, matiere_id, note_interro, note_devoir, semestre)
              VALUES (?, ?, ?, ?, 'S1') ON DUPLICATE KEY UPDATE note_interro = VALUES(note_interro), note_devoir = VALUES(note_devoir)");
      $stmt->execute([(int)$eid, $matiereId, $i, $d]);
    }
    $_SESSION['flash'] = 'Notes enregistrées (brouillon). Le responsable académique validera et publiera ces notes.';
  }
  header('Location: notes.php?matiere=' . $matiereId);
  exit;
}

$etudiants = [];
$nbValidees = 0;
$nbEnAttente = 0;
if ($matiere) {
  // On n'affiche QUE les étudiants dont la note n'est pas encore validée
  $stmt = $pdo->prepare("SELECT e.*, n.id AS note_id, n.note_interro, n.note_devoir, n.moyenne, n.validee
        FROM etudiants e
        LEFT JOIN notes n ON n.etudiant_id = e.id AND n.matiere_id = ?
        WHERE e.classe_id = ?
          AND (n.id IS NULL OR n.validee = 0)
        ORDER BY e.nom");
  $stmt->execute([$matiereId, $matiere['classe_id']]);
  $etudiants = $stmt->fetchAll();

  $st = $pdo->prepare("SELECT SUM(validee = 1) AS v, SUM(validee = 0) AS p FROM notes WHERE matiere_id = ?");
  $st->execute([$matiereId]);
  $r = $st->fetch();
  $nbValidees = (int)($r['v'] ?? 0);
  $nbEnAttente = (int)($r['p'] ?? 0);
}

include __DIR__ . '/../../includes/header.php';
?>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="mb-4 px-4 py-3 bg-green-50 text-green-700 rounded-xl text-sm flex items-center gap-2 border border-green-200">
    <i class="ri-checkbox-circle-line"></i><?= e($_SESSION['flash']) ?>
  </div>
  <?php $_SESSION['flash'] = null; ?>
<?php endif; ?>

<!-- Welcome Banner -->
<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
          <i class="ri-pencil-line text-2xl text-blue-600"></i>
        </div>
        <div>
          <h2 class="text-lg font-bold text-slate-800">Saisie des notes</h2>
          <p class="text-sm text-slate-500">Moyenne = 30% Interrogation + 70% Devoir · Enregistrez en brouillon, le responsable validera</p>
        </div>
      </div>
      <div class="flex gap-2">
        <span class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold"><i class="ri-time-line"></i> Brouillon : <?= $nbEnAttente ?></span>
        <span class="px-3 py-1.5 rounded-lg bg-green-100 text-green-700 text-xs font-semibold"><i class="ri-check-double-line"></i> Approuvées : <?= $nbValidees ?></span>
      </div>
    </div>
  </div>
</div>

<!-- Sélection matière -->
<div class="bg-white rounded-2xl p-5 mb-5 shadow-sm border border-slate-100">
  <form method="GET" class="flex flex-wrap gap-4 items-center">
    <div>
      <label class="text-sm font-medium text-slate-700 block mb-1">Matière</label>
      <select name="matiere" onchange="this.form.submit()" class="border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[280px]">
        <?php foreach ($matieres as $m): ?>
          <option value="<?= $m['id'] ?>" <?= $m['id'] == $matiereId ? 'selected' : '' ?>>
            <?= e($m['code'] . ' — ' . $m['nom'] . ' (' . $m['classe'] . ')') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="text-slate-400 text-sm ml-auto">
      <i class="ri-information-line mr-1"></i>Une fois saisies, le responsable académique validera et publiera les notes.
    </div>
  </form>
</div>

<?php if ($matiere): ?>
  <form method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
    <input type="hidden" name="action" value="save">
    <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50 flex-wrap gap-3">
      <div>
        <h2 class="font-semibold text-slate-800 text-lg"><?= e($matiere['nom']) ?></h2>
        <p class="text-xs text-slate-500">Classe : <?= e($matiere['classe']) ?> · Coefficient <?= $matiere['coefficient'] ?> · <?= count($etudiants) ?> étudiant(s) en attente</p>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm flex items-center gap-2 transition">
          <i class="ri-save-line"></i> Enregistrer (brouillon)
        </button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
          <tr>
            <th class="px-5 py-3 text-left">Matricule</th>
            <th class="px-5 py-3 text-left">Étudiant</th>
            <th class="px-5 py-3 text-center w-32">Interro /20</th>
            <th class="px-5 py-3 text-center w-32">Devoir /20</th>
            <th class="px-5 py-3 text-center w-24">Moyenne</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($etudiants as $et): ?>
            <tr class="hover:bg-slate-50 transition">
              <td class="px-5 py-3 font-mono text-xs text-slate-600"><?= e($et['matricule']) ?></td>
              <td class="px-5 py-3 font-medium text-slate-800"><?= e($et['nom'] . ' ' . $et['prenom']) ?></td>
              <td class="px-5 py-3">
                <input type="number" step="0.25" min="0" max="20"
                  name="notes[<?= $et['id'] ?>][interro]"
                  value="<?= $et['note_interro'] ?>"
                  class="note-i w-24 border border-slate-200 rounded-xl px-3 py-2 text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
              </td>
              <td class="px-5 py-3">
                <input type="number" step="0.25" min="0" max="20"
                  name="notes[<?= $et['id'] ?>][devoir]"
                  value="<?= $et['note_devoir'] ?>"
                  class="note-d w-24 border border-slate-200 rounded-xl px-3 py-2 text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
              </td>
              <td class="px-5 py-3 text-center font-bold text-blue-700 bg-blue-50/50 rounded-xl" id="moyenne_<?= $et['id'] ?>">
                <?= $et['moyenne'] ?: '—' ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($etudiants)): ?>
            <tr><td colspan="5" class="text-center py-12 text-slate-400">
              <i class="ri-check-double-line text-4xl mb-2 block text-green-400"></i>
              Toutes les notes ont été validées par le responsable académique pour cette matière.
            </td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </form>

  <script>
    document.querySelectorAll('.note-i, .note-d').forEach(el => {
      el.addEventListener('input', function() {
        const row = this.name.match(/notes\[(\d+)\]/)[1];
        const interro = parseFloat(document.querySelector(`input[name="notes[${row}][interro]"]`).value) || 0;
        const devoir = parseFloat(document.querySelector(`input[name="notes[${row}][devoir]"]`).value) || 0;
        const moyenne = (interro * 0.3 + devoir * 0.7).toFixed(2);
        document.getElementById(`moyenne_${row}`).textContent = moyenne + '/20';
      });
    });
  </script>
<?php elseif (!empty($matieres)): ?>
  <div class="bg-white rounded-2xl p-8 text-center border border-slate-100">
    <i class="ri-survey-line text-5xl text-slate-300 mb-3 block"></i>
    <p class="text-slate-500">Sélectionnez une matière pour commencer la saisie</p>
  </div>
<?php else: ?>
  <div class="bg-white rounded-2xl p-8 text-center border border-slate-100">
    <i class="ri-book-open-line text-5xl text-slate-300 mb-3 block"></i>
    <p class="text-slate-500">Aucune matière assignée</p>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
