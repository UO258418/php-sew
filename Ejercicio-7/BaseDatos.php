<?php
class BaseDatos {

    private $username = "DBUSER2020";
    private $password = "DBPSWD2020";
    private $selectedDB;
    private $con;

    function __construct() {
        $this->conectar();
        $this->crearBD("uo258418");

        $sql = "CREATE TABLE constellations (
            `name` VARCHAR(30) PRIMARY KEY,
            abbreviation VARCHAR(30),
            genitive VARCHAR(30),
            symbolism VARCHAR(30),
            right_ascension VARCHAR(30),
            declination VARCHAR(30),
            area VARCHAR(30),
            main_stars VARCHAR(20),
            stars_with_planets VARCHAR(20),
            brightest_star VARCHAR(30)
        )";

        $this->crearTabla($sql);

        $sql = "CREATE TABLE systems (
            star VARCHAR(30) PRIMARY KEY,
            constellation VARCHAR(30),
            right_ascension VARCHAR(30),
            declination VARCHAR(30),
            apparent_magnitude VARCHAR(30),
            distance VARCHAR(30),
            spectral_type VARCHAR(20),
            mass VARCHAR(30),
            temperature VARCHAR(30),
            age VARCHAR(30),
            planets VARCHAR(30)
        )";

        $this->crearTabla($sql);

        $sql = "CREATE TABLE planets (
            companion VARCHAR(30) PRIMARY KEY,
            star VARCHAR(30),
            mass VARCHAR(30),
            semimajor_axis VARCHAR(30),
            orbital_period VARCHAR(30),
            eccentricity varchar(20),
            inclination VARCHAR(20),
            radius VARCHAR(20),
            CONSTRAINT fk_star FOREIGN KEY(star) REFERENCES systems(star)
        )";

        $this->crearTabla($sql);

        $this->createConstellationsFromCSV("res/constellations.csv");
        $this->createSystemsFromCSV("res/systems.csv");
        $this->createPlanetsFromCSV("res/planets.csv");
    }

    function conectar() {
        $this->con = new mysqli("localhost", $this->username, $this->password, $this->selectedDB);
        if ($this->con->connect_errno) {
            $this->console_log("Error de conexión: " . $this->con->connect_error);
        } 
    }

    function cerrarConexion() {
        if($this->con != null)
            $this->con->close();
    }

    function crearBD($dbname) {
        $sql = "CREATE DATABASE $dbname";
        if(!$this->con->query($sql)) {
            $this->console_log("Error al crear la bd: " . $this->con->error);
        } 
        $this->seleccionarBD($dbname);
    }

    function seleccionarBD($dbname) {
        $this->selectedDB = $dbname;
        $this->con->select_db($dbname);
    }

    function crearTabla($sql) {
        if(!$this->con->query($sql)) {
            $this->console_log("Error al crear la tabla: " . $this->con->error);
        }
    }

    // constellations

    function createConstellation($dto) {
        $sql = "INSERT INTO constellations(
            `name`, 
            abbreviation,
            genitive,
            symbolism,
            right_ascension,
            declination,
            area,
            main_stars,
            stars_with_planets,
            brightest_star
        ) VALUES(
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        if(!($stmt = $this->con->prepare($sql))) {
            $this->console_log("Prepare failed: (" . $this->con->errno . ") " . $this->con->error);
        } 

        if(!$stmt->bind_param("ssssssssss", 
            $dto["name"],
            $dto["abbreviation"],
            $dto["genitive"],
            $dto["symbolism"],
            $dto["right_ascension"],
            $dto["declination"],
            $dto["area"],
            $dto["main_stars"],
            $dto["stars_with_planets"],
            $dto["brightest_star"]
        )){
            $this->console_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        if(!$stmt->execute()) {
            $this->console_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        $stmt->close();
            
    }

    function findAllConstellations() {
        $sql = "SELECT * FROM constellations";
        $constellations = null;

        if ($result = $this->con->query($sql)) {
            $constellations = array();
            for($i=0; $i<$result->num_rows; $i++) {
                $constellations["row$i"] = $result->fetch_assoc();
            }

            $result->free();
        }

        return $constellations;
    }

    function displayConstellation($constellation) {
        echo "
        <tr>
            <td><a href='./systems.php?constellation=" . $constellation["name"] . "'>" . $constellation["name"] . "</a></td>
            <td>" . $constellation["abbreviation"] . "</td>
            <td>" . $constellation["genitive"] . "</td>
            <td>" . $constellation["symbolism"] . "</td>
            <td>" . $constellation["right_ascension"] . "</td>
            <td>" . $constellation["declination"] . "</td>
            <td>" . $constellation["area"] . "</td>
            <td>" . $constellation["main_stars"] . "</td>
            <td>" . $constellation["stars_with_planets"] . "</td>
            <td>" . $constellation["brightest_star"] . "</td>
        </tr>";
    }

    function createConstellationsFromCSV($filename) {
        if($file = fopen($filename, 'r')) {
            while($result = fgetcsv($file, 1000, ';')) {
                $dto = array(
                    "name" => $result[0],
                    "abbreviation" => $result[1],
                    "genitive" => $result[2],
                    "symbolism" => $result[3],
                    "right_ascension" => $result[4],
                    "declination" => $result[5],
                    "area" => $result[6],
                    "main_stars" => $result[7],
                    "stars_with_planets" => $result[8],
                    "brightest_star" => $result[9],
                );

                $this->createConstellation($dto);
            }

            fclose($file);
        }
    }

    // systems

    function createSystem($dto) {
        $sql = "INSERT INTO systems(
            star,
            constellation,
            right_ascension,
            declination,
            apparent_magnitude,
            distance,
            spectral_type,
            mass,
            temperature,
            age,
            planets 
        ) VALUES(
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        if(!($stmt = $this->con->prepare($sql))) {
            $this->console_log("Prepare failed: (" . $this->con->errno . ") " . $this->con->error);
        } 

        if(!$stmt->bind_param("sssssssssss", 
            $dto["star"],
            $dto["constellation"],
            $dto["right_ascension"],
            $dto["declination"],
            $dto["apparent_magnitude"],
            $dto["distance"],
            $dto["spectral_type"],
            $dto["mass"],
            $dto["temperature"],
            $dto["age"],
            $dto["planets"]
        )){
            $this->console_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        if(!$stmt->execute()) {
            $this->console_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        $stmt->close();
            
    }

    function findAllSystems() {
        $sql = "SELECT * FROM systems";
        $systems = null;

        if ($result = $this->con->query($sql)) {
            $systems = array();
            for($i=0; $i<$result->num_rows; $i++) {
                $systems["row$i"] = $result->fetch_assoc();
            }

            $result->free();
        }

        return $systems;
    }

    function findAllSystemsForConstellation($constellation) {
        $sql = "SELECT * FROM systems where constellation = ?";
        $systems = null;

        if(!($stmt = $this->con->prepare($sql))) {
            $this->console_log("Prepare failed: (" . $this->con->errno . ") " . $this->con->error);
        } 

        if(!$stmt->bind_param("s", $constellation)){
            $this->console_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        if(!$stmt->execute()) {
            $this->console_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        if ($result = $stmt->get_result()) {
            $systems = array();
            for($i=0; $i<$result->num_rows; $i++) {
                $systems["row$i"] = $result->fetch_assoc();
            }

            $result->free();
        }

        $stmt->close();

        return $systems;
    }

    function displaySystem($system) {
        echo "
        <tr>
            <td><a href='./planets.php?system=" . $system["star"] . "'>" . $system["star"] . "</a></td>
            <td>" . $system["constellation"] . "</td>
            <td>" . $system["right_ascension"] . "</td>
            <td>" . $system["declination"] . "</td>
            <td>" . $system["apparent_magnitude"] . "</td>
            <td>" . $system["distance"] . "</td>
            <td>" . $system["spectral_type"] . "</td>
            <td>" . $system["mass"] . "</td>
            <td>" . $system["temperature"] . "</td>
            <td>" . $system["age"] . "</td>
            <td>" . $system["planets"] . "</td>
        </tr>";
    }

    function createSystemsFromCSV($filename) {
        if($file = fopen($filename, 'r')) {
            while($result = fgetcsv($file, 1000, ';')) {
                $dto = array(
                    "star" => $result[0],
                    "constellation" => $result[1],
                    "right_ascension" => $result[2],
                    "declination" => $result[3],
                    "apparent_magnitude" => $result[4],
                    "distance" => $result[5],
                    "spectral_type" => $result[6],
                    "mass" => $result[7],
                    "temperature" => $result[8],
                    "age" => $result[9],
                    "planets" => $result[10],
                );

                $this->createSystem($dto);
            }

            fclose($file);
        }
    }

    // planets

    function createPlanet($dto) {
        $sql = "INSERT INTO planets(
            companion,
            star,
            mass,
            semimajor_axis,
            orbital_period,
            eccentricity,
            inclination,
            radius
        ) VALUES(
            ?, ?, ?, ?, ?, ?, ?, ?
        )";

        if(!($stmt = $this->con->prepare($sql))) {
            $this->console_log("Prepare failed: (" . $this->con->errno . ") " . $this->con->error);
        } 

        if(!$stmt->bind_param("ssssssss", 
            $dto["companion"],
            $dto["star"],
            $dto["mass"],
            $dto["semimajor_axis"],
            $dto["orbital_period"],
            $dto["eccentricity"],
            $dto["inclination"],
            $dto["radius"],
        )){
            $this->console_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        if(!$stmt->execute()) {
            $this->console_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        $stmt->close();
            
    }

    function findAllPlanets() {
        $sql = "SELECT * FROM planets";
        $planets = null;

        if ($result = $this->con->query($sql)) {
            $planets = array();
            for($i=0; $i<$result->num_rows; $i++) {
                $planets["row$i"] = $result->fetch_assoc();
            }

            $result->free();
        }

        return $planets;
    }

    function findAllPlanetsForSystem($star) {
        $sql = "SELECT * FROM planets where star = ?";
        $planets = null;

        if(!($stmt = $this->con->prepare($sql))) {
            $this->console_log("Prepare failed: (" . $this->con->errno . ") " . $this->con->error);
        } 

        if(!$stmt->bind_param("s", $star)){
            $this->console_log("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        if(!$stmt->execute()) {
            $this->console_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        if ($result = $stmt->get_result()) {
            $planets = array();
            for($i=0; $i<$result->num_rows; $i++) {
                $planets["row$i"] = $result->fetch_assoc();
            }

            $result->free();
        }

        $stmt->close();

        return $planets;
    }

    function displayPlanet($planet) {
        echo "
        <tr>
            <td>" . $planet["companion"] . "</td>
            <td>" . $planet["star"] . "</td>
            <td>" . $planet["mass"] . "</td>
            <td>" . $planet["semimajor_axis"] . "</td>
            <td>" . $planet["orbital_period"] . "</td>
            <td>" . $planet["eccentricity"] . "</td>
            <td>" . $planet["inclination"] . "</td>
            <td>" . $planet["radius"] . "</td>
        </tr>";
    }

    function createPlanetsFromCSV($filename) {
        if($file = fopen($filename, 'r')) {
            while($result = fgetcsv($file, 1000, ';')) {
                $dto = array(
                    "companion" => $result[0],
                    "star" => $result[1],
                    "mass" => $result[2],
                    "semimajor_axis" => $result[3],
                    "orbital_period" => $result[4],
                    "eccentricity" => $result[5],
                    "inclination" => $result[6],
                    "radius" => $result[7],
                );

                $this->createPlanet($dto);
            }

            fclose($file);
        }
    }

    private function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
    ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }

    function __sleep() {
        $this->cerrarConexion();
        return array('username', 'password', 'selectedDB');
    }

    function __wakeup() {
        $this->conectar();
    }

}
?>