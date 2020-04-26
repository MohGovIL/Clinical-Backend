
<?php
// Ensure this script is not called separately
if ((empty($_SESSION['acl_setup_unique_id'])) ||
    (empty($unique_id)) ||
    ($unique_id != $_SESSION['acl_setup_unique_id'])) {
    die('Authentication Error');
}
unset($_SESSION['acl_setup_unique_id']);

use OpenEMR\Common\Acl\AclExtended;

//EXAMPLE
/* Vaccines groups */
//$receptionist_view = @addNewACL('Vaccine receptionists', 'receptionist', 'view', 'Things that receptionists can read but not modify');
//$receptionist_write = @addNewACL('Vaccine receptionists', 'receptionist', 'write', 'Things that receptionists can modify');
$admin_write = getAclIdNumber('Administrators', 'write');
$admin_view = getAclIdNumber('Administrators', 'view');

$imaging_technician_write =@addNewACL('Imaging technician', 'imaging_technician', 'write', 'Things that imaging technician can modify');
$imaging_technician_view =@addNewACL('Imaging technician', 'imaging_technician', 'view', 'Things that imaging technician can read but not modify');
$imaging_doctor_write =@addNewACL('Imaging doctor', 'imaging_doctor', 'write', 'Things that imaging doctor can modify');
$imaging_doctor_view =@addNewACL('Imaging doctor', 'imaging_doctor', 'view', 'Things that imaging doctor can read but not modify');
$imaging_clinic_manager_write =@addNewACL('Imaging manager', 'imaging_clinic_manager', 'write', 'Things that imaging clinic manager can modify');
$imaging_clinic_manager_view =@addNewACL('Imaging manager', 'imaging_clinic_manager', 'view', 'Things that imaging clinic manager can read but not modify');
$imaging_call_center_representative_write =@addNewACL('Imaging representative', 'imaging_call_center_representative', 'write', 'Things that imaging call center representative can modify');
$imaging_call_center_representative_view =@addNewACL('Imaging representative', 'imaging_call_center_representative', 'view', 'Things that imaging call center representative can read but not modify');
$imaging_receptionist_write =@addNewACL('Imaging receptionist', 'imaging_receptionist', 'write', 'Things that imaging receptionist can modify');
$imaging_receptionist_view =@addNewACL('Imaging receptionist', 'imaging_receptionist', 'view', 'Things that imaging receptionist can read but not modify');

/**********************************************************/

//using BY https://matrixil.sharepoint.com/:x:/r/sites/DTG/Clinical/_layouts/15/Doc.aspx?sourcedoc=%7B49114388-8709-407B-BF08-8E8F26C8119F%7D&file=%D7%A7%D7%9C%D7%99%D7%A0%D7%99%D7%A7%D7%9C%20%D7%93%D7%99%D7%9E%D7%95%D7%AA%20-%20%D7%94%D7%A8%D7%A9%D7%90%D7%95%D7%AA%20%D7%9C%D7%A4%D7%99%20%D7%AA%D7%A4%D7%A7%D7%99%D7%93%D7%99%D7%9D%20%D7%92%D7%A8%D7%A1%D7%94%201.xlsx&action=default&mobileredirect=true&cid=6559357a-ef09-499a-b410-0a6b0078e2e7
//OBJECT OF ACL
addObjectSectionAcl('client_app', 'Client Application');
addObjectAcl('client_app', 'Client Application', 'PatientTrackingInvited','Patient Tracking Invited');
addObjectAcl('client_app', 'Client Application', 'PatientTrackingWaitingForExamination','Patient Tracking Waiting for Examination');
addObjectAcl('client_app', 'Client Application', 'PatientTrackingWaitingForDecoding','Patient Tracking Waiting for Decoding');
addObjectAcl('client_app', 'Client Application', 'PatientTrackingFinished','Patient Tracking Finished');
addObjectAcl('client_app', 'Client Application', 'PatientAdmission','Patient Admission');
addObjectAcl('client_app', 'Client Application', 'AddPatient','Add Patient');
addObjectAcl('client_app', 'Client Application', 'AppointmentsAndEncounters','Appointments And Encounters');
addObjectAcl('client_app', 'Client Application', 'EncounterSheet','Encounter Sheet');
addObjectAcl('client_app', 'Client Application', 'SuperUser','Super User');


updateAcl($admin_write, 'Administrators', 'client_app', 'Client Application', 'SuperUser','Super User', 'write');


