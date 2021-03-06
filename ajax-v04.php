<?
error_reporting(0);

$admins = array(1);

global $state,$tokeDelay,$admin;
$tokeDelay = 60;

session_start();
require_once "./handlers/module/cms.config.handler.php";
require_once "./handlers/module/db.class.php";
require_once "./handlers/module/utils.class.php";
Db::dbConnect();
$uid = Utils::myUID();
require_once("mysql.inc.php");
require_once("timestr.php");

$op = $_GET['params'];
if (!$op) $op = 'loop';

if ($uid && in_array($uid, $admins)) $admin = true; else $admin = false;

$state['tokesInLast'] = tokesInLast();
getBanner();
getTokeEvents();

if (function_exists('ajax_' . $op) && ($op != 'loop')) call_user_func('ajax_'.$op);

ajax_loop();

if ($op == 'getControlPanel') {
 controlPanelPage();
} else {
 echo out();
}

function controlPanelPage() {
 global $admin;
 if ($admin) {
  echo "<div style=\"font-size: 10px;\">Add banner message</div>";
  echo "<input type=text id=bannermsg size=26>";
  echo "<br>";
  echo "<span style=\"font-size: 16px;\">Duration:</span> <select id=bannerduration>";
  echo "<option>1m</option>";
  echo "<option>10m</option>";
  echo "<option>30m</option>";
  echo "<option>1h</option>";
  echo "<option>2h</option>";
  echo "<option>3h</option>";
  echo "<option>6h</option>";
  echo "<option>9h</option>";
  echo "<option>12h</option>";
  echo "<option>24h</option>";
  echo "<option>EOD</option>";
  echo "</select>";
  echo "<span style=\"font-size: 16px;\">Persistant:</span> <input type=checkbox id=bannerpersistant title=\"Selecting this means the banner will be the ONLY one shown.\"><br>";
  echo "<a href=\"#\">Add</a>";
 } else {
  echo "Not an admin, sorry.";
 }
 
}

function getTokeEvents() {
 global $state;
 $result = mysql_query("SELECT * FROM tokeEvents WHERE expire>".time());
 $tokeEvents = array();
 while ($tokeEventData = mysql_fetch_assoc($result)) {
  $tokeEvents[$tokeEventData['id']] = array_merge($tokeEventData,json_decode($tokeEventData['event']));
 }

 foreach ($tokeEvents as $tokeEvent) {
  print_r($tokeEvent);
 }
}

function getBanner() {
 global $state;

 $timePerBanner = 600;

 $banners = array(
  'banner_tokesToday',
  'banner_tokesLast24',
  'banner_inTheLead',
  'banner_tokesLastWeek',
  'banner_randomQuote',
  'banner_tokersToday'
 );

 $now = time();
 $epochs = floor($now / $timePerBanner);

 $timeToNewBanner = $timePerBanner - ($now - ($epochs * $timePerBanner));
# $state['nextBannerIn'] = $timeToNewBanner;
 
 $key = $epochs % count($banners);
 if (!$banners[$key]) return;

 $minute = date('i');
 if ($minute >= 15 && $minute < 20) 
  $state['banner'] = "<B>" . banner_timeTo420() . "</B>";
 elseif ($minute == 20)
  $state['banner'] = "Happy " . date('g') . ":20!";
 else
  $state['banner'] = call_user_func($banners[$key]);
}

function banner_tokersToday() {
 $tokers = tokersInLast();
 
 if (count($tokers) <= 0) return "Nobody has shared a toke today :(";
 return count($tokers) . " stoners got high together here today.";
}

function banner_inTheLead() {
 $tokers = tokersInLast();

 if (count($tokers) <= 0) return "Nobody has toked today :(";
 foreach ($tokers as $toker => $tokes) { break; }
 return $toker . " is the most social stoner today with " . $tokes . " group tokes!";
}

function ajax_tokersThisWeek() {
 global $state;

 $tokers = tokersInLast(60*60*24*7);

 return count($tokers) . " stoners got high together here this week.";
}

function banner_tokesToday() {
 global $state;
 return $state['tokesInLast'] . " group tokes today";
}

function banner_tokesLast24() {
 return tokesInLast(60*60*24) . " shared tokes over last 24 hours";
}

function banner_tokesLastWeek() {
 return tokesInLast(60*60*24*7) . " shared tokes over last 7 days";
}

function banner_tokesLastHr() {
 return tokesInLast(60*60) . " tokes over last hour";
}

function banner_randomQuote() {
 $perQuote = 60*60;
 $quotes = array(
  'Stay calm, get high.',
  'Have you toked enough, today?',
  'Brought to you by your friend, Mr. Cannabis'
 );

 $now = time();
 $epochs = floor($now / $perQuote);
 $key = $epochs % count($quotes);
 return $quotes[$key];
}

function banner_timeTo420() {
 global $state;
 
 $now = time();
 $next420 = strtotime(date("G").":20",time());
 if ($next420 < $now)
  if (date("G") == 23)
   $next420 = strtotime("tomorrow 0:20",time());
  else 
   $next420 = strtotime((date("G")+1).":20",time());

 $hr = date("g");

 if ($next420 < $now) $hr += 1;
 if ((date("G") == 23) && $next420 > $now) $hr = 12;

 $until = $next420 - $now;
 return timestr($until, 'medium') . " until 4:20";
}

