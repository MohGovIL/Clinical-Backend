REPLACE INTO `moh_import_data` (`id`, `external_name`, `clinikal_name`, `static_name`, `source`, `update_at`) VALUES
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
(19, 'MOH_KUPAT_CHOLIM_BRANCHES', 'MOH_KUPAT_CHOLIM_BRANCHES', 'mkcb', 'CSV', '2017-03-29 00:00:00'),
(20, 'MOH_DRUGS', '	codes', 'moh_drugs', 'EDM', '2020-03-29 00:00:00');

#IfNotRow2D code_types ct_key MOH_DRUGS ct_id 9911
INSERT INTO `code_types` (`ct_key`, `ct_id`, `ct_seq`, `ct_mod`, `ct_just`, `ct_mask`, `ct_fee`, `ct_rel`, `ct_nofs`, `ct_diag`, `ct_active`, `ct_label`, `ct_external`, `ct_claim`, `ct_proc`, `ct_term`, `ct_problem`, `ct_drug`)
VALUES
('MOH_DRUGS', 9911, 9911, 12, 'MOH_DRUGS', '', 1, 0, 1, 0, 1, 'MOH DRUGS LIST', 0, 1, 0, 1, 1, 0);
#EndIf
