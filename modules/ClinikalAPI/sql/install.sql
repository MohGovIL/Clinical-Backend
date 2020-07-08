
-- INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`, `timestamp`) VALUES ('apps', 'react-dev', '../client-app/dev-mode/build', '100', '1', '0', '', NULL, '', '0', '0', '1', '', '1', '2017-07-23 09:33:02');
-- UPDATE globals SET gl_value = 'style_clinikal_generic.css' WHERE gl_name = 'css_header';
UPDATE globals SET gl_value = '1' WHERE gl_name = 'rest_api';


ALTER TABLE `patient_data`
ADD `mh_house_no` VARCHAR(255) NOT NULL AFTER `guardianemail`,
ADD `mh_pobox` VARCHAR(255) NOT NULL AFTER `mh_house_no`,
ADD `mh_type_id` VARCHAR(255) NOT NULL AFTER `mh_pobox`,
ADD `mh_english_name` VARCHAR(255) NOT NULL AFTER `mh_type_id`,
ADD `mh_insurance_organiz` VARCHAR(255) NOT NULL AFTER `mh_english_name`;


REPLACE INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`, `timestamp`) VALUES
('lists', 'mh_cities', 'moh cities', 311, 1, 0, '', '', '', 0, 0, 1, '', 1, '2017-03-02 07:07:44');


REPLACE INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`, `timestamp`) VALUES
('lists', 'mh_streets', 'moh streets', 312, 1, 0, '', '', '', 0, 0, 1, '', 1, '2017-03-02 07:07:44');

-- ALTER TABLE patient_data ADD COLUMN IF NOT EXISTS column_a VARCHAR(255);



DELETE FROM `list_options` WHERE `list_id` like "userlist3";
INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `notes`,`activity`)
VALUES
('userlist3', 'teudat_zehut', 'Teudat zehut', '10', '1', '0','','1'),
('userlist3', 'passport', 'Passport', '20', '0', '0','', '1'),
('userlist3', 'temporary', 'Temporary', '30', '0', '0','' ,'1');


CREATE TABLE `clinikal_patient_tracking_changes` (
  `facility_id` int(11) NOT NULL,
  `update_date` datetime NOT NULL DEFAULT current_timestamp,
   PRIMARY KEY (`facility_id`)
) ;

CREATE TABLE `clinikal_templates_map` (
  `form_id` varchar(50) NOT NULL COMMENT 'FK registry -> directory',
  `form_field` varchar(50) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `reason_code` varchar(50) NOT NULL,
  `message_id` varchar(50) NOT NULL,
  `seq` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `clinikal_templates_map`
  ADD PRIMARY KEY (`form_id`,`form_field`,`service_type`,`reason_code`,`message_id`);


CREATE TABLE `form_context_map` (
    `form_id`           INT NOT NULL,
    `context_type`      varchar(255) NOT NULL COMMENT 'reason_code / service_type',
    `context_id`        INT NOT NULL,
    PRIMARY KEY (`form_id`,`context_type`,`context_id`)
);



INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`) VALUES
('lists', 'clinikal_templates', 'Clinikal templates', 0, 0, 0, '', '', '', 0, 0, 1, '', 1);


ALTER TABLE `registry` ADD `component_name` VARCHAR(255) NULL AFTER `aco_spec`;


DELETE FROM list_options where list_id="drug_route" OR option_id="drug_route";
INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `activity`) VALUES
('lists', 'drug_route', 'Drug Route', 0, 1),
('drug_route', 'per_oris', 'Per oris', 10, 1),
('drug_route', 'to_skin', 'To skin', 20, 1),
('drug_route', 'per_nostril', 'Per nostril', 30, 1),
('drug_route', 'both_ears', 'Both ears', 40, 1),
('drug_route', 'other', 'Other', 50, 1);



DELETE FROM list_options where list_id="drug_interval" OR option_id="drug_interval";
INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `activity`) VALUES
('lists', 'drug_interval', 'Drug interval', 0, 1),
('drug_interval', 'once_a_day', 'Once a day', 10, 1),
('drug_interval', 'twice_a_day', 'Twice a day', 20, 1),
('drug_interval', '3_times_a_day', '3 times a day', 30, 1),
('drug_interval', '4_times_a_day', '4 times a day', 40, 1),
('drug_interval', 'every_hour', 'Every hour', 50, 1),
('drug_interval', 'every_3_hours', 'Every 3 hours', 60, 1),
('drug_interval', 'every_4_hours', 'Every 4 hours', 70, 1),
('drug_interval', 'every_5_hours', 'Every 5 hours', 80, 1),
('drug_interval', 'every_6_hours', 'Every 6 hours', 90, 1),
('drug_interval', 'every_8_hours', 'Every 8 hours', 100, 1),
('drug_interval', 'before_eating', 'Before eating', 110, 1),
('drug_interval', 'after_eating', 'After eating', 120, 1),
('drug_interval', 'before_noon', 'Before noon', 130, 1),
('drug_interval', 'afternoon', 'Afternoon', 140, 1),
('drug_interval', 'before_bedtime', 'Before bedtime', 150, 1),
('drug_interval', 'as_needed', 'As needed', 160, 1);


DELETE FROM list_options where list_id="drug_form" OR option_id="drug_form";
INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `activity`) VALUES
('lists', 'drug_form', 'Drug Form', 0, 1),
('drug_form', 'tablet', 'Tablet', 10, 1),
('drug_form', 'drops', 'Drops', 20, 1),
('drug_form', 'tsp', 'Tsp', 30, 1),
('drug_form', 'ml', 'ml', 40, 1),
('drug_form', 'ointment', 'Ointment', 50, 1),
('drug_form', 'cream', 'Cream', 60, 1),
('drug_form', 'solution', 'Solution', 70, 1),
('drug_form', 'suspension', 'Suspension', 80, 1);


CREATE TABLE form_commitment_questionnaire(
    id bigint(20) NOT NULL AUTO_INCREMENT,
    encounter varchar(255) DEFAULT NULL,
    form_id bigint(20) NOT NULL,
    question_id int(11) NOT NULL,
    answer text,
    PRIMARY KEY (`id`)
);

ALTER TABLE `form_commitment_questionnaire` ADD UNIQUE `unique_index`( `form_id`, `question_id`);

INSERT INTO `fhir_questionnaire` (`name`, `directory`, `state`, `aco_spec`) VALUES
('Commitment questionnaire', 'commitment_questionnaire', '1', 'encounters|notes');

INSERT INTO `questionnaires_schemas` (`qid`, `form_name`,`form_table`, `column_type`, `question`)
VALUES
('1', 'commitment_questionnaire','form_commitment_questionnaire', 'integer', 'Commitment number'),
('2', 'commitment_questionnaire','form_commitment_questionnaire', 'date', 'Commitment date'),
('3', 'commitment_questionnaire','form_commitment_questionnaire', 'date', 'Commitment expiration date'),
('4', 'commitment_questionnaire','form_commitment_questionnaire', 'string', 'Signing doctor'),
('5', 'commitment_questionnaire','form_commitment_questionnaire', 'integer', 'doctor license number'),
('6', 'commitment_questionnaire','form_commitment_questionnaire', 'string', 'Payment amount'),
('7', 'commitment_questionnaire','form_commitment_questionnaire', 'string', 'Payment method'),
('8', 'commitment_questionnaire','form_commitment_questionnaire', 'string', 'Receipt number');

INSERT INTO `globals` (`gl_name`, `gl_value`) VALUES ('s3_version', '2006-03-01');
