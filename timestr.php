<?

global $wordSets;
$wordSets['long'] = array(
 's' => ' Second',
 'm' => ' Minute',
 'h' => ' Hour',
 'd' => ' Day',
 'plurality' => true
);


$wordSets['medium'] = array(
 's' => ' Sec',
 'm' => ' Min',
 'h' => ' Hr',
 'd' => ' Day',
 'plurality' => true
);

$wordSets['short'] = array(
 's' => 'S',
 'm' => 'M',
 'h' => 'H',
 'd' => 'D',
 'plurality' => false
);

function timestr($secs, $wordset = null) {
 global $wordSets;
 if ($wordset && $wordSets[$wordset]) $wordset = $wordSets[$wordset];
 if ($secs <= 59) {
  $str = $secs . timestr_word($wordset, $secs, 's');
 } elseif($secs <= 3599) {
  $tmp = floor($secs / 60);
  $remain = floor($secs - ($tmp * 60));
  $str = $tmp . timestr_word($wordset, $tmp, 'm') . " " . $remain . timestr_word($wordset, $remain, 's');
 } elseif($secs <= 86399) {
  $tmp = floor(($secs / 60) / 60);
  $remain = floor(($secs / 60) - ($tmp * 60));
  $str = $tmp . timestr_word($wordset, $tmp, 'h') . " " . $remain . timestr_word($wordset, $remain, 'm');
 } elseif($secs >= 86400) {
  $tmp = floor((($secs / 60) / 60) / 24);
  $remain = floor((($secs / 60) - (($tmp * 24) * 60)) / 60);
  $str = $tmp . timestr_word($wordset, $tmp, 'd') . " " . $remain . timestr_word($wordset, $remain, 'h');
 } else { $str = "Invalid Time Argument: $secs"; }
 return($str); 
}

function timestr_word($wordset, $n, $type = null) {
 if (!$wordset) $wordset = array('s'=>' Second','m'=>' Minute','h'=>' Hour','d'=>' Day','plurality'=>true);
 if ($wordset['plurality'] && ($n != 1)) $plural = 's';
 return $wordset[$type] . $plural;
}

