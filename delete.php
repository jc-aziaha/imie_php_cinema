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

        // S'assurer que l'identifiant correspond à un film de la base de données
        $req = $db->prepare("SELECT * FROM film WHERE id=:id");
        $req->bindValue(":id", $filmId);
        $req->execute();

        // Récupérer le film correspondant
        $film = $req->fetch();

        // Préparons la requête de suppression du film de la base de données
        $req2 = $db->prepare("DELETE FROM film WHERE id=:id");

        // Passons l'identifiant du film
        $req2->bindValue(":id", $film['id']);

        // Exécutons la requête
        $req2->execute();

        // Effectuons une redirection vers la page d'accueil
            // Arrêtons lexécution du script
        return header('Location: index.php');
        