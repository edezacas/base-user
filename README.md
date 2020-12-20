Digital Ascetic Base User
=======

This library provide basic abstract classes to work with objects and entities.

### Config
The security/user system is pluggable and can be configured this way:

```yaml

digital_ascetic_base_user:   
  user_class: 'App\Entity\User'

```

### Extends

This bundle is compatible with JMSSerializerBundle, detecting if it's enabled and add AbstractBaseUser serialize mappings to its configuration.