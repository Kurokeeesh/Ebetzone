<?php

require_once "controllers/pagesController.php";
require_once "controllers/utilities.php";

try{

    if (empty($_GET['page'])){
        $page = "connexion";
    }else{
        $path = explode ("/", filter_var($_GET["page"],FILTER_SANITIZE_URL));
        $page = $path[0];
    }

    switch ($page){
        case "home":
            homePage();
        break;

        case "matchs":
            matchsPage();
        break;

        case "paris":
            parisPage();
        break;

        case "parier":
            placeBetAction();
        break;

        case "connexion":
            cnxPage();
        break;

        case "inscription":
            registerPage();
        break;

        case "mentions-legales":
            legalPage();
        break;

        case 'logout':
            logoutPage();
        break;

        case "admin":
            adminPage();
        break;

        case "Moderation":
            adminPage();
        break;

        case "admin-match-create":
            createMatchAction();
        break;

        case "admin-match-delete":
            deleteMatchAction();
        break;

        case "admin-match-close":
            closeMatchAction();
        break;

        case "admin-region-create":
            createRegionAction();
        break;

        case "admin-team-create":
            createTeamAction();
        break;

        case "admin-region-delete":
            deleteRegionAction();
        break;

        case "admin-team-delete":
            deleteTeamAction();
        break;

        default:
            throw new Exception("La page n'existe pas !");
    }
}catch(Exception $e){
    echo "Erreur : " .$e->getMessage();
}
