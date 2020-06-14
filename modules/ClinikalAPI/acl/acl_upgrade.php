<?php
// Ensure this script is not called separately
if ((empty($_SESSION['acl_setup_unique_id'])) ||
    (empty($unique_id)) ||
    ($unique_id != $_SESSION['acl_setup_unique_id'])) {
    die('Authentication Error');
}
unset($_SESSION['acl_setup_unique_id']);

use OpenEMR\Common\Acl\AclExtended;

return $ACL_UPGRADE = array(

    '0.2.0' => function () {

        AclExtended::addObjectSectionAcl('client_app', 'Client Application');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'PatientTrackingInvited','Patient Tracking Invited');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'PatientTrackingWaitingForExamination','Patient Tracking Waiting for Examination');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'PatientTrackingWaitingForDecoding','Patient Tracking Waiting for Decoding');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'PatientTrackingFinished','Patient Tracking Finished');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'PatientAdmission','Patient Admission');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'AddPatient','Add Patient');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'AppointmentsAndEncounters','Appointments And Encounters');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'EncounterSheet','Encounter Sheet');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'SuperUser','Super User');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'SearchPatient','Search Patient');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'Calendar','Calendar');
        AclExtended::addObjectAcl('client_app', 'Client Application', 'AppointmentDetails','Appointment Details');

        AclExtended::addObjectSectionAcl('clinikal_api', 'Clinikal API');
        AclExtended::addObjectAcl('clinikal_api', 'Clinikal API', 'general_settings','General settings');
        AclExtended::addObjectAcl('clinikal_api', 'Clinikal API', 'lists','Lists');
    }
);
