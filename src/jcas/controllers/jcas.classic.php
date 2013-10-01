<?php
/**
* @author  Laurent Jouanneau
* @copyright  2013 Laurent Jouanneau
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class jcasCtrl extends jController {

    public $pluginParams = array(
        '*' => array('auth.required'=>false)
    );

    function logout() {
        jAuth::logout();
        phpCAS::logout();
        return $this->notauthenticated();
    }

    function notauthenticated() {
        $rep = $this->getResponse('htmlauth');
        $rep->title = 'Not authenticated';
        $tpl = new jTpl();
        $plugin = jApp::coord()->getPlugin('auth');
        $tpl->assign('error', $plugin->errorMessage);
        $rep->body->assign('MAIN',$tpl->fetch('notauthenticated'));
        return $rep;
    }
}

