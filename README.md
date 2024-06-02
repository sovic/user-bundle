# UserBundle

Add the following to your `config/services.yaml` file:

```
    UserBundle\:
        resource: '../vendor/sovic/user-bundle/src/'
        exclude:
            - '../vendor/sovic/user-bundle/src/Entity/'

    UserBundle\User\UserFactoryInterface:
        alias: App\User\UserFactory
        public: true

    _instanceof:
        UserBundle\User\UserFactoryInterface:
          tags: [ 'user.user_factory' ]
          calls:
            - [ setEntityClass, [ 'App\Entity\User' ] ]
            - [ setModelClass, [ 'App\User\User' ] ]
```