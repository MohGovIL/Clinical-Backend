# Description
This repository contains modules that add functionality to Openemr.

# Installation
These instructions assume you have a working installation of Openemr.  

Add the following to the openemr/composer.json:  
``` json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:israeli-moh/clinikal-backend.git"
    },
    {
        "type": "vcs",
        "url": "git@github.com:israeli-moh/composer-installers-clinikal-extender.git"
    }
],
"require": {
    "clinikal/clinikal-backend": "dev-master",
    "clinikal/composer-installers-clinikal-extender": "dev-master"
},
"extra": {
    "installer-types": [
        "clinikal-vertical"
    ],
}
```

In a terminal, `cd` into the openemr root directory (where the composer.json is), and run:  
```
composer update clinikal
```  
  
This downloads the modules code into the openemr/vendor/clinikal and triggers the composer installer extension in the composer-installers-clinikal-extender repository.  
The extension creates links from the files in vendor/clinikal to there appropriate places in the openemr codebase.  
This enables us to use any modules, styles, and menus downloaded by composer into the vendor/clinikal directory.

All modules can now be registered and enabled in the Manage Modules screen.  

This project is sponsored by the Israeli Ministry Of Health.
