<?php

/**
 * Class CurlCommand
 */
class CurlCommand
{
    /**
     * @var CurlHandle|false|resource
     */
    private $curl;

    /**
     * @var array
     */
    private $curlOpt;

    /**
     * @var array
     */
    private $Opt;

    /**
     * @var bool
     */
    private $overrideDefault;

    /**
     * @var string
     */
    private $authorization;

    /**
     * CurlCommand constructor.
     */
    public function __construct(bool $overrideDefault = false, array $curlOpt = [])
    {
        $this->Opt = $curlOpt;
        $this->overrideDefault = $overrideDefault;
    }

    /**
     * init curl opt
     */
    private function initOpt(){

        //Check if Opt Total override is needed
        if (!$this->overrideDefault){

            $this->curlOpt =
                array(
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 50,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => '',
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; fr; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13',
                    CURLOPT_POSTFIELDS =>'',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                    ),
                );

            //Check if Opt is added to the base $curlOpt array
            if (!empty($this->Opt)){
                foreach ($this->Opt as  $key => $option){
                    $this->addOpt($key, $option);
                }
            }

        }

    }

    /**
     * @param string $optName
     * @param string $optContent
     */
    private function addOpt(string $optName, string $optContent){
        $this->curlOpt = array_replace($this->curlOpt, array(constant ( $optName ) => $optContent));
    }

    private function removeOpt(string $optName){
        unset($this->curlOpt[constant ( $optName )]);
    }

    /**
     * @param string $optName
     * @param string $optContent
     */
    private function addHttpHeaderOpt(string $optName, string $optContent){
        if (!isset($this->curlOpt[constant ( 'CURLOPT_HTTPHEADER' )])){
            $this->curlOpt = array_replace($this->curlOpt, [constant ( 'CURLOPT_HTTPHEADER' )=>array($optName.": ".$optContent)] );
        } else {
            array_push($this->curlOpt[constant ( 'CURLOPT_HTTPHEADER' )], $optName.": ".$optContent);
        }
    }

    /**
     * @param string $url
     * @param string $requestType
     * @param array $requestContent
     * @param string|null $cookieName
     * @param bool $readOnly
     * @return mixed|string[]
     */
    public function crudCommand(string $url, string $requestType, array $requestContent, string $cookieName = NULL, bool $readOnly = false, bool $WriteRead = false){

        $this->initCurl();

        $this->addOpt('CURLOPT_CUSTOMREQUEST',$requestType);
        $this->addOpt('CURLOPT_URL',$url);
        if ($requestContent){
            $this->addOpt('CURLOPT_POSTFIELDS',json_encode($requestContent));
        } else {
            $this->removeOpt('CURLOPT_HTTPHEADER');
        }


        if ($cookieName !== NULL){
            if (!$readOnly){
                if ($WriteRead){
                    $this->addOpt('CURLOPT_COOKIEJAR',TMP_PATH.DIRECTORY_SEPARATOR.$cookieName);
                    $this->addOpt('CURLOPT_COOKIEFILE',TMP_PATH.DIRECTORY_SEPARATOR.$cookieName);
                } else {
                    $cookieName = md5($cookieName);
                    $this->addOpt('CURLOPT_COOKIEJAR',TMP_PATH.DIRECTORY_SEPARATOR.$cookieName);
                }

            }

            if ($readOnly){
                $this->addOpt('CURLOPT_COOKIEFILE',TMP_PATH.DIRECTORY_SEPARATOR.$cookieName);
                $this->removeOpt('CURLOPT_COOKIEJAR');
            }
        }


        if (!empty($this->authorization))
            $this->addHttpHeaderOpt('Authorization', $this->authorization);





        curl_setopt_array($this->curl,$this->curlOpt);

        $response = curl_exec($this->curl);

        $http_code = curl_getinfo($this->curl,CURLINFO_HTTP_CODE);

        if (!$response && $http_code !== 204){
            $error = array('error'=>'curl error : ' . curl_error($this->curl));
            curl_close($this->curl);
            return $error;
        }


        curl_close($this->curl);

        $response = json_decode($response, true);
        $response['http_code'] = $http_code;

        return $response;
    }

    /**
     * Inialisation des options et de CURL
     */
    private function initCurl(){
        $this->curl = curl_init();
        $this->initOpt();
    }

    /**
     * Ajoute une authorisation au header de curl
     * @param string $Authorization
     */
    public function setAuthorization(string $Authorization){
        if (!empty($Authorization))
            $this->authorization = $Authorization;
    }

    /**
     * Supprime l'autorisation
     */
    public function resetAuthorization(){
        $this->authorization = '';
    }





}