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
                    jAcl2DbManager::removeRight($id, 'auth.users.create', '-', true);
                    jAcl2DbManager::removeRight($id, 'auth.users.delete', '-', true);
                }
            }
        }
    }
}