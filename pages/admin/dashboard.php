<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole(['admin', 'responsable']);
$pageTitle = 'Tableau de bord';

date_default_timezone_set('Africa/Porto-Novo');

$stats = [
  'etudiants' => $pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn(),
  'enseignants' => $pdo->query("SELECT COUNT(*) FROM enseignants")->fetchColumn(),
  'classes' => $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn(),
  'matieres' => $pdo->query("SELECT COUNT(*) FROM matieres")->fetchColumn(),
];
$moyClasse = $pdo->query("SELECT c.nom, ROUND(AVG(n.moyenne),2) as moy
    FROM notes n JOIN etudiants e ON n.etudiant_id=e.id
    JOIN classes c ON e.classe_id=c.id GROUP BY c.id")->fetchAll();
$top = $pdo->query("SELECT e.nom, e.prenom, e.matricule, ROUND(AVG(n.moyenne),2) as moy
    FROM notes n JOIN etudiants e ON n.etudiant_id=e.id
    GROUP BY e.id ORDER BY moy DESC LIMIT 5")->fetchAll();

$userName = 'Utilisateur';
$userRole = $_SESSION['role'] ?? '';

if (isset($_SESSION['user_id'])) {
  $stmt = $pdo->prepare("SELECT nom, prenom FROM utilisateurs WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch();
  if ($user) {
    $userName = $user['prenom'] . ' ' . $user['nom'];
  }
}

function getGreeting()
{
  $hour = (int)date('H');
  if ($hour >= 5 && $hour < 12) {
    return ['Bonjour', 'fas fa-sun', 'text-yellow-400', '🌞', 'Matinée'];
  } elseif ($hour >= 12 && $hour < 18) {
    return ['Bon après-midi', 'fas fa-cloud-sun', 'text-orange-400', '', 'Après-midi'];
  } elseif ($hour >= 18 && $hour < 22) {
    return ['Bonsoir', 'fas fa-moon', 'text-indigo-300', '🌙', 'Soirée'];
  } else {
    return ['Bonne nuit', 'fas fa-star-and-crescent', 'text-purple-300', '🌙✨', 'Nuit'];
  }
}

list($greeting, $iconClass, $iconColor, $emoji, $dayPeriod) = getGreeting();
$currentDate = date('l d F Y');
$currentTime = date('H:i:s');

include __DIR__ . '/../../includes/header.php';
?>


<style>
  @keyframes pulse {

    0%,
    100% {
      opacity: 1;
    }

    50% {
      opacity: 0.7;
    }
  }

  .clock-animation {
    animation: pulse 2s infinite;
  }

  .greeting-card {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  }
</style>

<div class="mb-8">
  <div class="greeting-card rounded-2xl p-6 text-white shadow-xl">
    <div class="flex items-center justify-between flex-wrap gap-4">
      <div class="flex items-center gap-5">
        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm shadow-inner">
          <i class="<?= $iconClass ?> text-3xl"></i>
        </div>
        <div>
          <div class="text-sm font-medium text-white/80 mb-1">
            <?= $greeting ?> <span class="ml-2 px-2 py-0.5 bg-white/20 rounded-full text-xs"><?= ucfirst($userRole) ?></span>
          </div>
          <div class="text-2xl font-bold"><?= e($userName) ?> <?= $emoji ?></div>
          <div class="text-sm text-white/70 mt-2">
            <i class="fas fa-calendar-alt mr-2"></i><?= $currentDate ?>
          </div>
        </div>
      </div>
      <div class="flex items-center gap-4">
        <div class="bg-white/10 rounded-xl px-5 py-3 text-center backdrop-blur-sm">
          <div class="text-2xl font-bold clock-animation">
            <i class="fas fa-clock mr-2 text-yellow-300"></i>
            <span id="liveClock"><?= $currentTime ?></span>
          </div>
          <div class="text-xs text-white/70 mt-1"><?= $dayPeriod ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
  <?php
  $cards = [
    ['Étudiants', $stats['etudiants'], 'fas fa-user-graduate', 'from-blue-500 to-blue-700', 'bg-blue-50', 'text-blue-700'],
    ['Enseignants', $stats['enseignants'], 'fas fa-chalkboard-user', 'from-emerald-500 to-emerald-700', 'bg-emerald-50', 'text-emerald-700'],
    ['Classes', $stats['classes'], 'fas fa-building', 'from-violet-500 to-violet-700', 'bg-violet-50', 'text-violet-700'],
    ['Matières', $stats['matieres'], 'fas fa-book-open', 'from-amber-500 to-amber-700', 'bg-amber-50', 'text-amber-700'],
  ];
  foreach ($cards as $c): ?>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-lg transition duration-300 hover:scale-[1.02] group">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-sm text-slate-500 font-medium mb-1"><?= $c[0] ?></div>
          <div class="text-3xl font-bold text-slate-800 tracking-tight"><?= $c[1] ?></div>
          <div class="text-xs text-slate-400 mt-2">Total inscrits</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?= $c[3] ?> flex items-center justify-center text-white shadow-md group-hover:shadow-lg transition">
          <i class="<?= $c[2] ?> text-xl"></i>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="grid lg:grid-cols-3 gap-6 mt-6">
  <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="font-semibold text-slate-800 text-lg"><i class="fas fa-chart-bar text-blue-500 mr-2"></i>Moyenne par classe</h2>
        <p class="text-xs text-slate-400 mt-0.5">Performance académique</p>
      </div>
      <span class="text-xs px-3 py-1.5 bg-slate-100 text-slate-600 rounded-full font-medium">Année 2024-2025</span>
    </div>
    <canvas id="chartClasses" height="150"></canvas>
  </div>

  <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition">
    <div class="flex items-center justify-between mb-5">
      <div>
        <h2 class="font-semibold text-slate-800 text-lg"><i class="fas fa-trophy text-amber-500 mr-2"></i>Top 5 étudiants</h2>
        <p class="text-xs text-slate-400 mt-0.5">Meilleures moyennes générales</p>
      </div>
      <i class="fas fa-medal text-2xl text-amber-400"></i>
    </div>
    <div class="space-y-4">
      <?php foreach ($top as $i => $t): ?>
        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition">
          <div class="w-10 h-10 rounded-xl <?= $i === 0 ? 'bg-gradient-to-br from-amber-400 to-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600' ?> flex items-center justify-center font-bold text-sm">
            <?php if ($i === 0): ?>
              <i class="fas fa-crown text-sm"></i>
            <?php else: ?>
              #<?= $i + 1 ?>
            <?php endif; ?>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-slate-700 truncate"><?= e($t['prenom'] . ' ' . $t['nom']) ?></div>
            <div class="text-xs text-slate-500"><?= e($t['matricule']) ?></div>
          </div>
          <div class="text-sm font-bold text-blue-700 bg-blue-50 px-2 py-1 rounded-lg"><?= $t['moy'] ?>/20</div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
  function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const timeString = hours + ':' + minutes + ':' + seconds;

    const clockElement = document.getElementById('liveClock');
    if (clockElement) {
      clockElement.textContent = timeString;
    }
  }
  setInterval(updateClock, 1000);
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const data = <?= json_encode($moyClasse) ?>;
  new Chart(document.getElementById('chartClasses'), {
    type: 'bar',
    data: {
      labels: data.map(d => d.nom),
      datasets: [{
        label: 'Moyenne /20',
        data: data.map(d => d.moy),
        backgroundColor: 'rgba(59,130,246,.7)',
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Moyenne: ' + context.raw + '/20';
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          max: 20,
          title: {
            display: true,
            text: 'Moyenne (/20)'
          }
        }
      }
    }
  });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>