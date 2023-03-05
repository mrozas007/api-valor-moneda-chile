<?php
require_once "./class_model.php";
class class_api extends class_model
{
    protected $key = "56aabba320a8b164ab51be7d4d0af840e46fb912";

    private function consume_api($JsonSource)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $JsonSource);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }


    private function getDolar()
    {
        $dolar = null;

        $JsonSource = "https://api.sbif.cl/api-sbifv3/recursos_api/dolar?apikey=" . $this->key . "&formato=json";

        $json =  json_decode($this->consume_api($JsonSource));

        if (!isset($json->CodigoHTTP)) {
            $dolar  = str_replace(array("$", ".", ","), array("", "", "."), $json->Dolares[0]->Valor);
        }

        return $dolar;
    }

    function actualizaMonedas()
    {
        $dolar = $this->getDolar();
        $dolar = 100;
        if (!is_null($dolar)) {
            echo "<<<<";
        }
        echo "<hr>";

        $JsonSource = "https://api.sbif.cl/api-sbifv3/recursos_api/euro?apikey=" . $this->key . "&formato=json";
        echo $this->consume_api($JsonSource);


        $JsonSource = "https://api.sbif.cl/api-sbifv3/recursos_api/utm?apikey=" . $this->key . "&formato=json";
        echo $this->consume_api($JsonSource);
        echo "<hr><pre>";
        print_r($this->updateMoneda("", ""));
        echo "</pre>";
    }

    private function updateMoneda($JsonSource, $moneda)
    {

        $sql = "SELECT *FROM ci_precio_monedas";
        $busca = $this->_db->query($sql);
        $respuesta = $busca->fetch_all(MYSQLI_ASSOC);
        if ($respuesta) {

            return $respuesta;
            $respuesta->close();
            $this->_db->close();
        }
    }
}
