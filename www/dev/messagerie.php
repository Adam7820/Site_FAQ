<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ((isset($_SESSION['userId']))){
    include "../../sql/database.php";
    $connect = mysqli_connect("localhost", "root", "", "coding_faq");

    $userId = $_SESSION['userId'];

    $query = "SELECT email FROM users WHERE id_user = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['userId']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $userData = mysqli_fetch_assoc($result);
    $userEmail = $userData ? $userData['email'] : '';
}

// Inclure PHPMailer (vous devez installer PHPMailer via Composer ou télécharger les fichiers)
require_once '../../vendor/autoload.php'; // Si installé via Composer
// OU
// require_once 'PHPMailer/src/PHPMailer.php';
// require_once 'PHPMailer/src/SMTP.php';
// require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configuration SMTP - À MODIFIER selon votre fournisseur
$smtp_config = [
    'host' => 'smtp.gmail.com',        // Serveur SMTP (Gmail, Outlook, etc.)
    'port' => 587,                     // Port SMTP (587 pour TLS, 465 pour SSL)
    'username' => 'faqcoding@gmail.com',  // Votre email
    'password' => 'vxsw jgfu omqg nfgh',      // Votre mot de passe ou mot de passe d'application
    'encryption' => PHPMailer::ENCRYPTION_STARTTLS,  // TLS ou SSL
    'from_email' => 'faqcoding@gmail.com',
    'from_name' => 'Messagerie ESIEE-IT'
];

