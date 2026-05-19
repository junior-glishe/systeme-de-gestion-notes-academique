<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole(['enseignant', 'admin']);
$pageTitle = "Saisie des notes";

date_default_timezone_set('Africa/Porto-Novo');
$user = currentUser();

$ens = $pdo->prepare("SELECT id FROM enseignants WHERE utilisateur_id=?");
$ens->execute([$user['id']]);
$ens = $ens->fetch();
$matieres = [];

if ($ens) {
  $stmt = $pdo->prepare("SELECT m.*, c.nom AS classe FROM matieres m LEFT JOIN classes c ON c.id = m.classe_id WHERE m.enseignant_id = ? ORDER BY m.nom");
  $stmt->execute([$ens['id']]);
  $matieres = $stmt->fetchAll();
}

include __DIR__ . '/../../includes/header.php';

$msg = null; $err = null;
$matiere_id = (int)($_GET['matiere'] ?? ($matieres[0]['id'] ?? 0));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  checkCsrf();
  $action = $_POST['action'] ?? '';

  if ($action === 'save') {
    $mid = (int)$_POST['matiere_id'];
    $eid = (int)$_POST['etudiant_id'];
    $i = (float)$_POST['interro'];
    $d = (float)$_POST['devoir'];
    if ($i < 0 || $i > 20 || $d < 0 || $d > 20) {
      $err = "Les notes doivent être comprises entre 0 et 20.";
    } else {
      $check = $pdo->prepare("SELECT validee FROM notes WHERE etudiant_id = ? AND matiere_id = ?");
      $check->execute([$eid, $mid]);
      $row = $check->fetch();
      if ($row && (int)$row['validee'] === 1) {
        $err = "Cette note est déjà validée et ne peut plus être modifiée.";
      } else {
        $stmt = $pdo->prepare("INSERT INTO notes (etudiant_id, matiere_id, note_interro, note_devoir, semestre)
                  VALUES (?, ?, ?, ?, 'S1')
                  ON DUPLICATE KEY UPDATE note_interro = VALUES(note_interro), note_devoir = VALUES(note_devoir)");
        $stmt->execute([$eid, $mid, $i, $d]);
        $msg = "Note enregistrée (brouillon). Le responsable validera et publiera.";
      }
    }
    $matiere_id = $mid;
  } elseif ($action === 'delete') {
    $nid = (int)$_POST['note_id'];
    $check = $pdo->prepare("SELECT validee FROM notes WHERE id = ?");
    $check->execute([$nid]);
    $row = $check->fetch();
    if ($row && (int)$row['validee'] === 1) {
      $err = "Impossible de supprimer une note validée.";
    } else {
      $pdo->prepare("DELETE FROM notes WHERE id = ?")->execute([$nid]);
      $msg = "Note supprimée.";
    }
  }
}

