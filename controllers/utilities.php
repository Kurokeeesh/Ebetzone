<?php

function showArray($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function startSessionIfNeeded() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function baseUrl($path = "") {
    $scriptName = $_SERVER["SCRIPT_NAME"] ?? "";
    $base = rtrim(str_replace("\\", "/", dirname($scriptName)), "/");

    if ($base === "" || $base === ".") {
        $base = "";
    }

    if ($path === "") {
        return $base;
    }

    return $base . "/" . ltrim($path, "/");
}

function redirectTo($path) {
    header("Location: " . baseUrl($path));
    exit();
}

function setFlashMessage($type, $message) {
    startSessionIfNeeded();
    $_SESSION["flash"] = [
        "type" => $type,
        "message" => $message,
    ];
}

function getFlashMessage() {
    startSessionIfNeeded();

    if (empty($_SESSION["flash"])) {
        return null;
    }

    $flash = $_SESSION["flash"];
    unset($_SESSION["flash"]);

    return $flash;
}

function currentUser() {
    startSessionIfNeeded();
    return $_SESSION["user"] ?? null;
}

function requireLogin() {
    if (!currentUser()) {
        setFlashMessage("error", "Veuillez vous connecter pour acceder a cette page.");
        redirectTo("connexion");
    }
}

function requireAdmin() {
    $user = currentUser();

    if (!$user || ($user["role"] ?? "") !== "admin") {
        setFlashMessage("error", "Acces reserve a l'administrateur.");
        redirectTo("home");
    }
}

function drawPage($datas_page) {
    extract($datas_page);

    ob_start();
    require_once($view);
    $content = ob_get_clean();

    // 🛡️ Sécurisation du layout
    if (!empty($layout) && file_exists($layout)) {
        require_once($layout);
    } else {
        echo $content;
    }
}


?>
