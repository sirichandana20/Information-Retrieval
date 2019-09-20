<?php
include 'SpellCorrector.php';
include 'simple_html_dom.php';
header('Content-Type: text/html; charset=utf-8');
$val=0;
$val1 = ""; $val2=""; $op = "";
$limit = 10;
$query = isset($_REQUEST['auto_val']) ? $_REQUEST['auto_val'] : false;
$results = false;
if ($query)
{
  $option = isset($_REQUEST['sort'])? $_REQUEST['sort'] : "solr";
  require_once('/Users/siri/IR/solr-php-client/Apache/Solr/Service.php');
  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/myassign');
  if (get_magic_quotes_gpc() == 1)
    $query = stripslashes($query);

    if($option == "solr")
     $additionalParameters=array('sort' => '');
   else
    $additionalParameters=array('sort' => 'pageRankFile desc');

  $wd = explode(" ",$query);
  $sz = sizeof($wd);
  $sp = $wd[$sz-1];
  for($ct=0; $ct<$sz; $ct++)
  { ini_set('memory_limit',-1);
    $value_num = SpellCorrector::correct($wd[$ct]);
    if($val1!="")
      $val1 = $val1."+".trim($value_num);
    else{ $val1 = trim($value_num);}
          $val2 = $val2." ".trim($value_num); }
    $val2 = str_replace("+"," ",$val1);
    $val=0;
    if(strtolower($query)==strtolower($val2)){
      $results = $solr->search($query, 0, $limit, $additionalParameters);
    }
    else {
      $val =1;
      $results = $solr->search($query, 0, $limit, $additionalParameters);
      $ur = "http://localhost/index.php?auto_val=$val1&sort=$option";
      $op =
      "Do you mean: <a href='$ur'>$val2</a>";
    }
}
?>
<?php
if($val){
echo $op;
}
$tot =0;
$prev="";
$arr =  array_map('str_getcsv', file('/Users/siri/solr-7.5.0/mercury_news/URLtoHTML_mercury.csv'));
if ($results)
{
$t = $results->response->numFound;
$total = (int)$t;
$s1= min(1, $total);
$s2 = min($limit, $total);
echo " results $s1: $s2 of $total :<ol>";
foreach ($results->response->docs as $document)
{
 $id = $document->id;
 $title = $document->title;
 $description = $document->description;
 if($title=="" ||$title==null){
  $title = $document->dc_title;
  if($title=="" ||$title==null)
    $title="N/A";
}
$id2 = $id;
$id = str_replace("/Users/siri/solr-7.5.0/mercury_news/mercurynews/","",$id);
foreach($arr as $data)
{
 if($id==$data[0])
 {
   $url = $data[1];
   break;
 }
}
$ar_val = explode(" ",$_GET["auto_val"]);
$tot = 0;
$g = 0;
$a2 = "";
$name = "/Users/siri/solr-7.5.0/mercury_news/mercurynew/" . substr($id,0,strlen($id)-5);
$file_n = fopen($name,"r");
while(! feof($file_n))
{
 $snip_text = fgets($file_n);
 $p = strtolower($snip_text);
 foreach($ar_val as $wd)
 {
   $wd = strtolower($wd);
   if (strpos($p, $wd) !== false)
   $tot = $tot+1;
 }
 if($g<$tot)
 { $a2 = $snip_text;
   $g = $tot;
 }
 else if($g==$tot && $tot>0)
 { if(strlen($a2)<strlen($snip_text)){
     $a2 = $snip_text;
     $g = $tot;
 }}
 $tot = 0;
}
$location = 0;
$wd = "";
foreach ($ar_val as $wd) {
 $st = strpos(strtolower($a2), strtolower($wd));
 if (st!== false)
 { $location = st;
   break;
 }
}
$s1= 0;
if($location>80)
 $s1= $location - 80;
else
 $s1= 0;
$s2 = $s1+ 160;
$l = strlen($a2);
if($l<$s2)
{
 $s2 = $l-1;
 $fill = "";
}
else
 $fill = "...";

if($l>160)
{
 if($start>0)
   $pre = "...";
 else
   $pre = "";
 $a2 = $pre . substr($a2,$start,$s2-$start+1) . $fill;
}
if($l==0)
 $a2 = $description;
unset($data);
echo "<li><a href='$url' target='_blank'>$title</a></br>
<a href='$url' target='_blank'>$url</a></br>
<b>Snippet:</b> $a2<br/>
<b>ID: </b> $id2</li></br></br>";
}
// <b>descriptionription:</b> $desc<br/>
}
?>
