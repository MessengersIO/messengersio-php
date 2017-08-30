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

$dbname = 'teamup.db';
if(!class_exists('SQLite3'))
  die("SQLite 3 NOT supported.");
try{
  $db = new SQLite3($dbname);
} catch(Exception $exception) {
  echo $exception->getMessage();
}

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

	if($message instanceof CallbackMessage){
		if($message->getValue() === "REGISTER"){
			$thread->moveAndLoadState("REGISTER");
			return; // Interruption
		}
	}

	// On envoie directement une image
	$thread->send(new ImageMessage("https://www.creageneve.com/wp-content/uploads/2017/07/bighack_cover_dev.jpg"));

	// Création d'un texte
	$answer = new TextMessage("Bienvenue au Big Hack !");

	// Ajout de deux boutons
	$answer->addButton(new Button("S'enregistrer", "REGISTER"));

	// Envoi du texte
	$thread->send($answer);

});

$app->state("REGISTER", function(Thread $thread, Message $message) {

	if($message instanceof TextMessage) {
		$thread->send(new TextMessage("Salut ".$message->getText()));
	} else {
		$thread->send(new TextMessage("Quel est votre prénom ?"));
	}
});


// Récupération de la requête
$result = $app->run();


// Show result
var_dump($result);
