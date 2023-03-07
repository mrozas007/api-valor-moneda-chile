<?php
require_once "./class_model.php";
class class_api extends class_model
{
    protected $key = "56aabba320a8b164ab51be7d4d0af840e46fb912";
    protected $debugger = true;

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

    private function getEuro()
    {
        $euro = null;

        $JsonSource = "https://api.sbif.cl/api-sbifv3/recursos_api/euro?apikey=" . $this->key . "&formato=json";

        $json = json_decode($this->consume_api($JsonSource));

        if (!isset($json->CodigoHTTP)) {
            $euro   =  str_replace(array("$", ".", ","), array("", "", "."), $json->Euros[0]->Valor);
        }

        return $euro;
    }

    private function getUtm()
    {
        $utm = null;

        $JsonSource = "https://api.sbif.cl/api-sbifv3/recursos_api/utm?apikey=" . $this->key . "&formato=json";

        $json = json_decode($this->consume_api($JsonSource));

        if (!isset($json->CodigoHTTP)) {
            $utm    =  str_replace(array("$", ".", ","), array("", "", "."), $json->UTMs[0]->Valor);
        }

        return $utm;
    }

    function actualizaMonedas()
    {
        if ($this->debugger) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }

        $dolar = $this->getDolar();
        if (!is_null($dolar)) {
            $statusDolar = $this->updateMoneda($dolar, "DOLAR");
        }


        $euro = $this->getEuro();
        if (!is_null($euro)) {
            $statusEuro = $this->updateMoneda($euro, "EURO");
        }

        $utm = $this->getUtm();
        if (!is_null($utm)) {
            $statusUTM = $this->updateMoneda($utm, "UTM");
        }

        if ($this->debugger) {
            $this->getMoneda();
        }

        $this->_db->close();
    }

    private function getMoneda()
    {
        $sql = "SELECT *FROM ci_precio_monedas";
        $busca = $this->_db->query($sql);

        while ($obj = $busca->fetch_array(MYSQLI_ASSOC)) {
            echo "<pre>";
            print_r($obj);
            echo "</pre>";
        }
    }

    private function updateMoneda($valor, $moneda)
    {

        $sql = "UPDATE ci_precio_monedas SET
                    ci_pm_fecha = sysdate(),
                    ci_pm_precio = $valor 
                WHERE 
                    ci_pm_moneda = '$moneda'";

        $modifica = $this->_db->query($sql);

        if (!$modifica) {
            return  false;
        } else {
            return true;
        }
    }
}
