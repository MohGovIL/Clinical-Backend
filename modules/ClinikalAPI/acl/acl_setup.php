
<?php
// Ensure this script is not called separately
if ((empty($_SESSION['acl_setup_unique_id'])) ||
    (empty($unique_id)) ||
    ($unique_id != $_SESSION['acl_setup_unique_id'])) {
    die('Authentication Error');
}
unset($_SESSION['acl_setup_unique_id']);

use OpenEMR\Common\Acl\AclExtended;

//AclExtended::addObjectSectionAcl('pfeh', 'PatientFilterEventHook');


?>
<html>
<head>
    <title>___________ ACL Setup</title>
    <link rel=STYLESHEET href="interface/themes/style_blue.css">
</head>
<body>
<b>OpenEMR[_____________] ACL Setup</b>
<br>
All done configuring and installing access controls (php-GACL)!
</body>
</html>