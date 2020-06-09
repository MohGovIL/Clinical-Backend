
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


INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`, `timestamp`) VALUES ('apps', 'react-dev', '../client-app/dev-mode/build', '100', '1', '0', '', NULL, '', '0', '0', '1', '', '1', '2017-07-23 09:33:02');
-- UPDATE globals SET gl_value = 'style_clinikal_generic.css' WHERE gl_name = 'css_header';
UPDATE globals SET gl_value = '1' WHERE gl_name = 'rest_api';


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


INSERT INTO `globals` (`gl_name`, `gl_index`, `gl_value`) VALUES
('clinikal_hide_appoitments', 0, '0');


INSERT INTO `globals` (`gl_name`, `gl_index`, `gl_value`) VALUES
('clinikal_pa_commitment_form', 0, '1');


INSERT INTO `globals` (`gl_name`, `gl_index`, `gl_value`) VALUES
('clinikal_pa_arrival_way', 0, '0');

INSERT INTO `globals` (`gl_name`, `gl_index`, `gl_value`) VALUES
('clinikal_pa_next_enc_status', 0, 'arrived');

CREATE TABLE `clinikal_templates_map` (
  `form_id` int(11) NOT NULL,
  `form_field` varchar(50) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `reason_code` varchar(50) NOT NULL,
  `message_id` varchar(50) NOT NULL,
  `seq` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `clinikal_templates_map`
  ADD PRIMARY KEY (`form_id`,`form_field`,`service_type`,`reason_code`,`message_id`);

INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`) VALUES
('lists', 'clinikal_templates', 'Clinikal templates', 0, 0, 0, '', '', '', 0, 0, 1, '', 1);


DELETE FROM list_options where list_id="drug_route" OR option_id="drug_route";
INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `activity`) VALUES
('lists', 'drug_route', 'Drug Route', 0, 1),
('drug_route', 'per oris', 'Per oris', 10, 1),
('drug_route', 'to skin', 'To skin', 20, 1),
('drug_route', 'per nostril', 'Per nostril', 30, 1),
('drug_route', 'both ears', 'Both ears', 40, 1),
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
