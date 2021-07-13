Description 
===

Le plugin OpenEVSE permet de piloter un chargeur de voiture électrique OpenEVSE ou EmonEVSE.

En application courante, il permet par exemple de demarrer ou arreter une charge manuellement mais encore d'ajuster automatiquement la puissance de charge en fonction de la puissance réelle générée par une installation PV depuis un scénario dans Jeedom.

Le chargeur est disponible ici:
https://shop.openenergymonitor.com/emonevse-wifi-connected-ev-charging-station-type-2/

Configuration du plugin 
===

La configuration du plugin est très simple.
Une fois installé, il suffit de créer un nouvel équipement et de le configurer de la manière suivantes:

![OpenEVSE](https://sattaz.github.io/Jeedom_OpenEVSE/pictures/OpenEVSE_1.jpg)

Comme pour chaque plugin Jeedom, il faudra indiquer le 'Nom de l'équipement', un 'Objet parent' et une 'Catégorie'.
Ne pas oublier de cocher les cases 'Activer' et 'Visible'.

Puis viennent aussi quelques paramètres dédiés aux spécifications du chargeur et l'installation électrique:

-   IP de la borne : veuillez renseigner l'adresse IP de l'interface web du chargeur OpenEVSE/EmonEVSE.

-   Ampérage de charge maximum (A) : veuillez renseigner l'ampérage maximum que le chargeur ne doit pas dépasser, ceci en fonction de votre installation/abonnement électrique (en ampères)

-> Veuillez dès à présent appuyer sur le bouton 'Sauvegarder' afin d'enregistrer la configuration.
-> Cette action va automatiquement créer les commandes de l'équipement.

Commandes de l'équipement 
===

Comme énoncé dans le précédent chapitre, les commandes de l'équipement sont automatiquement crées dès lors que la configuration est sauvegardée.

![OpenEVSE](https://sattaz.github.io/Jeedom_OpenEVSE/pictures/OpenEVSE_2.jpg)

Le widget 
===

Le widget arrive comme montré sur la photo ci-après et le curseur permettant le réglage de la puissance du chargeur est calibrée (min/max) par l'ampérage de charge maximum indiquée dans la configuration de l'équipement.

![OpenEVSE](https://sattaz.github.io/Jeedom_OpenEVSE/pictures/OpenEVSE_3.jpg)

Libre à vous de modifier le widget afin de l'adapter à votre style de présentation.

Autres informations 
===

* Le plugin rafraîchi les données toutes les minutes.
* Vous pouvez créer plusieurs équipements pour gérer différents chargeurs.
