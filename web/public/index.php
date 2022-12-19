<?php

include '../app/vendor/autoload.php';
$util = new App\Acme\Util();

$mysqli = new mysqli("mysql", "dev", "dev", "fungi_data");

$fungiId = null;
$fungiUuid = null;
$seekerId = null;
$seekerUuid = null;

function createNewSeekerId(): void
{
    global $util, $mysqli, $seekerId, $seekerUuid;
    $seekerUuid = $util->getRandomUuid();

    try {
        $stmt = $mysqli->prepare("INSERT INTO seeker(uuid) VALUES (?)");
        $stmt->bind_param("s", $seekerUuid);
        $stmt->execute();

        $result = $mysqli->query("SELECT * FROM seeker WHERE uuid = '$seekerUuid' LIMIT 1;");
        $seeker = $result->fetch_assoc();
        $seekerId = $seeker["id"];
    }
    catch (Throwable $e) {
        var_dump($e);
    }

    setcookie("seekerUuid", $seekerUuid, time()+315360000);
}

function seekerFindsFungi(): void
{
    global $mysqli, $fungiId, $seekerId;

    try {
        $stmt = $mysqli->prepare("INSERT INTO fungi_seeker(fungi_id, seeker_id) VALUES (?, ?)");
        $stmt->bind_param("ss", $fungiId, $seekerId);
        $stmt->execute();
    }
    catch (Throwable $e) {}
}

// get seeker:
if (isset($_COOKIE['seekerUuid'])) {
    $seekerUuid = $_COOKIE['seekerUuid'];

    try {
        $result = $mysqli->query("SELECT * FROM seeker WHERE uuid = '$seekerUuid' LIMIT 1;");
        $seeker = $result->fetch_assoc();
    }
    catch (Throwable $e) {
        var_dump($e);
    }
    if ($seeker) {
        $seekerId = $seeker["id"];
    }
    else {
        createNewSeekerId();
    }
}
else {
    createNewSeekerId();
}

// get fungi:
if (isset($_GET['id'])) {
    $fungiUuid = $_GET['id'];

    try {
        $result = $mysqli->query("SELECT * FROM fungi WHERE uuid = '$fungiUuid' LIMIT 1;");
        $fungi = $result->fetch_assoc();
        if ($fungi) {
            $fungiId = $fungi["id"];
        }
    }
    catch (Throwable $e) {}
}

if ($fungiId) {
    seekerFindsFungi();

    echo "fungi gefunden!";
}
else {
    echo "Stratseite";
}

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Docker</title>
    </head>
    <body>
        <h1>fungi <?php echo $fungiId; ?></h1>
        <h2>seeker <?php echo $seekerId; ?></h2>
    </body>
</html>
