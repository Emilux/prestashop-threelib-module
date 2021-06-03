<?php
//Lien du site de l'api
define('API_URL_SITE','https://api.emilien-fuchs.fr');

//Chemin vers l'emplacement de l'api
define('API_PATH','/api');

//Chemin endpoint des models
define('MODELS_PATH','/models');

//Chemin endpoint des users
define('USER_PATH','/users');

//Chemin endpoint du login
define('LOGIN_PATH','/login');

//Chemin endpoint pour refresh le token
define('REFRESH_PATH',API_PATH.'/token/refresh');

//Chemin vers le dossier tmp
define('TMP_PATH',_PS_MODULE_DIR_."threelib/tmp");
