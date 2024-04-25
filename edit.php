<?php
session_start();

    // Le serveur

        // Si l'identifiant du film n'existe pas ou qu'il est vide
        if ( ! isset($_GET['film_id']) || empty($_GET['film_id']) ) 
        {
            // Effectuer une redirection vers la page d'accueil
            return header("Location: index.php");
        }

        // Dans le cas contraire,
        // Récupérons l'identifiant du film
        $filmId = (int) htmlspecialchars($_GET['film_id']);

        // Etablir une connexion avec la base de données
        require __DIR__ . "/db/connexion.php";

        // S'assurer que l'identifiant correspond à une film de la base de données
        $req = $db->prepare("SELECT * FROM film WHERE id=:id");
        $req->bindValue(":id", $filmId);
        $req->execute();

        // Récupérer le film correspondant
        $film = $req->fetch();
        $req->closeCursor();

        // 1- Si la méthode d'envoi des données vers le serveur est POST
        if ( $_SERVER['REQUEST_METHOD'] === "POST" ) 
        {
            // 2- Un peu de cyber-sécurité
                // Protéger le serveur contre les failles de type XSS
                $postClean   = [];
                $formErrors  = [];

                foreach ($_POST as $key => $value) 
                {
                    $postClean[$key] = htmlspecialchars($value);
                }

                // ** Protéger le serveur contre les failles de type CSRF
                // Si les tokens (celui en session et celui provenant du formulaire) n'existent pas,
                if ( ! isset($_SESSION['csrf_token']) || ! isset($postClean['_csrf_token']) ) 
                {
                    // Effectuons une redirection vers page de laquelle proviennent les informations
                    // Puis arrêtons l'exécution du script.
                    return header("Location: " . $_SERVER['HTTP_REFERER']);
                }
                
                // Si les token (celui en session et celui provenant du formulaire) sont vides,
                if ( empty($_SESSION['csrf_token']) || empty($postClean['_csrf_token']) )
                {
                    // Effectuons une redirection vers page de laquelle proviennent les informations
                    // Puis arrêtons l'exécution du script.
                    return header("Location: " . $_SERVER['HTTP_REFERER']);
                }
                
                if ( $_SESSION['csrf_token'] !== $postClean['_csrf_token'] ) 
                {
                    // Effectuons une redirection vers page de laquelle proviennent les informations
                    // Puis arrêtons l'exécution du script.
                    return header("Location: " . $_SERVER['HTTP_REFERER']);
                }

            
            // 3- Définir les contraintes de validation des données du formulaire

                // Si le nom existe 
                if ( isset($postClean['name']) ) 
                {
                    // Mais qu'il est vide
                    if (empty($postClean['name'])) 
                    {
                        // C'est qu'il y a une erreur,
                        // Donc remplissions le tableau prévu pour les erreurs.
                        $formErrors['name'] = "Le nom du film est obligatoire.";
                    }
                    else if( mb_strlen($postClean['name']) > 255 )
                    {
                        $formErrors['name'] = "Le nom du film ne doit pas dépasser 255 caractères.";
                    }
                }

                // Si le nom du/des acteurs existe 
                if ( isset($postClean['actors']) )
                {
                    // Mais qu'il est vide
                    if (empty($postClean['actors']))
                    {
                        // C'est qu'il y a une erreur,
                        // Donc remplissions le tableau prévu pour les erreurs.
                        $formErrors['actors'] = "Le nom du/des acteurs(s) est obligatoire.";
                    }
                    else if( mb_strlen($postClean['name']) > 255 )
                    {
                        $formErrors['actors'] = "Le nom du/des acteurs(s) ne doit pas dépasser 255 caractères.";
                    }
                }
                
                // Si la note existe 
                if ( isset($postClean['review']) )
                {
                    // Mais qu'elle n'est pas vide
                    if (! empty($postClean['review']))
                    {
                        if ( ! is_numeric($postClean['review']) ) 
                        {
                            $formErrors['review'] = "La note doit être un nombre.";
                        }
                        else if( $postClean['review'] < "0" || $postClean['review'] > "5" )
                        {
                            $formErrors['review'] = "La note doit être comprise entre 0 et 5.";
                        }
                    }
                }
                
                // Si la note existe 
                if ( isset($postClean['comment']) )
                {
                    // Mais qu'elle n'est pas vide
                    if (! empty($postClean['comment']))
                    {
                        if ( mb_strlen($postClean['comment']) > 1000 ) 
                        {
                            $formErrors['comment'] = "Le commentaire ne doit pas dépasser 1000 caractères.";
                        }
                    }
                }

                // S'il existe au moins une erreur dans le tableau des erreurs
                if ( count($formErrors) > 0 ) 
                {
                    // Sauvegardons le tableau des erreurs en session
                    $_SESSION['form_errors'] = $formErrors;

                    // Sauvegardons les données précedement envoyées par le client en session
                    $_SESSION['old'] = $postClean;

                    // Faisons la redirection vers la page de laquelle proviennent les informations
                    // Puis, arrêtons l'exécution du script
                    return header("Location: " . $_SERVER['HTTP_REFERER']);
                }
    
            // Arrondisons la note à une chiffre après la virgule
            if ( isset($postClean['review']) && ($postClean['review'] != "") ) 
            {
                $reviewRounded = round($postClean['review'], 1);
            }

            // 4- Etablir une connexion avec la base de données
            require __DIR__ . "/db/connexion.php";
    
            // 5- Effectuer la requête d'insertion du nouveau film en base de données
            $req = $db->prepare("UPDATE film SET name=:name, actors=:actors, review=:review, comment=:comment, updated_at=now() WHERE id=:id ");

            $req->bindValue(":name",    $postClean['name']);
            $req->bindValue(":actors",  $postClean['actors']);
            $req->bindValue(":review",  $reviewRounded ? $reviewRounded : '');
            $req->bindValue(":comment", $postClean['comment']);
            $req->bindValue(":id",      $film['id']);

            $req->execute();

            $req->closeCursor();
    
            // 6- Effectuer une redirection vers la page d'accueil
                // Arrêter l'exécution du script
            return header('Location: index.php');
        }

        // Générons une chaine de caractères aléatoire qui représente de jéton de sécurité(token)
            // Et sauvegardons-le en session.
        $_SESSION['csrf_token'] = bin2hex(random_bytes(30));

