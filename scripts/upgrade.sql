/*!40101 SET NAMES utf8 */;


CREATE TABLE `countries` (
  `id` int(5) unsigned NOT NULL DEFAULT '0',
  `name` char(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

insert  into `countries`(`id`,`name`) values 
(0,'SIN DEFINIR'),
(13,'AFGANISTAN'),
(17,'ALBANIA'),
(23,'ALEMANIA'),
(26,'ARMENIA'),
(27,'ARUBA'),
(29,'BOSNIA Y HERZEGOVINA'),
(31,'BURKINA FASO'),
(37,'ANDORRA'),
(40,'ANGOLA'),
(41,'ANGUILA'),
(43,'ANTIGUA Y BARBUDA'),
(47,'ANTILLAS HOLANDESAS'),
(53,'ARABIA SAUDITA'),
(59,'ARGELIA'),
(63,'ARGENTINA'),
(69,'AUSTRALIA'),
(72,'AUSTRIA'),
(74,'AZERBAIYAN'),
(77,'BAHAMAS'),
(80,'BAHREIN'),
(81,'BANGLADESH'),
(83,'BARBADOS'),
(87,'BELGICA'),
(88,'BELICE'),
(90,'BERMUDA'),
(91,'BELARUSIA'),
(93,'MYANMAR'),
(97,'BOLIVIA'),
(100,'BONAIRE ISLA'),
(101,'BOTSWANA'),
(105,'BRASIL'),
(108,'BURNEI DARASSALAM'),
(111,'BULGARIA'),
(115,'BURUNDI'),
(119,'BULAN'),
(127,'CABO VERDE'),
(137,'ISLAS CAIMAN'),
(141,'CAMBOYA'),
(145,'CAMERUN'),
(149,'CANADA'),
(155,'ISLAS ANGLONORMANDAS'),
(156,'CEILAN'),
(157,'ISLAS CANTON Y ENDERBURRY'),
(159,'SANTA SEDE'),
(165,'ISLAS COCOS'),
(169,'COLOMBIA'),
(173,'COMORAS'),
(177,'CONGO'),
(183,'ISLAS COOK'),
(187,'COREA DEL SUR'),
(190,'COREA DEL NORTE'),
(193,'COSTA DE MARFIL'),
(196,'COSTA RICA'),
(198,'CROACIA'),
(199,'CUBA'),
(201,'CURAZAO'),
(203,'CHAD'),
(207,'CHECOSLOVAQUIA'),
(211,'CHILE'),
(215,'CHINA'),
(218,'TAIWAN'),
(221,'CHIPRE'),
(229,'BENIN'),
(232,'DINAMARCA'),
(235,'DOMINICA'),
(239,'ECUADOR'),
(240,'EGIPTO'),
(242,'EL SALVADOR'),
(243,'ERITRENEA'),
(244,'EMIRATOS ARABES UNIDOS'),
(245,'ESPAÑA'),
(246,'ESLOVAQUIA'),
(247,'ESLOVENIA'),
(249,'ESTADOS UNIDOS'),
(251,'ESTONIA'),
(253,'ETIOPIA'),
(259,'ISLAS FEROE'),
(267,'FILIPINAS'),
(271,'FINLANDIA'),
(275,'FRANCIA'),
(281,'GABON'),
(285,'GAMBIA'),
(287,'GEORGIA'),
(289,'CHANA'),
(293,'GIBRALTAR'),
(297,'GRANADA'),
(301,'GRECIA'),
(305,'GROENLANDIA'),
(309,'GUADALUPE'),
(313,'GUAM'),
(317,'GUATEMALA'),
(325,'GUYANA'),
(329,'GUINEA'),
(331,'GUINEA ECUATORIAL'),
(334,'GUINEAS BISSAU'),
(337,'GUYANA FRANCESA'),
(341,'HAITI'),
(345,'HONDURAS'),
(351,'HONG KONG'),
(355,'HUNGRIA'),
(361,'INDIA'),
(365,'INDONESIA'),
(369,'IRAK'),
(372,'IRAN'),
(375,'IRLANDA'),
(379,'ISLANDIA'),
(383,'ISRAEL'),
(386,'ITALIA'),
(391,'JAMAICA'),
(395,'ISLAS JOHNSTON'),
(399,'JAPON'),
(403,'JORDANIA'),
(406,'KAZAJSTAN'),
(410,'KENIA'),
(411,'KIRIBATI'),
(412,'KIRGUISTAN'),
(413,'KUWAIT'),
(420,'LAOS'),
(426,'LESOTHO'),
(429,'LETONIA'),
(431,'LIBANO'),
(434,'LIBERIA'),
(438,'LIBIA'),
(440,'LIECHTENSTEIN'),
(443,'LITUANIA'),
(445,'LUXEMBURGO'),
(447,'MACAO'),
(448,'MACEDONIA'),
(450,'MADAGASCAR'),
(455,'MALASIA'),
(461,'MALDIVAS'),
(464,'MALI'),
(467,'MALTA'),
(469,'ISLAS MARIANAS DEL NORTE'),
(472,'ISLAS MARSHALL'),
(474,'MARRUECOS'),
(477,'MARTINICA'),
(485,'MAURICIO'),
(488,'MAURITANIA'),
(493,'MEXICO'),
(494,'MICRONESIA'),
(495,'ISLAS MIDWAY'),
(496,'MOLDAVIA'),
(497,'MONGOLIA'),
(498,'MONACO'),
(501,'MONTSERRAT'),
(505,'MOZAMBIQUE'),
(507,'NAMBIA'),
(508,'NAURU'),
(511,'ISLAS CHRISTMAS'),
(517,'NEPAL'),
(521,'NICARAGUA'),
(525,'NIGER'),
(528,'NIGERIA'),
(531,'NIUE'),
(535,'ISLAS NORFOLK'),
(538,'NORUEGA'),
(542,'NUEVA CELEDONIA'),
(545,'PAPUA NUEVA GUINEA'),
(548,'NUEVA ZELANDIA'),
(551,'VANUATU'),
(556,'OMAN'),
(563,'PACIFICO ISLAS ADMINISTRADAS P'),
(573,'PAISES BAJOS'),
(576,'PAKISTAN'),
(578,'PALAU'),
(580,'PANAMA'),
(586,'PARAGUAY'),
(587,'MALASIA'),
(589,'PERU'),
(593,'PITCAIRN'),
(599,'POLONESIA FRANCESA'),
(603,'POLONIA'),
(607,'PORTUGAL'),
(611,'PUERTO RICO'),
(618,'QATAR'),
(628,'INGLATERRA'),
(629,'ESCOCIA'),
(640,'REPUBLICA CENTROAFRICANA'),
(644,'REPUBLICA CHECA'),
(647,'REPUBLICA DOMINICANA'),
(660,'REUNION'),
(665,'ZUMBABWE'),
(670,'RUMANIA'),
(675,'RUANDA'),
(676,'RUSIA'),
(677,'ISLAS SALOMON'),
(685,'SAHARA OCCIDENTAL'),
(687,'SAMOA'),
(690,'SAMOA AMERICANA'),
(695,'SAN CRISTOVAL Y NIEVES'),
(697,'SAN MARINO'),
(700,'SAN PEDRO Y MIQUELON'),
(705,'SAN VICENTE Y LAS GRANADINAS'),
(710,'SANTA ELENA'),
(715,'SANTA LUCIA'),
(720,'SANTO TOME Y PRINCIPE'),
(728,'SENEGAL'),
(731,'SEYCHELLES'),
(735,'SIERRA LEONA'),
(741,'SINGAPUR'),
(744,'SIRIA'),
(748,'SOMALIA'),
(750,'SRI LANKA'),
(756,'SUDAFRICA'),
(759,'SUDAN'),
(764,'SUECIA'),
(767,'SUIZA'),
(770,'SURINAM'),
(773,'SWAZILANDIA'),
(774,'TAYIKISTAN'),
(776,'TAILANDIA'),
(780,'TANZANIA'),
(783,'DJIBOUTI'),
(785,'PALESTINA'),
(786,'ANTARTIDA'),
(787,'TERRITORIO BRITANICO'),
(788,'TIMOR DEL ESTE'),
(800,'TOGO'),
(805,'TOKELAU'),
(810,'TONGA'),
(815,'TRINIDAD Y TOBAGO'),
(820,'TUNEZ'),
(823,'ISLAS TURCAS Y CAICOS'),
(825,'TURKMENISTAN'),
(827,'TURQUIA'),
(828,'TUVALU'),
(830,'UCRANIA'),
(833,'UGANDA'),
(840,'RUSIA'),
(845,'URUGUAY'),
(847,'UZBEKISTAN'),
(850,'VENEZUELA'),
(855,'VIETNAM'),
(858,'VIETNAM DEL SUR'),
(863,'ISLAS BRITANICAS'),
(866,'ISLAS VIRGENES USA'),
(870,'FIJI'),
(873,'ISLAS WAKE'),
(875,'ISLAS WALLIS Y FORTUNA'),
(880,'YEMEN'),
(881,'YEMEN DEMOCRATICO'),
(885,'YUGUSLAVIA'),
(888,'CONGO'),
(890,'ZAMBIA'),
(999,'PAISES NO PRECISADOS');



DELIMITER $$

USE `huhmp`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `viem_form_dynamic`(survey_form_id INT, person_id INT )
BEGIN  
   
	SET @sql = NULL;

	SELECT
	  GROUP_CONCAT(DISTINCT
	      CONCAT(
		  'max(case when field_key = ''',
		  field_key,
		  ''' then field_value end) ',
		  field_key
	      )
	    ) INTO @sql 
	FROM(
		SELECT
		     sf.id AS survey_form_id,
		     CONCAT('field',sfd.id) AS field_key,
		     CASE
			WHEN IFNULL(dd.code,'') = 'SELECT' THEN IFNULL(sfdv.value,'')
			ELSE IFNULL(san.value,'')
		     END AS field_value
		FROM survey_forms sf
		INNER JOIN survey_form_details sfd ON sfd.survey_form_id = sf.id AND sfd.state = 'A'
		INNER JOIN detail_definitions dd ON dd.id = sfd.type
		LEFT JOIN survey_applications sa ON sa.survey_form_id  = sf.id AND sa.person_id = person_id
		LEFT JOIN survey_answers san ON san.survey_application_id = sa.id AND san.survey_form_detail_id = sfd.id
		LEFT JOIN survey_form_detail_values sfdv ON sfdv.id = san.value
		WHERE sf.state = 'P' AND sf.id = survey_form_id
	 ) AS relation;
	  
	SET @sql = CONCAT('SELECT ',person_id,' as person_id, survey_form_id, ', @sql, ' 
			  FROM (
				SELECT
				     sf.id AS survey_form_id,
				     CONCAT(\'field\',sfd.id) AS field_key,
				     CASE
					WHEN IFNULL(dd.code,\'\') = \'SELECT\' THEN IFNULL(sfdv.value,\'\')
					ELSE IFNULL(san.value,\'\')
				     END AS `field_value`
				FROM survey_forms sf
				INNER JOIN survey_form_details sfd ON sfd.survey_form_id = sf.id AND sfd.state = \'A\'
				INNER JOIN detail_definitions dd ON dd.id = sfd.type
				LEFT JOIN survey_applications sa ON sa.survey_form_id  = sf.id AND sa.person_id = ',person_id,'
				LEFT JOIN survey_answers san ON san.survey_application_id = sa.id AND san.survey_form_detail_id = sfd.id
				LEFT JOIN survey_form_detail_values sfdv ON sfdv.id = san.value
				WHERE sf.state = \'P\' AND sf.id = ', survey_form_id,'
			  ) as relation GROUP BY survey_form_id');
			  
			  
	PREPARE stmt FROM @sql;
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
END$$

DELIMITER ;
