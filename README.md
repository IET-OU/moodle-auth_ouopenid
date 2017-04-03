
[![Build status — Travis-CI][travis-icon]][travis]
[![js-semistandard-style][semi-icon]][semi]


# moodle-auth_ouopenid

This plugin is a wrapper around the [OpenID authentication plugin][openid] for [Moodle][].

Available via IET-OU Satis:

* <https://embed.open.ac.uk/iet-satis/#!/moo>

## Purpose

To facilitate pilot studies using Moodle (e.g. for TeSLA) by:

1. Enabling OpenID login using just a username, not a full URL (e.g. https://openid.example.org/{username})
2. Simple login page with custom instructions (edit via Moodle language customisations), `/index.php`,
3. Importing pilot-related data into a separate DB table, `mdl_auth_ouopenid_users`,
4. Making the pilot-related data available via `/user/ajax.php`,
5. Tries to redirect the participant to a bespoke URL (a work-in-progress),
6. LESS styles to hide parts of the Moodle user-interface to simplify the experience,
7. Javascript & LESS fixes relating to the pilot study,
8. ...?

Note: this plugin probably needs splitting into two or more plugins!

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
<link href="/auth/ouopenid/style/ouop-styles.less" rel="stylesheet/less" />
<script src="/auth/ouopenid/user/ouop-local-fixes.js"></script>
<script src="/auth/ouopenid/user/ouop-analytics"></script>
<script src="/auth/ouopenid/user/script.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.7.2/less.min.js"></script>
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
[semi]: https://github.com/Flet/semistandard
[semi-icon]: https://img.shields.io/badge/code%20style-semistandard-brightgreen.svg?style=flat-square
    "Javascript coding style — 'semistandard'"
