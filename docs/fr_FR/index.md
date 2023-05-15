Description 
===

Le plugin OpenEVSE permet de piloter un chargeur de voiture électrique OpenEVSE ou EmonEVSE.

En application courante, il permet par exemple de démarrer ou arrêter une charge manuellement mais encore d'ajuster automatiquement la puissance de charge en fonction de la puissance réelle générée par une installation photovoltaïque depuis un scénario dans Jeedom.

Le chargeur est disponible ici:
[Lien vers le chargeur EmonEVSE de chez OpenEnergyMonitor](https://shop.openenergymonitor.com/emonevse-wifi-connected-ev-charging-station-type-2/)


Remarques:

- La borne existe en monophasé et en triphasé.

- La borne OpenEVSE peut varier l’intensité de charge de 6 a 32 ampères.

Configuration du plugin 
===

La configuration du plugin est très simple.
Une fois installé, il suffit de créer un nouvel équipement et de le configurer de la manière suivantes:

![OpenEVSE](https://sattaz.github.io/Jeedom_OpenEVSE/pictures/OpenEVSE_1.jpg)

Comme pour chaque plugin Jeedom, il faudra indiquer le 'Nom de l'équipement', un 'Objet parent' et une 'Catégorie'.
Ne pas oublier de cocher les cases 'Activer' et 'Visible'.

Puis viennent aussi quelques paramètres dédiés aux spécifications du chargeur et l'installation électrique:

-   IP de la borne : veuillez renseigner l'adresse IP de l'interface web du chargeur OpenEVSE/EmonEVSE.

-   Identifiant : veuillez renseigner votre identifiant si vous avez activé l'authentification HTTP depuis l'interface web du chargeur OpenEVSE/EmonEVSE.

-   Mot de passe : veuillez renseigner le mot de passe.

-   Mode de fonctionnement : veuillez sélectionner le type d'API à utiliser:
    - RAPI (Obsolete) -> il est préconisé de ne plus utiliser ce mode, les anciens et nouveaux firmwares wifi de la borne gèrent encore RAPI pour le moment.
    - WIFI -> c'est la nouvelle méthode d'interraction avec la borne, intégré dans les derniers firmwares wifi de la borne. Important: votre borne OpenEVSE doit être à jour avec une version de firmware wifi minimum 4.1.6 
    
    [Lien Firmware : https://github.com/OpenEVSE/ESP32_WiFi_V4.x/releases](https://github.com/OpenEVSE/ESP32_WiFi_V4.x/releases)

-   Intensité de charge minimum (A) : veuillez sélectionner l'ampérage minimum que le chargeur doit délivrer (en ampères)

-   Intensité de charge maximum (A) : veuillez sélectionner l'ampérage maximum que le chargeur ne doit pas dépasser, ceci en fonction de votre installation/abonnement électrique (en ampères)

-   Commande ajustement tension (V) : Vous pouvez spécifier une valeur numérique, renseigner une commande de type information ou encore une variable, cette donnée va servir à ajuster la valeur de la tension de référence du chargeur afin d'optimiser le calcul de la session de charge au plus précis.

- Commande lecture mode tarification : Vous pouvez renseigner une commande de type information qui indiquera au chargeur le passage aux heures creuses. En mode automatique, le plugin activera la charge lors du passage en heures creuses et désactivera la charge lors du basculement en heures pleines.

- Mode tarification chargement auto. : En complément de la commande lecture mode tarification, vous devrez spécifier a quoi correspond le mode de tarification, par exemple 'HC' ou encore 'HC...'.

-> Veuillez dès à présent appuyer sur le bouton 'Sauvegarder' afin d'enregistrer la configuration.

-> Cette action va automatiquement créer les commandes de l'équipement.

Commandes de l'équipement 
===

Comme énoncé dans le précédent chapitre, les commandes de l'équipement sont automatiquement crées dès lors que la configuration est sauvegardée.

IMPORTANT : Afin de pouvoir utiliser les boutons graphique des commandes de type ACTION pour démarrer/arrêter la charge ou encore changer le mode manuel/automatique, il faut impérativement nommer ces commandes avec '_ON' ou '_OFF' à la fin de chaque commande.

Example: Charge_ON / Charge_OFF ou encore ModeAuto_ON / ModeAuto_OFF

![OpenEVSE](https://sattaz.github.io/Jeedom_OpenEVSE/pictures/OpenEVSE_2.jpg)

Informations pratiques:

-> Les commandes 'Perso. Texte', 'Perso. Num.' et 'Perso. Bin.' peuvent etre utilisée comme bon vous semble afin, par exemple, d'afficher des informations que vous obtiendrez depuis un scénario et les afficher sur la tuile du plugin.

Le widget 
===

Le widget arrive comme montré sur la photo ci-après et le curseur permettant le réglage de la puissance du chargeur est calibrée (min/max) par l'ampérage de charge maximum indiquée dans la configuration de l'équipement.

![OpenEVSE](https://sattaz.github.io/Jeedom_OpenEVSE/pictures/OpenEVSE_3.jpg)

Libre à vous de modifier le widget afin de l'adapter à votre style de présentation.

Informations pratiques:

-> Le bouton 'ON' / 'OFF' permet de démarrer ou arrêter une charge.

-> Le bouton 'Man.' / 'Auto.' positionné en mode 'auto.' activera automatiquement la charge en heures creuses lorsque les paramètres sont indiqués dans la configuration de l'équipement. Il permet aussi de changer l'information 'Mode' du plugin en 'Manuel' ou 'Automatique', vous pouvez donc utiliser un scenario depuis Jeedom afin d'interragir de facon différente en fonction du 'Mode' du plugin:

* En 'Manuel', le scénario pourra par exemple ignorer tout automatisme et donc laisser l'utilisateur démarrer ou arrêter une charge avec les boutons 'ON' et 'OFF' manuellement.

* En 'Automatique', le scénario pourra par exemple utiliser une information de puissance produite par une installation photovoltaïque et interragir avec le plugin en ajustant la consigne d'ampérage de charge, démarrer une session de charge en executant la commande 'ON', arrêter une session de charge en executant la commande 'OFF' ou encore faire tout cela pendant les heures creuses en ajustant l'ampérage de charge au maximum.

Bref, les possibilités sont multiples.

Autres informations 
===

* Le plugin rafraîchi les données toutes les minutes.

* Vous pouvez créer plusieurs équipements pour gérer différents chargeurs.
