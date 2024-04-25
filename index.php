<?php
    // Le serveur

    // Etablir une connexion avec lka base de données
    require __DIR__ . "/db/connexion.php";

    // Effectuer la requête de récupération des données
    $req = $db->prepare("SELECT * FROM film ORDER BY created_at DESC");
    $req->execute();
    $films = $req->fetchAll();

    $req->closeCursor();
?>

<?php require __DIR__ . "/partials/head.php"; ?>

    <?php require __DIR__ . "/partials/nav.php"; ?>
    
    <!-- Le contenu spécifique à cette page -->
    <main class="container-fluid">
        <h1 class="text-center my-3 display-5">Hello World</h1>

        <div class="d-flex justify-content-end align-items-center">
            <a href="create.php" class="btn btn-primary">Nouveau film</a>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <?php foreach($films as $film) : ?>
                        <div class="my-3 shadow p-4">
                            <p><strong>Titre</strong>: <?= $film['name']; ?></p>
                            <p><strong>Acteur(s)</strong>: <?= $film['actors']; ?></p>
                            <hr>
                            <a data-bs-toggle="modal" data-bs-target="#modal<?= $film['id'] ?>" title="Les détails du film: <?= $film['name']; ?>" href="" class="text-dark mx-2"><i class="fa-solid fa-eye"></i></a>

                            <!-- Modal -->
                            <div class="modal fade" id="modal<?= $film['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel"><?= $film['name'] ?></h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ...
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save changes</button>
                                </div>
                                </div>
                            </div>
                            </div>

                            <a title="Modifier le film: <?= $film['name']; ?>" href="edit.php?film_id=<?= $film['id']; ?>" class="text-secondary mx-2"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="delete.php?film_id=<?= $film['id']; ?>" class="text-danger mx-2"><i class="fa-regular fa-trash-can"></i></a>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>

    </main>
    
    <?php require __DIR__ . "/partials/footer.php"; ?>

<?php require __DIR__ . "/partials/foot.php"; ?>