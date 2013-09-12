
This module needs Jelix 1.4.5 minimum

- copy the jcas directory into your application
- copy the configuration file jcas/plugins/coord/cas/cas.coord.ini.php.dist
  into your application var/config/cas.coord.ini.php
- in the configuration of your application, declare the cas plugin for the coordinator

    [coordplugins]
    cas = cas.coord.ini.php
    cas.name = auth

    Important: you should remove the "auth" plugin from this section!

- configure the file var/config/cas.coord.ini.php

- deactivate following rights in jAcl2,  for ALL groups and ALL users:
    - auth.user.change.password "The user can change his password"
    - auth.users.change.password "Change the password of a user"
    - auth.users.create "Create a new user"
    - auth.users.delete "Delete a user"



