Module is installed in a regular way - simply upload your archive and click install.

CHANGELOG:
===========================
v 1.6.3 (April 14, 2017)
===========================
- [+] New pattern variables: {ean13}, {feature_xx}, {attributes_xx}
- [*] Fixed bug with empty product listing if there are no any manufacturers
- [*] Remove unrecognized UTF-8 characters before updating meta fields

Files modified:
-----
- /metatagsgenerator.php
- /views/templates/admin/configure.tpl

===========================
v 1.6.2 (February 20, 2017)
===========================
- [+] Filter items by status (active, not active)
- [*] Minor interface improvements

Files modified:
-----
- /metatagsgenerator.php
- /views/css/back.css
- /views/js/back.js
- /views/templates/admin/configure.tpl
- /views/templates/admin/resource-list.tpl

===========================
v 1.6.1 (January 19, 2017)
===========================
- [*] Do not strip UTF-8 diacritics in generated meta tags

Files modified:
-----
- /metatagsgenerator.php

===========================
v 1.6.0 (January 12, 2017)
===========================
- [+] Compatibility with PS 1.7
- [+] Filter categories by their parents
- [*] Minor code optimizations

Files modified:
-----
- /metatagsgenerator.php
- /views/js/back.js
- /views/templates/admin/configure.tpl

Files added:
-----
- /views/js/update-product-fields.js

===========================
v 1.5.1 (August 25, 2016)
===========================
- [+] New pattern variable {price_iso_code}
- [*] Minor fix for rendering patterns on module page load
- [*] Remove possible duplicates in generated meta keywords
- [*] Automatically update cached versions of css/js files

Files modified:
-----
- /metatagsgenerator.php
- /views/templates/admin/configure.tpl
- /views/js/back.js

===========================
v 1.5.0 (August 11, 2016)
===========================
- [+] Autofill empty meta tags on saving products/categories/CMS etc.
- [+] New pattern variable {shop_name}
- [+] Added documentation
- [*] Fixed pattern dots bug
- [*] Improved autosaving for patterns
- [*] Retro-compatibility fixes

Files modified:
-----
- /metatagsgenerator.php
- /views/templates/admin/configure.tpl
- /views/templates/admin/resource-list.tpl
- /views/js/back.js
- /views/css/back.css

Files added:
-----
- /readme_en.pdf
- /upgrade/install-1.5.0.php
- /upgrade/index.php

Directories removed automatically:
-----
- /saved_patterns/

UPGRADE: patterns based by shop + SHOP CONTEXT not by emplyee id

===========================
v 1.0.3 (April 28, 2016)
===========================
- [+] Additional sorting by id/date_add/date_upd
- [*] Fixed bug with special characters in manually edited fields

Files modified:
-----
- /metatagsgenerator.php
- /views/templates/admin/configure.tpl
- /views/templates/admin/resource-list.tpl
- /views/js/back.js
- /views/css/back.css

===========================
v 1.0.2 (March 17, 2016)
===========================
- [*] Improved multistore support
- [*] Improved autodetection of image type in product listings on configuration page

Files modified:
-----
- /metatagsgenerator.php
- /views/templates/admin/configure.tpl

===========================
v 1.0.1 (February 16, 2016)
===========================
- [*] Strip forbidden symbols for saved meta-fields (pass isGenericName validation)

Files modified:
-----
- /metatagsgenerator.php

===========================
v 1.0.0 (October 21, 2015)
===========================
Initial relesase
