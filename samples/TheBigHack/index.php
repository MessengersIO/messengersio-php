<?php

/*///////////////////////////////////////////////////////////
// NE PAS TOUCHER
///////////////////////////////////////////////////////////*/
file_put_contents("last-message.json", json_encode($_GET)."\n\n".file_get_contents("php://input"));
include(__DIR__."/../../vendor/autoload.php");
use MessengersIO\App;
use MessengersIO\Message\Button;
use MessengersIO\Message\CallbackMessage;
use MessengersIO\Message\ImageMessage;
use MessengersIO\Message\LocationMessage;
use MessengersIO\Message\Message;
use MessengersIO\Message\TextMessage;
use MessengersIO\Thread;
$config = json_decode(file_get_contents(__DIR__.'/conf.json'));
$app = new App($config->apiKey);
/*///////////////////////////////////////////////////////////

   _____ _           ______ _         _   _            _
  |_   _| |          | ___ (_)       | | | |          | |
    | | | |__   ___  | |_/ /_  __ _  | |_| | __ _  ___| | __
    | | | '_ \ / _ \ | ___ \ |/ _` | |  _  |/ _` |/ __| |/ /
    | | | | | |  __/ | |_/ / | (_| | | | | | (_| | (__|   <
    \_/ |_| |_|\___| \____/|_|\__, | \_| |_/\__,_|\___|_|\_\
                               __/ |
                              |___/

	Bienvenue au big hack! Vous pouvez éditer tout ce qui est ci-dessous.
	Faites des copier-coller, expérimentez, salissez-vous les mains, etc.

	Nous sommes à votre disposition au TechBar.

*/


/*
 * DEFINITION DE L'ETAT PAR DEFAUT
 */
$app->setDefaultState("WELCOME");


/*
 * ETAT: WELCOME
 *
 * Ceci est également l'état par défaut (car nommé comme ci-dessus)
 * Répondre un texte de bienvenue, et rediriger au niveau suivant
 */
$app->state("WELCOME", function(Thread $thread,  Message $message){

	// On envoie directement une image
	$thread->send(new ImageMessage("https://www.creageneve.com/wp-content/uploads/2017/07/bighack_cover_dev.jpg"));

	// Création d'un texte
	$answer = new TextMessage("Bienvenue au Big Hack!");

	// Ajout de deux boutons
	$answer->addButton(new Button("Merci!"));
	$answer->addButton(new Button("C'est parti"));

	// Changer vers un autre état (appliqué lors d'un envoi de la réponse)
	$thread->moveToState('INTRO');

	// Envoi du texte
	$thread->send($answer);

});


/*
 * ETAT: INTRO
 *
 * - Présentation du lieu
 * - Demander que faire après
 */
$app->state('INTRO', function(Thread $thread, Message $message){

	// Vérification du changement d'état
	if($message instanceof CallbackMessage){
		if($message->getValue() === "TEAMS"){
			$thread->moveAndLoadState("TEAMS")->send(new TextMessage("Moving to teams"));
			return; // Interruption
		}elseif($message->getValue() === "EXAMPLES"){
			$thread->moveAndLoadState("EXAMPLES")->send(new TextMessage("Moving to examples"));
			return; // Interruption
		}
	}

	// Affichage d'un lieu
	$thread->send(new TextMessage("Vous êtes ici:"));
	$thread->send(new LocationMessage(46.1912586,6.1303793));

	// Attente de 3 secondes
	sleep(5);

	// Envoi d'une suggestion de suite (traité ci-dessus)
	$answer = new TextMessage("Que souhaites-tu faire?");

	$answer->addButton(new Button("Faire les équipes","TEAMS")); // Le callback TEAMS est utilisé ci-dessus
	$answer->addButton(new Button("Exemples","EXAMPLES")); // Le callback EXAMPLES aussi.

	$thread->send($answer);


});


/*
 * ETAT: EXEMPLES
 */
$app->state('EXEMPLES', function(Thread $thread, Message $message){

	// A améliorer
	$thread
		->moveToState("WELCOME")
		->send(new TextMessage("EXEMPLES: Rien pour le moment. Retour à la case départ"));

});


/*
 * ETAT: TEAMS
 *
 * Présentation du
 * Puis lançons automatiquement
 */
$app->state('TEAMS', function(Thread $thread, Message $message){

	if($message->isTreated())
		$thread->send(new TextMessage("This message is already treated in another state"));

	// A améliorer
	$thread
		->moveToState("WELCOME")
		->send(new TextMessage("TEAMS: Rien pour le moment. Retour à la case départ"));

});


// Récupération de la requête
$result = $app->run();


// Show result
var_dump($result);