//ADMIN ACL
/*  updateAcl($admin_write, 'Administrators', 'client_app', 'Client Application', 'PatientTrackingInvited','Patient Tracking Invited', 'write');
  updateAcl($admin_write, 'Administrators', 'client_app', 'Client Application', 'PatientTrackingWaitingForExamination','Patient Tracking Waiting for Examination', 'write');
  updateAcl($admin_write, 'Administrators', 'client_app', 'Client Application', 'PatientTrackingWaitingForDecoding','Patient Tracking Waiting for Decoding', 'write');
  updateAcl($admin_write, 'Administrators', 'client_app', 'Client Application', 'PatientTrackingFinished','Patient Tracking Finished', 'write');
  updateAcl($admin_write, 'Administrators', 'client_app', 'Client Application', 'PatientAdmission','Patient Admission', 'write');
  updateAcl($admin_write, 'Administrators', 'client_app', 'Client Application', 'AddPatient','Add Patient', 'write');
  updateAcl($admin_view , 'Administrators', 'client_app', 'Client Application', 'AppointmentsAndEncounters','Appointments And Encounters', 'view');
  updateAcl($admin_write, 'Administrators', 'client_app', 'Client Application', 'EncounterSheet','Encounter Sheet', 'write');*/


//Receptionist ACL
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'client_app', 'Client Application', 'PatientTrackingInvited','Patient Tracking Invited', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'client_app', 'Client Application', 'PatientTrackingWaitingForExamination','Patient Tracking Waiting for Examination', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'client_app', 'Client Application', 'PatientTrackingWaitingForDecoding','Patient Tracking Waiting for Decoding', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'client_app', 'Client Application', 'PatientTrackingFinished','Patient Tracking Finished', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'client_app', 'Client Application', 'PatientAdmission','Patient Admission', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'client_app', 'Client Application', 'AddPatient','Add Patient', 'write');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'client_app', 'Client Application', 'AppointmentsAndEncounters','Appointments And Encounters', 'view');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'client_app', 'Client Application', 'EncounterSheet','Encounter Sheet', 'write');

//Clinic manager ACL
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'client_app', 'Client Application', 'PatientTrackingInvited','Patient Tracking Invited', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'client_app', 'Client Application', 'PatientTrackingWaitingForExamination','Patient Tracking Waiting for Examination', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'client_app', 'Client Application', 'PatientTrackingWaitingForDecoding','Patient Tracking Waiting for Decoding', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'client_app', 'Client Application', 'PatientTrackingFinished','Patient Tracking Finished', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'client_app', 'Client Application', 'PatientAdmission','Patient Admission', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'client_app', 'Client Application', 'AddPatient','Add Patient', 'write');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'client_app', 'Client Application', 'AppointmentsAndEncounters','Appointments And Encounters', 'view');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'client_app', 'Client Application', 'EncounterSheet','Encounter Sheet', 'write');

//Clinic manager ACL
//updateAcl($imaging_technician_write, 'Imaging technician', 'client_app', 'Client Application', 'PatientTrackingInvited','Patient Tracking Invited', 'write');
updateAcl($imaging_technician_write, 'Imaging technician', 'client_app', 'Client Application', 'PatientTrackingWaitingForExamination','Patient Tracking Waiting for Examination', 'write');
updateAcl($imaging_technician_write, 'Imaging technician', 'client_app', 'Client Application', 'PatientTrackingWaitingForDecoding','Patient Tracking Waiting for Decoding', 'write');
//updateAcl($imaging_technician_write, 'Imaging technician', 'client_app', 'Client Application', 'PatientTrackingFinished','Patient Tracking Finished', 'write');
//updateAcl($imaging_technician_write, 'Imaging technician', 'client_app', 'Client Application', 'PatientAdmission','Patient Admission', 'write');
//updateAcl($imaging_technician_write, 'Imaging technician', 'client_app', 'Client Application', 'AddPatient','Add Patient', 'write');
updateAcl($imaging_technician_view,  'Imaging technician', 'client_app', 'Client Application', 'AppointmentsAndEncounters','Appointments And Encounters', 'view');
updateAcl($imaging_technician_write, 'Imaging technician', 'client_app', 'Client Application', 'EncounterSheet','Encounter Sheet', 'write');

