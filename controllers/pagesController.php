<?php
require_once "controllers/utilities.php";
require_once "models/matchModel.php";
require_once "models/betModel.php";
require_once "models/userModel.php";
require_once "models/regionModel.php";
require_once "models/teamModel.php";

function syncCurrentUserSession() {
    $user = currentUser();

    if (!$user) {
        return;
    }

    $userModel = new UserModel();
    $freshUser = $userModel->getById($user["id"]);

    if ($freshUser) {
        $_SESSION["user"] = [
            "id" => (int) $freshUser["id"],
            "login" => $freshUser["login"],
            "username" => $freshUser["username"],
            "role" => $freshUser["role"],
            "points" => (int) $freshUser["points"],
        ];
    }
}

function homePage(){
    requireLogin();
    syncCurrentUserSession();

    $datas_page=[
        "description" => "Page home du site",
        "title" => "Accueil : eBetZone",
        "view" => "views/pages/home.php",
    ];
    drawPage($datas_page);
}

function matchsPage(){
    requireLogin();
    syncCurrentUserSession();

    $datas_page=[
        "description" => "Page matchs du site",
        "title" => "Matchs : eBetZone",
        "view" => "views/pages/matchs.php",
    ];
    drawPage($datas_page);
}

function parisPage(){
    requireLogin();
    syncCurrentUserSession();

    $datas_page=[
        "description" => "Page paris du site",
        "title" => "Paris : eBetZone",
        "view" => "views/pages/paris.php",
    ];
    drawPage($datas_page);
}

function cnxPage(){
    $datas_page=[
        "description" => "Page de connexion du site",
        "title" => "Connexion : eBetZone",
        "view" => "views/pages/cnx.php",
    ];
    drawPage($datas_page);
}

function registerPage(){
    $datas_page=[
        "description" => "Page d'inscription du site",
        "title" => "Inscription : eBetZone",
        "view" => "views/pages/register.php",
    ];
    drawPage($datas_page);
}

function legalPage(){
    $datas_page=[
        "description" => "Mentions legales du site",
        "title" => "Mentions legales : eBetZone",
        "view" => "views/pages/legal.php",
    ];
    drawPage($datas_page);
}

function logoutPage() {
    startSessionIfNeeded();
    session_destroy();
    redirectTo("connexion");
}

function adminPage() {
    requireLogin();
    requireAdmin();
    syncCurrentUserSession();

    $datas_page=[
        "description" => "Page administration du site",
        "title" => "Administration : eBetZone",
        "view" => "views/pages/admin.php",
    ];
    drawPage($datas_page);
}

function placeBetAction() {
    requireLogin();

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        redirectTo("matchs");
    }

    $user = currentUser();
    $matchId = (int) ($_POST["match_id"] ?? 0);
    $choice = $_POST["choix"] ?? "";

    if (!$matchId || !in_array($choice, ["equipe1", "equipe2"], true)) {
        setFlashMessage("error", "Pari invalide.");
        redirectTo("matchs");
    }

    $matchModel = new MatchModel();
    $match = $matchModel->getMatchById($matchId);

    if (!$match) {
        setFlashMessage("error", "Match introuvable.");
        redirectTo("matchs");
    }

    if ($match["date_match"] <= date("Y-m-d H:i:s")) {
        setFlashMessage("error", "Les paris sont fermes pour ce match.");
        redirectTo("matchs");
    }

    if ($match["gagnant"] !== "en cours") {
        setFlashMessage("error", "Ce match est deja cloture.");
        redirectTo("matchs");
    }

    $betModel = new BetModel();
    $betModel->placeBet($user["id"], $matchId, $choice);

    setFlashMessage("success", "Votre pari a bien ete enregistre.");
    redirectTo("paris");
}

function createMatchAction() {
    requireLogin();
    requireAdmin();

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        redirectTo("admin");
    }

    $team1Id = (int) ($_POST["team1_id"] ?? 0);
    $team2Id = (int) ($_POST["team2_id"] ?? 0);
    $dateMatch = trim($_POST["date_match"] ?? "");

    if (!$team1Id || !$team2Id || $dateMatch === "") {
        setFlashMessage("error", "Vous devez choisir deux equipes et une date.");
        redirectTo("admin");
    }

    if ($team1Id === $team2Id) {
        setFlashMessage("error", "Les deux equipes doivent etre differentes.");
        redirectTo("admin");
    }

    $teamModel = new TeamModel();
    $team1 = $teamModel->getTeamById($team1Id);
    $team2 = $teamModel->getTeamById($team2Id);

    if (!$team1 || !$team2) {
        setFlashMessage("error", "Une des equipes selectionnees est introuvable.");
        redirectTo("admin");
    }

    if ($team1["jeu"] !== $team2["jeu"]) {
        setFlashMessage("error", "Les deux equipes doivent appartenir au meme jeu.");
        redirectTo("admin");
    }

    $matchModel = new MatchModel();
    $matchModel->createMatch(
        $team1["jeu"],
        $team1["nom"],
        $team2["nom"],
        str_replace("T", " ", $dateMatch) . ":00"
    );

    setFlashMessage("success", "Le match a ete ajoute.");
    redirectTo("admin");
}

