#!/usr/bin/php -q
<?php
 ob_implicit_flush(false);
 error_reporting(0);
 set_time_limit(300);

//   Nerd Vittles Amazon Polly TTS Interface  ver. 1.0, (c) Copyright Ward Mundy & Associates LLC, 2007-2017. All rights reserved.

//                    This software is licensed under the GPL2 license.
//
//   For a copy of license, visit http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
//
//   Edit by norman@olssoo.com 
//   OLSSOO FACTORY LLC.

//-------- DON'T CHANGE ANYTHING ABOVE THIS LINE ----------------

// 아마존 AWS 정보
 $Amazon_key    = "XXXXXXXXXXXXXXXXXXXX";
 $Amazon_secret = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

 //$Amazon_region = "us-east-1";
 // 서울리젼으로 변경
 $Amazon_region = "ap-northeast-2";
 $email         = "yourname@yourdomain" ;
 $email         = "norman@olssoo.com" ;

 $debug = 1;
 $newlogeachdebug = 1;
 $emaildebuglog = 0;


//-------- DON'T CHANGE ANYTHING BELOW THIS LINE ----------------


$log = "/var/log/asterisk/pollytts.txt" ;
if ($debug and $newlogeachdebug) :
 if (file_exists($log)) :
  unlink($log) ;
 endif ;
endif ;

 $stdlog = fopen($log, 'a'); 
 $stdin = fopen('php://stdin', 'r'); 
 $stdout = fopen( 'php://stdout', 'w' ); 

if ($debug) :
  fputs($stdlog, "Nerd Vittles Polly TTS Interface ver. 1.0 (c) Copyright 2007-2017, Ward Mundy & Associates LLC. All Rights Reserved.\n\n" . date("F j, Y - H:i:s") . "  *** New session ***\n\n" ); 
endif ;

function read() {  
 global $stdin;  
 $input = str_replace("\n", "", fgets($stdin, 4096));  
 dlog("read: $input\n");  
 return $input;  
}  

function write($line) {  
 dlog("write: $line\n");  
 echo $line."\n";  
}  

function dlog($line) { 
 global $debug, $stdlog; 
 if ($debug) fputs($stdlog, $line); 
} 

function execute_agi( $command ) 
{ 
GLOBAL $stdin, $stdout, $stdlog, $debug; 
 
fputs( $stdout, $command . "\n" ); 
fflush( $stdout ); 
if ($debug) 
fputs( $stdlog, $command . "\n" ); 
 
$resp = fgets( $stdin, 4096 ); 
 
if ($debug) 
fputs( $stdlog, $resp ); 
 
if ( preg_match("/^([0-9]{1,3}) (.*)/", $resp, $matches) )  
{ 
if (preg_match('/result=([-0-9a-zA-Z]*)(.*)/', $matches[2], $match))  
{ 
$arr['code'] = $matches[1]; 
$arr['result'] = $match[1]; 
if (isset($match[3]) && $match[3]) 
$arr['data'] = $match[3]; 
return $arr; 
}  
else  
{ 
if ($debug) 
fputs( $stdlog, "Couldn't figure out returned string, Returning code=$matches[1] result=0\n" );  
$arr['code'] = $matches[1]; 
$arr['result'] = 0; 
return $arr; 
} 
}  
else  
{ 
if ($debug) 
fputs( $stdlog, "Could not process string, Returning -1\n" ); 
$arr['code'] = -1; 
$arr['result'] = -1; 
return $arr; 
} 
}  

// ------ Code execution begins here
// parse agi headers into array  
//while ($env=read()) {  
// $s = split(": ",$env);  
// $agi[str_replace("agi_","",$s0)] = trim($s1); 
// if (($env == "") || ($env == "\n")) {  
//   break;  
// }  
//}  

while ( !feof($stdin) )  
{ 
$temp = fgets( $stdin ); 
 
if ($debug) 
fputs( $stdlog, $temp ); 
 
// Strip off any new-line characters 
$temp = str_replace( "\n", "", $temp ); 
 
$s = explode( ":", $temp ); 
$agivar[$s[0]] = trim( $s[1] ); 
if ( ( $temp == "") || ($temp == "\n") ) 
{ 
break; 
} 
}  

$text2say = $_SERVER["argv"][1];
//$text2say = "Here is a test.";

if ($debug) :
fputs($stdlog, "Text to Say: " . $text2say . "\n\n" );
endif ;

$speech = array('Text' => $text2say,
  'OutputFormat' => 'mp3',
  'TextType' => 'text',
  //'VoiceId' => 'Joanna');
  //Seoyeon = 한국어 TTS
  'VoiceId' => 'Seoyeon');

if ($debug) :
 foreach ( $speech as $item ) {
  fputs($stdlog, "Speech: " . $item . "\n" );}
  fputs($stdlog, "\n" );
endif ;


$credentials = array('key' => $Amazon_key,
  'secret' => $Amazon_secret);

$config = array('version' => 'latest',
  'region' => $Amazon_region,
  'credentials' => $credentials);

if ($debug) :
 foreach ( $config as $item ) {
  fputs($stdlog, "Config: " . $item . "\n" );}
endif ;

//if ($debug) :
// foreach ( $credentials as $item ) {
//  fputs($stdlog, "Credentials: " . $item . "\n" );}
//  fputs($stdlog, "\n" );
//endif ;


require('/var/lib/asterisk/agi-bin/vendor/autoload.php');

use Aws\Polly\PollyClient;

// get service handle
try {$client = new PollyClient($config);}
catch(Exception $e) {print_r($e); fputs($stdlog, "Exception: " . $e . "\n\n" ); exit;}

// get speech
$response = $client->synthesizeSpeech($speech);

// save response file
file_put_contents('/tmp/text.mp3', $response['AudioStream']);

// convert MP3 to GSM for Asterisk
unlink ("/tmp/text.gsm") ;

system('sox /tmp/text.mp3 -r 8000 -c 1 /tmp/text.gsm');
unlink ("/tmp/text.mp3") ;

$rc = execute_agi("STREAM FILE /tmp/text \"\" ");

if ($emaildebuglog) :
 system("mime-construct --to $email --subject " . chr(34) . "Nerd Vittles Polly TTS Interface ver. 1.0 Session Log" . chr(34) . " --attachment $log --type text/plain --file $log") ;
endif ;

// clean up file handlers etc.
fclose($stdin);
fclose($stdout);
fclose($stdlog);
exit;

?>
