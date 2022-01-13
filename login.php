<?php
$yhendus=new mysqli("localhost", "aleksei20", "123123", "aleksei20");
//login vorm Andmebaasis salvestatud kasutajanimega ja prooliga
session_start();

// kontroll kas login vorm on täidetud?
if(isset($_REQUEST['knimi']) && isset($_REQUEST['psw'])) {
    $login = htmlspecialchars($_REQUEST['knimi']);
    $pass = htmlspecialchars($_REQUEST['psw']);

    $sool = 'vagavagatekst';
    $krypt = crypt($pass, $sool);
    // kontrollime kas andmebaasis on selline kasutaja

    $kask = $yhendus->prepare("
SELECT id, unimi, psw, isadmin FROM uuedkasutajad WHERE unimi=?");
    $kask->bind_param("s", $login);
    $kask->bind_result($id, $kasutajanimi, $parool, $onadmin);
    $kask->execute();

    if ($kask->fetch() && $krypt == $parool) {
        $_SESSION['unimi'] = $login;
        if ($onadmin == 1) {
            $_SESSION['admin'] = true;
        }
        header("Location: kaubahaldus.php");
        $yhendus->close();
        exit();
    }
    echo "kasutaja $login või parool $krypt on vale";
    $yhendus->close();
}

?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css" type="text/css">
</head>
<body>

<div class="wrapper">
    <a href="#demo-modal">Logi sisse</a>
</div>

<div id="demo-modal" class="modal">
    <div class="modal__content">
        <h1>Logi sisse</h1>


        <form class="modal-content" action="login.php" method="post">
            <div class="container">
                <label for="knimi">Kasutajanimi</label>
                <input type="text" placeholder="Sisesta kasutajanimi"
                       name="knimi" id="knimi" required>
                <br>
                <label for="psw">Parool</label>
                <input type="password" placeholder="Sisesta parool"
                       name="psw" id="psw" required>
                <br>
                <br>
                <input type="submit" value="Logi sisse">
                <input type="submit" value="Loobu"
                       onclick="window.location.href='kaubahaldus.php'"
                       class="cancelbtn">
            </div>
        </form>




        <a href="#" class="modal__close">&times;</a>
    </div>
</div>





</body>
</html>