<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['userId'])) {
    header("Location: profile.php");
    exit;
}

$connect = mysqli_connect("localhost", "root", "root", "coding_faq");

$userId = $_SESSION['userId'];
$query = "SELECT role FROM users WHERE id_user = ?";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
$role = 'Responsable';

if (!$user || $user['role'] != $role) {
    header("Location: profile.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - ESIEE-IT</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<?php
include "../../sql/database.php";
$db = db_connect();
include '../utils/header.php';
$connect = mysqli_connect("localhost", "root", "", "coding_faq");

// Récupération des données des utilisateurs
$query = "SELECT * FROM users";
$result = mysqli_query($connect, $query);

// Separate users
$students = [];
$staff = [];

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['role'] === 'Élève') {
        $students[] = $row;
    } else {
        $staff[] = $row;
    }
}

function usersTable($users, $isStaff = false) {
    foreach ($users as $row) {
        echo "<tr>";

        echo "<td>";
        echo "<form method='post' action='admin.php'>";
        echo "<input type='hidden' name='user_id' value='" . (int)$row['id_user'] . "'>";
        echo "<select name='role' onchange='this.form.submit()'>";
        $roles = ['Élève', 'Product Owner', 'Responsable'];
        foreach ($roles as $role) {
            $selected = ($row['role'] === $role) ? 'selected' : '';
            echo "<option value='$role' $selected>$role</option>";
        }
        echo "</select>";
        echo "</form>";
        echo "</td>";
        
        echo "<td>";
        echo "<form method='post' action='admin.php'>";
        echo "<input type='hidden' name='user_id' value='" . (int)$row['id_user'] . "'>";
        echo "<select name='promotion' onchange='this.form.submit()'>";
        echo "<option value='NULL'" . (is_null($row['promotion']) ? ' selected' : '') . ">-</option>";
        $promotions = ['B1', 'B2', 'B3'];
        foreach ($promotions as $promo) {
            $selected = ($row['promotion'] === $promo) ? 'selected' : '';
            echo "<option value='$promo' $selected>$promo</option>";
        }
        echo "</select>";
        echo "</form>";
        echo "</td>";

        echo "<td>" . htmlspecialchars($row['email']) . "</td>";

        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";

        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";

        echo "<td>";
        echo "<form method='post' action='admin.php'>";
        echo "<input type='hidden' name='user_id' value='" . (int)$row['id_user'] . "'>";
        echo "<select name='admin_status' onchange='this.form.submit()'>";
        echo "<option value='0'" . ($row['admin'] == 0 ? ' selected' : '') . ">-</option>";
        echo "<option value='1'" . ($row['admin'] == 1 ? ' selected' : '') . ">Admin</option>";
        echo "</select>";
        echo "</td>";
        
        echo "</form>";
        echo "</tr>";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $user_id = (int) $_POST['user_id'];
    $role = $_POST['role'];

    $stmt = mysqli_prepare($connect, "UPDATE users SET role = ? WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, 'si', $role, $user_id);
    mysqli_stmt_execute($stmt);

    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['promotion'])) {
    $user_id = (int)$_POST['user_id'];
    $promotion = $_POST['promotion'];

    if ($promotion === 'NULL') {
        $stmt = mysqli_prepare($connect, "UPDATE users SET promotion = NULL WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
    }
    else {
        $stmt = mysqli_prepare($connect, "UPDATE users SET promotion = ? WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, 'si', $promotion, $user_id);
    }
    mysqli_stmt_execute($stmt);

    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['admin_status'])) {
    $user_id = (int)$_POST['user_id'];
    $adminStatus = $_POST['admin_status'];

    $stmt = $connect->prepare("UPDATE users SET admin = ? WHERE id_user = ?");
    $stmt->bind_param("ii", $adminStatus, $user_id);
    $stmt->execute();

    header("Location: admin.php");
    exit;
}

?>
<body>
<div class="container">
    <h1>Espace Admin</h1>

    <h2>Étudiants</h2>
    <table>
        <thead>
        <tr>
            <th>Role</th>
            <th>Promotion</th>
            <th>Email</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Permissions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($students)) {
            usersTable($students);
        } else {
            echo "<tr><td colspan='7'>Aucun étudiant trouvé</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <h2>Staff</h2>
    <table>
        <thead>
        <tr>
            <th>Role</th>
            <th>Promotion</th>
            <th>Email</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Permissions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($staff)) {
            usersTable($staff, true);
        } else {
            echo "<tr><td colspan='7'>Aucun staff trouvé</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
<?php
include '../utils/footer.php';
?>
</body>
</html>