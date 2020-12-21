Digital Ascetic Base User
=======

This library provide basic abstract class to work with User entity.

Also has implemented routes and services to handle login, logout (form template) and reset password (through api rest
calls).

## Configuration

The security/user system is pluggable and can be configured this way:

#### config/packages/digital_ascetic_base_user.yaml:

```yaml
digital_ascetic_base_user:
  user_class: 'App\Entity\User' /// User entity class
```

#### config/packages/security.yaml:

```yaml
security:
  providers:
    base_user_provider:
      id: DigitalAscetic\BaseUserBundle\Security\UserProvider
```

#### config/routes.yaml:

```yaml
asc_base_user:
  resource: "@DigitalAsceticBaseUserBundle/Resources/config/all.xml"
```

## Reset Password

You can implement your own form and template and call our ResetPasswordService to handle this functionality.

ResetPasswordService dispatch an BaseUserEvent::USER_RESET_PASSWORD_REQUESTED event that allow you for example send an
instan email.

## Extends

This bundle is compatible with JMSSerializerBundle, detecting if it's enabled and add AbstractBaseUser serialize
mappings to its configuration.

```
    id:
      groups: [ id ]
      type: integer
    username:
      groups: [ user.default ]
      type: string
    email:
      groups: [ user.default ]
      type: string
    plainPassword:
      groups: [ user.default ]
      type: string
    roles:
      groups: [ user.roles ]
      type: array
```

## Testing

Run test executing:

```
./vendor/bin/simple-phpunit
```