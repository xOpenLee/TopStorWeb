<?php 
 $content = $_GET["file"];
 #preg_replace("/[^a-zA-Z0-9:@.]+/","",html_entity_decode($content,ENT_QUOTES)); 
 $content = str_replace ('"','',$content);
 $msgformat = explode("@",$content);
 fclose($myfile); 
 $msgcode = array_shift($msgformat); 
 print $msgcode;
 $myfile = fopen("msgs.txt","r");
 $msglinesall = fread($myfile,filesize("msgs.txt"));
 fclose($myfile); 
 $msglines = explode("\n",$msglinesall);
# $msglines = array_reverse($msglines);
 $pos = -1;
 $isfound = "";
 $msgf = "no error code found";
 for ($l = 0; $l < count($msglines); $l++) {
  $isfound = strpos ($msglines[$l], $msgcode);
  if(  is_numeric($isfound)  ) {  $pos = $l; };
 };
 if ($pos >= 0 ) { 
  $msgfarr = explode(":",$msglines[$pos]); 
  $msgf = "";
  array_shift($msgfarr);
  array_push($msgformat," ");
  for ($w = 0; $w < count($msgfarr); $w++) {
   $msgf .= $msgfarr[$w] . $msgformat[$w]; 
  };
 };
 
 
 print str_replace("\n","",$msgf);
?>
