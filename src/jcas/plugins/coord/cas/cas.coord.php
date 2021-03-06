<?php
/**
* @author  Laurent Jouanneau
* @copyright  2013 Laurent Jouanneau
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

require(dirname(__FILE__).'/../../../lib/CAS.php');

require(JELIX_LIB_PATH.'auth/jAuth.class.php');
require(JELIX_LIB_PATH.'auth/jAuthDummyUser.class.php');

/**
* the plugin for the coordinator, that checks authentication at each page call
*/
class casCoordPlugin implements jICoordPlugin {
    public $config;
    public $errorMessage = '';
    /**
     * TODO: support lang
     */
    function __construct($conf){
        $this->config = $conf;

        if (!isset($this->config['session_name'])
            || $this->config['session_name'] == ''){
            $this->config['session_name'] = 'JELIX_USER';
        }

        $casConf = $conf['cas'];
        // Initialize phpCAS
        switch($casConf['server_version']) {
            case 'CAS_1.0': $version = CAS_VERSION_1_0; break;
            case 'CAS_2.0': $version = CAS_VERSION_2_0; break;
            case 'SAML_1.1':
            default:
                $version = SAML_VERSION_1_1; break;
        }

        if (isset($casConf['debug_log']) && $casConf['debug_log']!='') {
            $logFile = str_replace(array('app:','lib:','var:', 'www:'),
                                    array(jApp::appPath(), LIB_PATH, jApp::varPath(), jApp::wwwPath()),
                                    $casConf['debug_log']);
            phpCAS::setDebug($logFile);
        }

        phpCAS::client($version, $casConf['host'], intval($casConf['port']), $casConf['context']);
        phpCAS::setHTMLheader('<div id="cas-error"><h2>__TITLE__</h2>');
        phpCAS::setHTMLFooter('</div>');

        if ($casConf['server_ca_cert_path']) {
            $realPath = str_replace(array('app:','lib:','var:', 'www:'),
                                    array(jApp::appPath(), LIB_PATH, jApp::varPath(), jApp::wwwPath()),
                                    $casConf['server_ca_cert_path']);
            phpCAS::setCasServerCACert($realPath);
        }

        if (isset($casConf['disable_validation']) && $casConf['disable_validation'])
            phpCAS::setNoCasServerValidation();
    }

    /**
     * @param    array  $params   plugin parameters for the current action
     * @return null or jSelectorAct  if action should change
     */
    public function beforeAction ($params){
        $notLogged = false;
        $selector = null;

        //Creating the user's object if needed
        if (! isset ($_SESSION[$this->config['session_name']])){
            $notLogged = true;
            $_SESSION[$this->config['session_name']] = new jAuthDummyUser();
        }else{
            $notLogged = ! jAuth::isConnected();
        }
        
        try {
            ob_start(); // we want to intercept error displayed by phpCAS :-/

            // Handle SAML logout requests that emanate from the CAS host exclusively.
            if (isset($this->config['cas']['real_hosts']))
                phpCAS::handleLogoutRequests(true, $this->config['cas']['real_hosts']);

            $needAuth = isset($params['auth.required']) ? ($params['auth.required']==true):$this->config['auth_required'];

            if ($needAuth) {
                // if this is an ajax request, we don't want redirection to a web page
                // so we shouldn't force authentication if we are not logged
                if ($notLogged && jApp::coord()->request->isAjax()) {
                    throw new jException($this->config['error_message']);
                }

                $authok = false;
                // force authentication
                phpCAS::forceAuthentication();

                // if we are here, this is because we are authenticated with the CAS server
                if ($notLogged) {
                    // we didn't not authenticated at the jelix layer
                    $login = phpCAS::getUser();
                    // first try to get the user from the database
                    $user = jAuth::getUser($login);
                    // the user doesn't exist: let's create it
                    if (!$user) {
                        if ($this->config['cas']['automatic_registering']) {
                            $user = jAuth::createUserObject($login, '');
                            $user->email = '';
                            jEvent::notify ('CASNewUser', array('user'=>$user));
                            jAuth::saveNewUser($user);
                            // do login with jAuth
                            // it may fails if a module forbid the given user for example
                            $authok = jAuth::login($login,'');
                        }
                        else {
                            $authok = false;
                        }
                    }
                    else {
                        // do login with jAuth
                        // it may fails if a module forbid the given user for example
                        $authok = jAuth::login($login,'');
                    }
                }
                else
                    $authok = true;

                if (!$authok) {
                    // call the page that says that we are not authenticated
                    $selector = new jSelectorAct($this->config['on_error_action']);
                }
            }

            ob_end_clean(); // erase traces made by phpCas
        }
        catch(CAS_Exception $error) {
            $this->errorMessage = ob_get_clean(); // retrieve phpCas HTML output
            $selector= new jSelectorAct($this->config['on_error_action']);
        }
        
        return $selector;
    }


    public function beforeOutput(){}

    public function afterProcess (){}

}
