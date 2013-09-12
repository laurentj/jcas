
This module needs Jelix 1.4.5 minimum

- copy the jcas directory into your application
- copy the configuration file jcas/plugins/coord/auth/auth.coord.ini.php.dist
  into your application var/config/auth.coord.ini.php
- in the configuration of your application, declare the auth plugin for the coordinator

    [coordplugins]
    auth = auth.coord.ini.php

- configure the file var/config/auth.coord.ini.php

- deactivate following rights in jAcl2,  for ALL groups and ALL users:
    - auth.user.change.password "The user can change his password"
    - auth.users.change.password "Change the password of a user"
    - auth.users.create "Create a new user"
    - auth.users.delete "Delete a user"