//Doctor ACL
//updateAcl($imaging_doctor_write, 'Imaging doctor', 'client_app', 'Client Application', 'PatientTrackingInvited','Patient Tracking Invited', 'write');
//updateAcl($imaging_doctor_write, 'Imaging doctor', 'client_app', 'Client Application', 'PatientTrackingWaitingForExamination','Patient Tracking Waiting for Examination', 'write');
updateAcl($imaging_doctor_write, 'Imaging doctor', 'client_app', 'Client Application', 'PatientTrackingWaitingForDecoding','Patient Tracking Waiting for Decoding', 'write');
updateAcl($imaging_doctor_write, 'Imaging doctor', 'client_app', 'Client Application', 'PatientTrackingFinished','Patient Tracking Finished', 'write');
//updateAcl($imaging_doctor_write, 'Imaging doctor', 'client_app', 'Client Application', 'PatientAdmission','Patient Admission', 'write');
//updateAcl($imaging_doctor_write, 'Imaging doctor', 'client_app', 'Client Application', 'AddPatient','Add Patient', 'write');
updateAcl($imaging_doctor_view,  'Imaging doctor', 'client_app', 'Client Application', 'AppointmentsAndEncounters','Appointments And Encounters', 'view');
updateAcl($imaging_doctor_write, 'Imaging doctor', 'client_app', 'Client Application', 'EncounterSheet','Encounter Sheet', 'write');

//Clinic manager ACL
//updateAcl($imaging_call_center_representative_write, 'Imaging representative', 'client_app', 'Client Application', 'PatientTrackingInvited','Patient Tracking Invited', 'write');
//updateAcl($imaging_call_center_representative_write, 'Imaging representative', 'client_app', 'Client Application', 'PatientTrackingWaitingForExamination','Patient Tracking Waiting for Examination', 'write');
//updateAcl($imaging_call_center_representative_write, 'Imaging representative', 'client_app', 'Client Application', 'PatientTrackingWaitingForDecoding','Patient Tracking Waiting for Decoding', 'write');
//updateAcl($imaging_call_center_representative_write, 'Imaging representative', 'client_app', 'Client Application', 'PatientTrackingFinished','Patient Tracking Finished', 'write');
//updateAcl($imaging_call_center_representative_write, 'Imaging representative', 'client_app', 'Client Application', 'PatientAdmission','Patient Admission', 'write');
updateAcl($imaging_call_center_representative_write, 'Imaging representative', 'client_app', 'Client Application', 'AddPatient','Add Patient', 'write');
updateAcl($imaging_call_center_representative_view,  'Imaging representative', 'client_app', 'Client Application', 'AppointmentsAndEncounters','Appointments And Encounters', 'view');
//updateAcl($imaging_call_center_representative_write, 'Imaging representative', 'client_app', 'Client Application', 'EncounterSheet','Encounter Sheet', 'write');

//Insert the 'notes' object from the 'patients' section
//updateAcl($receptionist_write, 'Vaccine receptionists', 'patients', 'Patients', 'notes', 'Patient Notes (write,addonly optional)', 'write');


addObjectSectionAcl('fhir_api', 'FHIR API');
addObjectAcl('fhir_api', 'FHIR API', 'patient','Patient');
addObjectAcl('fhir_api', 'FHIR API', 'appointment','Appointment');
addObjectAcl('fhir_api', 'FHIR API', 'encounter','Encounter');
addObjectAcl('fhir_api', 'FHIR API', 'practitioner','Practitioner');
addObjectAcl('fhir_api', 'FHIR API', 'organization','Organization');
addObjectAcl('fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service');
addObjectAcl('fhir_api', 'FHIR API', 'valueset','Value Set');
addObjectAcl('fhir_api', 'FHIR API', 'relatedperson','Related Person');
addObjectAcl('fhir_api', 'FHIR API', 'documentreference','Document Reference');

addObjectAcl('fhir_api', 'FHIR API', 'questionnaire','Questionnaire');
addObjectAcl('fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response');



updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'patient','Patient', 'write');
updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'write');
updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'encounter','Encounter', 'write');
//updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'practitioner','Practitioner', 'write');
//updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'organization','Organization', 'write');
//updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service', 'write');
updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'valueset','Value Set', 'write');
updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'relatedperson','Related Person', 'write');
updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'documentreference','Document Reference', 'write');
updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'questionnaire','Questionnaire', 'write');
updateAcl($admin_write, 'Administrators', 'fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response', 'write');

updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'patient','Patient', 'view');
updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'view');
updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'encounter','Encounter', 'view');
updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'practitioner','Practitioner', 'view');
updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'organization','Organization', 'view');
updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service', 'view');
updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'valueset','Value Set', 'view');
updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'relatedperson','Related Person', 'view');
updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'documentreference','Document Reference', 'view');
updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'questionnaire','Questionnaire', 'view');
updateAcl($admin_view,  'Administrators', 'fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response', 'view');

/**********************************************************/

updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'patient','Patient', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'encounter','Encounter', 'write');
//updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'practitioner','Practitioner', 'write');
//updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'organization','Organization', 'write');
//updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'valueset','Value Set', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'relatedperson','Related Person', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'documentreference','Document Reference', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'questionnaire','Questionnaire', 'write');
updateAcl($imaging_receptionist_write, 'Imaging receptionist', 'fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response', 'write');

updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'patient','Patient', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'encounter','Encounter', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'practitioner','Practitioner', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'organization','Organization', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'valueset','Value Set', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'relatedperson','Related Person', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'documentreference','Document Reference', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'questionnaire','Questionnaire', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist', 'fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response', 'view');


/**********************************************************/

updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'patient','Patient', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'encounter','Encounter', 'write');
//updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'practitioner','Practitioner', 'write');
//updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'organization','Organization', 'write');
//updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'valueset','Value Set', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'relatedperson','Related Person', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'documentreference','Document Reference', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'questionnaire','Questionnaire', 'write');
updateAcl($imaging_clinic_manager_write, 'Imaging manager', 'fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response', 'write');

updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'patient','Patient', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'encounter','Encounter', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'practitioner','Practitioner', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'organization','Organization', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'valueset','Value Set', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'relatedperson','Related Person', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'documentreference','Document Reference', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'questionnaire','Questionnaire', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager', 'fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response', 'view');


/**********************************************************/

updateAcl($imaging_doctor_write, 'Imaging doctor', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'write');
updateAcl($imaging_doctor_write, 'Imaging doctor', 'fhir_api', 'FHIR API', 'encounter','Encounter', 'write');
//updateAcl($imaging_doctor_write, 'Imaging doctor', 'fhir_api', 'FHIR API', 'practitioner','Practitioner', 'write');
//updateAcl($imaging_doctor_write, 'Imaging doctor', 'fhir_api', 'FHIR API', 'organization','Organization', 'write');
//updateAcl($imaging_doctor_write, 'Imaging doctor', 'fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service', 'write');
updateAcl($imaging_doctor_write, 'Imaging doctor', 'fhir_api', 'FHIR API', 'valueset','Value Set', 'write');
updateAcl($imaging_doctor_write, 'Imaging doctor', 'fhir_api', 'FHIR API', 'relatedperson','Related Person', 'write');
updateAcl($imaging_doctor_write, 'Imaging doctor', 'fhir_api', 'FHIR API', 'documentreference','Document Reference', 'write');
updateAcl($imaging_doctor_write, 'Imaging doctor', 'fhir_api', 'FHIR API', 'questionnaire','Questionnaire', 'write');
updateAcl($imaging_doctor_write, 'Imaging doctor', 'fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response', 'write');

updateAcl($imaging_doctor_view,  'Imaging doctor', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor', 'fhir_api', 'FHIR API', 'encounter','Encounter', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor', 'fhir_api', 'FHIR API', 'practitioner','Practitioner', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor', 'fhir_api', 'FHIR API', 'organization','Organization', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor', 'fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor', 'fhir_api', 'FHIR API', 'valueset','Value Set', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor', 'fhir_api', 'FHIR API', 'relatedperson','Related Person', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor', 'fhir_api', 'FHIR API', 'documentreference','Document Reference', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor', 'fhir_api', 'FHIR API', 'questionnaire','Questionnaire', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor', 'fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response', 'view');

/**********************************************************/

