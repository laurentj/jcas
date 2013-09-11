<?php
/**
* @package    jelix
* @subpackage auth_driver
* @author     Laurent Jouanneau
* @copyright  2005-2013 Laurent Jouanneau
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/


/**
* authentification driver for authentification with a CAS server
*/
class casAuthDriver extends jAuthDriverBase implements jIAuthDriver {

    public function saveNewUser($user){
        $dao = jDao::get($this->_params['dao'], $this->_params['profile']);
        $dao->insert($user);
        return true;
    }

    public function removeUser($login){
        $dao = jDao::get($this->_params['dao'], $this->_params['profile']);
        $dao->deleteByLogin($login);
        return true;
    }

    public function updateUser($user){
        $dao = jDao::get($this->_params['dao'], $this->_params['profile']);
        $dao->update($user);
        return true;
    }

    public function getUser($login){
        $dao = jDao::get($this->_params['dao'], $this->_params['profile']);
        return $dao->getByLogin($login);
    }

    public function createUserObject($login,$password){
        $user = jDao::createRecord($this->_params['dao'], $this->_params['profile']);
        $user->login = $login;
        // we ignore password since it is managed by the CAS server
        $user->password = '--no password--';
        return $user;
    }

    public function getUserList($pattern){
        $dao = jDao::get($this->_params['dao'], $this->_params['profile']);
        if($pattern == '%' || $pattern == ''){
            return $dao->findAll();
        }else{
            return $dao->findByLogin($pattern);
        }
    }

    public function changePassword($login, $newpassword){
        // we ignore password since it is managed by the CAS server
        return 1;
    }

    public function verifyPassword($login, $password){
        // we ignore password since it is managed by the CAS server
        $daouser = jDao::get($this->_params['dao'], $this->_params['profile']);
        $user = $daouser->getByLogin($login);
        if (!$user) {
            return false;
        }
        return $user;
    }
}