// Traitement du formulaire
$message_envoye = false;
$erreur = '';
$debug_info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données
    $email_expediteur = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $objet = htmlspecialchars(trim($_POST['objet']));
    $contenu = htmlspecialchars(trim($_POST['contenu']));
    $destinataire = filter_var(trim($_POST['destinataire']), FILTER_VALIDATE_EMAIL);

    // Validation
    if (!$email_expediteur) {
        $erreur = "Adresse email de l'expéditeur invalide.";
    } elseif (!$destinataire) {
        $erreur = "Adresse email du destinataire invalide.";
    } elseif (empty($objet) || empty($contenu)) {
        $erreur = "L'objet et le contenu sont requis.";
    } elseif (strlen($contenu) < 10) {
        $erreur = "Le message doit contenir au moins 10 caractères.";
    } else {
        // Configuration de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host       = $smtp_config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp_config['username'];
            $mail->Password   = $smtp_config['password'];
            $mail->SMTPSecure = $smtp_config['encryption'];
            $mail->Port       = $smtp_config['port'];

            // Encodage
            $mail->CharSet = 'UTF-8';

            // Expéditeur
            $mail->setFrom($smtp_config['from_email'], $smtp_config['from_name']);
            $mail->addReplyTo($email_expediteur, $email_expediteur);

            // Destinataire
            $mail->addAddress($destinataire);

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = '[ESIEE-IT Messagerie] ' . $objet;

            // Corps de l'email en HTML
            $mail->Body = "
            <html>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                    <h2 style='color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;'>
                        Nouveau message de la messagerie ESIEE-IT
                    </h2>
                    
                    <table style='width: 100%; margin: 20px 0;'>
                        <tr>
                            <td style='background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; font-weight: bold;'>
                                Expéditeur :
                            </td>
                            <td style='padding: 10px; border: 1px solid #dee2e6;'>
                                " . htmlspecialchars($email_expediteur) . "
                            </td>
                        </tr>
                        <tr>
                            <td style='background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; font-weight: bold;'>
                                Objet :
                            </td>
                            <td style='padding: 10px; border: 1px solid #dee2e6;'>
                                " . htmlspecialchars($objet) . "
                            </td>
                        </tr>
                        <tr>
                            <td style='background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; font-weight: bold;'>
                                Date :
                            </td>
                            <td style='padding: 10px; border: 1px solid #dee2e6;'>
                                " . date('d/m/Y à H:i:s') . "
                            </td>
                        </tr>
                    </table>
                    
                    <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #3498db; margin: 20px 0;'>
                        <h3 style='margin-top: 0; color: #2c3e50;'>Message :</h3>
                        <p style='margin: 0; white-space: pre-wrap;'>" . htmlspecialchars($contenu) . "</p>
                    </div>
                    
                    <hr style='border: none; border-top: 1px solid #dee2e6; margin: 20px 0;'>
                    
                    <p style='font-size: 12px; color: #6c757d; text-align: center; margin: 0;'>
                        Ce message a été envoyé depuis la messagerie du site ESIEE-IT<br>
                        Pour répondre, utilisez directement l'adresse : " . htmlspecialchars($email_expediteur) . "
                    </p>
                </div>
            </body>
            </html>";

            // Version texte alternative
            $mail->AltBody = "Nouveau message de la messagerie ESIEE-IT\n\n"
                           . "Expéditeur: " . $email_expediteur . "\n"
                           . "Objet: " . $objet . "\n"
                           . "Date: " . date('d/m/Y à H:i:s') . "\n\n"
                           . "Message:\n" . $contenu . "\n\n"
                           . "---\n"
                           . "Ce message a été envoyé depuis la messagerie du site ESIEE-IT";

            // Envoi
            $mail->send();
            $message_envoye = true;

            $log_entry = date('Y-m-d H:i:s') . " - Email envoyé de " . $email_expediteur . " vers " . $destinataire . " - Objet: " . $objet . "\n";
            file_put_contents('emails_log.txt', $log_entry, FILE_APPEND | LOCK_EX);

            header("Location: messagerie.php");
            exit;

        } catch (Exception $e) {
            $erreur = "Erreur lors de l'envoi du message : " . $mail->ErrorInfo;
            $debug_info = "Détails techniques : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Messagerie - ESIEE-IT</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/Site_FAQ/www/css/messagerie.css">
  <link rel="stylesheet" href="/Site_FAQ/www/css/index.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<?php
$page_title = "ESIEE-IT école d'ingénieurs et de l'expertise numérique";
$css_file = "messagerie.css";
include '../utils/header.php';
?>

  <main>
    <div class="messagerie-header">
      <h1>Messagerie</h1>
      <p>Un endroit pour pouvoir contacter un PO ou un responsable</p>
    </div>

    <div class="form-container">
      <?php if ($message_envoye): ?>
        <div class="alert alert-success">
          ✅ Votre message a été envoyé avec succès !
          <br><small>Le destinataire recevra votre message sous peu.</small>
        </div>
      <?php endif; ?>

      <?php if ($erreur): ?>
        <div class="alert alert-error">
          ❌ <?php echo $erreur; ?>
          <?php if ($debug_info): ?>
            <div class="debug-info">
              <?php echo $debug_info; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <form id="messageForm" method="POST" action="">
        <div class="form-group">
          <label for="email">Votre adresse e-mail :</label>
          <input type="email" id="email" name="email" placeholder="votre.email@esiee-it.fr"
                 value="<?php echo htmlspecialchars($_POST['email'] ?? $userEmail ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
          <small>Cette adresse sera utilisée pour les réponses</small>
        </div>

        <div class="form-group">
          <label for="destinataire">Destinataire :</label>
          <input type="email" id="destinataire" name="destinataire" placeholder="responsable@esiee-it.fr"
                 value="<?php echo isset($_POST['destinataire']) ? htmlspecialchars($_POST['destinataire']) : ''; ?>" required>

          <div class="quick-emails">
            <h3>Contacts rapides :</h3>
            <span class="email-button" onclick="setDestinataire('direction@esiee-it.fr')">Direction</span>
            <span class="email-button" onclick="setDestinataire('scolarite@esiee-it.fr')">Scolarité</span>
            <span class="email-button" onclick="setDestinataire('stages@esiee-it.fr')">Stages</span>
            <span class="email-button" onclick="setDestinataire('international@esiee-it.fr')">International</span>
            <span class="email-button" onclick="setDestinataire('support@esiee-it.fr')">Support IT</span>
          </div>
        </div>

        <div class="form-group">
          <label for="objet">Objet :</label>
          <input type="text" id="objet" name="objet" placeholder="Sujet de votre message"
                 value="<?php echo isset($_POST['objet']) ? htmlspecialchars($_POST['objet']) : ''; ?>" required>
        </div>

        <div class="form-group">
          <label for="contenu">Contenu du message :</label>
          <textarea id="contenu" name="contenu" placeholder="Décrivez votre demande, question ou problème en détail..."
                    rows="8" required><?php echo isset($_POST['contenu']) ? htmlspecialchars($_POST['contenu']) : ''; ?></textarea>
          <div class="char-counter">
            <span id="charCount">0</span> caractères (minimum 10)
          </div>
        </div>

        <div class="button-group">
          <a href="/Site_FAQ/www/dev/index.php" class="button back-button">Retour à l'accueil</a>
          <button type="submit" class="button">Envoyer le message</button>
        </div>
      </form>
    </div>
  </main>
 
  <?php include '../utils/footer.php'; ?>



</body>
</html>