updateAcl($imaging_technician_write, 'Imaging technician', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'write');
updateAcl($imaging_technician_write, 'Imaging technician', 'fhir_api', 'FHIR API', 'encounter','Encounter', 'write');
//updateAcl($imaging_technician_write, 'Imaging technician', 'fhir_api', 'FHIR API', 'practitioner','Practitioner', 'write');
//updateAcl($imaging_technician_write, 'Imaging technician', 'fhir_api', 'FHIR API', 'organization','Organization', 'write');
//updateAcl($imaging_technician_write, 'Imaging technician', 'fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service', 'write');
updateAcl($imaging_technician_write, 'Imaging technician', 'fhir_api', 'FHIR API', 'valueset','Value Set', 'write');
updateAcl($imaging_technician_write, 'Imaging technician', 'fhir_api', 'FHIR API', 'relatedperson','Related Person', 'write');
updateAcl($imaging_technician_write, 'Imaging technician', 'fhir_api', 'FHIR API', 'documentreference','Document Reference', 'write');
updateAcl($imaging_technician_write, 'Imaging technician', 'fhir_api', 'FHIR API', 'questionnaire','Questionnaire', 'write');
updateAcl($imaging_technician_write, 'Imaging technician', 'fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response', 'write');

updateAcl($imaging_technician_view,  'Imaging technician', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'view');
updateAcl($imaging_technician_view,  'Imaging technician', 'fhir_api', 'FHIR API', 'encounter','Encounter', 'view');
updateAcl($imaging_technician_view,  'Imaging technician', 'fhir_api', 'FHIR API', 'practitioner','Practitioner', 'view');
updateAcl($imaging_technician_view,  'Imaging technician', 'fhir_api', 'FHIR API', 'organization','Organization', 'view');
updateAcl($imaging_technician_view,  'Imaging technician', 'fhir_api', 'FHIR API', 'healthcareservice','Healthcareb Service', 'view');
updateAcl($imaging_technician_view,  'Imaging technician', 'fhir_api', 'FHIR API', 'valueset','Value Set', 'view');
updateAcl($imaging_technician_view,  'Imaging technician', 'fhir_api', 'FHIR API', 'relatedperson','Related Person', 'view');
updateAcl($imaging_technician_view,  'Imaging technician', 'fhir_api', 'FHIR API', 'documentreference','Document Reference', 'view');
updateAcl($imaging_technician_view,  'Imaging technician', 'fhir_api', 'FHIR API', 'questionnaire','Questionnaire', 'view');
updateAcl($imaging_technician_view,  'Imaging technician', 'fhir_api', 'FHIR API', 'questionnaireresponse','Questionnaire Response', 'view');

/**********************************************************/

updateAcl($imaging_call_center_representative_write, 'Imaging representative', 'fhir_api', 'FHIR API', 'patient','Patient', 'write');
updateAcl($imaging_call_center_representative_write, 'Imaging representative', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'write');

updateAcl($imaging_call_center_representative_view,  'Imaging representative', 'fhir_api', 'FHIR API', 'patient','Patient', 'view');
updateAcl($imaging_call_center_representative_view,  'Imaging representative', 'fhir_api', 'FHIR API', 'appointment','Appointment', 'view');

/**********************************************************/


addObjectSectionAcl('clinikal_api', 'Clinikal API');
addObjectAcl('clinikal_api', 'Clinikal API', 'general_settings','General settings');
addObjectAcl('clinikal_api', 'Clinikal API', 'lists','Lists');

updateAcl($admin_view,  'Administrators',                                    'clinikal_api', 'Clinikal API', 'general_settings','General settings', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist',               'clinikal_api', 'Clinikal API', 'general_settings','General settings', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager',                  'clinikal_api', 'Clinikal API', 'general_settings','General settings', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor',                           'clinikal_api', 'Clinikal API', 'general_settings','General settings', 'view');
updateAcl($imaging_technician_view,  'Imaging technician',                   'clinikal_api', 'Clinikal API', 'general_settings','General settings', 'view');
updateAcl($imaging_call_center_representative_view,'Imaging representative', 'clinikal_api', 'Clinikal API', 'general_settings','General settings', 'view');

updateAcl($admin_view,  'Administrators',                                    'clinikal_api', 'Clinikal API', 'lists','lists', 'view');
updateAcl($imaging_receptionist_view,  'Imaging receptionist',               'clinikal_api', 'Clinikal API', 'lists','lists', 'view');
updateAcl($imaging_clinic_manager_view,  'Imaging manager',                  'clinikal_api', 'Clinikal API', 'lists','lists', 'view');
updateAcl($imaging_doctor_view,  'Imaging doctor',                           'clinikal_api', 'Clinikal API', 'lists','lists', 'view');
updateAcl($imaging_technician_view,  'Imaging technician',                   'clinikal_api', 'Clinikal API', 'lists','lists', 'view');
updateAcl($imaging_call_center_representative_view,'Imaging representative', 'clinikal_api', 'Clinikal API', 'lists','lists', 'view');


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