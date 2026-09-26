<?php
$servername = "127.0.0.1";
$username = "root";  // Remplacez par votre nom d'utilisateur MySQL
$password = "";  // Remplacez par votre mot de passe MySQL
$dbname = "cptbank";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les données du formulaire
$user = $_POST['username'];
$pass = $_POST['password'];

// Préparer la requête SQL pour éviter les injections SQL
$stmt = $conn->prepare("SELECT id, type FROM connexion WHERE user=? AND password=?");
$stmt->bind_param("ss", $user, $pass);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // L'utilisateur est trouvé
    $stmt->bind_result($id, $type);
    $stmt->fetch();

    if ($type === 'boss' || $type === 'admin') {
        // Démarrer la session et enregistrer les informations de l'utilisateur
        session_start();
        $_SESSION['user'] = $user;
        $_SESSION['id'] = $id;
        $_SESSION['type'] = $type;

        echo "Login successful! Welcome, " . htmlspecialchars($user) . ".";
        // Redirection vers le tableau de bord de l'administrateur
        header("Location: admin_dashboard.php");
    } else {
        // Utilisateur trouvé mais n'est pas un administrateur
        echo "Access denied. Only administrators are allowed.";
    }
} else {
    // Utilisateur non trouvé
    echo "Invalid username or password.";
}

$stmt->close();
$conn->close();
?>
