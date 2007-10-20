#!/bin/sh
DIRS=Classes/Base,\
Classes/Phaux-base,\
Classes/Phaux-extras,\
Classes/Phaux-render,\
Classes/Phaux-test,\
Classes/REServe,\
Classes/REServe-Phaux

IGNORE=base.php,\
phaux-base.php,\
phaux-extras.php,\
phaux-render.php,\
phaux-test.php,\
reserve_main.php,\
reserve-phaux.php,\
BogusFile.php

phpdoc --target Docs --directory $DIRS --ignore $IGNORE --title "Phaux API Documentation" --output HTML:frames:phpedit --sourcecode on --quiet on