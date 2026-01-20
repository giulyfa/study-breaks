<?php
function checkAndResetStreak($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT ultima_sessione, streak FROM utenti WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();

    if ($user_data && $user_data['ultima_sessione']) {
        $oggi = new DateTime(date('Y-m-d'));
        $ultima = new DateTime($user_data['ultima_sessione']);

        $intervallo = $oggi->diff($ultima);
        if ($intervallo->days > 1) {
            $stmtReset = $pdo->prepare("UPDATE utenti SET streak = 0 WHERE id = ?");
            $stmtReset->execute([$user_id]);

            if(isset($_SESSION['streak'])) {
                $_SESSION['streak'] = 0;
            }
            return 0; 
        }
        return $user_data['streak'];
    }
    return 0;
}
?>