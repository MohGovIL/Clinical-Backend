<?php
// +-----------------------------------------------------------------------------+

// +------------------------------------------------------------------------------+

include_once("../../globals.php");
include_once($GLOBALS["srcdir"] . "/api.inc");

/* cannot use this :define('REPORT_NAME', '#FORMNAME#'); in multiple forms*/


define('HANDLER_PATH', '/modules/zend_modules/public/Formhandler/report?form=');
function #FORMNAME#_report($pid, $encounter, $cols, $id) {
$parm="&pid="."$pid"."&encounter=".$encounter."&col=".$cols."&id=".$id;
$divname="divid_container_".$id."_".$pid.'_'.'#FORMNAME#';
?>
    <div id="<?php echo $divname; ?>">
        <?php getToolTip_#FORMNAME#($id);?>
    </div>
    <script>
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", " <?php echo $GLOBALS[rootdir].HANDLER_PATH.'#FORMNAME#'.$parm;?>", false);
        xhttp.send();
        document.getElementById("<?php echo $divname;?>").innerHTML = xhttp.responseText;
    </script>
<?php } ?>

<?php

function getDocument($documentId)
{
    $host       = $GLOBALS['couchdb_host'];
    $port       = $GLOBALS['couchdb_port'];
    $usename    = $GLOBALS['couchdb_user'];
    $password   = decryptStandard($GLOBALS['couchdb_pass']);
    $database	= $GLOBALS['couchdb_dbase'];
    $enable_log = ($GLOBALS['couchdb_log'] == 1) ? true : false;

    $options = array(
    'host' 		  => $host,
    'port' 		  => $port,
    'user' 		  => $usename,
    'password' 	  => $password,
    'logging' 	  => $enable_log,
    'dbname'	  => $database
    );
    $connection = \Doctrine\CouchDB\CouchDBClient::create($options);
    $document = $connection->findDocument($documentId);
    return $document;
}


function getToolTip_#FORMNAME#($id)
{
        $count = 0;
        $data = formFetch("form_"."#FORMNAME#", $id);

        $form_name="#FORMNAME#";
        $form = getDocument($form_name);
        $form_fields=$form->body['document']['fields'];

        if ($data) {
            print "<div style='font-size:10px;'>";
            $rowBreak=7;//rows
            foreach ($data as $key => $value) {
                if ($key == "id" || $key == "pid" || $key == "user" || $key == "groupname" || $key == "authorized" || $key == "activity" || $key == "date" || $value == "" || $value == "0000-00-00 00:00:00") {
                continue;
                }
                if ($value == "on") {
                    $value = "yes";
                }
                //$key = ucwords(str_replace("_", " ", $key));

                $label=$form_fields[$key]['options']['label'];
                if ($label){
                    $key =$label;
                }
                else{
                    //$key = ucwords(str_replace("_", " ", $key));
                    $key=   ($key=="encounter")? "encounter" : "without title";
                }

                print "<p><span class=bold>" . xlt($key) . ": </span><span class=text>" . text($value) . "</span></p>";
                $count++;
                if ($count == $cols) {
                    $count = 0;
                    print "...\n";
                }
            }
        }
        print "</div>";
}
?>

