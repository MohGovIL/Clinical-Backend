UPDATE globals SET gl_value = '1' WHERE gl_name = 'rest_fhir_api';

CREATE TABLE `fhir_healthcare_services` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `active` BOOLEAN NOT NULL DEFAULT 1,
    `providedBy` INT NOT NULL,
    `category` INT NOT NULL,
    `type` INT NOT NULL,
    `name` VARCHAR(125) NOT NULL,
    `comment` TEXT,
    `extraDetails` TEXT,
    `availableTime` JSON,
    `notAvailable` JSON,
    `availabilityExceptions` TEXT,
    CONSTRAINT time_json CHECK (Json_valid(`availableTime`)),
    CONSTRAINT tn_avlbl_json CHECK (Json_valid(`notAvailable`)),
    PRIMARY KEY (`id`)
) ENGINE = innodb;

-- fhir routing rest api builders table

 CREATE TABLE `fhir_rest_elements` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `fhir_rest_elements`
ADD PRIMARY KEY (`id`);

ALTER TABLE `fhir_rest_elements`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE encounter_reasoncode_map (
    eid INT(6) UNSIGNED,
    reason_code  INT(6) UNSIGNED
);

CREATE TABLE questionnaires_schemas(
    qid int(11) NOT NULL AUTO_INCREMENT,
    form_name varchar(255) NOT NULL,
    form_table varchar(255) NOT NULL,
    column_name varchar(255) DEFAULT NULL,
    column_type varchar(255) NOT NULL,
    question varchar(255) DEFAULT NULL,
    PRIMARY KEY (`qid`,`form_name`)
);


CREATE TABLE `questionnaire_response`(
    id bigint(20) NOT NULL AUTO_INCREMENT,
    form_name varchar(255) NOT NULL,
    encounter bigint(20) NOT NULL,
    subject bigint(20) NOT NULL,
    subject_type VARCHAR(255) NOT NULL DEFAULT 'Patient',
    create_date datetime DEFAULT current_timestamp,
    update_date datetime DEFAULT current_timestamp,
    create_by bigint(20) NOT NULL,
    update_by bigint(20) NOT NULL,
    source  bigint(20) NOT NULL,
    source_type VARCHAR(255) NOT NULL DEFAULT 'Patient',
    status  varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
);


