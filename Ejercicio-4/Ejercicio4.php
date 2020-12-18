<!DOCTYPE html>

<html lang="es-ES">
    <head>
        <title>Ejercicio4</title>
        <meta charset="UTF-8"/>
        <link rel="stylesheet" href="Ejercicio4.css"/>
    </head>

    <body>
        <h1>Currency converter</h1>

        <form action='#' method='post' name='convert'>
            <input type="text"  title="amount" id="amount" name="amount"/>

            <label for="from">From:</label>
            <select name="from" id="from">
                <option value="EUR">Euro</option>
                <option value="USD">United States Dollar</option>
                <option value="GBP">British Pound Sterling</option>
                <option value="RUB">Russian ruble</option>
                <option value="RON">Romanian Leu</option>
                <option value="PLN">Polish zloty</option>
            </select>

            <label for="to">To:</label>
            <select name="to" id="to">
                <option value="EUR">Euro</option>
                <option value="USD">United States Dollar</option>
                <option value="GBP">British Pound Sterling</option>
                <option value="RUB">Russian ruble</option>
                <option value="RON">Romanian Leu</option>
                <option value="PLN">Polish zloty</option>
            </select>

            <input type="submit" value="Convertir" name="submit"/>
        </form>

        <?php 
        class Converter {

            private $apikey = "61a46e025f6747fb3205";
            private $baseURL = "https://free.currconv.com/api/v7/convert";
        
            function __construct() {
                
            }
        
            function convert() {
                $query = $_POST["from"] . "_" . $_POST["to"];

                $data = array(
                    "q" => $query,
                    "compact" => "ultra",
                    "apiKey" => $this->apikey
                );

                $url = sprintf("%s?%s", $this->baseURL, http_build_query($data));
            
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($curl);

                curl_close($curl);

                return $result;
            }
            
            function showConvertedAmount() {
                $amount = $_POST["amount"];
                $value = "";
                if(!empty($amount)) {
                    $json = json_decode($this->convert());
                    $query = $_POST["from"] . "_" . $_POST["to"];
                    $value = $json->$query * $amount;
                }
                echo "<input type='text' title='display' id='display' value='$value' disabled/>";
            }
        
        }

        $converter = new Converter();

        if(isset($_POST["submit"])) {
            if(!empty($_POST["from"])) {
                $converter->showConvertedAmount();
            }
        }
        ?>
    </body>
</html>