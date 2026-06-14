<?php
header('Content-Type: application/json');
require_once 'config/db.php';
require_once 'config/auth.php';
require_once 'models/Progression.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_SESSION['role'] !== 'etudiant') {
    echo json_encode(['success' => false, 'message' => 'Action non autorisée.']);
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Sécurité CSRF invalide.']);
    exit();
}

$lecon_id = (int)$_POST['lecon_id'];
$evaluation_id = (int)$_POST['evaluation_id'];
$reponses_etudiant = $_POST['reponse'] ?? [];

$stmt = $pdo->prepare("SELECT id, reponse_correcte FROM questions WHERE evaluation_id = ?");
$stmt->execute([$evaluation_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($questions);
$correctes = 0;

foreach ($questions as $q) {
    if (isset($reponses_etudiant[$q['id']]) && $reponses_etudiant[$q['id']] === $q['reponse_correcte']) {
        $correctes++;
    }
}

$score = $total > 0 ? round(($correctes / $total) * 100) : 0;

$progressionModel = new \Models\Progression($pdo);
$certificat_decerne = $progressionModel->enregistrerScore($_SESSION['user_id'], $lecon_id, $score);

echo json_encode([
    'success' => true,
    'score' => $score,
    'valide' => $score >= 50,
    'certificat' => $certificat_decerne
]);
exit();
?>
