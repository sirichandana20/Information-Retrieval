<?php
include 'indexi.php'
?>
<html>
<head>
  <title>Assignment</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
</head>
<body>
  <form method="get">
    <label for="auto_val">Query</label>
    <input id="auto_val" name="auto_val" type="text" value="<?php $input = htmlspecialchars($query, ENT_QUOTES, 'utf-8');echo $input; ?>"/>
    <br/>
    <input type="radio" name="sort" value="pagerank"
    <?php if(isset($_REQUEST['sort']) && $option == "pagerank")
    { echo 'checked="checked"';}
    ?>
    >pagerank
    <input type="radio" name="sort" value="solr"
    <?php if(isset($_REQUEST['sort']) && $option == "solr")
    { echo 'checked="checked"';}
    ?>
    >solr
    <br/>
    <input type="submit" value="Submit"/>
  </form>
  <script>
   $(function() {
     var pref = "http://localhost:8983/solr/myassign/suggest?q=";
     var suf = "&wt=json&indent=true";
     var count=0;
     var n = 0;
     var tags = [];
     $("#auto_val").autocomplete({
       source : function(req, response) {
         var valid="";
         var previous="";
         var v = "";
         var q = $("#auto_val").val().toLowerCase();
         var gp =  $("#auto_val").val().toLowerCase().lastIndexOf(' ');
         if(q.length-1>gp && gp!=-1){
          valid=q.substr(gp+1);
          previous = q.substr(n,gp);
        }
        else{
          v=q.substr(n);
          valid = v;
        }
        var myurl = pref + valid+ suf;
        $.ajax({
         url : myurl,
         success : function(inp_data) {
          var jsonData = JSON.parse(JSON.stringify(inp_data.suggest.suggest));
          var result =jsonData[valid].suggestions;
          var j=0;
          var bef =[];
          for(var i=0;i<5 && j<result.length;i++,j++){
            if(result[j].term==valid)
            {
              i--;
              continue;
            }
            for(var k=0;k<i && i>0;k++){
              if(tags[k].indexOf(result[j].term) >=0){
                i--;
                continue;
              }
            }
            if(result[j].term.indexOf('.')>=0 || result[j].term.indexOf('_')>=0)
            {
              i--;
              continue;
            }
            var s =(result[j].term);
            if(bef.length == 5)
              break;
            if(bef.indexOf(s) == -1)
            {
              bef.push(s);
              if(previous==""){
                tags[i]=s;
              }
              else
              {
                tags[i] = previous+" ";
                tags[i]+=s;
              }
            }
          }
          console.log(tags);
          response(tags);
        },
        dataType : 'jsonp',
        jsonp : 'json.wrf'
      });
      },
      minLength : 1
    })
   });
  </script>
</body>
</html>
