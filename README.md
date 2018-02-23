
[![Build status — Travis-CI][travis-icon]][travis]
[![js-semistandard-style][semi-icon]][semi]
[![OU-OpenID moodle plugin][browse-icon]][gh]


# moodle-auth_ouopenid

This plugin is a wrapper around the [OpenID authentication plugin][openid] for [Moodle][],
plus other research-related functionality.

Available via IET-OU Satis:

* <https://embed.open.ac.uk/iet-satis/#!/moo>

## Purpose

To facilitate pilot research studies using Moodle (e.g. for TeSLA) by:

1. Enabling OpenID login using just a username, not a full URL (e.g. <https://example.org/openid/{username}>)
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
    ```sh
    {PATHTOMOODLE}/auth/ouopenid
    ```

    Then, enable the plugin in the [authentication plugins section of your Moodle's site administration][auth].

2. To test the plugin in standalone mode, install via [Composer][]:

```sh
composer install
composer sym-links
composer npm-install
composer eslint-config
composer build
```

## Test

```sh
composer test
composer eslint
```

## Generate text-images

Re-create images of long-texts / poetry, to prevent copy-paste
(e.g. for TeSLA keystroke dynamics pilot):

```sh
composer phantom-clone
composer text-srv
composer text-images
```

## Site-wide Javascript and styles
### Additional HTML - development

To embed the plugin's Javascript and stylesheet on every page:

1. Visit the [Additional HTML section of your Moodle's site administration][addhtml];
2. Copy and paste the HTML snippet below;
3. Press the "Save changes" button.


```html
<link href="/auth/ouopenid/style/ouop-styles.less" rel="stylesheet/less" />
<script src="/auth/ouopenid/dist.js"></script>

<script src="https://unpkg.com/less@2.7.2/dist/less.min.js"></script>
```


### Additional HTML - live

```html
<link href="/auth/ouopenid/style/ouop-styles.css?r=2017-08-16.a" rel="stylesheet" />
<script src="/auth/ouopenid/dist.min.js?r=2017-08-16.a"></script>
```


Developed for the [TeSLA project][].

---
© 2017-2018 [The Open University][ou]. ([Institute of Educational Technology][iet])


[auth]: https://example.edu/your-moodle/admin/settings.php?section=manageauths
    "Your Moodle > Site administration > Plugins > Authentication > Manage Authentication"
[addhtml]: https://example.edu/your-moodle/admin/settings.php?section=additionalhtml#admin-additionalhtmlfooter
    "Your Moodle > Site administration > Appearance > Additional HTML"

[TeSLA project]: http://tesla-project.eu/
[Moodle]: https://moodle.org/
[openid]: https://github.com/remotelearner/moodle-auth_openid
[ouopenid]: https://github.com/IET-OU/moodle-auth_ouopenid
[gh]: https://github.com/IET-OU/moodle-auth_ouopenid
[composer]: https://getcomposer.org/
[npm]: https://npmjs.com/
[iet]: https://iet.open.ac.uk/
[ou]: http://www.open.ac.uk/
[travis]:  https://travis-ci.org/IET-OU/moodle-auth_ouopenid
[travis-icon]: https://api.travis-ci.org/IET-OU/moodle-auth_ouopenid.svg
    "Build status – Travis-CI (PHP + NPM/eslint)"
[semi]: https://github.com/Flet/semistandard
[semi-icon]: https://img.shields.io/badge/code%20style-semistandard-brightgreen.svg?style=flat-square
    "Javascript coding style — 'semistandard'"
[browse]: https://npmjs.com/package/browserify
[browse-icon]: https://img.shields.io/badge/built_with-browserify-blue.svg
    "Built with browserify"
