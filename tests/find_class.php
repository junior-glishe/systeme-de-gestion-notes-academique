<?php
require __DIR__ . '/../config/database.php';
$r = $pdo->query("SELECT c.id,c.nom,COUNT(n.id) nb FROM classes c JOIN etudiants e ON e.classe_id=c.id JOIN notes n ON n.etudiant_id=e.id WHERE n.validee=0 GROUP BY c.id HAVING nb>0 LIMIT 1")->fetch();
if ($r) echo json_encode($r) . PHP_EOL;
else echo "null\n";
