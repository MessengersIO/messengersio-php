<?php

/*///////////////////////////////////////////////////////////
// NE PAS TOUCHER
///////////////////////////////////////////////////////////*/
file_put_contents("last-message.json", json_encode($_GET)."\n\n".file_get_contents("php://input"));
include(__DIR__."/../../vendor/autoload.php");
include('tools.php');
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

// Connexion à la base de données
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
 * Répondre un texte de bienvenue, et rediriger
 */
$app->state("WELCOME", function(Thread $thread,  Message $message){

	if($message instanceof CallbackMessage){
		if($message->getValue() === "REGISTER"){
			$thread->moveAndLoadState("REGISTER");
			return; // Interruption
		}
    if($message->getValue() === "TEAMS"){
      $thread->moveAndLoadState("TEAMS");
      return; // Interruption
    }
	}

	// On envoie directement une image
	$thread->send(new ImageMessage("https://www.creageneve.com/wp-content/uploads/2017/07/bighack_cover_dev.jpg"));

	// Création d'un texte
	$answer = new TextMessage("Bienvenue au Big Hack !");

	// Ajout de deux boutons
	$answer->addButton(new Button("S'enregistrer", "REGISTER"));
  $answer->addButton(new Button("Voir les équipes", "TEAMS"));

	// Envoi du texte
	$thread->send($answer);

});

/*
 * ETAT: REGISTER
 *
 * Demander le nom et prénom d'un participant pour l'enregistrer dans une équipe
 */
$app->state("REGISTER", function(Thread $thread, Message $message) {

  // Lorsqu'on entre dans l'état, on commence par demander le prénom
  if($message->isTreated()){
    $thread->send(new TextMessage("Quel est votre prénom ?"));
    return;
  }

  // À partir de la seconde requête dans l'état, on a des données stockées à récupérer
  $data =($thread->getData()) ? explode(';', $thread->getData()) : [];

  // Lorsque l'utilisateur envoie un message Texte
	if($message instanceof TextMessage) {
    // Si on a aucune donnée encore enregistrée...
    if(count($data) == 0) {
      // On retient le texte qu'il a entré comme prénom
      $thread->setData($message->getText());
      // On demande le nom de famille
      $thread->send(new TextMessage("Et votre nom de famille ?"));
      return;
    }
    // Quand on a des données déjà enregistrées (le prénom)...
    // On enregistre le nom de famille qu'il a envoyé
    $thread->setData($thread->getData().";".$message->getText());
    // On répond avec ce qu'on sait de lui (nom et prénom)
    $answer = "Bonjour ". str_replace(';', ' ', $thread->getData());
    // On passe dans l'était PROFILE
    $thread->moveAndLoadState("PROFILE")->send(new TextMessage($answer));
	}
});

/*
 * ETAT: PROFILE
 *
 * Demander le profile d'un participant pour trouver la bonne équipe
 * et lance une recherche d'équipe.
 */
$app->state("PROFILE", function(Thread $thread, Message $message) {
    // Lorsqu'on entre dans l'état, on commence par demander le profil
    if($message->isTreated()){
      $answer = new TextMessage("Quel est votre profil ?");
      $answer->addButton(new Button('Tech', 1));
      $answer->addButton(new Button('Marketing', 2));
      $answer->addButton(new Button('Créatif', 3));
      return $thread->send($answer);
    }

    // Lorsque l'utilisateur clique sur un bouton (action de callback)...
    if($message instanceof CallbackMessage) {
      // On récupère l'ID du profil qu'il a choisi
      $profileID = $message->getValue();
      // On attribue une équipe au participant
      $team = getTeam(str_replace(';', ' ', $thread->getData()), $profileID);
      // Si une image est précisée pour l'équipe, on l'affiche
      if(isset($team['url']))
        $thread->send(new ImageMessage($team['url']));
      // Réponse au participant
      $thread->send(new TextMessage("Vous êtes dans l'équipe ".$team["name"]." !"));
      // On vide les données (elles sont désormais dans la base de données SQLite)
      $thread->setData('');
      // On retourne dans l'était initial après 5s
      sleep(5);
      $thread->moveAndLoadState("WELCOME");
      return;
    }

    // Si l'utilisateur envoie "reset", on retourne à l'était initial
    if($message instanceof TextMessage) {
      if($message->getText() == 'reset') {
        $thread->setData('');
        $thread->moveAndLoadState("WELCOME");
      }
    }
});

/*
 * ETAT: TEAMS
 *
 * Renvoie la liste des équipes et leurs membres
 */
$app->state("TEAMS", function(Thread $thread,  Message $message){
    // Si l'utilisateur envoie un message, on retourne à l'état initial
    if($message instanceof TextMessage) {
        $thread->setData('');
        return $thread->moveAndLoadState("WELCOME");
    }
    // Sinon, on récupère la liste des équipes, on la formate et on l'envoi
    $teams = getAllTeams();
    foreach($teams as $team) {
      $team = str_replace('<1>', "{Tech}", $team);
      $team = str_replace('<2>', "{Marketing}", $team);
      $team = str_replace('<3>', "{Créatif}", $team);
      $thread->send(new TextMessage($team));
    }
});

// Récupération de la requête
$result = $app->run();


// Show result
var_dump($result);
