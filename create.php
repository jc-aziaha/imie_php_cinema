<?php
    // Le serveur

        // var_dump($_SERVER); 
        // die();

        // 1- Si la méthode d'envoi des données vers le serveur est POST
        if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
        {
            // 2- Un peu de cyber-sécurité
                // Protéger le serveur contre les failles de type XSS
                $dataClean = [];

                foreach ($_POST as $key => $value) 
                {
                    $dataClean[$key] = htmlspecialchars($value);
                }

                // Protéger le serveur contre les failles de type CSRF
                
            
            // 3- Définir les contraintes de validation des données du formulaire
    
            // 4- Etablir une connexion avec la base de données
    
            // 5- Effectuer la requête d'insertion du nouveau en base de données
    
            // 6- Effectuer une redirection vers la page d'accueil
    
            // 7- Arrêter l'exécution du script
        }

        // Générons une chaine de caractère aléatoire qui représente de jéton de sécurité(token)
            // Et sauvegardons-le en session.
        $_SESSION['csrf_token'] = bin2hex(random_bytes(30));

?>

<?php require __DIR__ . "/partials/head.php"; ?>

    <?php require __DIR__ . "/partials/nav.php"; ?>
    
    <!-- Le contenu spécifique à cette page -->
    <main class="container-fluid">
        <h1 class="text-center my-3 display-5">Nouveau film</h1>

        <div class="row my-4">
            <div class="col-md-6 col-lg-4 mx-auto">
                <form method="post">
                    <div class="mb-3">
                        <label for="name">Nom du film :</label>
                        <input type="text" name="name" class="form-control" id="name">
                    </div>
                    <div class="mb-3">
                        <label for="actors">Nom du/des acteur(s) :</label>
                        <input type="text" name="actors" class="form-control" id="actors">
                    </div>
                    <div class="mb-3">
                        <label for="review">La note / 5 :</label>
                        <input type="number" step="0.1" name="review" class="form-control" id="review">
                    </div>
                    <div class="mb-3">
                        <label for="comment">Laissez un commentaire :</label>
                        <textarea name="comment" id="comment" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>

    </main>
    
    <?php require __DIR__ . "/partials/footer.php"; ?>

<?php require __DIR__ . "/partials/foot.php"; ?>