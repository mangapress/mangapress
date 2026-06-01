# Changelog

All notable changes to Manga+Press will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

> **Note on 2.7.4:** This version does not appear in the WordPress.org SVN and has no
> Trac entry. It may have been a hotfix folded into 2.7.5 or only tagged on the old
> Google Code / GitHub repo. Date is unknown.

## [Unreleased]

### Added
- File autoloading (non PSR-4)
- Page indicators to the Page screen in Admin
- JSON-LD support for the Comic post-type and Series taxonomy (see [schema.org/ComicSeries](https://schema.org/ComicSeries))
  - New Basic Options settings: JSON-LD Integration checkbox (unchecked by default), comic name and description fields (fall back to site settings if blank)
  - New Comic post-type fields: Description (from Excerpt), Name (from Title), contributor roles (Author, Artist, Colorist, Editor, Letterer) as repeating meta fields
  - Comic Cover Art support (`"@type": "ComicCoverArt"`) — implementation TBD (dedicated post-type vs. checkbox on existing Comic post-type)
- Comic bookmarking ([#24](https://github.com/mangapress/mangapress/issues/24))
- Lightbox ([#79](https://github.com/mangapress/mangapress/issues/79))
- Social media integration ([#78](https://github.com/mangapress/mangapress/issues/78))

## [3.1.1] - 2026-05-29

### Fixed
- Compatibility with WordPress 7.0
- Various security issues and legacy code

## [3.1.0] - 2024-03-28

### Added
- Improved block theme support
- WordPress REST API support
- Random comic link option

### Changed
- Refactoring and general code cleanup

### Deprecated
- Several functions superseded by WordPress Core equivalents

### Removed
- Group By Parent option (code complexity and maintenance burden)

## [3.0.2] - 2024-03-26

### Changed
- Minimum PHP requirement raised to 7.4
- Codebase updated for PHP 8 compliance
- Tested with WordPress 6.4

## [3.0.1] - 2020-09-29

### Added
- Support for WordPress 5.5.1

### Fixed
- Templates for Latest Comic page and Comic Archive page not loading correctly
- CSS positioning for the comic navigation wrapper

## [3.0.0] - 2017-07-23

### Added
- Sorting options for the Comic Archive page
- Comic archive calendar template
- Comic archive gallery template

### Removed
- Child themes
- Insert navigation option

## [2.9.3] - 2015-10-04

### Changed
- Spanish language updates for embedded themes

## [2.9.2] - 2015-05-01

### Added
- Support for Jetpack Publicize feature

## [2.9.1] - 2015-01-09

### Fixed
- Comic posts not assigned to the default Series taxonomy on save
- Comic post 404 / missing Comic post issue
- Appearance of comic navigation on the Latest Comic page

## [2.9.0] - 2014-12-24

### Added
- Contextual help tabs
- Calendar template tag for comics
- Filter for overriding the Comic post-type front slug (defaults to `comic`)
- Manga+Press-specific version of the WordPress calendar widget

### Changed
- Updated navigation CSS
- Updated Comic date permalink structure
- Updated Spanish language files
- Updated child-themes to include styling for the Comic Calendar widget
- Adjusted template hierarchy for Latest Comic and Comic Archive pages to follow WordPress defaults (`page-{slug}.php`, `{custom-page-template}.php`)
- Aligned the default Single Comic template with Latest Comic and Comic Archive template handling

### Fixed
- Missing "No comics" message on the Latest Comic page
- Comic Post terms being incorrectly updated on post-save
- Spanish language file loading
- Comic navigation issues when Group Comics / Group By Parent options are active
- Single Comic post display incompatibilities with third-party themes

### Removed
- "Order By" option — now always orders by date
- "Use Theme Template" option — now always uses theme templates

## [2.8.3] - 2014-11-30

### Fixed
- Blank output when "Use Theme Template" is selected with third-party themes

## [2.8.2] - 2014-09-26

### Fixed
- Latest Comic template error when the Latest Comic page is set as the front page

## [2.8.1.1] - 2014-09-09

### Fixed
- Undefined function error when the Latest Comic template is used inside the TwentyFourteen theme

## [2.8.1] - 2014-09-06

### Changed
- Updated font icons

### Fixed
- E_STRICT notice on plugin activation

## [2.8.0] - 2014-02-23

### Added
- Bundled child-themes for TwentyEleven, TwentyTwelve, TwentyThirteen, and TwentyFourteen
- New Media Library popup (removes legacy ThickBox dependency)

### Changed
- Admin interface updated for WordPress 3.8
- Adjusted template stack for single comics
- Code review and general cleanup

### Fixed
- 404 errors for the custom post-type immediately after activation

### Removed
- Legacy Comic Banner option

## [2.7.5] - 2013-12-13

### Fixed
- 404 when visiting comic pages after updating the plugin (ported from upcoming 2.8)
- Undefined index errors caused by checkboxes when the settings page is saved

## [2.7.4] - YYYY-MM-DD

### Fixed
- SQL bugs related to the "Group By Category" option

## [2.7.3] - 2012-11-21

### Added
- "Group By Category" parent option

## [2.7.2] - 2012-09-13

### Added
- Spanish language support

### Changed
- Query handling on the Latest Comic page

### Fixed
- Various comic navigation issues

## [2.7.1] - 2012-08-08

### Fixed
- Undefined index notices when `WP_DEBUG` is enabled

## [2.7.0] - 2012-06-20

### Changed
- Partial templates moved to a sub-directory inside `templates/`
- Ajax hooks scoped to admin-only contexts
- Updated template processing logic

### Fixed
- Issues in comic-specific conditional functions
- Path issue in the options framework that could prevent option fields from rendering

[Unreleased]: https://github.com/mangapress/mangapress/compare/3.1.1...HEAD
[3.1.1]: https://github.com/mangapress/mangapress/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/mangapress/mangapress/compare/3.0.2...3.1.0
[3.0.2]: https://github.com/mangapress/mangapress/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/mangapress/mangapress/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/mangapress/mangapress/compare/2.9.3...3.0.0
[2.9.3]: https://github.com/mangapress/mangapress/compare/2.9.2...2.9.3
[2.9.2]: https://github.com/mangapress/mangapress/compare/2.9.1...2.9.2
[2.9.1]: https://github.com/mangapress/mangapress/compare/2.9.0...2.9.1
[2.9.0]: https://github.com/mangapress/mangapress/compare/2.8.4...2.9.0
[2.8.3]: https://github.com/mangapress/mangapress/compare/2.8.2...2.8.3
[2.8.2]: https://github.com/mangapress/mangapress/compare/2.8.1.1...2.8.2
[2.8.1.1]: https://github.com/mangapress/mangapress/compare/2.8.1...2.8.1.1
[2.8.1]: https://github.com/mangapress/mangapress/compare/2.8.0...2.8.1
[2.8.0]: https://github.com/mangapress/mangapress/compare/2.7.5...2.8.0
[2.7.5]: https://github.com/mangapress/mangapress/compare/2.7.4...2.7.5
[2.7.4]: https://github.com/mangapress/mangapress/compare/2.7.3...2.7.4
[2.7.3]: https://github.com/mangapress/mangapress/compare/2.7.2...2.7.3
[2.7.2]: https://github.com/mangapress/mangapress/compare/2.7.1...2.7.2
[2.7.1]: https://github.com/mangapress/mangapress/compare/2.7.0...2.7.1
[2.7.0]: https://github.com/mangapress/mangapress/releases/tag/2.7
