
[![Build status — Travis-CI][travis-icon]][travis]


# moodle-auth_ouopenid

This plugin is a wrapper around the [OpenID authentication plugin][openid] for [Moodle][].

Available via IET-OU Satis:

* <https://embed.open.ac.uk/iet-satis/#!/moo>


## Install

1. To integrate within Moodle, either use the composer-based method described below, or unzip the code at:
    ```
    PATHTOMOODLE/auth/ouopenid
    ```

    Then, enable the plugin in the [authentication plugins section of your Moodle's site administration][auth].

2. To test the plugin in standalone mode, install via [Composer][]:

```sh
composer install
composer sym-links
composer npm-install
composer eslint-config
```

## Test

```sh
composer test
composer eslint
```

## Site-wide Javascript and styles
### Admin / Additional HTML

To embed the plugin's Javascript and stylesheet on every page:

1. Visit the [Additional HTML section of your Moodle's site administration][addhtml];
2. Copy and paste the HTML snippet below;
3. Press the "Save changes" button.


```html
<link href="/auth/ouopenid/user/ouop-styles.css" rel="stylesheet" />

<script src="/auth/ouopenid/user/ouop-local-fixes.js"></script>
<script src="/auth/ouopenid/user/script.js"></script>
```


Developed for the [TeSLA project][].

---
© 2017 [The Open University][ou]. ([Institute of Educational Technology][iet])


[auth]: http://your-moodle.example.com/admin/settings.php?section=manageauths
    "Your Moodle > Site administration > Plugins > Authentication > Manage Authentication"
[addhtml]: http://your-moodle.example.com/admin/settings.php?section=additionalhtml#admin-additionalhtmlfooter
    "Your Moodle > Site administration > Appearance > Additional HTML"

[TeSLA project]: http://tesla-project.eu/
[Moodle]: https://moodle.org/
[openid]: https://github.com/remotelearner/moodle-auth_openid
[ouopenid]: https://github.com/IET-OU/moodle-auth_ouopenid
[composer]: https://getcomposer.org/
[iet]: http://iet.open.ac.uk/
[ou]: http://www.open.ac.uk/
[travis]:  https://travis-ci.org/IET-OU/moodle-auth_ouopenid
[travis-icon]: https://api.travis-ci.org/IET-OU/moodle-auth_ouopenid.svg
    "Build status – Travis-CI (PHP + NPM/eslint)"
