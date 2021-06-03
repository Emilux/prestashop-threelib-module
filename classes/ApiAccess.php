<?php

/**
 * Class ApiAccess
 */
class ApiAccess
{
    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var CurlCommand
     */
    private $curlCommand;

    /**
     * @var Authentication
     */
    private $Authentification;

    /**
     * ApiAccess constructor.
     * @param string $apiUrl
     * @param CurlCommand $curlCommand
     * @param Authentication $Authentication
     */
    public function __construct(string $apiUrl, CurlCommand $curlCommand, Authentication $Authentication)
    {
        $this->apiUrl = $apiUrl;
        $this->curlCommand = $curlCommand;
        $this->Authentification = $Authentication;
    }

    /**
     * @param string $path
     * @param array $userInfo
     * @return mixed|string
     */
    public function login(string $path, array $userInfo){

        $reponse = false;

        if (isset($userInfo['username']) && isset($userInfo['password'])){
            $reponse = $this->curlCommand->crudCommand($this->apiUrl.$path,'POST',$userInfo,$userInfo['username']);
        }

        return $reponse;
    }

    /**
     * @param string $token
     * @return bool
     */
    public function retryLogin(string $path, string $authCookieName){
        $reponse = $this->curlCommand->crudCommand($this->apiUrl.$path,'POST',array(), $authCookieName,false,true);
        if (isset($reponse['http_code'])){
            if ($reponse['http_code'] === 401){
                return $reponse;
            }
        }
        return $reponse;
    }

    public function getElement(string $path, string $authCookieName){
        $reponse = false;

        if ($authCookieName){
            $reponse = $this->curlCommand->crudCommand($this->apiUrl.$path,'GET',array(),$authCookieName,true);

            if (isset($reponse['http_code'])){
                if ($reponse['http_code'] === 401){

                    $refresh = $this->retryLogin(REFRESH_PATH, $authCookieName);

                    if ($refresh['http_code'] === 200 || $refresh['http_code'] === 204){
                        $reponse = $this->curlCommand->crudCommand($this->apiUrl.$path,'GET',array(),$authCookieName,true);
                    } elseif ($refresh === 'logout') {
                        $this->Authentification->logout();

                    } else {
                        return $refresh;
                    }
                }
            }
        }



        return $reponse;
    }

}