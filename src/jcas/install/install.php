<?php
/**
* @author      Laurent Jouanneau
* @contributor
* @copyright   2013 Laurent Jouanneau
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

/**
 */
class jcasModuleInstaller extends jInstallerModule {

    function install() {
        if ($this->entryPoint->type == 'cmdline')
            return;
        $casconfig = $this->config->getValue('cas','coordplugins');
        $casconfigMaster = $this->config->getValue('cas','coordplugins', null, true);
        if (!$casconfig && !$casconfigMaster) {
            $entrypointconfig = $this->config->getOverrider();
            $entrypointconfig->setValue('cas','cas.coord.ini.php','coordplugins');
            $entrypointconfig->setValue('cas.name','auth','coordplugins');
            $entrypointconfig->removeValue('auth','coordplugins');

            $configfile = jApp::configPath('cas.coord.ini.php');
            if ($this->firstExec('jcas:installconfigfile')) {
                if (!file_exists($configfile)) {
                    $this->copyFile('cas.coord.ini.php', $configfile);
                }
            }
        }
        if ($this->firstExec('jcas:acl2')) {
            if (class_exists('jAcl2DbManager')) {
                $groups = jDao::get('jacl2db~jacl2group', 'jacl2_profile')->findAll();
                foreach($groups as $group) {
                    $id = $group->id_aclgrp;
                    jAcl2DbManager::removeRight($id, 'auth.user.change.password', '-', true);
                    jAcl2DbManager::removeRight($id, 'auth.users.change.password', '-', true);
                    //jAcl2DbManager::removeRight($id, 'auth.users.create', '-', true);
                    //jAcl2DbManager::removeRight($id, 'auth.users.delete', '-', true);
                }
            }
        }
        $authconfig =  new jIniFileModifier(jApp::configPath('cas.coord.ini.php'));
        $daoName = $authconfig->getValue('dao', 'cas');
        if ($daoName == 'jauthdb~jelixuser' && $this->firstDbExec()) {
            $this->execSQLScript('install_jauth.schema', 'jauthdb');
            $login = $this->getParameter('useradmin');
            $email =  $this->getParameter('emailadmin');
            if ($login) {
                $cn = $this->dbConnection();
                $cn->exec("INSERT INTO ".$cn->prefixTable('jlx_user')." (usr_login, usr_password, usr_email ) VALUES
                            (".$cn->quote($login).", '' , ".$cn->quote($email).")");
                jAcl2DbUserGroup::createUser($login);
                jAcl2DbUserGroup::addUserToGroup($login, 'admins');
            }
        }

    }
}