<?php
require_once __DIR__ . '/auth.php';
requireLogin();
$user = currentUser();
$role = $user['role'];
$base = baseUrl();

$menus = [
  'admin' => [
    ['Tableau de bord', '/pages/admin/dashboard.php', 'dashboard'],
    ['Étudiants', '/pages/admin/etudiants.php', 'user'],
    ['Enseignants', '/pages/admin/enseignants.php', 'user-star'],
    ['Classes', '/pages/admin/classes.php', 'building'],
    ['Matières', '/pages/admin/matieres.php', 'book'],
    ['Relevé collectif', '/pages/admin/releve.php', 'file-list'],
    ['Statistiques', '/pages/admin/statistiques.php', 'bar-chart'],
    ['Gérer les utilisateurs', '/pages/admin/compte.php', 'checkbox-circle'],
  ],
  'enseignant' => [
    ['Tableau de bord', '/pages/enseignant/dashboard.php', 'dashboard'],
    ['Saisir notes', '/pages/enseignant/notes.php', 'edit'],
    ['Saisie individuelle', '/pages/enseignant/saisie.php', 'edit-2'],
    ['Mes matières', '/pages/enseignant/matieres.php', 'book'],
    ['Mes étudiants', '/pages/enseignant/etudiants.php', 'team'],
  ],
  'etudiant' => [
    ['Tableau de bord', '/pages/etudiant/dashboard.php', 'dashboard'],
    ['Mes notes', '/pages/etudiant/notes.php', 'file-list'],
    ['Mon bulletin', '/pages/etudiant/bulletin.php', 'file-copy'],
    ['Historique', '/pages/etudiant/historique.php', 'history'],
  ],
  'responsable' => [
    ['Tableau de bord', '/pages/admin/dashboard.php', 'dashboard'],
    ['Validation des délibérations', '/pages/admin/deliberations.php', 'check-double'],
    ['Statistiques', '/pages/admin/statistiques.php', 'bar-chart'],
    ['Relevé collectif', '/pages/admin/releve.php', 'file-list'],
  ],
];
$menu = $menus[$role] ?? [];
$current = $_SERVER['SCRIPT_NAME'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Gestion des Notes') ?> · Institut Académique</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

  <!-- Configuration Tailwind -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              DEFAULT: '#1e40af',
              50: '#eff6ff',
              100: '#dbeafe',
              500: '#3b82f6',
              600: '#2563eb',
              700: '#1d4ed8',
              900: '#1e3a8a'
            },
            accent: '#0ea5e9'
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', 'sans-serif']
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8fafc;
    }

    .sidebar-link {
      transition: all 0.2s ease;
      position: relative;
    }

    .sidebar-link.active {
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.15), transparent);
      border-left: 3px solid #3b82f6;
      color: #1d4ed8;
      font-weight: 600;
    }

    .sidebar-link.active i {
      color: #2563eb;
    }

    .sidebar-link:hover {
      background: rgba(59, 130, 246, 0.08);
      transform: translateX(4px);
    }

    /* Scrollbar personnalisée */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }

    ::-webkit-scrollbar-track {
      background: #e2e8f0;
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
      background: #94a3b8;
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #64748b;
    }

    @media print {
      .no-print {
        display: none !important;
      }

      body {
        background: white;
        padding: 0;
        margin: 0;
      }

      main {
        margin: 0;
        padding: 0;
      }
    }

    .card-hover {
      transition: all 0.3s ease;
    }

    .card-hover:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }
  </style>
</head>

<body class="bg-slate-50 min-h-screen">
  <div class="flex min-h-screen">

    <aside class="no-print w-64 bg-white border-r border-slate-200 flex-shrink-0 hidden md:flex flex-col shadow-sm">
      <div class="p-6 border-b border-slate-100">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-600 to-primary-900 flex items-center justify-center text-white shadow-md">
            <i class="ri-graduation-cap-fill text-xl"></i>
          </div>
          <div>
            <div class="font-bold text-slate-800 leading-tight text-lg">Institut-Lumiere</div>
            <div class="text-xs text-slate-500">Gestion académique</div>
          </div>
        </div>
      </div>

      <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto">
        <?php foreach ($menu as $m):
          $active = str_ends_with($current, $m[1]) ? 'active' : '';
        ?>
          <a href="<?= $base . $m[1] ?>" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-slate-600 text-sm transition <?= $active ?>">
            <i class="ri-<?= $m[2] ?>-line text-xl"></i>
            <span><?= e($m[0]) ?></span>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="p-4 border-t border-slate-100 mt-auto">
        <a href="<?= $base ?>/pages/logout.php" class="flex items-center gap-3 text-sm text-slate-600 hover:text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg transition-all duration-200">
          <i class="ri-logout-box-r-line text-xl"></i>
          <span>Déconnexion</span>
        </a>
      </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0">

      <header class="no-print bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
        <div>
          <h1 class="text-xl font-bold text-slate-800"><?= e($pageTitle ?? 'Tableau de bord') ?></h1>
          <p class="text-xs text-slate-500 mt-0.5">
            <i class="ri-calendar-line text-xs mr-1"></i>
            <?= ucfirst($role) ?> · Année scolaire 2025-2026
          </p>
        </div>

        <div class="flex items-center gap-4">
          <button class="relative text-slate-500 hover:text-primary-600 transition-colors">
            <i class="ri-notification-3-line text-xl"></i>
            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span>
          </button>

          <div class="flex items-center gap-3">
            <div class="text-right hidden md:block">
              <div class="text-sm font-semibold text-slate-700"><?= e($user['prenom'] . ' ' . $user['nom']) ?></div>
              <div class="text-xs text-slate-500"><?= e($user['email']) ?></div>
            </div>
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-900 text-white flex items-center justify-center font-semibold shadow-md">
              <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
            </div>
          </div>
        </div>
      </header>

      <div class="flex-1 p-6">