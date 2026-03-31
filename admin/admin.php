<?php
session_start();
require_once '../config/db.php';

// Kötelező admin jogosultság
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/index.php');
    exit;
}

$messages = [];

// Felhasználó szerepkör módosítása vagy törlése
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && !empty($_POST['target_id'])) {
        $targetId = intval($_POST['target_id']);
        $action = $_POST['action'];

        if ($action === 'set_admin') {
            $upd = $conn->prepare("UPDATE users SET role='admin' WHERE id = ? AND id != ?");
            $upd->bind_param('ii', $targetId, $_SESSION['user_id']);
            if ($upd->execute()) {
                $messages[] = "Sikeresen adminná teszed a felhasználót (#$targetId).";
            } else {
                $messages[] = "Hiba a felhasználó admin szerepkörének beállításakor.";
            }
            $upd->close();
        } elseif ($action === 'set_user') {
            $upd = $conn->prepare("UPDATE users SET role='user' WHERE id = ? AND id != ?");
            $upd->bind_param('ii', $targetId, $_SESSION['user_id']);
            if ($upd->execute()) {
                $messages[] = "Sikeresen normál felhasználóvá tetted a felhasználót (#$targetId).";
            } else {
                $messages[] = "Hiba a felhasználó szerepkörének visszaállításakor.";
            }
            $upd->close();
        } elseif ($action === 'delete_user') {
            $del = $conn->prepare("DELETE FROM users WHERE id = ? AND id != ?");
            $del->bind_param('ii', $targetId, $_SESSION['user_id']);
            if ($del->execute()) {
                $messages[] = "Sikeresen törölted a felhasználót (#$targetId).";
            } else {
                $messages[] = "Hiba a felhasználó törlésekor.";
            }
            $del->close();
        } elseif ($action === 'delete_appointment' && !empty($_POST['appt_id'])) {
            $apptId = intval($_POST['appt_id']);
            $del = $conn->prepare("DELETE FROM appointments WHERE id = ?");
            $del->bind_param('i', $apptId);
            if ($del->execute()) {
                $messages[] = "Sikeresen törölted a foglalást (#$apptId).";
            } else {
                $messages[] = "Hiba a foglalás törlésekor.";
            }
            $del->close();
        } elseif ($action === 'set_status' && !empty($_POST['appt_id']) && !empty($_POST['status'])) {
            $apptId = intval($_POST['appt_id']);
            $status = in_array($_POST['status'], ['foglalt', 'lemondva'], true) ? $_POST['status'] : 'foglalt';
            $upd = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            $upd->bind_param('si', $status, $apptId);
            if ($upd->execute()) {
                $messages[] = "Sikeresen módosítottad a foglalás státuszát (#$apptId -> $status).";
            } else {
                $messages[] = "Hiba a foglalás státuszának módosításakor.";
            }
            $upd->close();
        }
    }
}

// Felhasználók listája
$usersResult = $conn->query("SELECT id, username, email, role FROM users ORDER BY id ASC");
if (!$usersResult) {
    $messages[] = "Felhasználói lista lekérdezési hiba: " . $conn->error;
    $users = [];
} else {
    $users = $usersResult->fetch_all(MYSQLI_ASSOC);
}

