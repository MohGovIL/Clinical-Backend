# [OpenEMR](https://www.open-emr.org) extension modules 

ClinikalAPI
--------------------------------------
API for none FHIR calls

FhirAPI     
--------------------------------------
Implementaion of  [hl7 FHIR version 4](https://www.hl7.org/fhir/) 
Examples can be found [here](https://clinikal-documentation.readthedocs.io/en/latest/api/fhir/#appointment)

Formhandler
--------------------------------------
Tool to automatically create and handle OpenEMR forms. 

GenericTools
--------------------------------------
Collection of libs 	

ImportData
--------------------------------------
csv data import module 

Inheritance
--------------------------------------

# Installation guide 

1.install [openEMR](https://www.open-emr.org)
```
git clone https://github.com/openemr/openemr.git
cd openemr
composer install --no-dev
npm install
npm run build
composer dump-autoload -o
```

2. Edit openemr/composer.json 
```
    "require": {
    ...
        "clinikal/composer-installers-clinikal-extender": "dev-master",
        "clinikal/clinikal-backend": "dev-develop"
    },

    "extra": {
        "installer-types": [
            "clinikal-vertical",
            "clinikal-react"
        ],
        "installer-paths": {
        }
    },
    ...
        "repositories": [
        ...
        {
            "type": "vcs",
            "url": "git@github.com:israeli-moh/composer-installers-clinikal-extender.git"
        },
        {
            "type": "vcs",
            "url": "git@github.com:israeli-moh/clinikal-backend.git"
        }
    ],
```


3. run composer update

4.open in browsre http://localhost/  your installation dir  /openemr/setup.php

5. Follow the installation guide 

you can run the folling commads before install to speed up the prosses 
'''
sudo chmod 777 sites/default/sqlconf.php
sudo chmod -R 777 sites/default/documents
'''


