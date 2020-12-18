<?php
    require "BaseDatos.php";
    session_start();
?>

<!DOCTYPE html>

<html>
    <head>
        <title>Planets</title>
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
            <a href='./systems.php'>Systems</a>
        </nav>

        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>Companion</th>
                        <th>Star</th>
                        <th>Mass</th>
                        <th>Semimajor axis<span class="mu">(AU)</span></th>
                        <th>Orbital period<span class="mu">(days)</span></th>
                        <th>Eccentricity</th>
                        <th>Inclination<span class="mu">(ly)</span></th>
                        <th>Radius</th>
                    </tr>
                </thead>
                
                <tbody>							
                <?php

                    if(isset($_GET["system"])) {
                        $planets = $db->findAllPlanetsForSystem($_GET["system"]);
                    } else {
                        $planets = $db->findAllPlanets();
                    }
                    
                    foreach($planets as $planet) {
                        $db->displayPlanet($planet);
                    }

                ?>
                </tbody>
            </table>
        </div>
    </body>
</html>