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

    // get max fungi count:
    $maxFungiCount = -1;
    try {
        $result = $mysqli->query("SELECT count(*) as 'fungiCount' FROM fungi;");
        $maxFungiCount = $result->fetch_assoc()["fungiCount"];
    }
    catch (Throwable $e) {}

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

        $page = "fungiDetail";
    }
    else {
        $page = "startseite";
    }

    // get all found fungi IDs:
    $foundFungiIds = [];
    try {
        $result = $mysqli->query("SELECT * FROM fungi_seeker WHERE seeker_id = '$seekerId';");
        $alreadyFoundFungis = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($alreadyFoundFungis as $alreadyFoundFungi) {
            $foundFungiIds[] = $alreadyFoundFungi["fungi_id"];
        }
    }
    catch (Throwable $e) {}

    // get rank in highscore:
    $allSeekersSorted = [];
    $allFungisCount = 0;
    $seekerRank = -1;
    try {
        if (count($foundFungiIds) == $maxFungiCount) {
            $seekerRank = 1;
        }
        else {
            $result = $mysqli->query(
                "SELECT seeker_id, count(seeker_id) as foundFungisCount
                FROM fungi_seeker
                GROUP BY seeker_id
                ORDER BY foundFungisCount DESC;"
            );
            $allSeekersSorted = $result->fetch_all(MYSQLI_ASSOC);

            $rankCounter = 1;
            foreach ($allSeekersSorted as $seeker) {
                if ($seeker["seeker_id"] == $seekerId) {
                    break;
                }
                else {
                    $rankCounter++;
                }
            }
            $seekerRank = $rankCounter;
        }
    }
    catch (Throwable $e) {}

    echo $page;

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title>Docker</title>
    </head>
    <body>
        <header>
            <br><br><br><br><br>
            HEADER
            BLA BLA
        </header>
        <h1>fungi: <?php echo $fungiId; ?></h1>
        <h2>seeker: <?php echo $seekerId; ?></h2>
        <h3>seeker rank: <?php echo $seekerRank; ?></h3>
    <footer>
        FOOTER
        BLA BLA
    </footer>
    </body>
</html>
