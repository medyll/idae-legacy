#!/bin/bash

# save
rsync -ruv /var/www/idaertys.mydde.fr/web/ /var/www/idaertys_last.mydde.fr/web/

prod
rsync -ruv --exclude 'images_base' /var/www/idaertys_preprod.mydde.fr/web/ /var/www/idaertys.mydde.fr/web/
