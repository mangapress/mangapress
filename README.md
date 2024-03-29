# Manga+Press Comic Manager
* Contributors: Jess Green
* Donate link: http://www.manga-press.com/
* License: GPLv2

Manga+Press is a webcomic management system for WordPress.

## Description

Manga+Press is a webcomic management system for WordPress. Manga+Press uses WordPress' posts, pages and categories to help you keep track of your comic posts. Manga+Press also includes its own custom template tags to help make creating themes easier.

## Changelog
### 3.2 — Development Version
#### 3.2.0 — Planned
   * Add support for block themes and full site editing.
   * Add Page indicators to Page screen in Admin.
   * Add file autoloading (non PSR-4).
   * Add unit tests?
   * Bring over features from cancelled 4.0/4.1 release.
     * [ ] Comic bookmarking (#24)
     * [ ] Lightbox (#79)
     * [ ] Social media integration (#78)

### 3.1 — Stable
#### 3.1.0
   * Refactoring and code cleanup
   * Deprecation of functions in favor of WordPress Core functions
   * Added better block theme support
   * Removed Group By parent option due to code complexity and ease of maintenance
   * Added WordPress REST api support

### 3.0
#### 3.0.2
   * Change PHP requirement to 7.4
   * Updated code to be PHP 8-compliant

#### 3.0.1
   * Fixed issue where templates for Latest Comic page and Comic Archive page weren't loading correctly
   * Corrected CSS positioning for comic navigation wrapper
   * Added support for WordPress 5.5.1

#### 3.0.0
   * Removed child themes
   * Added sorting options for Comic Archive Page
   * Added comic archive calendar template
   * Added comic archive gallery template
   * Removed insert navigation option

### 2.9
#### 2.9.3
   * Spanish language updates to embedded themes.

#### 2.9.2
   * Added support for Jetpack Publicize feature.

#### 2.9.1
   * Corrects an issue where Comic posts were not getting assigned to a default Series taxonomy on save.
   * Corrects Comic post 404 error/Missing Comic post issue.
   * Corrects appearance of comic navigation on Latest Comic page

#### 2.9.0
   * Updated navigation CSS
   * Removed "Order By" Option. Now defaults to date.
   * Removed "Use Theme Template" options. Now defaults to using theme templates.
   * Added contextual help tabs
   * Added Calendar template tag for comics
   * Added filter for changing Comic post-type front slug (defaults to `comic`)
   * Fixed missing "No comics" message for Latest Comic page.
   * Corrected issue with Comic Post terms getting updated on post-save.
   * Updated Spanish Language files.
   * Updated child-themes to handle styling for Comic Calendar widget
   * Corrected issues in comic navigation when Group Comics/Group By Parent options are used.
   * Added Manga+Press-specific version of WordPress calendar widget
   * Updated Comic date permalink structure
   * Updated and fixed loading of Spanish Language files
   * Adjusted template hierarchy for Latest Comic and Comic Archive pages to use WordPress' defaults (page-{slug-name}.php and {custom-page-template}.php)
   * Brought default Single Comic template in line with default Latest Comic and Comic Archive template handling
      * Incidently corrects an issue where a Single Comic post might not display correctly due to markup being incompatible with a user's selected theme.

### 2.8
#### 2.8.3
   * Correcting blank issue when "Use Theme Template" is selected when used with third-party themes

#### 2.8.2
   * Correcting Latest Comic template error when Latest Comic is used as front page.

#### 2.8.1.1
   * Correcting problem with undefined function error appearing when Latest Comic template in TwentyFourteen theme is used


#### 2.8.1
   * Corrected E_STRICT notice on plugin activation
   * Updated font icons

#### 2.8
   * Added bundled child-themes for TwentyEleven, TwentyTwelve, TwentyThirteen, and TwentyFourteen
   * Updated admin interface to fit WordPress 3.8
   * Corrected 404 issues for custom post-type after activation
   * Adjusted template stack for single comics
   * Added new Media Library popup (eliminating legacy ThickBox dependency)
   * Code review and cleanup
   * Removed legacy options (Comic Banner)

### 2.7
#### 2.7.5
   * Fixed 404 when visiting comic pages after update (ported from upcoming 2.8 release)
   * Fixed undefined index errors caused by checkboxes when settings page is updated
   * Tested works with WordPress 3.8

#### 2.7.4
   * Fixed SQL bugs relating to "Group By Category" option

#### 2.7.3
   * Added "Group By Category" parent option

#### 2.7.2
   * Added Spanish Language support.
   * Fixed issues with comic navigation.
   * Addressing query-usage on Latest Comic page.

#### 2.7.1
   * Fixed undefined index notices (WP_DEBUG turned on)

#### 2.7 RC 1
   * Moved partial templates to sub-directory inside templates.
   * Corrected issues in comic-specific conditional functions.
   * Changed Ajax hooks to be admin-specific.

#### 2.7 Beta 3
   * Updates processing for templates.

#### 2.7 Beta 2
   * Fixes a problem with the Manga+Press Options page. A path issue in the framework may prevent option fields from displaying properly.


## Installation

1. Unpack the .zip file onto your hard drive.

2. Upload the `mangapress` folder to the `/wp-content/plugins/` directory.

3. Activate the plugin through the 'Plugins' menu in WordPress

4. Create two new pages; these pages will be your latest comic and comic archives pages. Label them something like 'Latest Comic' and 'Past Comics' or whatever, as long as it makes sense to you.

6. Click on the Manga+Press Options tab under Settings and go to Basic Manga+Press Options, and set Latest Comic Page, and Comic Archive Page to your two newly created pages.

## Credits

(c) 2008-2020 Jess C. Green

Found a bug? Or did you find a bug and figure out a fix? Visit [Manga+Press Support @ WordPress.org](http://wordpress.org/support/plugin/mangapress/), or my [GitHub page](https://github.com/jesgs/mangapress/) to make a bug report, or email me at support@manga-press.com. Please include screenshots, WordPress version, a list of any other plugins you might have installed, or code (if you figured out a fix) and webserver configuration info. Be as detailed as possible.

For updates and development progress, visit http://www.manga-press.com/

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
