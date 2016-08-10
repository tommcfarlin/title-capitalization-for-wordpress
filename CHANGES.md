#### 1.2.0 / 2016-08-06

* Fixed to work with WP 4.6
* Fixed to correctly title case contractions such as _they're_ and _should've_

#### 1.1.5 / 2016-04-02

* Added a note about WordPress compatibility and a new version of this plugin
that's in development.

#### 1.1.4 / 2015-07-06

* Fixing a problem in `class TitleCase` so letters following apostrophes aren't capitalized. [Issue #5](https://github.com/tommcfarlin/title-capitalization-for-wordpress/issues/11)
* Adding header `Network: false` as the plugin doesn't like being Network Activated. This doesn't prevent Network Activation. :P

#### 1.1.3 / 2015-01-02

* Updated to work with Markdown markup for headers and leaves markdown markup intact
* Fix for [JetPack Markdown](https://github.com/tommcfarlin/title-capitalization-for-wordpress/issues/5)

#### 1.1.2 / 2014-05-04

* Fixing a problem with the core plugin file that was not showing updates from the GitHub Updater
* Updating the stable version in README.md
* May the Fourth be with you!

#### 1.1.0 / 2014-05-02

* Adding a Text Domain and the `languages` directory
* Adding type-hinting to the functions specific to the classes in this plugin
* Checking to make sure the `TitleCapitalizer` class does not exist before actually importing it
* Fixing a typo in the README file
* Fixing a typo in the mail plugin file
* Renaming the `init` function to `run` in `class-title-caps-loader.php`
* Renaming `class-title-caps-loader.php` to `class-title-capitalizer-loader.php`
* Renaming the core plugin file
* Removing the `@since` tags
* Renaming `inc` to `includes` and `lib` to `vendor`
* Renaming the package to TitleCapitalizer
* Updating the link to the ChangeLog so that it conforms to the GitHub Updater standards
* Updating file-level and class-level tags
* Updating the `for` loop when iterating through the content to be capitalized
* Updating method names to be verbs (and to be more descriptive)

#### 1.0.3 / 2014-04-25

* removing debug statements

#### 1.0.2 / 2014-04-25

* Modifying the call back for the post capitalization so that shortcodes are not processed.

#### 1.0.1 / 2014-04-21

* Renamed ChangeLog to CHANGES file for GitHub Updater.

#### 1.0.0 / 2014-04-20

* Initial commit
