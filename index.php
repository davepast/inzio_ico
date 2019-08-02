<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>IČO Search</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

</head>
<body>
<main role="main" class="container">
    <h1>Vyhledání subjektu pomocí IČO přes ARES</h1>
    <div class="starter-template">
        <div class="container">
            <form action="index.php" method="get">
                <input class="form-control form-control-sm" type="number" name="ico" placeholder="Zadejte IČO">
                <button class="btn btn-success">Search</button>
            </form>



            <?php

            include('./xml2array.php');

            if (!isset($_GET['ico']) || $_GET['ico'] == "") {
                echo ("<h3>Zadejte platné IČO</h3>");
            } else {

                $contents = file_get_contents('http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_rzp.cgi?ico=' . $_GET['ico'] . '&xml=0&ver=1.0.4');
                $contents2 = xml2array($contents);//27074358, 07070152

                //print_r($contents2);
                echo ("<br>");
                echo ("<br>");
                echo ("<h3>Základní údaje</h3>");
                echo "<table class='table'><tbody>";
                $zaklUdaje = $contents2['are:Ares_odpovedi']['are:Odpoved']['dtt:Vypis_RZP']['dtt:Zakladni_udaje'];

                echo ("<tr><td>Obchodní firma</td><td>" . $zaklUdaje['dtt:Obchodni_firma'] . "</td></tr>");
                echo ("<tr><td>Stav</td><td>" . $zaklUdaje['dtt:Stav'] . "</td></tr>");
                echo ("<tr><td>Datum změny</td><td>" . $zaklUdaje['dtt:Datum_zmeny'] . "</td></tr>");
                echo ("<tr><td>IČO</td><td>" . $zaklUdaje['dtt:ICO'] . "</td></tr>");
                echo ("<tr><td>Typ subjektu</td><td>" . $zaklUdaje['dtt:Pravni_forma']['dtt:PF_osoba'] . " - " . $zaklUdaje['dtt:Pravni_forma']['dtt:Text'] . "</td></tr>");
                echo ("<tr><td>Živnostenský úřad</td><td>" . $zaklUdaje['dtt:Zivnostensky_urad']['dtt:Kod_ZU'] . " - " . $zaklUdaje['dtt:Zivnostensky_urad']['dtt:Nazev_ZU'] . "</td></tr>");
                echo ("<tr><td>První živnost</td><td>" . $zaklUdaje['dtt:Prvni_zivnost'] . "</td></tr>");
                echo ("<tr><td>Všech živností</td><td>" . $zaklUdaje['dtt:Vsech_zivnosti'] . "</td></tr>");
                echo ("<tr><td>Aktivních živností</td><td>" . $zaklUdaje['dtt:Aktivnich_zivnosti'] . "</td></tr>");
                if (isset($zaklUdaje['dtt:Aktivnich_provozoven'])) {
                    echo ("<tr><td>Aktivních provozoven</td><td>" . $zaklUdaje['dtt:Aktivnich_provozoven'] . "</td></tr>");
                }

                $address = $contents2['are:Ares_odpovedi']['are:Odpoved']['dtt:Vypis_RZP']['dtt:Adresy']['dtt:Adresa'];

                if (isset($address['dtt:Cislo_domovni']) && isset($address['dtt:Cislo_do_adresy'])){
                    $addressCisloDomu = $address['dtt:Cislo_do_adresy'] . "/" . $address['dtt:Cislo_domovni'];

                } else if (!isset($address['dtt:Cislo_do_adresy'])) {
                    $addressCisloDomu = $address['dtt:Cislo_domovni'];
                } else if (!isset($address['dtt:Cislo_domovni'])) {
                    $addressCisloDomu = $address['dtt:Cislo_do_adresy'];
                }
                echo ("<tr><td>Adresa</td><td>" . $address['dtt:Nazev_ulice'] . " " . $addressCisloDomu . ", " . $address['dtt:PSC'] . " " . $address['dtt:Nazev_obce'] . " " . $address['dtt:Nazev_casti_obce']) . "</td></tr>";


                $names = $contents2['are:Ares_odpovedi']['are:Odpoved']['dtt:Vypis_RZP']['dtt:Osoby']['dtt:Osoba'];
                echo ("<tr><td>Osoby</td><td>");
                if (array_keys($names)[0] == "0") {
                    foreach ($names as $osoba => $osobyUdaje) {
                        if (isset($osobyUdaje['dtt:Titul_pred'])) {
                            echo($osobyUdaje['dtt:Titul_pred'] . " " . $osobyUdaje['dtt:Jmeno'] . " " . $osobyUdaje['dtt:Prijmeni'] . ", datum narození:" . $osobyUdaje['dtt:Datum_narozeni'] . "<br>");
                        } else {
                            echo($osobyUdaje['dtt:Jmeno'] . " " . $osobyUdaje['dtt:Prijmeni'] . ", datum narození: " . $osobyUdaje['dtt:Datum_narozeni'] . "<br>");
                        }
                    }
                } else {
                    if (isset($names['dtt:Titul_pred'])) {
                        echo($names['dtt:Titul_pred'] . " " . $names['dtt:Jmeno'] . " " . $names['dtt:Prijmeni'] . ", datum narození:" . $names['dtt:Datum_narozeni'] . "<br>");
                    } else {
                        echo($names['dtt:Jmeno'] . " " . $names['dtt:Prijmeni'] . ", datum narození: " . $names['dtt:Datum_narozeni'] . "<br>");
                    }
                }

                echo ("</td></tr></tbody></table>");
                echo ("<h3>Živnosti včetně provozoven</h3>");

                $zivnosti = $contents2['are:Ares_odpovedi']['are:Odpoved']['dtt:Vypis_RZP']['dtt:Zivnosti']['dtt:Zivnost'];

                if (array_keys($zivnosti)[0] == "0"){
                    foreach ($zivnosti as $zivnost=>$zivnostUdaje){
                        echo ("<h4>" . $zivnostUdaje['dtt:Predmet_podnikani'] . "</h4>");
                        echo ("<table class='table'><tbody>");
                        echo ("<tr><td>Stav</td><td>" . $zivnostUdaje['dtt:Stav'] . "</td></tr>");
                        echo ("<tr><td>Druh</td><td>" . $zivnostUdaje['dtt:Druh'] . "</td></tr>");
                        echo ("<tr><td>Vznik</td><td>" . $zivnostUdaje['dtt:Vznik'] . "</td></tr>");
                        if (isset($zivnostUdaje['dtt:Obory_cinnosti'])) {
                            if (array_keys($zivnostUdaje['dtt:Obory_cinnosti']['dtt:Obor_cinnosti'])[0] == "0") {
                                echo ("<tr><td>Obory činnosti</td><td>");
                                foreach ($zivnostUdaje['dtt:Obory_cinnosti']['dtt:Obor_cinnosti'] as $oborCinnosti) {
                                    echo($oborCinnosti['dtt:Text'] . "<br>");
                                }
                                echo ("</td></tr>");
                            } else {
                                echo ("<tr><td>Obor činnosti</td><td>" . $zivnostUdaje['dtt:Obory_cinnosti']['dtt:Obor_cinnosti']['dtt:Text'] . "</td></tr>");
                            }
                        }
                        echo ("</tbody></table>");
                    }
                } else {
                    if (isset($zivnosti['dtt:Predmet_podnikani'])) {
                        echo ("<h4>" . $zivnosti['dtt:Predmet_podnikani'] . "</h4>");
                    }
                    echo ("<table class='table'><tbody>");
                    echo ("<tr><td>Stav</td><td>" . $zivnosti['dtt:Stav'] . "</td></tr>");
                    echo ("<tr><td>Druh</td><td>" . $zivnosti['dtt:Druh'] . "</td></tr>");
                    echo ("<tr><td>Vznik</td><td>" . $zivnosti['dtt:Vznik'] . "</td></tr>");
                    if (isset($zivnosti['dtt:Obory_cinnosti'])) {
                        if (array_keys($zivnosti['dtt:Obory_cinnosti']['dtt:Obor_cinnosti'])[0] == "0") {
                            echo ("<tr><td>Obory činnosti</td><td>");
                            foreach ($zivnosti['dtt:Obory_cinnosti']['dtt:Obor_cinnosti'] as $oborCinnosti) {
                                echo($oborCinnosti['dtt:Text'] . "<br>");
                            }
                            echo ("</td></tr>");
                        } else {
                            echo ("<tr><td>Obor činnosti</td><td>" . $zivnosti['dtt:Obory_cinnosti']['dtt:Obor_cinnosti']['dtt:Text'] . "</td></tr>");
                        }
                    }
                }

                echo ("</tbody></table>");
               // print_r($contents2);
            }
            ?>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</body>
</html>