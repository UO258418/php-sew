<?php
    require "BaseDatos.php";
    session_start();
?>

<!DOCTYPE html>

<html>
    <head>
        <title>Systems</title>
        <meta charset="UTF-8"/>
        <link rel="stylesheet" href="style.css"/>
    </head>

    <?php
        if(!isset($_SESSION["db"]))
            $_SESSION["db"] = serialize(new BaseDatos());

        $db = unserialize($_SESSION["db"]);
    ?>

    <body>
        <nav>
            <a href='./index.php'>Constellations</a>
            <a href='./planets.php'>Planets</a>
        </nav>

        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>Star</th>
                        <th>Constellation</th>
                        <th>Right ascension</th>
                        <th>Declination</th>
                        <th>Apparent magnitude</th>
                        <th>Distance<span class="mu">(ly)</span></th>
                        <th>Spectral Type</th>
                        <th>Mass<span class="mu">(M<sub>☉</sub>)</span></th>
                        <th>Temperature<span class="mu">(K)</span></th>
                        <th>Age<span class="mu">(Gyr)</span></th>
                        <th>Planets</th>
                    </tr>
                </thead>
                
                <tbody>							    
                <?php

                    if(isset($_GET["constellation"])) {
                        $systems = $db->findAllSystemsForConstellation($_GET["constellation"]);
                    } else {
                        $systems = $db->findAllSystems();
                    }

                    foreach($systems as $system) {
                        $db->displaySystem($system);
                    }

                ?>
                </tbody>
            </table>
        </div>
    </body>
</html>