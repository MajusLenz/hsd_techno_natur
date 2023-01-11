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

        $pageName = "fungi-detail";
    }
    else {
        $pageName = "landingpage";
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
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <title>Rhizom</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
        <header>
            <h1>
                <a href="/">
                    <img alt="Rhizom" src="assets/img/rhizomlogo.svg">
                </a>
            </h1>
        </header>
        <main>
            <section class="page-toggle-area">
                <div id="page-toggle-right" style="<?php if($pageName == "fungi-detail") echo "display: none;"; ?>">
                    <button class="page-toggle-button" id="page-toggle-button-right-arrow"
                            type="button" onclick="togglePage(this);">
                        <img src="assets/img/arrowright.svg" alt="Zur Startseite">
                    </button>
                </div>
                <div id="page-toggle-left" style="<?php if($pageName == "landingpage") echo "display: none;"; ?>">
                    <button class="page-toggle-button" id="page-toggle-button-left-arrow"
                            type="button" onclick="togglePage(this);">
                        <img src="assets/img/arrowleft.svg" alt="Zur Pilzübersicht">
                    </button>
                </div>
            </section>

            <section class="content-section" id="landingpage-section"
                     style="<?php if($pageName == "fungi-detail") echo "display: none;"; ?>">
                Landing Page!
            </section>

            <section class="content-section" id="fungi-detail-section"
                     style="<?php if($pageName == "landingpage") echo "display: none;"; ?>">
                fungi Detail!
            </section>

            <section class="test">
                <h1>fungi: <?php echo $fungiId; ?></h1>
                <h2>seeker: <?php echo $seekerId; ?></h2>
                <h3>seeker rank: <?php echo $seekerRank; ?></h3>

                <h4>all found fungis: <?php var_dump($foundFungiIds); ?></h4>
                <?php
                foreach ($foundFungiIds as $foundFungiId) {
                    //echo "<img src='assets/img/fungi/$foundFungiId.jpg' />";
                }
                ?>
            </section>
        </main>
        <footer>
            <a href="/impressum.html">Impressum</a>
        </footer>
    </body>
    <script type="application/javascript">
        function togglePage(element) {
            var landingpageSection = document.getElementById("landingpage-section");
            var fungiDetailSection = document.getElementById("fungi-detail-section");
            var toggleRightDiv = document.getElementById("page-toggle-right");
            var toggleLeftDiv = document.getElementById("page-toggle-left");

            if (element.id === "page-toggle-button-left-arrow") {
                toggleRightDiv.style.display = "block";
                landingpageSection.style.display = "block";
                toggleLeftDiv.style.display = "none";
                fungiDetailSection.style.display = "none";
            }
            if (element.id === "page-toggle-button-right-arrow") {
                toggleRightDiv.style.display = "none";
                landingpageSection.style.display = "none";
                toggleLeftDiv.style.display = "block";
                fungiDetailSection.style.display = "block";
            }

        }
    </script>
</html>
