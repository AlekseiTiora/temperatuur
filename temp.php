<?php
//lisame oma kasutajanimi, parooli, ja ab_nimi
$yhendus=new mysqli("localhost", "aleksei20", "123123", "aleksei20");
//$sorttulp - sorteerimise veerg
//$otsisona - otsingusõna
function kysiKaupadeAndmed($sorttulp="temperatuur", $otsisona=""){
    global $yhendus;
    $lubatudtulbad=array("temperatuur", "maakonnanimi", "aeg");
    if(!in_array($sorttulp, $lubatudtulbad)){
        return "lubamatu tulp";
    }
    //addslashes - striplashes -lisab langjoone - kustutab langjoo

    $otsisona=addslashes(stripslashes($otsisona));
    $kask=$yhendus->prepare("SELECT ilmatemperatuur.id, temperatuur, maakonnanimi, aeg
       FROM ilmatemperatuur, maakondad
       WHERE ilmatemperatuur.maakonna_id=maakondad.id
        AND (temperatuur LIKE '%$otsisona%' OR maakonnanimi LIKE '%$otsisona%')
       ORDER BY $sorttulp");
    //echo $yhendus->error;
    $kask->bind_result($id, $temperatuur, $maakonnanimi, $kuupaev);
    $kask->execute();
    $hoidla=array();
    while($kask->fetch()){
        $kaup=new stdClass();
        $kaup->id=$id;
        $kaup->nimetus=htmlspecialchars($temperatuur);
        $kaup->grupinimi=htmlspecialchars($maakonnanimi);
        $kaup->hind=$kuupaev;
        array_push($hoidla, $kaup);
    }
    return $hoidla;
}
?>
<?php
function looRippMenyy($sqllause, $valikunimi, $valitudid=""){
    global $yhendus;
    $kask=$yhendus->prepare($sqllause);
    $kask->bind_result($id, $sisu);
    $kask->execute();
    $tulemus="<select name='$valikunimi'>";
    while($kask->fetch()){
        $lisand="";
        if($id==$valitudid){$lisand=" selected='selected'";}
        $tulemus.="<option value='$id' $lisand >$sisu</option>";
    }
    $tulemus.="</select>";
    return $tulemus;
}
//lisab uue kaubagrupi
function lisaGrupp($maakonnanimi){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO maakondad (maakonnanimi)
                      VALUES (?)");
    $kask->bind_param("s", $maakonnanimi);
    $kask->execute();
}
//lisa andmed tabeli Kauab
function lisaKaup($temperatuur, $maakonna_id, $kuupaev){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO
       ilmatemperatuur (temperatuur, maakonna_id, aeg)
       VALUES (?, ?, ?)");
    $kask->bind_param("iis", $temperatuur, $maakonna_id, $kuupaev);
    $kask->execute();
}
//kustutab kaudab tabelist kaudab
function kustutaKaup($teavet_id){
    global $yhendus;
    $kask=$yhendus->prepare("DELETE FROM ilmatemperatuur WHERE id=?");
    $kask->bind_param("i", $teavet_id);
    $kask->execute();
}
//muudab andmed tabelis kaudab
function muudaKaup($teavet_id, $temperatuur, $maakonna_id, $kuupaev){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE ilmatemperatuur SET temperatuur=?, maakonna_id=?, aeg=? WHERE id=?");
    $kask->bind_param("iisi", $temperatuur, $maakonna_id, $kuupaev,  $teavet_id);
    $kask->execute();
}
?>