// Időpontok listája
$appResult = $conn->query("SELECT a.id, a.user_id, u.username, u.email, a.date, a.time, h.name AS hairdresser, a.status FROM appointments a LEFT JOIN users u ON a.user_id = u.id LEFT JOIN hairdressers h ON a.hairdresser_id = h.id ORDER BY a.date ASC, a.time ASC");
if (!$appResult) {
    $messages[] = "Időpontok lista lekérdezési hiba: " . $conn->error;
    $appointments = [];
} else {
    $appointments = $appResult->fetch_all(MYSQLI_ASSOC);
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

$usersCount = count($users);
$appointmentCount = count($appointments);

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin felület | Molnár Barber Shop</title>
    <link rel="stylesheet" href="../css/adminstyle.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="../images/favicon.png" />
</head>
<body>
    <nav class="appointment-navbar">
        <div class="nav-left">
            <i class='bx bx-user-circle'></i>
            Üdv, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> (admin)
        </div>
        <div class="nav-center">Admin felület</div>
        <div class="nav-right">
            <a href="../public/index.php" class="back-btn-float"><span class="back-txt">Főoldal</span><span class="back-circle"><i class="bx bx-arrow-back"></i></span></a>
            <a href="../public/logout.php" class="logout-btn"><i class='bx bx-log-out'></i><span>Kijelentkezés</span></a>
        </div>
    </nav>

    <main class="main-noscroll-wrapper">
        <div class="noscroll-grid">
            <section class="section appointments-list-card" style="width: 100%;">
                <h2><i class='bx bx-shield-quarter'></i> Admin műveletek</h2>

                <div class="msg-info" style="margin-bottom: 1rem;">Összes felhasználó: <?php echo $usersCount; ?>, összes foglalás: <?php echo $appointmentCount; ?></div>

                <?php foreach ($messages as $m): ?>
                    <div class="msg-success" style="margin-bottom: 1rem;"><?php echo htmlspecialchars($m); ?></div>
                <?php endforeach; ?>

                <h3>Felhasználók</h3>
                <div class="appointments-grid" style="display: block;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>#</th><th>Felhasználónév</th><th>Email</th><th>Szerepkör</th><th>Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr style="border-top:1px solid #ddd;">
                                <td><?php echo $u['id']; ?></td>
                                <td><?php echo htmlspecialchars($u['username']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['role']); ?></td>
                                <td>
                                    <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                                        <form method="POST" style="display:inline-block; margin:0 4px;">
                                            <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">
                                            <input type="hidden" name="action" value="<?php echo $u['role']==='admin' ? 'set_user' : 'set_admin'; ?>">
                                            <button type="submit" class="btn-primary" style="font-size:0.75rem;"><?php echo $u['role']==='admin' ? 'Normál' : 'Admin'; ?></button>
                                        </form>
                                        <form method="POST" style="display:inline-block; margin:0 4px;" onsubmit="return confirm('Biztos törlöd ezt a felhasználót?');">
                                            <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">
                                            <input type="hidden" name="action" value="delete_user">
                                            <button type="submit" class="msg-error" style="font-size:0.75rem;">Törlés</button>
                                        </form>
                                    <?php else: ?>
                                        (saját fiók)
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <h3 style="margin-top: 2rem;">Időpontok</h3>
                <div class="appointments-grid" style="display: block; max-height: 60vh; overflow: auto;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>#</th><th>Felhasználó</th><th>Időpont</th><th>Fodrász</th><th>Státusz</th><th>Műveletek</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $a): ?>
                                <tr style="border-top:1px solid #ddd;">
                                    <td><?php echo $a['id']; ?></td>
                                    <td><?php echo htmlspecialchars($a['username'] . ' (' . $a['email'] . ')'); ?></td>
                                    <td><?php echo htmlspecialchars($a['date'] . ' ' . $a['time']); ?></td>
                                    <td><?php echo htmlspecialchars($a['hairdresser'] ?: 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($a['status']); ?></td>
                                    <td>
                                        <form method="POST" style="display:inline-block; margin-right:6px;">
                                            <input type="hidden" name="appt_id" value="<?php echo $a['id']; ?>">
                                            <input type="hidden" name="action" value="set_status">
                                            <select name="status" style="font-size:0.75rem;">
                                                <option value="foglalt" <?php echo $a['status']=='foglalt' ? 'selected' : ''; ?>>foglalt</option>
                                                <option value="lemondva" <?php echo $a['status']=='lemondva' ? 'selected' : ''; ?>>lemondva</option>
                                            </select>
                                            <button type="submit" class="btn-primary" style="font-size:0.75rem;">Ment</button>
                                        </form>
                                        <form method="POST" style="display:inline-block;" onsubmit="return confirm('Biztos törlöd ezt az időpontot?');">
                                            <input type="hidden" name="appt_id" value="<?php echo $a['id']; ?>">
                                            <input type="hidden" name="action" value="delete_appointment">
                                            <button type="submit" class="msg-error" style="font-size:0.75rem;">Törlés</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</body>
</html>