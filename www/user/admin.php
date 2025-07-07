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


?>
<body>
<?php
include '../utils/header.php';
$connect = mysqli_connect("localhost", "root", "", "coding_faq");

// Récupération des données des utilisateurs
$nonAdmin = 0;
$query = "SELECT * FROM users WHERE admin = ?";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, 'i', $nonAdmin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['promotion'])) {
    $user_id = (int)$_POST['user_id'];
    $promotion = $_POST['promotion'];

    $valid_promotions = ['B1', 'B2', 'B3'];
    if (in_array($promotion, $valid_promotions)) {
        $stmt = mysqli_prepare($connect, "UPDATE users SET promotion = ? WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, 'si', $promotion, $user_id);
        mysqli_stmt_execute($stmt);
        header("Location: admin.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['admin_status'])) {
    $userId = (int) $_POST['user_id'];
    $adminStatus = 1; // since only one option to set admin = true

    $stmt = $connect->prepare("UPDATE users SET admin = ? WHERE id_user = ?");
    $stmt->bind_param("ii", $adminStatus, $userId);
    $stmt->execute();

    // Redirect to avoid resubmission on page refresh
    header("Location: admin.php");
    exit;
}

?>
<div class="container">
    <h1>Espace admin</h1>
    <table>
        <thead>
        <tr>
            <th>Role</th>
            <th>Promotion</th>
            <th>Email</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Permissions</th>
            <th> </th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['role']) . "</td>";;
                echo "<td>";
                echo "<form method='post' action='admin.php'>";
                echo "<input type='hidden' name='user_id' value='" . (int)$row['id_user'] . "'>";
                echo "<select name='promotion' onchange='this.form.submit()'>";
                $promotions = ['B1', 'B2', 'B3']; // Your promotion options

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
                if ($row['admin'] == 1) {
                    echo "Admin"; // Already admin
                } else {
                    echo "<form method='post' action='admin.php'>";
                    echo "<input type='hidden' name='user_id' value='" . (int)$row['id_user'] . "'>";
                    echo "<select name='admin_status' onchange='this.form.submit()'>";
                    echo "<option value='1'>Admin</option>";
                    echo "</select>";
                }
                echo "</td>";
                echo "<td>";
                echo "<button type='submit'>Valider</button>";
                echo "</td>";
                echo "</form>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Aucun utilisateur trouvé</td></tr>";
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