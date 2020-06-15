--
--  Comment Meta Language Constructs:
--
--  #IfNotTable
--    argument: table_name
--    behavior: if the table_name does not exist,  the block will be executed

--  #IfTable
--    argument: table_name
--    behavior: if the table_name does exist, the block will be executed

--  #IfColumn
--    arguments: table_name colname
--    behavior:  if the table and column exist,  the block will be executed

--  #IfMissingColumn
--    arguments: table_name colname
--    behavior:  if the table exists but the column does not,  the block will be executed

--  #IfNotColumnType
--    arguments: table_name colname value
--    behavior:  If the table table_name does not have a column colname with a data type equal to value, then the block will be executed

--  #IfNotRow
--    arguments: table_name colname value
--    behavior:  If the table table_name does not have a row where colname = value, the block will be executed.

--  #IfNotRow2D
--    arguments: table_name colname value colname2 value2
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2, the block will be executed.

--  #IfNotRow3D
--    arguments: table_name colname value colname2 value2 colname3 value3
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2 AND colname3 = value3, the block will be executed.

--  #IfNotRow4D
--    arguments: table_name colname value colname2 value2 colname3 value3 colname4 value4
--    behavior:  If the table table_name does not have a row where colname = value AND colname2 = value2 AND colname3 = value3 AND colname4 = value4, the block will be executed.

--  #IfNotRow2Dx2
--    desc:      This is a very specialized function to allow adding items to the list_options table to avoid both redundant option_id and title in each element.
--    arguments: table_name colname value colname2 value2 colname3 value3
--    behavior:  The block will be executed if both statements below are true:
--               1) The table table_name does not have a row where colname = value AND colname2 = value2.
--               2) The table table_name does not have a row where colname = value AND colname3 = value3.

--  #IfRow2D
--    arguments: table_name colname value colname2 value2
--    behavior:  If the table table_name does have a row where colname = value AND colname2 = value2, the block will be executed.

--  #IfRow3D
--        arguments: table_name colname value colname2 value2 colname3 value3
--        behavior:  If the table table_name does have a row where colname = value AND colname2 = value2 AND colname3 = value3, the block will be executed.

--  #IfIndex
--    desc:      This function is most often used for dropping of indexes/keys.
--    arguments: table_name colname
--    behavior:  If the table and index exist the relevant statements are executed, otherwise not.

--  #IfNotIndex
--    desc:      This function will allow adding of indexes/keys.
--    arguments: table_name colname
--    behavior:  If the index does not exist, it will be created

--  #EndIf
--    all blocks are terminated with a #EndIf statement.

--  #IfNotListReaction
--    Custom function for creating Reaction List

--  #IfNotListOccupation
--    Custom function for creating Occupation List

--  #IfTextNullFixNeeded
--    desc: convert all text fields without default null to have default null.
--    arguments: none

--  #IfTableEngine
--    desc:      Execute SQL if the table has been created with given engine specified.
--    arguments: table_name engine
--    behavior:  Use when engine conversion requires more than one ALTER TABLE

--  #IfInnoDBMigrationNeeded
--    desc: find all MyISAM tables and convert them to InnoDB.
--    arguments: none
--    behavior: can take a long time.

--  #IfTranslationNeeded
--    desc: find all MyISAM tables and convert them to InnoDB.
--    arguments: constant_name english hebrew
--    behavior: can take a long time.



#IfNotRow2D list_options list_id userlist3 option_id passport
DELETE FROM `list_options` WHERE `list_id` like "userlist3";
INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `notes`,`activity`)
VALUES
('userlist3', 'teudat_zehut', 'Teudat zehut', '10', '1', '0','','1'),
('userlist3', 'passport', 'Passport', '20', '0', '0','', '1'),
('userlist3', 'temporary', 'Temporary', '30', '0', '0','' ,'1');

UPDATE `patient_data` SET `mh_type_id` = 'temporary' WHERE `patient_data`.`mh_type_id` = "idtype_3";
UPDATE `patient_data` SET `mh_type_id` = 'passport' WHERE `patient_data`.`mh_type_id` = "idtype_2";
UPDATE `patient_data` SET `mh_type_id` = 'id'       WHERE `patient_data`.`mh_type_id` = "idtype_1";
#EndIf


#IfNotTable clinikal_patient_tracking_changes
CREATE TABLE `clinikal_patient_tracking_changes` (
  `facility_id` int(11) NOT NULL,
  `update_date` datetime NOT NULL DEFAULT current_timestamp,
   PRIMARY KEY (`facility_id`)
) ;
#EndIf


-- always restore to default in upgrade

REPLACE INTO `globals` (`gl_name`, `gl_index`, `gl_value`) VALUES
('clinikal_hide_appoitments', 0, '0');


REPLACE INTO `globals` (`gl_name`, `gl_index`, `gl_value`) VALUES
('clinikal_pa_commitment_form', 0, '1');


REPLACE INTO `globals` (`gl_name`, `gl_index`, `gl_value`) VALUES
('clinikal_pa_arrival_way', 0, '0');

REPLACE INTO `globals` (`gl_name`, `gl_index`, `gl_value`) VALUES
('clinikal_pa_next_enc_status', 0, 'arrived');

#IfNotTable clinikal_templates_map
CREATE TABLE `clinikal_templates_map` (
  `form_id` int(11) NOT NULL,
  `form_field` varchar(50) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `reason_code` varchar(50) NOT NULL,
  `message_id` varchar(50) NOT NULL,
  `seq` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `clinikal_templates_map`
  ADD PRIMARY KEY (`form_id`,`form_field`,`service_type`,`reason_code`);
#EndIf

#IfNotRow2D list_options list_id lists option_id clinikal_templates
INSERT INTO `list_options` (`list_id`, `option_id`, `title`, `seq`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`) VALUES
('lists', 'clinikal_templates', 'Clinikal templates', 0, 0, 0, '', '', '', 0, 0, 1, '', 1);
#EndIf


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