function findActiveToke($optokeOnly = false) {
 global $tokeDelay;
 $now = time();
 $sixtyago = $now - ($tokeDelay + 15);
 if ($optokeOnly) $addStr = " AND optoke='1'";
 $toke = mysql_fetch_assoc(mysql_query("SELECT * FROM tokes WHERE date >= $sixtyago".$addStr." ORDER BY date ASC LIMIT 1"));
 if ($toke['data']) $toke['data'] = json_decode($toke['data']);
 return $toke;
}

function findActiveTokers() {
 global $tokeDelay;
 $sixtyago = time() - ($tokeDelay + 15);
 $tokeQuery = mysql_query("SELECT * FROM tokes WHERE date >= $sixtyago ORDER BY date ASC");
 $tokers = array();
 while ($toke = mysql_fetch_assoc($tokeQuery)) {
  $tokers[$toke['name']] = $toke;
 }
 $toking = array();
 foreach ($tokers as $toker => $tokeData) {
  $toking[] = $toker;
 }
 return $toking;
}


function getActiveToke() {
 global $state,$tokeDelay;
 $now = time();

 $state['tokeDelay'] = $tokeDelay;

 $toke = findActiveToke(true);
 if (!$toke) return;

 $ago = $now - $toke['date'];
 $left = $tokeDelay - $ago;

 $state['tokers'] = findActiveTokers();
 $state['nextLoop'] = 2;
 if ($left >= 0)
  performCommand('tokeIn', $left);
}

function ajax_init() {
 global $state;
# setHtml("status","Awaiting Command...");
 getActiveToke();
}

function getPhpfoxUser() {
 if (empty($_COOKIE['core2719user_id'])) return randomName();
 $sql = "SELECT user_name FROM phpfox.phpfox_user WHERE user_id = '" . addslashes($_COOKIE['core2719user_id']) . "' LIMIT 1";
 $query = mysql_query($sql);
 $user = mysql_fetch_assoc($query);
 $name = $user['user_name'];
 if (!$name) $name = randomName();
 return $name;
}

function randomName() {
 $key = $_SERVER['REMOTE_ADDR'];
 return "RandomToker-" . substr(md5($key), 0, 4);
}


function ajax_toke() {
 $now = time();
 $name = getPhpfoxUser();
 $data = json_encode(array());
 $toke = findActiveToke(true);
 if (!$toke) $optoke = 1; else $optoke = 0;

 $tokers = findActiveTokers();
 if (in_array($name, $tokers)) {
#  mysql_query("DELETE FROM tokes WHERE name='" . addslashes($name) . "'");
  return;
 } else {
  if ($optoke) { addTokeCount(); mysql_query("TRUNCATE TABLE tokes"); }
  mysql_query("INSERT INTO tokes (id,date,name,optoke,data) VALUES ('', '" . $now . "', '" . addslashes($name) . "', '" . $optoke . "', '" . addslashes($data) . "')");
 }
}

function tokesInLast($last = null) {
 global $state;
 if ($last) $ago = time() - $last;
 if (!$ago) $ago = strtotime("0:00",time()); 

 $count = 0;
 $result = mysql_query("SELECT COUNT(*) as count FROM tokeList WHERE date >= $ago AND tokerCount>1");
 $data = mysql_fetch_assoc($result);
 if ($data) $count = $data['count'];

 return $count;

// $state['tokesInLast'] = $count;
}

function tokersInLast($last = null) {
 if ($last) $ago = time() - $last;
 if (!$ago) $ago = strtotime("0:00",time()); 

 $result = mysql_query("SELECT tokers FROM tokeList WHERE date >= $ago AND tokerCount>1");
 $tokers = array();
 while ($tokeData = mysql_fetch_assoc($result)) {
  $tokerList = json_decode($tokeData['tokers']);
  foreach ($tokerList as $toker) {
   if (!$tokers[$toker]) $tokers[$toker] = 1;
   else $tokers[$toker]++;
  }
 }
 arsort($tokers);
 return $tokers;
}

function addTokeCount() {
 $result = mysql_query("SELECT * FROM tokes");
 $tokers = array();
 $date = time();
 while ($toker = mysql_fetch_assoc($result)) {
  if ($toker['optoke'] == 1) $date = $toker['date'];
  $tokers[] = $toker['name'];
  mysql_query("INSERT INTO tokeStats (name, count, lastToke) VALUES ('" . addslashes($toker['name']) . "', '1', '" . $date . "') ON DUPLICATE KEY UPDATE count=count+1,lastToke='" . $date . "'");
 }
 $tokerString = json_encode($tokers);
 mysql_query("INSERT INTO tokeList (id, date, tokerCount, tokers) VALUES ('', '" . $date . "', '" . count($tokers) . "', '" . addslashes($tokerString) . "')");
}

function ajax_loop() {
# performJs('$(\'#count\').html(\'' . rand(0,500) . '\');');
# performJs('$(\'#loopnum\').html(\'' . $_GET['loops'] . '\');');
 getActiveToke();
}

function performCommand($command,$opt = null) {
 global $state;
 if (!$state['perform']) $state['perform'] = array();
 if ($opt)
  $state['perform'][] = array('command'=>$command,'data'=>$opt);
 else
  $state['perform'][] = array('command'=>$command);
}

function performJs($js) {
 global $state;
 if (!$state['perform']) $state['perform'] = array();
 $state['perform'][] = array('js'=>$js);
}

function setHtml($element, $source) {
 global $state;
 if (!$state['html']) $state['html'] = array();
 $state['html'][$element] = $source;
}

function out() {
 global $state;

 if (!$state['nextLoop']) $state['nextLoop'] = 7;

 return json_encode($state);
}
