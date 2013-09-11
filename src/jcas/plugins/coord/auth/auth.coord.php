<?php
/**
* @package    jelix
* @subpackage coord_plugin
* @author  Laurent Jouanneau
* @copyright  2013 Laurent Jouanneau
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

require(dirname(__FILE__).'/../../../lib/CAS.php');

require(JELIX_LIB_PATH.'auth/jAuth.class.php');
require(JELIX_LIB_PATH.'auth/jAuthDummyUser.class.php');

/**
* @package    jelix
* @subpackage coord_plugin
*/
class AuthCoordPlugin implements jICoordPlugin {
    public $config;

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

        phpCAS::client($version, $casConf['host'], $casConf['port'], $casConf['context']);

        if ($casConf['server_ca_cert_path'])
            phpCAS::setCasServerCACert($casConf['server_ca_cert_path']);

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

        // Handle SAML logout requests that emanate from the CAS host exclusively.
        if (isset($this->config['cas']['real_hosts']))
            phpCAS::handleLogoutRequests(true, $this->config['cas']['real_hosts']);
        
        // Force CAS authentication on any page that includes this file
        

        
        $needAuth = isset($params['auth.required']) ? ($params['auth.required']==true):$this->config['auth_required'];
        $authok = false;

        if ($needAuth && $notLogged) {
            if (jApp::coord()->request->isAjax()) {
                throw new jException($this->config['error_message']);
            }

            // force authentication
            phpCAS::forceAuthentication();
            // set $authok with what we find in phpCAS session values
//FIXME

        }
        elseif (!$notLogged && $needAuth) {
            // we are logged, at least at the jelix layer.
            
            // force CAS authentication to be sure we are still authenticated
            // set $authok with what we find in phpCAS session values
            phpCAS::forceAuthentication();

//FIXME

            if (jApp::coord()->request->isAjax()) {
                throw new jException($this->config['error_message']);
            }

            //phpCAS::getUser();
            $authok= true;
        }

        if (!$authok) {
            // call the page that says that we are not authenticated
            $selector= new jSelectorAct($this->config['on_error_action']);
        }
        
        
        return $selector;
    }


    public function beforeOutput(){}

    public function afterProcess (){}

}
