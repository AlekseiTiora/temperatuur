<?php
require("temp.php");
session_start();

$sorttulp="temperatuur";
$otsisona="";
if(isSet($_REQUEST["maakonnaisamine"])){
    if (!empty(trim($_REQUEST["uuemakonnanimi"]))) {
        lisaGrupp($_REQUEST["uuemakonnanimi"]);
        header("Location: kaubahaldus.php");
        exit();
    }
}
if(isSet($_REQUEST["teavetlisamine"])) {
    if (!empty(trim($_REQUEST["temperatuur"])) && !empty(trim($_REQUEST["aeg"]))) {
        lisaKaup($_REQUEST["temperatuur"], $_REQUEST["maakonna_id"], $_REQUEST["aeg"]);
        header("Location: kaubahaldus.php");
        exit();
    }
}
if(isSet($_REQUEST["kustutusid"])){
    kustutaKaup($_REQUEST["kustutusid"]);
}
if(isSet($_REQUEST["muutmine"])){
    muudaKaup($_REQUEST["muudetudid"], $_REQUEST["nimetus"], $_REQUEST["maakonna_id"], $_REQUEST["aeg"]);
}
if(isSet($_REQUEST["sort"])){
    $sorttulp=$_REQUEST["sort"];
}
if(isSet($_REQUEST["otsisona"])){
    $otsisona=$_REQUEST["otsisona"];
}
$kaubad=kysiKaupadeAndmed($sorttulp, $otsisona);
?>
<!DOCTYPE html>
<head>
    <div class="header">
        <title>Temperatuuri lisamine</title>
    </div>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
<div id="menuArea">
    <input type="submit" value="Loo uus kasutaja" onclick="window.location.href='registr.php'">
    <?php
    if(isset($_SESSION['unimi'])){
        ?>
        <h1>Tere, <?="$_SESSION[unimi]"?></h1>
        <input type="submit" value="Logi välja" onclick="window.location.href='logout.php'">
        <?php

    } else {
        ?>
        <input type="submit" value="Logi sisse" onclick="window.location.href='login.php'">
        <?php
    }
    ?>
</div>

<div class="header">
    <h1>Tabelid * Kaubad ja kaubagrupid</h1>
</div>
    <div class="row">

        <div class="header">
            <form action="kaubahaldus.php">
                <h2>Temperatuuri lisamine</h2>
        </div>

        <div class="column">

            <h2>Andmed lisamine</h2>
            <dl>
                <dt>Temperatuur:</dt>
                <dd><input type="number" name="temperatuur"/></dd>
                <dt>Maakonna:</dt>
                <dd><?php

                    echo looRippMenyy("SELECT id, maakonnanimi FROM maakondad",
                        "maakonna_id");
                    ?>
                </dd>

                <dt>Kuupaev:</dt>
                <dd><input type="date" name="aeg" /></dd>
            </dl>
            <input class="bt" type="submit" name="teavetlisamine" value="Lisa andmed" />
        </div>
        <div class="column">
            <h2>Maakonna lisamine</h2>
            <input   type="text" name="uuemakonnanimi"/>
            <br>
            <input class="bt" type="submit" name="maakonnaisamine" value="Lisa maakonna" />
            </form>
        </div>
    <div class="column2">
<form action="kaubahaldus.php">
    <h2>Kaupade loetelu</h2>
    Otsi: <input type="text" name="otsisona"/>
    <table class="table">
        <tr>
            <th>Haldus</th>
            <th><a href="kaubahaldus.php?sort=temperatuur">temperatuur</a></th>
            <th><a href="kaubahaldus.php?sort=maakonnanimi">maakonna</a></th>
            <th><a href="kaubahaldus.php?sort=aeg">Kuupaev</a></th>
        </tr>
        <?php foreach($kaubad as $kaup): ?>
            <tr>
                <?php if(isSet($_REQUEST["muutmisid"]) &&
                    intval($_REQUEST["muutmisid"])==$kaup->id): ?>
                    <td>
                        <input type="submit" name="muutmine" value="Muuda" />
                        <input type="submit" name="katkestus" value="Katkesta" />
                        <input type="hidden" name="muudetudid" value="<?=$kaup->id ?>" />
                    </td>
                    <td><input type="text" name="nimetus" value="<?=$kaup->nimetus ?>" /></td>
                    <td><?php
                        echo looRippMenyy("SELECT id, grupinimi FROM kaubagrupid",
                            "kaubagrupi_id", $kaup->id);
                        ?></td>
                    <td><input type="text" name="hind" value="<?=$kaup->hind ?>" /></td>
                <?php else: ?>
                    <td>
                        <?php
                        if(isset($_SESSION['unimi'])){
                        ?>
                        <a href="kaubahaldus.php?kustutusid=<?=$kaup->id ?>"
                           onclick="return confirm('Kas ikka soovid kustutada?')">x</a>
                        <a href="kaubahaldus.php?muutmisid=<?=$kaup->id ?>">m</a>
                        <?php } ?>
                    </td>
                    <td><?=$kaup->nimetus ?></td>
                    <td><?=$kaup->grupinimi ?></td>
                    <td><?=$kaup->hind ?></td>
                <?php endif ?>
            </tr>
        <?php endforeach; ?>
    </table>
</form>
    </div>
</div>
</body>
</html>