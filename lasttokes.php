<html><head><title>CannaRoom Recent Tokes List</title>
<style>
html,body {
 color: white;
 background: black;
}
</style>
</head><body>
<?
require_once("mysql.inc.php");
require_once("timestr.php");

$limit = 30;
$result = mysql_query("SELECT * FROM tokeList ORDER BY date DESC LIMIT $limit");

echo "<h1>Last $limit tokes</h1>";
echo "* May not include the very latest toke<br>";
echo "* Statistics began on January 12th 2014<br>";
echo "<table border=2>";
echo "<tr><td>Toke #</td><td>Time</td><td>Tokers</td></tr>";
$now = time();
while ($toke = mysql_fetch_assoc($result)) {
 $secsago = $now - $toke['date'];
 $ago = timestr($secsago, 'medium');
 echo "<tr>";
 echo "<td>" . $toke['id'] . "</td>";
 echo "<td>" . $ago . " ago</td>";
 echo "<td>" . count(json_decode($toke['tokers'])) . " toker(s)</td>";
 echo "</tr>";
}
echo "</table>";
?></body></html>
