var stopAnimation = false;
var mute = false;
var ajaxUrl = 'ajax-v04.php';
var sounds = new Array;
var ajaxInAction = false;
var ajaxTimer = 0;
var tokeDelayDefault = 60;
var tokeDelay = tokeDelayDefault;
var hardLimit = 201600;
var loops = 0;
var activeToke = false;
var finishingToke = false;
var tokers = new Array;
var tokeInTimer = false;
var soundedTokers = new Array;
var onToke = 1;
var tokesInLast = 0;
var debug = false;
var banner = 'Loading...';
var nextLoop = 7000;
var cpLoaded = false;

function thexInit(aUrl) {
 $('#controlPanelDialog').dialog({
  autoOpen: false,
  show: { effect: "blind", duration: 800 },
  hide: { effect: "explode", duration: 1000 }
 });
 if (aUrl) ajaxUrl = aUrl;
 $('#chatbox').height($(window).height()-64);
 ajaxLoop('init');
 soundInit();
 log("=================================================================");
 log("===========  ThexChat Engine ("+ajaxUrl+") =====================");
 log("=================================================================");
}

function controlPanel() {
 $('#controlPanelDialog').dialog("open");
 if (!cpLoaded) {
  $('#controlPanelDialog').load(ajaxUrl + '?params=getControlPanel', function() {
   cpLoaded = true;
  });
 }
}

function log(msg) {
 if (!debug) return;
    setTimeout(function() {
        throw new Error("===TC=== " + msg);
    }, 0);
}

function soundInit() {
 soundManager.setup({
  url: 'swf/',
//  flashVersion: 9, // optional: shiny features (default = 8)
  // optional: ignore Flash where possible, use 100% HTML5 mode
  preferFlash: false,
  onready: function() {
    // Ready to use; soundManager.createSound() etc. can now be called.
   initSounds();
  }
 });
}

function initSounds() {
 newSound('toke','sounds/toke2.mp3');
}

function newSound(sid,mp3) {
 var sound = soundManager.createSound({
  id: sid+'er',
  url: mp3
 });
 sounds[sid] = sound;
 return sound;
}

function playSound(sid) {
 if (mute) return;
 if (!sid) return;
 if (!sounds[sid]) return;

 sounds[sid].play();
}

function moveBar() {
 $('#bar').insertAfter($('#chatbox'));
}

var paused = false;
function pauseUpdate() {
 clearTimeout(ajaxTimer);
 if (paused) {
  $('#pausebuttonImage').attr('src','images/pause.png');
  paused = false;
  ajaxInAction = false;
  ajaxLoop('loop');
 } else {
  $('#pausebuttonImage').attr('src','images/play.png');
  ajaxInAction = true;
  paused = true;
 }
}

function ajaxLoop(opt,force) {
 if (ajaxInAction && !force) return;
 if (ajaxInAction) clearTimeout(ajaxTimer);
 ajaxInAction = true;
 if (loops >= hardLimit) return;
 loops = loops + 1;

 var params = opt;
 if (!params) params = 'loop';

 $.ajax({
  url: ajaxUrl + "?loops=" + loops + "&params=" + params,
  complete: function(r,s){
   clearTimeout(ajaxTimer);
   if (paused) return;
   ajaxTimer = setTimeout('ajaxLoop()', nextLoop);
   ajaxInAction = false;
  },
  error: function(r,s,es){
   var errstr = '<span title="Error Response: ' + r.status + ' ' + es + '">Warning: No connection to server</span>';
   $('#status').html(errstr);
  },
  success: function(result){
   var jsonData = eval('(' + result + ')');

   if (jsonData['html']) {
    $.each(jsonData['html'], function (destObj, htmlData) {
     $('#'+destObj).html(htmlData);
    });
   }

   if (jsonData['perform']) {
    $.each(jsonData['perform'], function( i, actionData ) {
     if (typeof window[actionData['command']] === "function")
      if (actionData['data'] !== undefined)
       eval(actionData['command'] + '(\'' + actionData['data'] + '\')');
      else
       eval(actionData['command'] + '()');
     if (actionData['js'])
      eval(actionData['js']);
    });
   }

   if (jsonData['tokeDelay']) tokeDelay = jsonData['tokeDelay'];
   if (jsonData['tokers']) tokers = jsonData['tokers'];
   if (jsonData['banner']) { banner = jsonData['banner']; updateStatus(); }
   if (jsonData['tokesInLast']) { tokesInLast = jsonData['tokesInLast']; }

   if (!jsonData['nextLoop']) nextLoop = 7000;
   else nextLoop = jsonData['nextLoop'] * 1000;
  }
 });
}