CREATE TABLE `fhir_value_sets` (
    `id` VARCHAR(125) NOT NULL,
    `title` VARCHAR(125) NOT NULL,
    `status` ENUM('active', 'retired') NOT NULL DEFAULT 'active',
    PRIMARY KEY(`id`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

CREATE TABLE `fhir_value_set_systems` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `vs_id` VARCHAR(125) NOT NULL,
    `system` VARCHAR(125) NOT NULL,
    `type` ENUM('All', 'Partial', 'Exclude', 'Filter') NOT NULL,
    `filter` VARCHAR(125) DEFAULT NULL,
    PRIMARY KEY(`id`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

CREATE TABLE `fhir_value_set_codes` (
    `vss_id` INT NOT NULL,
    `code` VARCHAR(125) NOT NULL,
    PRIMARY KEY(`vss_id`, `code`)
) ENGINE=INNODB DEFAULT CHARSET=UTF8;

ALTER TABLE `openemr_postcalendar_events`
            ADD `pc_priority` INT NOT NULL DEFAULT '1' AFTER `pc_gid`,
            ADD `pc_service_type` INT NULL DEFAULT NULL AFTER `pc_priority`,
            ADD `pc_healthcare_service_id` INT NULL DEFAULT NULL AFTER `pc_service_type`;


CREATE TABLE `event_codeReason_map` (
  `event_id` int(11) NOT NULL,
  `option_id` varchar(100) NOT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE event_codeReason_map ADD PRIMARY KEY (event_id, option_id);

ALTER TABLE `openemr_postcalendar_events` CHANGE
`pc_healthcare_service_id` `pc_healthcare_service_id` INT NULL DEFAULT NULL COMMENT 'fhir_healthcare_services.id';


ALTER TABLE `fhir_healthcare_services` CHANGE
`providedBy` `providedBy` INT NULL DEFAULT NULL COMMENT 'facility.id';

CREATE TABLE `related_person` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) DEFAULT NULL,
  `identifier_type` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `pid` bigint(20) NOT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `phone_home` varchar(255) DEFAULT NULL,
  `phone_cell` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `form_encounter`
             ADD `status` VARCHAR(100) NULL AFTER `parent_encounter_id`,
             ADD `eid` INT NULL AFTER `status`,
             ADD `priority` INT DEFAULT 1 AFTER `status`,
             ADD `service_type` INT DEFAULT NULL AFTER `priority`,
             ADD `arrival_way` VARCHAR(255) NULL DEFAULT NULL AFTER `eid`,
             ADD `reason_codes_details` TEXT NULL DEFAULT NULL AFTER `arrival_way`;


ALTER TABLE facility
             ADD active int DEFAULT 1;

ALTER TABLE `form_encounter`
             ADD `escort_id` BIGINT(20) NULL DEFAULT NULL COMMENT 'related_person.id' AFTER `service_type`;

CREATE TABLE `fhir_questionnaire` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name`      varchar(255) NOT NULL,
    `directory` varchar(255) NOT NULL,
    `state`     tinyint(4)   DEFAULT NULL,
    `aco_spec`  varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ;



INSERT INTO `fhir_rest_elements` (`name`, `active`)
VALUES
('Organization', 1),
('Patient', 1),
('Appointment', 1),
('HealthcareService', 1),
('Encounter', 1),
('ValueSet', 1),
('DocumentReference', 1),
( 'Questionnaire', 1),
('QuestionnaireResponse', 1),
('Practitioner', 1),
('RelatedPerson', 1),
('MedicationStatement', 1),
('Condition', 1);

INSERT INTO `globals` (`gl_name`, `gl_index`, `gl_value`) VALUES
('fhir_type_validation', 0, '0');


DELETE FROM `list_options` WHERE `list_id` like "sex";
INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `notes`,`activity`)
VALUES
('sex', 'male', 'Male', '10', '1', '0','','1'),
('sex', 'female', 'Female', '20', '0', '0','', '1'),
('sex', 'other', 'Other', '30', '0', '0','', '1'),
('sex', 'unknown', 'Unknown', '40', '0', '0','' ,'0');


INSERT INTO `fhir_value_set_systems` (`vs_id`, `system`, `type`, `filter`)
VALUES
('gender', 'sex', 'Partial', NULL);

INSERT INTO `fhir_value_set_codes` (`vss_id`, `code`) VALUES
((SELECT id FROM fhir_value_set_systems WHERE vs_id = 'gender'), 'female'),
((SELECT id FROM fhir_value_set_systems WHERE vs_id = 'gender'), 'male'),
((SELECT id FROM fhir_value_set_systems WHERE vs_id = 'gender'), 'other');

INSERT INTO fhir_value_sets (id, title, status) VALUES('gender', 'Gender', 'active');


INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`)
VALUES
('lists', 'clinikal_app_statuses', 'Clinikal Appointment Statuses', 0, 0, 0, '', '', '', 0, 0, 1, '', 1),
('clinikal_app_statuses', 'pending', 'Pending Approval', 10, 0, 0, '', '', '', 0, 0, 1, '', 1),
('clinikal_app_statuses', 'booked', 'Booked', 20, 0, 0, '', '', '', 0, 0, 1, '', 1),
('clinikal_app_statuses', 'arrived', 'Arrived', 30, 0, 0, '', '', '', 0, 0, 1, '', 1),
('clinikal_app_statuses', 'cancelled', 'Cancelled', 40, 0, 0, '', '', '', 0, 0, 1, '', 1),
('clinikal_app_statuses', 'noshow', 'No Show', 50, 0, 0, '', '', '', 0, 0, 1, '', 1),
('clinikal_app_statuses', 'waitlist', 'Waitlisted', 60, 0, 0, '', '', '', 0, 0, 1, '', 1);


INSERT INTO `fhir_value_sets` (`id`, `title`) VALUES
('appointment_statuses', 'Appointment Statuses');

INSERT INTO `fhir_value_set_systems` (`vs_id`, `system`, `type`) VALUES
('appointment_statuses', 'clinikal_app_statuses', 'All');



ALTER TABLE `related_person` ADD `full_name` VARCHAR(255) NULL DEFAULT NULL AFTER `gender`;

DELETE  FROM `list_options` WHERE `list_id` LIKE 'outcome';

INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`, `timestamp`) VALUES
('outcome', '1', 'active', 5, 0, 0, 'all', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '2', 'recurrence', 10, 0, 0, 'Condition', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '3', 'relapse', 15, 0, 0, 'Condition', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '4', 'inactive', 20, 0, 0, 'Condition', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '5', 'resolved', 25, 0, 0, 'Condition', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '6', 'remission', 25, 0, 0, 'Condition', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),

('outcome', '7', 'completed', 25, 0, 0, 'MedicationStatement', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '8', 'entered-in-error', 25, 0, 0, 'MedicationStatement', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '9', 'intended', 25, 0, 0, 'MedicationStatement', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '10', 'stopped', 25, 0, 0, 'MedicationStatement', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '11', 'on-hold', 25, 0, 0, 'MedicationStatement', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '12', 'unknown', 25, 0, 0, 'MedicationStatement', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44'),
('outcome', '13', 'not-taken', 25, 0, 0, 'MedicationStatement', NULL, '', 0, 0, 1, '', 1, '2021-05-26 07:07:44');

INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`)
VALUES
('lists', 'clinikal_app_secondary_statuses', 'Clinikal Appointment Secondary Statuses', 0, 0, 0, '', 'In Progress ', '', 0, 0, 1, '', 1);
