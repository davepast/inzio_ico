<html>
<head>
    <title>IČO search</title>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
</head>
<body>

<form action="index.php" method="get">
    <input class="form-control form-control-sm" type="text" name="text" placeholder="IČO">
    <button class="btn btn-success">Search</button>
</form>



<?php

include('xml2array.php');

if (!isset($_GET['text'])) {
    echo ("Zadejte platné IČO");
} else {

    $contents = file_get_contents('http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_rzp.cgi?ico='.$_GET['text'].'&xml=0&ver=1.0.4');
    $contents2 = xml2array($contents);//27074358, 07070152

    //print_r($contents2);
    echo "<br>";
    echo "<br>";

    $zaklUdaje = $contents2['are:Ares_odpovedi']['are:Odpoved']['dtt:Vypis_RZP']['dtt:Zakladni_udaje'];

    echo ("Stav: " . $zaklUdaje['dtt:Stav'] . "<br>");
    echo ("Datum změny: " . $zaklUdaje['dtt:Datum_zmeny'] . "<br>");
    echo ("IČO: " . $zaklUdaje['dtt:ICO'] . "<br>");
    echo ("Obchodní firma: " . $zaklUdaje['dtt:Obchodni_firma'] . "<br>");
    echo ("Typ subjektu: " . $zaklUdaje['dtt:Pravni_forma']['dtt:PF_osoba'] . " - " . $zaklUdaje['dtt:Pravni_forma']['dtt:Text'] . "<br>");
    echo ("Živnostenský úřad: " . $zaklUdaje['dtt:Zivnostensky_urad']['dtt:Kod_ZU'] . " - " . $zaklUdaje['dtt:Zivnostensky_urad']['dtt:Nazev_ZU'] . "<br>");
    echo ("První živnost: " . $zaklUdaje['dtt:Prvni_zivnost'] . "<br>");
    echo ("Všech živností: " . $zaklUdaje['dtt:Vsech_zivnosti'] . "<br>");
    echo ("Aktivních živností: " . $zaklUdaje['dtt:Aktivnich_zivnosti'] . "<br>");
    if (isset($zaklUdaje['dtt:Aktivnich_provozoven'])) {
        echo ("Aktivních provozoven: " . $zaklUdaje['dtt:Aktivnich_provozoven'] . "<br>");
    }


    $address = $contents2['are:Ares_odpovedi']['are:Odpoved']['dtt:Vypis_RZP']['dtt:Adresy']['dtt:Adresa'];

    if (isset($address['dtt:Cislo_domovni']) && isset($address['dtt:Cislo_do_adresy'])){
        $addressCisloDomu = $address['dtt:Cislo_do_adresy'] . "/" . $address['dtt:Cislo_domovni'];

    } else if (!isset($address['dtt:Cislo_do_adresy'])) {
        $addressCisloDomu = $address['dtt:Cislo_domovni'];
    } else if (!isset($address['dtt:Cislo_domovni'])) {
        $addressCisloDomu = $address['dtt:Cislo_do_adresy'];
    }
    echo ("Adresa: " . $address['dtt:Nazev_ulice'] . " " . $addressCisloDomu . ", " . $address['dtt:PSC'] . " " . $address['dtt:Nazev_obce'] . " " . $address['dtt:Nazev_casti_obce']) . "<br>";


    $names = $contents2['are:Ares_odpovedi']['are:Odpoved']['dtt:Vypis_RZP']['dtt:Osoby']['dtt:Osoba'];
    foreach ($names as $osoba=>$osobyUdaje) {
        if (isset($osobyUdaje['dtt:Titul_pred'])) {
            echo("Osoba: " . $osobyUdaje['dtt:Titul_pred'] . " " . $osobyUdaje['dtt:Jmeno'] . " " . $osobyUdaje['dtt:Prijmeni'] . ", datum narození:" . $osobyUdaje['dtt:Datum_narozeni'] . "<br>");
        } else {
            echo("Osoba: " . $osobyUdaje['dtt:Jmeno'] . " " . $osobyUdaje['dtt:Prijmeni'] . ", datum narození: " . $osobyUdaje['dtt:Datum_narozeni'] . "<br>");
        }
    }

    $zivnosti = $contents2['are:Ares_odpovedi']['are:Odpoved']['dtt:Vypis_RZP']['dtt:Zivnosti']['dtt:Zivnost'];
    foreach ($zivnosti as $zivnost=>$zivnostUdaje){
        echo ($zivnostUdaje['dtt:Predmet_podnikani'] . "<br>");
        echo ("Stav: " . $zivnostUdaje['dtt:Stav'] . "<br>");
        echo ("Druh: " . $zivnostUdaje['dtt:Druh'] . "<br>");
        echo ("Vznik: " . $zivnostUdaje['dtt:Vznik'] . "<br>");
        if (isset($zivnostUdaje['dtt:Obory_cinnosti'])) {

            foreach ($zivnostUdaje['dtt:Obory_cinnosti']['dtt:Obor_cinnosti'] as $oborCinnosti) {
                echo ($oborCinnosti['dtt:Text'] . "<br>");
            }
        }
    }


    //print_r($names);
}
?>
</body>
</html>