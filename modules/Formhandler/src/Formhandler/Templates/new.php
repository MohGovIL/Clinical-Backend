<!--                          Update 2016/08/13  -->
<?php

include_once("../../globals.php");
include_once("$srcdir/api.inc");
define('REPORT_NAME', '#FORMNAME#');
define('HANDLER_PATH', "/modules/zend_modules/public/Formhandler?form=");
formHeader("Form: ".REPORT_NAME);
$returnurl = $GLOBALS['concurrent_layout'] ? 'encounter_top.php' : 'patient_encounter.php';
?>
<html><head>
    <?php //html_header_show();();?>
    <link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
</head>
<body class="body_top">
<div id="demo"></div>

<?php
$formid = 0 + (isset($_GET['id']) ? $_GET['id'] : '');
if ($formid) {
    $parm="&pid=".$_SESSION["pid"]."&encounter=".$_SESSION["encounter"]."&id=".$formid."&edit=true";
    ?>
    <iframe width="100%" height="94%" src="<?php echo $GLOBALS['rootdir'].HANDLER_PATH.REPORT_NAME.$parm;?>"></iframe>
<?php } else { ?>
    <iframe width="100%" height="94%" src="<?php echo $GLOBALS['rootdir'].HANDLER_PATH.REPORT_NAME;?>"></iframe>
<?php } ?>
<?php
formFooter();
?>