?>

<?php require __DIR__ . "/partials/head.php"; ?>

    <?php require __DIR__ . "/partials/nav.php"; ?>
    
    <!-- Le contenu spécifique à cette page -->
    <main class="container-fluid">
        <h1 class="text-center my-3 display-5">Modifier film</h1>

        
        <div class="row my-4">
            <div class="col-md-6 col-lg-4 mx-auto">

                <?php if(isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])) : ?>
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            <?php foreach($_SESSION['form_errors'] as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['form_errors']); ?>
                <?php endif ?>

                <form method="post">
                    <div class="mb-3">
                        <label title="Le nom du film est obligatoire." for="name">Nom du film <span class="text-danger">*</span>:</label>
                        <input type="text" name="name" class="form-control" id="name" value="<?= isset($_SESSION['old']['name']) ? $_SESSION['old']['name'] : $film['name']; unset($_SESSION['old']['name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label title="Le nom du/des acteurs est obligatoire." for="actors">Nom du/des acteur(s) <span class="text-danger">*</span> :</label>
                        <input type="text" name="actors" class="form-control" id="actors" value="<?= isset($_SESSION['old']['actors']) ? $_SESSION['old']['actors'] : $film['actors']; unset($_SESSION['old']['actors']); ?>">
                    </div>
                    <div class="mb-3">
                        <label title="La note n'est pas obligatoire." for="review">La note / 5 :</label>
                        <input type="number" step="0.1" name="review" class="form-control" id="review" value="<?= isset($_SESSION['old']['review']) ? $_SESSION['old']['review'] : $film['review']; unset($_SESSION['old']['review']); ?>">
                    </div>
                    <div class="mb-3">
                        <label title="Le commentaire n'est pas obligatoire." for="comment">Laissez un commentaire :</label>
                        <textarea name="comment" id="comment" class="form-control" rows="4"><?= isset($_SESSION['old']['comment']) ? $_SESSION['old']['comment'] : $film['comment']; unset($_SESSION['old']['comment']); ?></textarea>
                    </div>
                    <input type="hidden" name="_csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>

    </main>
    
    <?php require __DIR__ . "/partials/footer.php"; ?>

<?php require __DIR__ . "/partials/foot.php"; ?>