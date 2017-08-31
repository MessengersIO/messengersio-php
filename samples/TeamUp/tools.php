<?php

$dbname = 'teamup.db';


function getTeam($name, $profile) {
  global $dbname;

  try{
    $db = new SQLite3($dbname);
  } catch(Exception $exception) {
    echo $exception->getMessage();
  }

  $result = $db->query("SELECT count(*) FROM teams");
  $nbTeams = $result->fetchArray()[0];

  $result = $db->query("SELECT * FROM members WHERE profile == $profile");
  $countsInTeams = array_fill(0, $nbTeams, 0);
  while($member = $result->fetchArray()) {
    $countsInTeams[$member['team']]++;
  }
  $minNb = min($countsInTeams);
  foreach($countsInTeams as $teamID => $count) {
    if($count != $minNb)
      unset($countsInTeams[$teamID]);
  }
  $candidateIDs = array_keys($countsInTeams);
  $teamID = $candidateIDs[rand(0, count($candidateIDs) - 1)] % $nbTeams;
  $query = "INSERT INTO members (name, profile, team) VALUES ('$name', $profile, $teamID)";
  $result = $db->query($query);
  $result = $db->query("SELECT * FROM teams WHERE ID == $teamID");
  $team = $result->fetchArray();
  return $team;
}

function getAllTeams() {
  global $dbname;

  try{
    $db = new SQLite3($dbname);
  } catch(Exception $exception) {
    echo $exception->getMessage();
  }

  $result = $db->query("SELECT m.name AS name, t.name AS team, profile FROM members AS m INNER JOIN teams AS t ON m.team = t.ID");
  $teams = [];
  while($member = $result->fetchArray()) {
    if(!isset($teams[$member["team"]]))
      $teams[$member["team"]] = "*".$member["team"]."*".chr(10).chr(10);
    $teams[$member["team"]] = $teams[$member["team"]].$member['name']." <".$member['profile'].">".chr(10);
  }
  return $teams;
}

?>