function deleteMatchAction() {
    requireLogin();
    requireAdmin();

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        redirectTo("admin");
    }

    $matchId = (int) ($_POST["match_id"] ?? 0);

    if (!$matchId) {
        setFlashMessage("error", "Match introuvable.");
        redirectTo("admin");
    }

    $matchModel = new MatchModel();
    $match = $matchModel->getMatchById($matchId);

    if (!$match) {
        setFlashMessage("error", "Match introuvable.");
        redirectTo("admin");
    }

    if ($match["gagnant"] !== "en cours") {
        setFlashMessage("error", "Impossible de supprimer un match deja cloture.");
        redirectTo("admin");
    }

    $matchModel->deleteMatch($matchId);

    setFlashMessage("success", "Le match a ete supprime.");
    redirectTo("admin");
}

function closeMatchAction() {
    requireLogin();
    requireAdmin();

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        redirectTo("admin");
    }

    $matchId = (int) ($_POST["match_id"] ?? 0);
    $winner = $_POST["winner"] ?? "";

    if (!$matchId || !in_array($winner, ["equipe1", "equipe2"], true)) {
        setFlashMessage("error", "Resultat invalide.");
        redirectTo("admin");
    }

    $matchModel = new MatchModel();
    $match = $matchModel->getMatchById($matchId);

    if (!$match) {
        setFlashMessage("error", "Match introuvable.");
        redirectTo("admin");
    }

    if ($match["gagnant"] !== "en cours") {
        setFlashMessage("error", "Ce match a deja ete cloture.");
        redirectTo("admin");
    }

    $matchModel->closeMatch($matchId, $winner);

    $betModel = new BetModel();
    $betModel->settleBetsForMatch($matchId, $winner);

    syncCurrentUserSession();
    setFlashMessage("success", "Le resultat du match a ete enregistre et les points ont ete mis a jour.");
    redirectTo("admin");
}

function createRegionAction() {
    requireLogin();
    requireAdmin();

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        redirectTo("admin");
    }

    $nom = trim($_POST["region_nom"] ?? "");

    if ($nom === "") {
        setFlashMessage("error", "Le nom de la region est obligatoire.");
        redirectTo("admin");
    }

    $regionModel = new RegionModel();

    if ($regionModel->getRegionByName($nom)) {
        setFlashMessage("error", "Cette region existe deja.");
        redirectTo("admin");
    }

    $regionModel->createRegion($nom);
    setFlashMessage("success", "La region a bien ete ajoutee.");
    redirectTo("admin");
}

function createTeamAction() {
    requireLogin();
    requireAdmin();

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        redirectTo("admin");
    }

    $nom = trim($_POST["team_nom"] ?? "");
    $jeu = trim($_POST["team_jeu"] ?? "");
    $regionId = (int) ($_POST["region_id"] ?? 0);

    if ($nom === "" || $jeu === "") {
        setFlashMessage("error", "Le nom de l equipe et le jeu sont obligatoires.");
        redirectTo("admin");
    }

    $teamModel = new TeamModel();

    if ($teamModel->getTeamByName($nom)) {
        setFlashMessage("error", "Cette equipe existe deja.");
        redirectTo("admin");
    }

    $teamModel->createTeam($nom, $jeu, $regionId);
    setFlashMessage("success", "L equipe a bien ete ajoutee.");
    redirectTo("admin");
}

function deleteRegionAction() {
    requireLogin();
    requireAdmin();

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        redirectTo("admin");
    }

    $regionId = (int) ($_POST["region_id"] ?? 0);

    if (!$regionId) {
        setFlashMessage("error", "Region introuvable.");
        redirectTo("admin");
    }

    $regionModel = new RegionModel();
    $region = $regionModel->getRegionById($regionId);

    if (!$region) {
        setFlashMessage("error", "Region introuvable.");
        redirectTo("admin");
    }

    $regionModel->deleteRegion($regionId);
    setFlashMessage("success", "La region a bien ete supprimee.");
    redirectTo("admin");
}

function deleteTeamAction() {
    requireLogin();
    requireAdmin();

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        redirectTo("admin");
    }

    $teamId = (int) ($_POST["team_id"] ?? 0);

    if (!$teamId) {
        setFlashMessage("error", "Equipe introuvable.");
        redirectTo("admin");
    }

    $teamModel = new TeamModel();
    $team = $teamModel->getTeamById($teamId);

    if (!$team) {
        setFlashMessage("error", "Equipe introuvable.");
        redirectTo("admin");
    }

    $matchModel = new MatchModel();

    if ($matchModel->teamIsUsedInMatch($team["nom"])) {
        setFlashMessage("error", "Impossible de supprimer une equipe deja utilisee dans un match.");
        redirectTo("admin");
    }

    $teamModel->deleteTeam($teamId);
    setFlashMessage("success", "L equipe a bien ete supprimee.");
    redirectTo("admin");
}
