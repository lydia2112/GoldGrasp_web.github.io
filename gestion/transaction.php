<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="transaction.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="icon" type="image/png" href="image/logo.png">
  <title>GoldGrasp | Transaction</title>
</head>

<body>
  <nav class="nav">
    <div class="nav-logo">
        <p> GOLD GRASP </p>
    </div>
    <div class="nav-menu" id="navMenu">
        <ul>
            <li><a href="gestionCompte.html" class="link">Home</a></li>
            <li><a href="creerCompte.html" class="link">Register</a></li>
            <li><a href="transaction.html" class="link active">Transaction</a></li>
            <li><a href="movement.html" class="link">Mouvement</a></li>
            <li><a href="abou.html" class="link">About</a></li>
            <li>
                <a href="#" class="link" onclick="toggleMenu()">Profile</a>
                <div class="sub-menu-wrap" id="subMenu">
                    <div class="sub-menu">
                        <div class="user-info">
                           <img src="C:\Users\dell\Desktop\login & regestration\images\user.png">
                           <h2>RAMDA Lydia</h2>
                        </div> 
                        <hr>
                        <a href="#" class="sub-menu-link">
                            <img src="C:\Users\dell\Desktop\login & regestration\images\profile.png">
                            <p>Edite Profile</p>
                            <span>></span>
                        </a>
                        <a href="#" class="sub-menu-link">
                            <img src="C:\Users\dell\Desktop\login & regestration\images\help.png">
                            <p>Helps & Support</p>
                            <span>></span>
                        </a>
                        <a href="#" class="sub-menu-link">
                            <img src="C:\Users\dell\Desktop\login & regestration\images\logout.png">
                            <p>Logout</p>
                            <span>></span>
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
  </nav> 

  <header class="j">
    <div class="head">
      <h3>ESPACE TRANSACTION (Retrait/ Versement)</h3>
   </div>
  </header>

  <main class="container">
     <form action="" method="post">
       <div>
          <label class="num" for="num">Numéro du compte&nbsp;: </label>
          <input class="form-control" type="text" name="num" placeholder="Numéro du compte" id="num" required> 
       </div>
        <label for="op">Type de transaction&nbsp; :</label> 
        <select class="choix" name="op" id="op" required>
          <option value="" disabled selected>Veuillez choisir un type de transaction</option>
          <option value="Versement">Versement</option>
          <option value="Retrait">Retrait</option>
        </select>
        <div>
          <label for="solde">Montant :</label>
          <input class="form-control" type="number" name="solde" placeholder="Saisir le montant en chiffre (Exemple : 500)" id="solde" required> 
        </div>
        <button id="submit" class="shadow__btn" type="submit"> Procéder </button>
      </form>

      <?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $servername = "127.0.0.1";
          $username = "root";
          $password = "";
          $dbname = "cptbank";

          // Créer la connexion
          $conn = new mysqli($servername, $username, $password, $dbname);

          // Vérifier la connexion
          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          }

          $num = $_POST['num'];
          $op = $_POST['op'];
          $solde = $_POST['solde'];
          $currentDateTime = date('Y-m-d H:i:s');

          // Requête pour obtenir le solde actuel du compte
          $sql = "SELECT solde FROM compte WHERE numcp = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("i", $num);
          $stmt->execute();
          $stmt->store_result();

          if ($stmt->num_rows > 0) {
              $stmt->bind_result($currentBalance);
              $stmt->fetch();

              if ($op == "Retrait") {
                  if ($currentBalance - $solde >= 500) {
                      // Mise à jour du solde
                      $newBalance = $currentBalance - $solde;
                      $updateSql = "UPDATE compte SET solde = ? WHERE numcp = ?";
                      $updateStmt = $conn->prepare($updateSql);
                      $updateStmt->bind_param("di", $newBalance, $num);
                      $updateStmt->execute();

                      // Insérer la transaction
                      $insertSql = "INSERT INTO transactions (dateTrans, RefTrans, NumCompte, typeTrans, idClient, amountTrans) VALUES (?, ?, ?, ?, ?, ?)";
                      $insertStmt = $conn->prepare($insertSql);
                      $refTrans = uniqid(); // Générer une référence unique pour la transaction
                      $idClient = null; // Mettre l'ID du client si nécessaire
                      $insertStmt->bind_param("ssissi", $currentDateTime, $refTrans, $num, $op, $idClient, $solde);
                      $insertStmt->execute();

                      echo "Retrait effectué avec succès. Nouveau solde : " . $newBalance;
                  } else {
                      echo "Transaction refusée : solde insuffisant.";
                  }
              } elseif ($op == "Versement") {
                  // Mise à jour du solde
                  $newBalance = $currentBalance + $solde;
                  $updateSql = "UPDATE compte SET solde = ? WHERE numcp = ?";
                  $updateStmt = $conn->prepare($updateSql);
                  $updateStmt->bind_param("di", $newBalance, $num);
                  $updateStmt->execute();

                  // Insérer la transaction
                  $insertSql = "INSERT INTO transactions (dateTrans, RefTrans, NumCompte, typeTrans, idClient, amountTrans) VALUES (?, ?, ?, ?, ?, ?)";
                  $insertStmt = $conn->prepare($insertSql);
                  $refTrans = uniqid(); // Générer une référence unique pour la transaction
                  $idClient = null; // Mettre l'ID du client si nécessaire
                  $insertStmt->bind_param("ssissi", $currentDateTime, $refTrans, $num, $op, $idClient, $solde);
                  $insertStmt->execute();

                  echo "Versement effectué avec succès. Nouveau solde : " . $newBalance;
              }
          } else {
              echo "Numéro de compte invalide.";
          }

          $stmt->close();
          $conn->close();
      }
      ?>
  </main>

  <script>
    let subMenu = document.getElementById("subMenu");
    function toggleMenu(){
        subMenu.classList.toggle("open-menu");
    }
  </script> 
</body>
</html>
