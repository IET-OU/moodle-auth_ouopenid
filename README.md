
[![Build status — Travis-CI][travis-icon]][travis]


# moodle-auth_ouopenid

This plugin is a wrapper around the [OpenID authentication plugin][openid] for [Moodle][].


## Install

1. To integrate within Moodle, either use the composer-based method described below, or unzip the code at:
    ```
    PATHTOMOODLE/auth/ouopenid
    ```

    Then, enable the plugin in the Moodle admin interface.

2. To test the plugin in standalone mode, install via [Composer][]:

```sh
composer install
composer npm-install
composer eslint-config
```

## Test

```sh
composer test
```


---
© 2017 [The Open University][ou]. ([Institute of Educational Technology][iet])


[Moodle]: https://moodle.org/
[openid]: https://github.com/remotelearner/moodle-auth_openid
[ouopenid]: https://github.com/IET-OU/moodle-auth_ouopenid
[composer]: https://getcomposer.org/
[iet]: http://iet.open.ac.uk/
[ou]: http://www.open.ac.uk/
[travis]:  https://travis-ci.org/IET-OU/moodle-auth_ouopenid
[travis-icon]: https://api.travis-ci.org/IET-OU/moodle-auth_ouopenid.svg
    "Build status – Travis-CI (PHP + NPM/eslint)"
