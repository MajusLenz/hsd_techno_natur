<?php

//    include '../app/vendor/autoload.php';
    $configs = include('../app/config.php');
    include('../app/src/Util.php');
    $util = new App\Acme\Util();

    $mysqli = new mysqli($configs['host'], $configs['user'], $configs['pw'], $configs['db']);

    $fungiId = null;
    $fungiUuid = null;
    $floor = 0;
    $seekerId = null;
    $seekerUuid = null;

    function createNewSeekerId(): void
    {
        global $util, $mysqli, $seekerId, $seekerUuid;
        $seekerUuid = $util->getRandomUuid();

        try {
            $stmt = $mysqli->prepare("INSERT INTO seeker (uuid) VALUES (?)");
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
                $floor = $fungi["floor"];
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

    // get all fungi IDs ordered by floor:
    $allFungiIdsEg = [];
    $allFungiIds1og = [];
    try {
        $result = $mysqli->query("SELECT * FROM fungi");
        $fungis = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($fungis as $fungi) {
            if ($fungi["floor"] == 0) {
                $allFungiIdsEg[] = $fungi["id"];
            }
            elseif ($fungi["floor"] == 1) {
                $allFungiIds1og[] = $fungi["id"];
            }
        }
    }
    catch (Throwable $e) {}

    // get rank in highscore:
    $allSeekersSorted = [];
    $seekerRank = -1;
    $foundFungisCount = count($foundFungiIds);
    try {
        if ($foundFungisCount == $maxFungiCount) {
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
        <!-- favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicon/favicon-16x16.png">
        <link rel="manifest" href="assets/img/favicon/site.webmanifest">
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
                <p class="welcome-text" id="welcome-text">
                    Willkommen bei Rhizom,<br>
                    dem interaktiven<br>Pilz-Netzwerk!
                </p>
                <div class="info-area">
                    <div class="info-button-area">
                        <button id="show-info-button" class="toggle-info-button" type="button"
                                onclick="toggleInfo(this)" >
                            <img src="assets/img/infos.png" alt="Infos">
                        </button>
                        <button id="hide-info-button" class="toggle-info-button" type="button"
                                onclick="toggleInfo(this)" style="display: none;">
                            <img src="assets/img/back.png" alt="Infos">
                        </button>
                    </div>
                    <p class="info-text" id="info-text" style="max-height: 0; overflow: hidden;">
                        "Als Myzel wird die Gesamtheit aller fadenförmigen Zellen eines Pilzes bezeichnet. Im Sprachgebrauch wird nur der Fruchtkörper als Pilz bezeichnet, wobei der eigentliche Pilz dieses unterirdische Geflecht aus Zellen ist."
                        <br>
                        <br>
                        Im Erdgeschoss und der 1. Etage des Gebäudes 6 der HSD ist auch ein Myzel gewachsen. Findest Du die <?php echo $maxFungiCount; ?> Pilze, die daraus wachsen?
                        <br>
                        Scanne sie ein und werde Teil der Pilzkultur!
                        <br>
                        <br>
                        Auf die Pilze, fertig, LOS!
                        <br>
                        <br>
                    </p>
                </div>
                <div class="welcome-image-area" id="welcome-image-area">
                    <img src="assets/img/Pilz_Plakat5_q.png" alt="scan die Pilze!"
                    style="width: 80%;"/>
                </div>
            </section>

            <section class="content-section" id="fungi-detail-section"
                     style="<?php if($pageName == "landingpage") echo "display: none;"; ?>">
                <p class="fungi-facts">
                    <span class="found-fungis">
                        Prima, Du hast
                        <span class="fungi-numbers">
                            <?php echo "$foundFungisCount/$maxFungiCount" ?>
                        </span>
                        Pilze gefunden.
                    </span>
                    <br/>
                    <span class="seeker-rank">
                        Du bist auf
                        <span class="fungi-numbers">
                            Platz
                            <?php echo "$seekerRank" ?>
                        </span>
                        im Highscore.
                    </span>
                </p>
                <div class="fungi-map">
                    <p class="map-info-text">
                        Finde sie alle und werde Teil unseres<br>
                        großen Pilz-Netzwerks:
                    </p>
                    <div class="fungi-map-toggle">
                        <div class="fungi-map-toggle-eg" id="fungi-map-toggle-eg"
                        style="<?php if($floor == 1) echo 'display: none;' ?>">
                            <span class="fungi-map-toggle-text">EG</span>
                            <button type="button" class="fungi-map-toggle-button" id="eg-toggle-button"
                            onclick="toggleMap(this)">
                                <img src="assets/img/toggleleft.svg" alt="EG">
                            </button>
                        </div>
                        <div class="fungi-map-toggle-1og" id="fungi-map-toggle-1og"
                             style="<?php if($floor == 0) echo 'display: none;' ?>">
                            <span class="fungi-map-toggle-text">1.OG</span>
                            <button type="button" class="fungi-map-toggle-button" id="1og-toggle-button"
                                    onclick="toggleMap(this)">
                                <img src="assets/img/toggleright.svg" alt="1. OG">
                            </button>
                        </div>
                    </div>
                    <div class="fungi-map-images">
                        <div class="fungi-map-images-floor" id="fungi-map-images-eg"
                             style="<?php if($floor == 1) echo 'display: none;' ?>">
                            <div class="fungi-map-images-background">
                                <img src='assets/img/fungiMap/mapEg.png' alt=''/>
                            </div>
                            <div class="fungi-map-images-foreground">
                                <?php
                                foreach ($foundFungiIds as $foundFungiId) {
                                    if (in_array($foundFungiId, $allFungiIdsEg)) {
                                        echo "<img src='assets/img/fungiMap/$foundFungiId.jpg' alt=''/>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="fungi-map-images-floor" id="fungi-map-images-1og"
                             style="<?php if($floor == 0) echo 'display: none;' ?>">
                            <div class="fungi-map-images-background">
                                <img src='assets/img/fungiMap/map1og.png' alt=''/>
                            </div>
                            <div class="fungi-map-images-foreground">
                                <?php
                                foreach ($foundFungiIds as $foundFungiId) {
                                    if (in_array($foundFungiId, $allFungiIds1og)) {
                                        echo "<img src='assets/img/fungiMap/$foundFungiId.jpg' alt=''/>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="test" style="display: none;">
                <h1>fungi: <?php echo $fungiId; ?></h1>
                <h2>seeker: <?php echo $seekerId; ?></h2>
                <h3>seeker rank: <?php echo $seekerRank; ?></h3>
                <h4>all found fungis: <?php var_dump($foundFungiIds); ?></h4>
            </section>
        </main>
        <footer>
            <a href="/impressum.html">Impressum</a>
        </footer>
    </body>


    <script type="application/javascript">
        var landingpageSection = document.getElementById("landingpage-section");
        var fungiDetailSection = document.getElementById("fungi-detail-section");
        var toggleRightDiv = document.getElementById("page-toggle-right");
        var toggleLeftDiv = document.getElementById("page-toggle-left");

        var welcomeText = document.getElementById("welcome-text");
        var infoText = document.getElementById("info-text");
        var welcomeImageArea = document.getElementById("welcome-image-area");
        var showInfoButton = document.getElementById("show-info-button");
        var hideInfoButton = document.getElementById("hide-info-button");

        var toggleAreaEg = document.getElementById("fungi-map-toggle-eg");
        var ToggleArea1og = document.getElementById("fungi-map-toggle-1og");
        var fungiMapImagesEg = document.getElementById("fungi-map-images-eg");
        var fungiMapImages1og = document.getElementById("fungi-map-images-1og");

        function togglePage(element) {
            if (element.id === "page-toggle-button-left-arrow") {
                toggleRightDiv.style.display = "block";
                landingpageSection.style.display = "block";
                toggleLeftDiv.style.display = "none";
                fungiDetailSection.style.display = "none";

                // init accordion functionality of landingpage:
                if (showInfoButton.style.display != "none") {
                    welcomeText.style.maxHeight = welcomeText.scrollHeight + "px";
                }
            }
            if (element.id === "page-toggle-button-right-arrow") {
                toggleRightDiv.style.display = "none";
                landingpageSection.style.display = "none";
                toggleLeftDiv.style.display = "block";
                fungiDetailSection.style.display = "block";
            }
        }

        function toggleInfo(element) {
            if (element.id === "show-info-button") {
                showInfoButton.style.display = "none";
                hideInfoButton.style.display = "initial";
                welcomeImageArea.style.display = "none";
                welcomeText.style.maxHeight = 0;
                infoText.style.maxHeight = infoText.scrollHeight + "px";
            }
            if (element.id === "hide-info-button") {
                showInfoButton.style.display = "initial";
                hideInfoButton.style.display = "none";
                welcomeImageArea.style.display = "initial";
                welcomeText.style.maxHeight = welcomeText.scrollHeight + "px";
                infoText.style.maxHeight = 0;
            }
        }

        function toggleMap(element) {
            if (element.id === "eg-toggle-button") {
                toggleAreaEg.style.display = "none";
                ToggleArea1og.style.display = "block";
                fungiMapImagesEg.style.display = "none";
                fungiMapImages1og.style.display = "block";
            }
            if (element.id === "1og-toggle-button") {
                toggleAreaEg.style.display = "block";
                ToggleArea1og.style.display = "none";
                fungiMapImagesEg.style.display = "block";
                fungiMapImages1og.style.display = "none";
            }
        }

        // init accordion functionality of landingpage:
        welcomeText.style.maxHeight = welcomeText.scrollHeight + "px";
    </script>
</html>
