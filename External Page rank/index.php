<?php

// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');

$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;

$additionalparameters = array('sort'=>'pageRankFile desc');



if ($query)
{
  // The Apache Solr Client library should be on the include path
  // which is usually most easily accomplished by placing in the
  // same directory as this script ( . or current directory is a default
  // php include path entry in the php.ini)
  require_once('/Users/siri/IR/solr-php-client/Apache/Solr/Service.php');

  // create a new solr service instance - host, port, and webapp
  // path (all defaults in this example)
  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/myassign');

  // if magic quotes is enabled then stripslashes will be needed
  if (get_magic_quotes_gpc() == 1)
  {
    $query = stripslashes($query);
  }

  // in production code you'll always want to use a try /catch for any
  // possible exceptions emitted  by searching (i.e. connection
  // problems or a query parsing error)
  try
  {
    if($_REQUEST['rado']!='rank')
          $results = $solr->search($query, 0, $limit);
    else {
          $results = $solr->search($query, 0, $limit, $additionalparameters);
    }
  }
  catch (Exception $e)
  {
    // in production you'd probably log or email this error to an admin
    // and then show a special message to the user but for this example
    // we're going to show the full exception
    die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }
}

?>

<html>
  <head>
    <title>PHP Solr Client Example</title>
  </head>
  <body>
    <form  accept-charset="utf-8" method="get">
      <label for="q">Search:</label>
      <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
      <input type="radio" name="rado" value="lucene" <?php if(!isset($_REQUEST[ 'rado']) || $_REQUEST[ 'rado']=='lucene' ) echo "checked"; ?>> Solr-Lucene </td>
      <input type="radio" name="rado" value="rank" <?php if(!isset($_REQUEST[ 'rado']) || $_REQUEST[ 'rado']=='rank' ) echo "checked"; ?>> Page Rank </td>
      <input type="submit"/>
    </form>

<?php

// display results
if ($results)
{
  $total = (int) $results->response->numFound;
  $start = min(1, $total);
  $end = min($limit, $total);
?>
    <div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
    <ol>
<?php
  // iterate result documents
  foreach ($results->response->docs as $doc)
  {
?>
      <li>
        <table style="border: 1px solid black; text-align: left">
<?php
    // iterate document fields / values
    $title = "NA";
    $url = "NA";
    $id = "NA";
    $desc = "NA";

    foreach ($doc as $field => $value)
    {
                if($field== "og_url" ){
                $url=$value;
                }
                if($field== "id" ){
          				$id=$value;
          			}
          			if($field == "title"){
          				$title=$value;
          			}
          			if($field == "og_description"){
          				$desc=$value;
          			}

    }
            echo "<tr>";
            echo "Title = " . "<a href= " .$url. ">" .$title. "</a>" . "<br>";
            echo "Link=" . "<a href= " .$url. ">" .$url. "</a>" . "<br>";
            echo "Id=" . $id . "<br/>";
            echo "Description=" . $desc . "<br/>";
            echo "<br>";
            echo "</tr>";

?>
        </table>
      </li>
<?php
  }
?>
    </ol>
<?php
}
?>
  </body>
</html>
