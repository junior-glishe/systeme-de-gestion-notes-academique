<?php
require_once __DIR__ . '/../../includes/auth.php';
requireRole(['admin', 'responsable']);
$pageTitle = 'Statistiques';

date_default_timezone_set('Africa/Porto-Novo');

$parMatiere = $pdo->query("SELECT m.nom, ROUND(AVG(n.moyenne), 2) moy, MIN(n.moyenne) mn, MAX(n.moyenne) mx
    FROM notes n JOIN matieres m ON n.matiere_id = m.id GROUP BY m.id")->fetchAll();

$tauxReussite = $pdo->query("SELECT
    ROUND(100 * SUM(CASE WHEN moyenne >= 10 THEN 1 ELSE 0 END) / COUNT(*), 1) AS taux,
    COUNT(*) total 
    FROM notes")->fetch();

$repartition = $pdo->query("SELECT
    SUM(moyenne >= 16) tb, 
    SUM(moyenne >= 14 AND moyenne < 16) b, 
    SUM(moyenne >= 12 AND moyenne < 14) ab,
    SUM(moyenne >= 10 AND moyenne < 12) p, 
    SUM(moyenne < 10) ins 
    FROM notes")->fetch();

include __DIR__ . '/../../includes/header.php';
?>

<div class="mb-6">
  <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center">
        <i class="ri-bar-chart-line text-2xl text-blue-600"></i>
      </div>
      <div>
        <h2 class="text-lg font-bold text-slate-800">Statistiques globales</h2>
        <p class="text-sm text-slate-500">Analyse des performances académiques</p>
      </div>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
  <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 text-white rounded-2xl p-5 shadow-lg">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm opacity-90">Taux de réussite global</div>
        <div class="text-3xl font-bold mt-1"><?= $tauxReussite['taux'] ?>%</div>
        <div class="text-xs opacity-80 mt-1">sur <?= $tauxReussite['total'] ?> notes enregistrées</div>
      </div>
      <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
        <i class="ri-medal-line text-2xl"></i>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
    <div class="grid grid-cols-2 gap-3">
      <div class="text-center p-2 bg-slate-50 rounded-xl">
        <div class="text-xs text-slate-500">Matières</div>
        <div class="text-xl font-bold text-slate-800"><?= count($parMatiere) ?></div>
      </div>
      <div class="text-center p-2 bg-slate-50 rounded-xl">
        <div class="text-xs text-slate-500">Notes totales</div>
        <div class="text-xl font-bold text-slate-800"><?= $tauxReussite['total'] ?></div>
      </div>
    </div>
  </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
  <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
    <h3 class="font-semibold text-slate-800 mb-3 flex items-center gap-2 text-sm">
      <i class="ri-pie-chart-line text-blue-600"></i> Répartition des mentions
    </h3>
    <div class="max-w-xs mx-auto">
      <canvas id="chartMentions" height="250" width="250"></canvas>
    </div>
  </div>

  <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
    <h3 class="font-semibold text-slate-800 mb-3 flex items-center gap-2 text-sm">
      <i class="ri-bar-chart-2-line text-blue-600"></i> Performance par matière
    </h3>
    <canvas id="chartMatieres" height="250"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctxMatieres = document.getElementById('chartMatieres').getContext('2d');
  new Chart(ctxMatieres, {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_column($parMatiere, 'nom')) ?>,
      datasets: [{
        label: 'Moyenne /20',
        data: <?= json_encode(array_column($parMatiere, 'moy')) ?>,
        backgroundColor: 'rgba(59, 130, 246, 0.7)',
        borderRadius: 6,
        barPercentage: 0.7,
        categoryPercentage: 0.8
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
            label: (ctx) => `Moyenne: ${ctx.raw}/20`
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          max: 20,
          grid: {
            color: '#e2e8f0'
          },
          title: {
            display: true,
            text: 'Moyenne /20',
            font: {
              size: 11
            }
          }
        },
        x: {
          ticks: {
            font: {
              size: 11
            },
            rotation: 0
          },
          grid: {
            display: false
          }
        }
      }
    }
  });

  const ctxMentions = document.getElementById('chartMentions').getContext('2d');
  new Chart(ctxMentions, {
    type: 'doughnut',
    data: {
      labels: ['Très Bien (16-20)', 'Bien (14-16)', 'Assez Bien (12-14)', 'Passable (10-12)', 'Insuffisant (<10)'],
      datasets: [{
        data: [<?= (int)$repartition['tb'] ?>, <?= (int)$repartition['b'] ?>, <?= (int)$repartition['ab'] ?>, <?= (int)$repartition['p'] ?>, <?= (int)$repartition['ins'] ?>],
        backgroundColor: ['#059669', '#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
        borderWidth: 0,
        hoverOffset: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      cutout: '60%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 10,
            font: {
              size: 10
            },
            padding: 8
          }
        },
        tooltip: {
          callbacks: {
            label: (ctx) => {
              const total = <?= (int)$repartition['tb'] + (int)$repartition['b'] + (int)$repartition['ab'] + (int)$repartition['p'] + (int)$repartition['ins'] ?>;
              const value = ctx.raw;
              const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
              return `${ctx.label}: ${value} note(s) (${percentage}%)`;
            }
          }
        }
      }
    }
  });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>