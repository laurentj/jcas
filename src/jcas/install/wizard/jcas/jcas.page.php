<?php

/**
 * page pour choisir l'authentification
 */
class jcasWizPage extends installWizardPage {

    /**
     * action to display the page
     * @param jTpl $tpl the template container
     */
    function show ($tpl) {
        if (!isset($_SESSION['jcasconf'])) {
            $_SESSION['jcasconf'] = $this->_loadconf();
        }
        $tpl->assign($_SESSION['jcasconf']);

        $listhosts = null;
        if (isset($this->config['defaulthost'])) {
            if(is_string($this->config['defaulthost']))
                $listhosts = explode(',', trim($this->config['defaulthost']));
            else
                $listhosts = $this->config['defaulthost'];
        }

        $tpl->assign("listhosts", $listhosts);

        return true;
    }

    /**
     * action to process the page after the submit
     */
    function process() {
        $_SESSION['jcasconf'] = $_POST;
        if (trim($_POST['host']) == '') {
            $_SESSION['jcasconf']['error'] = 'host est obligatoire';
            return false;
        }

        if (trim($_POST['port']) == '' || intval($_POST['port']) == 0) {
            $_SESSION['jcasconf']['error'] = 'Le port est obligatoire et doit être numerique';
            return false;
        }
        if (trim($_POST['login']) == '') {
            $_SESSION['jcasconf']['error'] = 'Le login de l\'administrateur est obligatoire';
            return false;
        }

        $ini = new jIniFileModifier(jApp::configPath('defaultconfig.ini.php'));
        $ini->setValue('jcas.installparam', 'useradmin='.$_POST['login'].';emailadmin='.$_POST['email'], 'modules');
        $ini->save();
        $ini = new jIniFileModifier(jApp::configPath('cas.coord.ini.php'));
        $ini->setValue('host', $_POST['host'], 'cas');
        $ini->setValue('port', $_POST['port'], 'cas');
        $ini->setValue('context', $_POST['context'], 'cas');
        $ini->setValue('automatic_registering', $_POST['ar'], 'cas');
        $ini->save();
        unset($_SESSION['jcasconf']);
        return 0;
    }
    
    protected function _loadconf() {
        $defaultPort = 443;
        $defaultContext = 'cas/';
        $configfile = jApp::configPath('cas.coord.ini.php');
        if (file_exists($configfile)) {
            $ini = new jIniFileModifier($configfile);
            $host = $ini->getValue('host', 'cas');
            $port = $ini->getValue('port', 'cas');
            $context = $ini->getValue('context', 'cas');
            $ar = $ini->getValue('automatic_registering', 'cas');
            if ($port === null)
                $port = $defaultPort;
            if ($context === null)
                $context = $defaultContext;
            return array('host'=>$host, 'port'=>$port, 'context'=>$context, 'ar'=>$ar, 'login'=>'', 'email'=>'', 'error'=>'');
        }
        else {
            return array('host'=>'', 'port'=>$defaultPort, 'context'=>$defaultContext, 'ar'=>'off', 'login'=>'', 'email'=>'', 'error'=>'');
        }
    }
}
