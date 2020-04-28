

REPLACE INTO `modules` (`mod_id`, `mod_name`, `mod_directory`, `mod_parent`, `mod_type`, `mod_active`, `mod_ui_name`, `mod_relative_link`, `mod_ui_order`, `mod_ui_active`, `mod_description`, `mod_nick_name`, `mod_enc_menu`, `permissions_item_table`, `directory`, `date`, `sql_run`, `type`, `sql_version`, `acl_version`)
VALUES
(null, 'ImportData', 'ImportData', '', '', 1, 'Importdata', 'public/importdata/', 0, 0, '', '', '', NULL, '', '2020-04-28 10:14:25', 1, 1, '0.1.0', '');

--
-- Table structure for table `moh_import_data_log`
--

DROP TABLE IF EXISTS `moh_import_data_log`;
CREATE TABLE `moh_import_data_log` (
  `id` int(11) NOT NULL,
  `table` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `affected_records` int(11) DEFAULT NULL,
  `info` text DEFAULT NULL,
  `update_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `moh_import_data`
--

DROP TABLE IF EXISTS `moh_import_data`;
CREATE TABLE `moh_import_data` (
  `id` int(11) NOT NULL,
  `external_name` varchar(100) NOT NULL,
  `clinikal_name` varchar(100) NOT NULL COMMENT 'table / list',
  `static_name` varchar(100) NOT NULL,
  `source` enum('EDM','CSV') NOT NULL,
  `update_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `moh_import_data`
--

INSERT INTO `moh_import_data` (`id`, `external_name`, `clinikal_name`, `static_name`, `source`, `update_at`) VALUES
(1, 'country', 'moh_country', 'countries', 'EDM', '2017-03-02 09:09:32'),
(2, 'gender', 'sex', 'gender', 'EDM', '2017-03-29 00:00:00'),
(3, 'language', 'language', 'language', 'EDM', '2017-03-29 00:00:00'),
(4, 'city', 'mh_cities', 'city', 'EDM', '2017-03-29 00:00:00'),
(5, 'street', 'mh_streets', 'street', 'EDM', '2017-03-02 09:10:01'),
(6, 'death_situation', 'mh_reason_of_death', 'death', 'EDM', '2017-03-29 00:00:00'),
(7, 'ExtendedLogicalStatus', 'mh_medic_approval', 'elsList', 'EDM', '2017-03-29 00:00:00'),
(8, 'SocialSecurityOffices', 'mh_ss_branches', 'sso', 'EDM', '2017-04-20 00:00:00'),
(9, 'HMO', 'mh_ins_organizations', 'hmo', 'EDM', '2017-03-29 00:00:00'),
(10, 'title', 'titles', 'title', 'EDM', '2017-03-29 00:00:00'),
(11, 'icd_type', 'occurrence', 'frequency', 'EDM', '2017-03-29 00:00:00'),
(12, 'institute_type', 'moh_institute_type', 'institutetype', 'EDM', '2017-03-29 00:00:00'),
(13, 'institute', 'moh_institutes', 'institute', 'EDM', '2017-03-29 00:00:00'),
(14, 'id_type', 'userlist3', 'idtype', 'EDM', '2000-03-29 00:00:00'),
(15, 'expertise', 'physician_type', 'expertise', 'EDM', '2017-03-29 00:00:00'),
(16, 'family_status', 'marital', 'famillystatus', 'EDM', '2017-03-29 00:00:00'),
(17, 'ICD10', 'codes', 'icd10', 'EDM', '2017-03-29 00:00:00'),
(18, 'ICD9', 'codes', 'icd9', 'EDM', '2017-03-02 09:11:01'),
(19, 'MOH_KUPAT_CHOLIM_BRANCHES', 'MOH_KUPAT_CHOLIM_BRANCHES', 'mkcb', 'CSV', '2017-03-29 00:00:00');

