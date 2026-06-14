<?php
namespace Models;

class Progression {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function enregistrerScore($etudiant_id, $lecon_id, $score) {
        $valide = $score >= 50 ? true : false;

        $stmt = $this->pdo->prepare("
            INSERT INTO progressions (etudiant_id, lecon_id, note_obtenue, valide)
            VALUES (?, ?, ?, ?)
            ON CONFLICT (etudiant_id, lecon_id) 
            DO UPDATE SET note_obtenue = EXCLUDED.note_obtenue, valide = EXCLUDED.valide
        ");
        $stmt->execute([$etudiant_id, $lecon_id, $score, $valide ? 1 : 0]);

        if ($valide) {
            return $this->verifierEtAttribuerCertificat($etudiant_id, $lecon_id);
        }
        return false;
    }

    private function verifierEtAttribuerCertificat($etudiant_id, $lecon_id) {
        $stmt = $this->pdo->prepare("
            SELECT c.module_id FROM lecons l JOIN cours c ON l.cours_id = c.id WHERE l.id = ?
        ");
        $stmt->execute([$lecon_id]);
        $module_id = $stmt->fetchColumn();

        $stmt_check = $this->pdo->prepare("
            SELECT 
                (SELECT COUNT(*) FROM lecons l2 JOIN cours c2 ON l2.cours_id = c2.id WHERE c2.module_id = :mod_id) as total,
                (SELECT COUNT(*) FROM progressions p 
                 JOIN lecons l3 ON p.lecon_id = l3.id JOIN cours c3 ON l3.cours_id = c3.id
                 WHERE c3.module_id = :mod_id AND p.etudiant_id = :et_id AND p.valide = 1) as validees
        ");
        $stmt_check->execute(['mod_id' => $module_id, 'et_id' => $etudiant_id]);
        $verif = $stmt_check->fetch(\PDO::FETCH_ASSOC);

        if ($verif['total'] > 0 && $verif['total'] == $verif['validees']) {
            $stmt_cert = $this->pdo->prepare("
                INSERT INTO certificats (etudiant_id, module_id) VALUES (?, ?) ON CONFLICT (etudiant_id, module_id) DO NOTHING
            ");
            return $stmt_cert->execute([$etudiant_id, $module_id]);
        }
        return false;
    }
}
?>