$etudiants = [];
$matSel = null;
if ($matiere_id) {
  $stmt = $pdo->prepare("SELECT * FROM matieres WHERE id = ?");
  $stmt->execute([$matiere_id]);
  $matSel = $stmt->fetch();
  if ($matSel) {
    // N'afficher que les étudiants dont la note n'est pas encore validée
    $stmt = $pdo->prepare("SELECT e.id, u.nom, u.prenom, e.matricule, n.id AS note_id, n.note_interro, n.note_devoir
      FROM etudiants e
      JOIN utilisateurs u ON u.id = e.utilisateur_id
      LEFT JOIN notes n ON n.etudiant_id = e.id AND n.matiere_id = ?
      WHERE e.classe_id = ?
        AND (n.id IS NULL OR n.validee = 0)
      ORDER BY u.nom");
    $stmt->execute([$matiere_id, $matSel['classe_id']]);
    $etudiants = $stmt->fetchAll();
  }
}
$csrf = csrfToken();
function calculerMoyenneEnseignant($i, $d) {
  return $i && $d ? round($i * 0.3 + $d * 0.7, 2) : null;
}
?>

<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
        <i class="fas fa-pen-fancy text-2xl text-blue-600"></i>
      </div>
      <div>
        <h2 class="text-lg font-bold text-slate-800">Saisie des notes</h2>
        <p class="text-sm text-slate-500">Saisissez chaque note en brouillon. Le responsable académique validera et publiera.</p>
      </div>
    </div>
  </div>
</div>

<?php if ($msg): ?>
  <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-xl text-sm border border-green-200"><i class="fas fa-check-circle mr-2"></i><?= e($msg) ?></div>
<?php endif; ?>
<?php if ($err): ?>
  <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-xl text-sm border border-red-200"><i class="fas fa-exclamation-triangle mr-2"></i><?= e($err) ?></div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-5">
  <form method="GET" class="flex flex-wrap items-end gap-4">
    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1">Matière</label>
      <select name="matiere" onchange="this.form.submit()" class="px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none min-w-[280px]">
        <?php foreach ($matieres as $m): ?>
          <option value="<?= $m['id'] ?>" <?= $m['id'] == $matiere_id ? 'selected' : '' ?>>
            <?= e($m['nom']) ?> — <?= e($m['classe']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="text-sm text-slate-400 ml-auto">
      <i class="fas fa-info-circle mr-1"></i>Enregistrez en brouillon. Le responsable validera et publiera aux étudiants.
    </div>
  </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
  <div class="p-4 border-b border-slate-100 bg-slate-50">
    <h3 class="font-semibold text-slate-800">
      <i class="fas fa-list mr-2 text-blue-600"></i>
      <?= e($matSel['nom'] ?? 'Aucune matière') ?> — Notes en brouillon
    </h3>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="px-5 py-3 text-left">Matricule</th>
          <th class="px-5 py-3 text-left">Étudiant</th>
          <th class="px-5 py-3 text-center w-28">Interro /20</th>
          <th class="px-5 py-3 text-center w-28">Devoir /20</th>
          <th class="px-5 py-3 text-center w-24">Moyenne</th>
          <th class="px-5 py-3 text-center w-56">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($etudiants as $et):
          $moy = calculerMoyenneEnseignant((float)($et['note_interro'] ?? 0), (float)($et['note_devoir'] ?? 0));
          $hasNote = !empty($et['note_id']);
        ?>
          <tr class="hover:bg-slate-50 transition">
            <td class="px-5 py-3 font-mono text-xs text-slate-600"><?= e($et['matricule']) ?></td>
            <td class="px-5 py-3 font-medium text-slate-800"><?= e($et['nom'] . ' ' . $et['prenom']) ?></td>
            <form method="POST">
              <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
              <input type="hidden" name="action" value="save">
              <input type="hidden" name="matiere_id" value="<?= $matiere_id ?>">
              <input type="hidden" name="etudiant_id" value="<?= $et['id'] ?>">
              <td class="px-5 py-2"><input type="number" step="0.25" min="0" max="20" name="interro" value="<?= e($et['note_interro']) ?>" class="w-20 px-3 py-2 border border-slate-200 rounded-xl text-center focus:ring-2 focus:ring-blue-500 outline-none"></td>
              <td class="px-5 py-2"><input type="number" step="0.25" min="0" max="20" name="devoir" value="<?= e($et['note_devoir']) ?>" class="w-20 px-3 py-2 border border-slate-200 rounded-xl text-center focus:ring-2 focus:ring-blue-500 outline-none"></td>
              <td class="px-5 py-2 text-center font-semibold <?= $moy !== null && $moy >= 10 ? 'text-green-600' : 'text-red-600' ?>"><?= $moy !== null ? $moy . '/20' : '—' ?></td>
              <td class="px-5 py-2 text-center space-x-1">
                <button class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-xl transition"><i class="fas fa-save mr-1"></i>Enregistrer</button>
            </form>
            <?php if ($hasNote): ?>
              <form method="POST" class="inline" onsubmit="return confirm('Supprimer cette note ?')">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="note_id" value="<?= $et['note_id'] ?>">
                <button class="px-2 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded-xl transition"><i class="fas fa-trash"></i></button>
              </form>
            <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($etudiants)): ?>
          <tr><td colspan="6" class="text-center py-12 text-slate-400">
            <i class="fas fa-check-double text-4xl mb-2 block text-green-400"></i>
            Aucun étudiant en attente — toutes les notes ont été approuvées par le responsable.
          </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
