--
-- Table structure for table `#__yos_resources_manager_country`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_country` (
  `country_id` int(11) NOT NULL auto_increment,
  `country_name` varchar(64) default NULL,
  `country_3_code` char(3) default NULL,
  `country_2_code` char(2) default NULL,
  PRIMARY KEY  (`country_id`),
  KEY `idx_country_name` (`country_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Country records' AUTO_INCREMENT=245 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_coupon`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_coupon` (
  `id` int(11) NOT NULL auto_increment,
  `coupon_code` varchar(32) NOT NULL,
  `percent_or_total` enum('percent','total') NOT NULL default 'percent',
  `coupon_value` decimal(12,2) NOT NULL,
  `packages` varchar(255) NOT NULL,
  `count` int(11) NOT NULL default '1',
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_currency`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_currency` (
  `id` int(11) NOT NULL auto_increment,
  `currency_name` varchar(64) default NULL,
  `currency_code` char(3) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Used to store currencies' AUTO_INCREMENT=159 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_group`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_group` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) default NULL,
  `description` varchar(512) default NULL,
  `time_mapping` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_group_role_xref`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_group_role_xref` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_#__users`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_#__users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(45) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_mapping`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_mapping` (
  `id` int(11) NOT NULL auto_increment,
  `joomla_group_id` int(11) NOT NULL,
  `yrm_group_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=86 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_order`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_order` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `status` char(1) NOT NULL default 'P',
  `return_url` varchar(1000) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_package`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_package` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `description` varchar(512) NOT NULL,
  `value` varchar(45) default NULL COMMENT 'money (or credit) need to pay',
  `currency` int(11) NOT NULL default '0',
  `published` tinyint(4) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_package_object_xref`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_package_object_xref` (
  `id` int(11) NOT NULL auto_increment,
  `package_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `type` enum('group','role','resource') NOT NULL,
  `times_access` int(11) default NULL,
  `seconds` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=573 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_package_payment_method_xref`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_package_payment_method_xref` (
  `id` int(11) NOT NULL auto_increment,
  `package_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=203 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_payment_method`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_payment_method` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `description` varchar(512) default NULL,
  `published` tinyint(4) NOT NULL,
  `payment_class` varchar(50) NOT NULL,
  `payment_method_code` varchar(8) NOT NULL,
  `is_creditcard` tinyint(1) NOT NULL,
  `enable_processor` char(1) NOT NULL,
  `accepted_creditcards` varchar(128) NOT NULL,
  `payment_extrainfo` text NOT NULL,
  `payment_passkey` blob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_plug_in`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_plug_in` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `published` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_resource`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_resource` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `affected` enum('B','F','BF') NOT NULL default 'F' COMMENT 'affected domain\nB = back-end\nF = Front-end\nBF = both of back-end and front-end',
  `type` enum('request','module','menu','label') default NULL,
  `option` varchar(45) NOT NULL COMMENT 'to store Joomla option param',
  `task` varchar(45) default NULL COMMENT 'to store joomla task param',
  `view` varchar(45) default NULL COMMENT 'to store joomla view param',
  `params` varchar(512) NOT NULL COMMENT 'to store other params',
  `plug_in` int(11) default NULL,
  `redirect_url` varchar(512) default NULL,
  `redirect_message` varchar(512) default NULL,
  `description` varchar(512) default NULL,
  `sticky` tinyint(1) NOT NULL default '1',
  `published` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_resource_role_xref`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_resource_role_xref` (
  `id` int(11) NOT NULL auto_increment,
  `resource_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=120 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_role`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_role` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `description` varchar(512) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_user_group_xref`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_user_group_xref` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `start` datetime default NULL,
  `end` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=156 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_user_info`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_user_info` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `last_name` varchar(32) default NULL,
  `first_name` varchar(32) default NULL,
  `middle_name` varchar(32) default NULL,
  `phone` varchar(32) default NULL,
  `fax` varchar(32) default NULL,
  `address_1` varchar(64) NOT NULL default '',
  `address_2` varchar(64) default NULL,
  `city` varchar(32) NOT NULL default '',
  `state` varchar(32) NOT NULL default '',
  `country` varchar(32) NOT NULL default 'US',
  `zip` varchar(32) NOT NULL default '',
  `user_email` varchar(255) default NULL,
  `cdate` datetime default NULL,
  `mdate` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Customer Information, BT = BillTo' AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_user_resource_banned`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_user_resource_banned` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `description` varchar(512) default NULL,
  `redirect_url` varchar(512) default NULL,
  `redirect_message` varchar(512) default NULL,
  `start` datetime default NULL,
  `end` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_user_resource_xref`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_user_resource_xref` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `times_access` int(11) default NULL,
  `start` datetime default NULL,
  `end` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=201 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__yos_resources_manager_user_role_xref`
--

CREATE TABLE IF NOT EXISTS `#__yos_resources_manager_user_role_xref` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `start` datetime default NULL,
  `end` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=118 ;

--
-- Dumping data for table `#__yos_resources_manager_currency`
--

REPLACE INTO `#__yos_resources_manager_currency` (`id`, `currency_name`, `currency_code`) VALUES
(1, 'Andorran Peseta', 'ADP'),
(2, 'United Arab Emirates Dirham', 'AED'),
(3, 'Afghanistan Afghani', 'AFA'),
(4, 'Albanian Lek', 'ALL'),
(5, 'Netherlands Antillian Guilder', 'ANG'),
(6, 'Angolan Kwanza', 'AOK'),
(7, 'Argentine Peso', 'ARS'),
(9, 'Australian Dollar', 'AUD'),
(10, 'Aruban Florin', 'AWG'),
(11, 'Barbados Dollar', 'BBD'),
(12, 'Bangladeshi Taka', 'BDT'),
(14, 'Bulgarian Lev', 'BGL'),
(15, 'Bahraini Dinar', 'BHD'),
(16, 'Burundi Franc', 'BIF'),
(17, 'Bermudian Dollar', 'BMD'),
(18, 'Brunei Dollar', 'BND'),
(19, 'Bolivian Boliviano', 'BOB'),
(20, 'Brazilian Real', 'BRL'),
(21, 'Bahamian Dollar', 'BSD'),
(22, 'Bhutan Ngultrum', 'BTN'),
(23, 'Burma Kyat', 'BUK'),
(24, 'Botswanian Pula', 'BWP'),
(25, 'Belize Dollar', 'BZD'),
(26, 'Canadian Dollar', 'CAD'),
(27, 'Swiss Franc', 'CHF'),
(28, 'Chilean Unidades de Fomento', 'CLF'),
(29, 'Chilean Peso', 'CLP'),
(30, 'Yuan (Chinese) Renminbi', 'CNY'),
(31, 'Colombian Peso', 'COP'),
(32, 'Costa Rican Colon', 'CRC'),
(33, 'Czech Koruna', 'CZK'),
(34, 'Cuban Peso', 'CUP'),
(35, 'Cape Verde Escudo', 'CVE'),
(36, 'Cyprus Pound', 'CYP'),
(40, 'Danish Krone', 'DKK'),
(41, 'Dominican Peso', 'DOP'),
(42, 'Algerian Dinar', 'DZD'),
(43, 'Ecuador Sucre', 'ECS'),
(44, 'Egyptian Pound', 'EGP'),
(46, 'Ethiopian Birr', 'ETB'),
(47, 'Euro', 'EUR'),
(49, 'Fiji Dollar', 'FJD'),
(50, 'Falkland Islands Pound', 'FKP'),
(52, 'British Pound', 'GBP'),
(53, 'Ghanaian Cedi', 'GHC'),
(54, 'Gibraltar Pound', 'GIP'),
(55, 'Gambian Dalasi', 'GMD'),
(56, 'Guinea Franc', 'GNF'),
(58, 'Guatemalan Quetzal', 'GTQ'),
(59, 'Guinea-Bissau Peso', 'GWP'),
(60, 'Guyanan Dollar', 'GYD'),
(61, 'Hong Kong Dollar', 'HKD'),
(62, 'Honduran Lempira', 'HNL'),
(63, 'Haitian Gourde', 'HTG'),
(64, 'Hungarian Forint', 'HUF'),
(65, 'Indonesian Rupiah', 'IDR'),
(66, 'Irish Punt', 'IEP'),
(67, 'Israeli Shekel', 'ILS'),
(68, 'Indian Rupee', 'INR'),
(69, 'Iraqi Dinar', 'IQD'),
(70, 'Iranian Rial', 'IRR'),
(73, 'Jamaican Dollar', 'JMD'),
(74, 'Jordanian Dinar', 'JOD'),
(75, 'Japanese Yen', 'JPY'),
(76, 'Kenyan Schilling', 'KES'),
(77, 'Kampuchean (Cambodian) Riel', 'KHR'),
(78, 'Comoros Franc', 'KMF'),
(79, 'North Korean Won', 'KPW'),
(80, '(South) Korean Won', 'KRW'),
(81, 'Kuwaiti Dinar', 'KWD'),
(82, 'Cayman Islands Dollar', 'KYD'),
(83, 'Lao Kip', 'LAK'),
(84, 'Lebanese Pound', 'LBP'),
(85, 'Sri Lanka Rupee', 'LKR'),
(86, 'Liberian Dollar', 'LRD'),
(87, 'Lesotho Loti', 'LSL'),
(89, 'Libyan Dinar', 'LYD'),
(90, 'Moroccan Dirham', 'MAD'),
(91, 'Malagasy Franc', 'MGF'),
(92, 'Mongolian Tugrik', 'MNT'),
(93, 'Macau Pataca', 'MOP'),
(94, 'Mauritanian Ouguiya', 'MRO'),
(95, 'Maltese Lira', 'MTL'),
(96, 'Mauritius Rupee', 'MUR'),
(97, 'Maldive Rufiyaa', 'MVR'),
(98, 'Malawi Kwacha', 'MWK'),
(99, 'Mexican Peso', 'MXP'),
(100, 'Malaysian Ringgit', 'MYR'),
(101, 'Mozambique Metical', 'MZM'),
(102, 'Nigerian Naira', 'NGN'),
(103, 'Nicaraguan Cordoba', 'NIC'),
(105, 'Norwegian Kroner', 'NOK'),
(106, 'Nepalese Rupee', 'NPR'),
(107, 'New Zealand Dollar', 'NZD'),
(108, 'Omani Rial', 'OMR'),
(109, 'Panamanian Balboa', 'PAB'),
(110, 'Peruvian Nuevo Sol', 'PEN'),
(111, 'Papua New Guinea Kina', 'PGK'),
(112, 'Philippine Peso', 'PHP'),
(113, 'Pakistan Rupee', 'PKR'),
(114, 'Polish Złoty', 'PLN'),
(116, 'Paraguay Guarani', 'PYG'),
(117, 'Qatari Rial', 'QAR'),
(118, 'Romanian Leu', 'RON'),
(119, 'Rwanda Franc', 'RWF'),
(120, 'Saudi Arabian Riyal', 'SAR'),
(121, 'Solomon Islands Dollar', 'SBD'),
(122, 'Seychelles Rupee', 'SCR'),
(123, 'Sudanese Pound', 'SDP'),
(124, 'Swedish Krona', 'SEK'),
(125, 'Singapore Dollar', 'SGD'),
(126, 'St. Helena Pound', 'SHP'),
(127, 'Sierra Leone Leone', 'SLL'),
(128, 'Somali Schilling', 'SOS'),
(129, 'Suriname Guilder', 'SRG'),
(130, 'Sao Tome and Principe Dobra', 'STD'),
(131, 'Russian Ruble', 'RUB'),
(132, 'El Salvador Colon', 'SVC'),
(133, 'Syrian Potmd', 'SYP'),
(134, 'Swaziland Lilangeni', 'SZL'),
(135, 'Thai Bath', 'THB'),
(136, 'Tunisian Dinar', 'TND'),
(137, 'Tongan Pa''anga', 'TOP'),
(138, 'East Timor Escudo', 'TPE'),
(139, 'Turkish Lira', 'TRY'),
(140, 'Trinidad and Tobago Dollar', 'TTD'),
(141, 'Taiwan Dollar', 'TWD'),
(142, 'Tanzanian Schilling', 'TZS'),
(143, 'Uganda Shilling', 'UGS'),
(144, 'US Dollar', 'USD'),
(145, 'Uruguayan Peso', 'UYP'),
(146, 'Venezualan Bolivar', 'VEB'),
(147, 'Vietnamese Dong', 'VND'),
(148, 'Vanuatu Vatu', 'VUV'),
(149, 'Samoan Tala', 'WST'),
(150, 'Democratic Yemeni Dinar', 'YDD'),
(151, 'Yemeni Rial', 'YER'),
(152, 'New Yugoslavia Dinar', 'YUD'),
(153, 'South African Rand', 'ZAR'),
(154, 'Zambian Kwacha', 'ZMK'),
(155, 'Zaire Zaire', 'ZRZ'),
(156, 'Zimbabwe Dollar', 'ZWD'),
(157, 'Slovak Koruna', 'SKK'),
(158, 'Armenian Dram', 'AMD');

