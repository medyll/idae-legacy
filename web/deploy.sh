#!/bin/bash
# Indique au système que l'argument qui suit est le programme utilisé pour exécuter ce fichier
# En règle générale, les "#" servent à mettre en commentaire le texte qui suit comme ici

echo "avoid node";
echo "Sauvegarde";
rsync -ruv /var/www/idaertys.mydde.fr/web/ /var/www/idaertys_last.mydde.fr/web/

echo "Mise en production";
rsync -ruv --exclude 'images_base' --exclude 'app_node' /var/www/idaertys_preprod.mydde.fr/web/ /var/www/idaertys.mydde.fr/web/

echo "OK TERMINE"