function updateStatus() {
 if (!activeToke && !finishingToke)
  $('#status').html(banner);
}

function resetProgressBar() {
 stopAnimation = true;
 progress(0);
 $('#progressFill').animate({backgroundColor: '#0a0'}, "fast");
 $('#progressFill').html("Get&nbsp;Ready...");
 updateStatus();
 tokers = [ ];
 soundedTokers = [ ];
 $('#countdown').css('font-family', 'Arial, Helvetica, sans-serif');
 $('#countdown').css('font-size', '24px');
 $('#countdown').animate({color: '#fff'}, 25);
 finishingToke = false;
}

function animatedToke(i) {
 i = i + 1;
 if (stopAnimation) {
  stopAnimation = false;
  return;
 }
 if (i == 1) {
  $('#progressFill').html("Cheers ! ! !");
  setTimeout("resetProgressBar()", 15000);
 }
 if (i >= 300) { return; }

 var frequency = .3;
 var r = Math.floor(Math.sin(frequency*i + 0) * 127 + 128);
 var g = Math.floor(Math.sin(frequency*i + 2) * 127 + 128);
 var b = Math.floor(Math.sin(frequency*i + 4) * 127 + 128);

 var hue = 'rgb('+r+','+g+','+b+')';
// $('#countdown').animate({color: hue}, 25);
 $('#progressFill').animate({backgroundColor: hue}, 50, 'swing', function() { animatedToke(i); });
}

function tokeIn(remaining,newTokeDelay) {
 clearTimeout(tokeInTimer);

 if (newTokeDelay) tokeDelay = newTokeDelay;
 else tokeDelay = tokeDelayDefault;

 if ((remaining <= 0) || !remaining) {
  if (finishingToke) return;
  finishingToke = true;
  progress(100);
  animatedToke(0);
  $('#countdown').css('font-family', '"Comic Sans MS", cursive, sans-serif');
  $('#countdown').css('font-size', '16px');
  $('#countdown').html('420');
  return;
 } else {
  $('#countdown').html(remaining);
 }

 var percentToToke = (remaining/tokeDelay)*100;
 var tokePercentLeft = 100-percentToToke;
 progress(tokePercentLeft);

 if (tokers) {
  onToke = parseInt(tokesInLast,10);
  $('#status').html("Toke #" + onToke + " with " + tokers.join(', '));
  $.each(tokers, function (k,v) {
   if (!soundedTokers[v]) {
    playSound('toke');
    soundedTokers[v] = true;
   }
  });
 }

 var remMinusOne = remaining - 1;
 tokeInTimer = setTimeout("tokeIn(" + remMinusOne + ")", 1000);
}

function progress(progressValue) {
 if (!activeToke && progressValue>0) {
  activeToke = true;
  $('#progressBar').animate({opacity: 1}, "slow");
  $('#countdown').animate({opacity: 1}, "slow");
 }
 if (!progressValue) {
  $('#progressBar').animate({opacity: 0}, "fast");
  $('#countdown').animate({opacity: 0}, "fast");
  activeToke = false;
 }
 var barWidth = $('#progressBar').width()-2;
 var pixelsPerPercent = barWidth / 100;
 var newWidth = Math.floor(pixelsPerPercent * progressValue);
 $('#progressFill').animate({ "width": newWidth+"px" }, "slow");
}

function ajaxCommand(command) {
 ajaxLoop(command,true);
}

function toke() {
 ajaxCommand('toke');
}
