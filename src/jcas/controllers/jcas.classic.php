<?php
/**
* @author  Laurent Jouanneau
* @copyright  2013 Laurent Jouanneau
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

class jcasCtrl extends jController {

    function logout() {
        phpCAS::logout();

        return $this->notauthenticated();
    }

    function notauthenticated() {
        $rep = $this->getResponse('html');
        $rep->title = 'Not authenticated';
        $tpl = new jTpl();
        $rep->body->assign('MAIN',$tpl->fetch('notauthenticated'));
        return $rep;
    }
}

