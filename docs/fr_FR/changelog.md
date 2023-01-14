* 14-Janvier-2023:

Corrections de bugs concernant la nouvelle API WIFI:

-> La charge redémarrait lors du rafraichissement des commandes (bug de code lié au positionnement du curseur de consigne de charge).  
-> Lors de l'arrêt d'une charge, la consigne de charge sur le chargeur se mettait au maximum.  
-> Un message d'erreur apparaissait si l'équipement était mis sur 'Inactiver'.

* 12-Janvier-2023:

Modification de certaines fonctions afin d'utiliser l'endpoint '/override' pour l'ajustement de l'intensité, le démarrage ou l'arrêt d'une charge. (Merci à KipK pour tous les conseils et bravo pour la nouvelle GUI V2 du chargeur OpenEVSE!)

**ATTENTION : si vous migrez vers le firmware wifi GUI V2, merci de ne plus utiliser le mode RAPI depuis le plugin!!!**

* 04-Janvier-2023:

Ajout de la valeur des commandes dans les paramètres du plugin.

* 02-Janvier-2023:

Intégration de la nouvelle WIFI API. (RAPI peut encore être utilisé)

Possibilité d'utiliser une variable pour la commande ajustement tension depuis la configuration.

* 11-Avril-2022:

Ajout d'une commande pour mettre la charge en pause.

* 07-Décembre-2021:

Gestion de la valeur minimum de consigne de l'intensité.

Correction d'un bug d'affichage.

* 10-Août-2021:

Mise en place d'une liste de selection pour le choix de l'intensité maximum du chargeur.

Ajout de l'ajustement de la tension de référence du chargeur depuis une commande info dans Jeedom ou en spécifiant une valeur numérique.
				
* 07-Août-2021:

Ajout gestion d'erreur CURL et amélioration de la méthode de rafraîchissement du curseur de sélection de l'intensité.

* 16-Juillet-2021:

Première version du plugin.
