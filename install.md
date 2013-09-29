
This module needs Jelix 1.4.5 minimum

- deinstall module jauth
- activate module jauthdb
- copy the jcas directory into your application
- run php cmd.php installmodule jcas
- configure the file var/config/cas.coord.ini.php
- if some modules use jauth, you should change them. For exemple:
    - master_admin: you should provide your own template in your var/theme, to replace zone_admin_infobox.tpl


If you don't execute Jelix's installer, here is you should do:

- copy the configuration file jcas/install/cas.coord.ini.php into your application var/config/
- in the configuration of your application:

   1) declare the cas plugin for the coordinator

        [coordplugins]
        cas = cas.coord.ini.php
        cas.name = auth

        Important: you should remove the "auth" plugin from this section!

   2) deactivate the jauth module from the "modules" section

- deactivate following rights in jAcl2,  for ALL groups and ALL users:
    - auth.user.change.password "The user can change his password"
    - auth.users.change.password "Change the password of a user"
    - auth.users.create "Create a new user"
    - auth.users.delete "Delete a user"
 In fact, you have to deactivate all features in your application which allow to modify
 or set a password: this is useless with CAS.


