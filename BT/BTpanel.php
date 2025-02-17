<?php 
// UUID Generator
function uuid(){
  return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff), mt_rand( 0, 0xffff ),
    mt_rand( 0, 0x0fff ) | 0x4000,
    mt_rand( 0, 0x3fff ) | 0x8000,
    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff), mt_rand( 0, 0xffff ));
} 
  
// Prepare Keys
$keys = array();
$keys[0] = array("id"=>"http://www.gutp.jp/v1/bt/vbt", "attrName"=>"time", "select"=>"maximum");
$keys[1] = array("id"=>"http://www.gutp.jp/v1/bt/ibt", "attrName"=>"time", "select"=>"maximum"); 
$keys[2] = array("id"=>"http://www.gutp.jp/v1/bt/pbt", "attrName"=>"time", "select"=>"maximum"); 
$keys[3] = array("id"=>"http://www.gutp.jp/v1/bt/qbt", "attrName"=>"time", "select"=>"maximum"); 
$keys[4] = array("id"=>"http://www.gutp.jp/v1/bt/whbt", "attrName"=>"time", "select"=>"maximum"); 
$keys[5] = array("id"=>"http://www.gutp.jp/v1/bt/fbt", "attrName"=>"time", "select"=>"maximum"); 
$keys[6] = array("id"=>"http://www.gutp.jp/v1/bt/pfbt", "attrName"=>"time", "select"=>"maximum"); 
$keys[7] = array("id"=>"http://www.gutp.jp/v1/bt/vbdc", "attrName"=>"time", "select"=>"maximum"); 
$keys[8] = array("id"=>"http://www.gutp.jp/v1/bt/ibdc", "attrName"=>"time", "select"=>"maximum"); 
$keys[9] = array("id"=>"http://www.gutp.jp/v1/bt/pbdc", "attrName"=>"time", "select"=>"maximum"); 
$keys[10] = array("id"=>"http://www.gutp.jp/v1/bt/efbdc", "attrName"=>"time", "select"=>"maximum");
$keys[11] = array("id"=>"http://www.gutp.jp/v1/bt/efdc", "attrName"=>"time", "select"=>"maximum");  
  
// Generate Query, Header, and Transport for query
$query=array("type"=>"storage", "id"=>uuid(), "key"=>$keys);
$header=array("query"=>$query);
$transport=array("header"=>$header); 
$queryRQ=array("transport"=>$transport); 
  
// Call an IEEE1888 Storage server
// Specify the IP address of the SDK.
$server = new SoapClient("http://52.27.198.165/axis2/services/FIAPStorage?wsdl");
$queryRS = $server->query($queryRQ); 
  
// Parse IEEE1888 FETCH-Response 1 (Error Handling)
if($queryRS == NULL){
   echo "Error occured -- the result is empty.";
   exit;
}
if(!array_key_exists("transport",$queryRS)){
   echo "Error occured -- the transport in the result is empty.";
   exit;
}
$transport=$queryRS->transport;

if(!array_key_exists("header",$transport)){
   echo "Error occured -- the header in the transport is empty.";
   exit;
}
$header=$transport->header;

if(!array_key_exists("OK",$header)){
   if(!array_key_exists("error",$header)){
      echo "Error occured -- neither OK nor error presented in the header.";
      exit;
   }
   echo "Error:".$header->error->_;
   exit;
} 
  
// Parse IEEE1888 FETCH-Response 2 (Data Parsing, and Print out)
if(array_key_exists("body",$transport)){
  $body=$transport->body;
  if(array_key_exists("point",$body)){
    $points = $body->point;
    for($i=0;$i<count($points);$i++){
      if(count($points)==1){
        $point=$points;
      }else{
        $point=$points[$i];
            } 
      if(array_key_exists("value",$point)){
        $id=$point->id;
        $value=$point->value;

        $time=$value->time;
        $val=$value->_;

        header("Content-type: text/plain; charset=UTF-8");// is here ok?
        echo $val.",";
            }
       }
   }
}
?>

