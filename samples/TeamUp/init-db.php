<?php

$numberOfTeams = isset($_GET['teams']) ? $_GET['teams'] : 4;
$teamNames = ['Ketchup', 'Mayo', 'Moutarde', 'Curry', 'Guacamole', 'Safran', 'Cannelle'];
$teamImages = [
  'http://www.farwestchina.com/wp-content/uploads/2011/08/ketchup.jpg',
  'http://www.oporto.co.nz/wp-content/uploads/2015/09/sauce_mayonnaise.102271.png',
  'http://1.bp.blogspot.com/-nPqw4LiKVuU/Til7BqAVllI/AAAAAAAAAt0/DsogOPILnJk/s400/garlic+mustard-mustard.jpg',
  'http://www.terredepices.com/35-101-large/curry-indien.jpg',
  'https://d5bzqyuki558t.cloudfront.net/cms-assets/system/asset_versions/attachments/000/000/221/original/_0016_Guac.png?1428411972',
  'http://www.fruitsec-safran.com/wp-content/uploads/2013/04/safran-filament.jpg',
  'http://ileauxepices.com/60-large/cannelle.jpg'
]

$dbname = 'teamup.db';
if(!class_exists('SQLite3'))
  die("SQLite 3 NOT supported.");
try{
  $db = new SQLite3($dbname);
} catch(Exception $exception) {
  echo $exception->getMessage();
}
// echo "SQLite 3 supported.";

$$result = $db->exec("DROP TABLE IF EXISTS teams; DROP TABLE IF EXISTS members");

$query = "CREATE TABLE teams (
  ID INTEGER NOT NULL PRIMARY KEY,
  name TEXT,
  url TEXT
)";
$result = $db->exec($query);
echo "Table 'teams' créée<br>";

$query = "CREATE TABLE members (
  ID INTEGER NOT NULL PRIMARY KEY,
  name TEXT,
  profile INTEGER,
  team INTEGER,
  FOREIGN KEY(team) REFERENCES teams(ID)
)";
echo "Table 'members' créée<br>";

for ($i = 0; $i < $numberOfTeams; $i++) {
  $result = $db->exec("INSERT INTO teams VALUES ($i, '$teamNames[$i]', '$teamImages[$i]')");
}
echo "Table 'teams' remplie<br>";

// $result = $db->query("SELECT * FROM teams");
// while ($team = $result->fetchArray()) {
//   echo $team['name'];
// }

echo "OK."
?>
