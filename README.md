 Astus-Cité

Astus-Cité est une application web que j'ai développée lors de mon stage à ASTUSCITEE. L'objectif était de moderniser la gestion documentaire interne en donnant à chaque agent un espace de travail organisé pour ses fichiers et ses projets, tout en permettant à l'administration de garder une vue d'ensemble sur ce qui se passe dans chaque département.

 Comment ça fonctionne:

L'application a deux faces. La première est un site public où n'importe qui peut envoyer un message ou faire une demande de rendez-vous. Ces demandes arrivent directement côté administration, qui peut les consulter et les traiter depuis son tableau de bord. Les visiteurs peuvent aussi laisser un témoignage, qui n'apparaît sur le site qu'une fois relu et publié par l'administration.

La seconde face est l'espace de travail privé, accessible uniquement aux agents connectés. Chaque agent appartient à un département et voit uniquement ce qui concerne son périmètre. Depuis son tableau de bord, il retrouve ses projets actifs et ses fichiers récents. Il peut créer des projets, y rattacher des fichiers, les modifier, les archiver ou les supprimer. Pour les fichiers, deux approches sont possibles : soit on uploade un fichier existant depuis son ordinateur (PDF, image, vidéo, archive…), soit on rédige directement dans l'éditeur intégré à l'application, qui se charge ensuite de convertir et sauvegarder le document au format Word. Il y a aussi un espace brouillons séparé, pour les documents qu'on n'est pas encore prêt à soumettre.

Chaque projet a son propre espace de travail. On y suit l'avancement, on constitue l'équipe en ajoutant ou retirant des membres, on répartit les tâches et on les coche au fur et à mesure, on dépose les livrables et on échange dans le fil de discussion du projet. La visibilité de chaque fichier rattaché se règle au cas par cas. Un projet archivé n'est pas perdu : il reste consultable et peut être repris à tout moment.

Les administrateurs ont accès à davantage de choses : la liste complète des utilisateurs, des départements, des projets et des fichiers de toute la plateforme. Ils peuvent créer des départements, gérer les comptes et archiver ou supprimer n'importe quel élément. Les droits ne sont pas figés dans le code : chaque utilisateur reçoit un rôle et des permissions qui se configurent depuis le back-office. Un journal d'activité garde la trace de ce qui se passe sur chaque projet et pour chaque utilisateur.

Ce qu'il faut pour le faire tourner:

L'application est construite avec Laravel 12 (PHP 8.2), Livewire, Tailwind CSS et une base de données MySQL. L'authentification passe par Jetstream et Fortify, avec la possibilité d'activer la double authentification et de gérer ses sessions actives depuis son profil. La génération de fichiers Word est gérée par PHPWord, et les e-mails sont envoyés via SMTP.

Pour l'installer, clonez le dépôt, installez les dépendances avec `composer install` et `npm install`, copiez le fichier `.env.example` en `.env`, renseignez vos paramètres de base de données et d'e-mail, puis lancez `php artisan migrate`. Il ne reste plus qu'à compiler les assets avec `npm run dev` (ou `npm run build` pour la production) et démarrer le serveur avec `php artisan serve`.

---

Projet réalisé dans le cadre d'un stage chez ASTUSCITEE
