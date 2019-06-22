# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [4.3.0]

## [4.2.1]
- 100% Test coverage ([#9])
- Factor translate() into smaller methods ([#3])
- Fix misc issues for edge cases, especially when a plural rule couldn't be found
- General code improvements, test optimizations and code styling

## [4.2.0]
- Replaced `rockettheme/toolbox` with `userfrosting/uniformresourcelocator` from `userfrosting/support` **4.2.0**.

## [4.1.0]
- `MessageTranslator` is now an extension of the base `Repository` class in userfrosting/support.
- Factored out the path building methods into `LocalePathBuilder`.
- Removed loading methods.  We now rely on the `ArrayFileLoader` class in userfrosting/support.

## [4.0.3]
- Fixed Illuminate/config version requirement

## [4.0.2]
- Moved dependencies to `require`
- Fixed standards compliance issues

## [4.0.1]
- Replacing `str_replace` with Twig for placeholder replacement

## 4.0.0
- Initial release

[4.3.0]: https://github.com/userfrosting/i18n/compare/4.2.1...4.3.0
[4.2.1]: https://github.com/userfrosting/i18n/compare/4.2.0...4.2.1
[4.2.0]: https://github.com/userfrosting/i18n/compare/4.1.0...4.2.0
[4.1.0]: https://github.com/userfrosting/i18n/compare/4.0.3...4.1.0
[4.0.3]: https://github.com/userfrosting/i18n/compare/4.0.2...4.0.3
[4.0.2]: https://github.com/userfrosting/i18n/compare/4.0.1...4.0.2
[4.0.1]: https://github.com/userfrosting/i18n/compare/4.0.0...4.0.1
[#3]: https://github.com/userfrosting/i18n/issues/3
[#9]: https://github.com/userfrosting/i18n/issues/9
