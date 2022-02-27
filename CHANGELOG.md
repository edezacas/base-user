# CHANGELOG

2.4.0
-----
* Implemented command UpdateUserPassword.

2.3.0
-----
* Added Travis.
* Removed deprecations.

2.0.2
-----
* Fixed login template form action login_check.

2.0.1
-----
* Fixed dependencies version.

2.0.0
-----

* Routing files moved to Resources/config/routes directory.
* Implemented UserManagerInterface.
* Changed UserService implementing UserManagerInterface and handling plaiPassword encoding, and enabled value. 
* Removed UserDoctrineSubscriber; instead of it handle trough UserService::updateUser method.

1.2.2
-----
* Allow symfony/orm-pack version 2 and upper 

1.2.1
-----
* Handle empty username field

1.2.0
-----
* Added enabled field to AbstractBaseUser.
* Implemented UserChecker to allow logged in by enabled field.
* Added config parameters `firewall_name` and `user_enabled`.

1.1.2
-----
* Fixed composer.json

1.1.1
-----
* Fixed composer.json require version.

1.1.0
-----
* Implemented UserDoctrineSubscriber encoding and persist user password if plainPassword field has value.

1.0.2
-----
* Fixed UserProvider implementation of UserProviderInterface versions 4 and 5 incompatibility.

1.0.1
-----
* Added symfony/twig as dependency

1.0.0
-----
* Symfony 4 and above compatible. 
