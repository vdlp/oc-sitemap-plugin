# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.4.0] - 2025-07-03

* Added support for October CMS 4.x

## [2.3.0] - 2023-05-31

* Supports October CMS 3.x only.
* Supports PHP 8.0.2 or higher.
* General maintenance.

## [2.2.0] - 2022-11-15

* Add support for images.
* Add sitemap config resolver to configure the sitemap config on runtime. This can be useful for multisite projects.
* Add support for oc 1.
* Add support for priority zero.
* Fixed bug where sitemap would never regenerate when sitemap file exists.
* Escape illegal xml characters in loc and title elements.
* Log exception with stack trace and show 500 error when an error occurs.

## [2.0.0] - 2021-07-13

* Add support for PHP 7.4 or higher. Please review plugin configuration, check README.md

## [1.1.0] - 2021-05-28

* Update plugin dependencies

## [1.0.3] - 2020-09-01

* Code cleanup

## [1.0.2] - 2020-07-01

* Fix formatting of ModifiedAt DateTime

## [1.0.1] - 2019-11-05

* Add LICENSE file to plugin

## [1.0.0] - 2019-11-05

* First version of Vdlp.Sitemap
