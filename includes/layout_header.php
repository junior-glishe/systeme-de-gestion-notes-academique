<?php



require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

requireLogin();

$user = currentUser();
$role = $user['role'];

$menus = [
  'etudiant' => [
    ['key' => 'dashboard', 'label' => 'Tableau de bord', 'icon' => 'fa-gauge', 'url' => '/gestion-notes/pages/etudiant/dashboard.php'],
    ['key' => 'notes', 'label' => 'Mes notes', 'icon' => 'fa-clipboard-list', 'url' => '/gestion-notes/pages/etudiant/notes.php'],
    ['key' => 'bulletin', 'label' => 'Mon bulletin', 'icon' => 'fa-file-lines', 'url' => '/gestion-notes/pages/etudiant/bulletin.php'],
  ],
  'enseignant' => [
    ['key' => 'dashboard', 'label' => 'Tableau de bord', 'icon' => 'fa-gauge', 'url' => '/gestion-notes/pages/enseignant/dashboard.php'],
    ['key' => 'saisie', 'label' => 'Saisie des notes', 'icon' => 'fa-pen-to-square', 'url' => '/gestion-notes/pages/enseignant/saisie.php'],
    ['key' => 'etudiants', 'label' => 'Mes étudiants', 'icon' => 'fa-user-graduate', 'url' => '/gestion-notes/pages/enseignant/etudiants.php'],
  ],
  'admin' => [
    ['key' => 'dashboard', 'label' => 'Tableau de bord', 'icon' => 'fa-gauge', 'url' => '/gestion-notes/pages/admin/dashboard.php'],
    ['key' => 'utilisateurs', 'label' => 'Utilisateurs', 'icon' => 'fa-users', 'url' => '/gestion-notes/pages/admin/utilisateurs.php'],
    ['key' => 'classes', 'label' => 'Classes', 'icon' => 'fa-school', 'url' => '/gestion-notes/pages/admin/classes.php'],
    ['key' => 'matieres', 'label' => 'Matières', 'icon' => 'fa-book', 'url' => '/gestion-notes/pages/admin/matieres.php'],
    ['key' => 'releve', 'label' => 'Relevés par classe', 'icon' => 'fa-trophy', 'url' => '/gestion-notes/pages/admin/releve.php'],
    ['key' => 'statistiques', 'label' => 'Statistiques', 'icon' => 'fa-chart-line', 'url' => '/gestion-notes/pages/admin/statistiques.php'],
  ],
];
$currentMenu = $menus[$role] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Gestion des Notes') ?> | Institut</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#eef2ff',
              100: '#e0e7ff',
              500: '#6366f1',
              600: '#4f46e5',
              700: '#4338ca',
              800: '#3730a3',
              900: '#312e81'
            }
          }
        }
      }
    }
  </script>
  <style>
    body {
      font-family: 'Inter', system-ui, sans-serif;
    }

    .sidebar-collapsed {
      width: 5rem;
    }

    .sidebar-collapsed .label,
    .sidebar-collapsed .brand-text {
      display: none;
    }

    .menu-item.active {
      background: linear-gradient(90deg, #4f46e5, #6366f1);
      color: white;
    }

    .menu-item.active i {
      color: white;
    }
  </style>
</head>

<body class="bg-slate-100">
  <div class="flex min-h-screen">
    <aside id="sidebar" class="bg-slate-900 text-slate-200 w-64 flex flex-col fixed inset-y-0 left-0 z-30 transition-all duration-300">
      <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800">
        <div class="flex items-center gap-2">
          <div class="w-9 h-9 rounded-lg bg-brand-600 flex items-center justify-center"><i class="fas fa-graduation-cap text-white"></i></div>
          <span class="brand-text font-bold text-white">GestNotes</span>
        </div>
        <button onclick="document.getElementById('sidebar').classList.toggle('sidebar-collapsed')" class="text-slate-400 hover:text-white">
          <i class="fas fa-bars"></i>
        </button>
      </div>
      <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <?php foreach ($currentMenu as $m): ?>
          <a href="<?= e($m['url']) ?>" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-colors <?= ($activeMenu ?? '') === $m['key'] ? 'active' : '' ?>">
            <i class="fas <?= e($m['icon']) ?> w-5 text-center text-slate-400"></i>
            <span class="label text-sm font-medium"><?= e($m['label']) ?></span>
          </a>
        <?php endforeach; ?>
      </nav>
      <div class="p-3 border-t border-slate-800">
        <a href="/gestion-notes/pages/auth/logout.php" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-600/20 text-red-400 transition-colors">
          <i class="fas fa-right-from-bracket w-5 text-center"></i>
          <span class="label text-sm font-medium">Déconnexion</span>
        </a>
      </div>
    </aside>

    <div id="mainArea" class="flex-1 ml-64 transition-all duration-300">
      <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-20 flex items-center justify-between px-6">
        <h1 class="text-lg font-semibold text-slate-800"><?= e($pageTitle ?? 'Tableau de bord') ?></h1>
        <div class="flex items-center gap-4">
          <button class="relative text-slate-500 hover:text-brand-600">
            <i class="fas fa-bell text-lg"></i>
            <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
          </button>
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center font-semibold">
              <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
            </div>
            <div class="hidden md:block">
              <div class="text-sm font-semibold text-slate-800"><?= e($user['prenom'] . ' ' . $user['nom']) ?></div>
              <div class="text-xs text-slate-500 capitalize"><?= e($user['role']) ?></div>
            </div>
          </div>
        </div>
      </header>
      <main class="p-6">