<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_redirectlog_urls'] = array (
	'ctrl' => $TCA['tx_redirectlog_urls']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,starttime,endtime,partitial,old_url,new_pageid,new_url,header,language'
	),
	'feInterface' => $TCA['tx_redirectlog_urls']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'partitial' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.partitial',		
			'config' => array (
				'type' => 'check',
			)
		),
		'old_url' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.old_url',		
			'config' => array (
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
		'new_pageid' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.new_pageid',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'pages',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'new_url' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.new_url',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'header' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.header',		
			'config' => array (
				'type' => 'select',
				'items' => array (
					array('LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.header.I.0', '301', t3lib_extMgm::extRelPath('redirectlog').'res/selicon_tx_redirectlog_urls_301.gif'),
					array('LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.header.I.1', '302', t3lib_extMgm::extRelPath('redirectlog').'res/selicon_tx_redirectlog_urls_302.gif'),
					array('LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.header.I.2', '403', t3lib_extMgm::extRelPath('redirectlog').'res/selicon_tx_redirectlog_urls_403.gif'),
					array('LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.header.I.3', '404', t3lib_extMgm::extRelPath('redirectlog').'res/selicon_tx_redirectlog_urls_404.gif'),
					array('LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.header.I.4', '503', t3lib_extMgm::extRelPath('redirectlog').'res/selicon_tx_redirectlog_urls_503.gif'),
				),
				'size' => 1,	
				'maxitems' => 1,
			)
		),
		'language' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls.language',		
			'config' => array (
				'type' => 'select',	
				'items' => array (
					array('',0),
				),
				'foreign_table' => 'sys_language',	
				'foreign_table_where' => 'AND sys_language.pid=###SITEROOT### ORDER BY sys_language.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, partitial, old_url, new_pageid, new_url, header, language')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime')
	)
);



$TCA['tx_redirectlog_log'] = array (
	'ctrl' => $TCA['tx_redirectlog_log']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'old_url,new_url,referer,referer_extern,user_browser,user_ip,user_name,user_spider'
	),
	'feInterface' => $TCA['tx_redirectlog_log']['feInterface'],
	'columns' => array (
		'old_url' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_log.old_url',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',
			)
		),
		'new_url' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_log.new_url',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',
			)
		),
		'referer' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_log.referer',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',
			)
		),
		'referer_extern' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_log.referer_extern',		
			'config' => array (
				'type' => 'check',
			)
		),
		'user_browser' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_log.user_browser',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',
			)
		),
		'user_ip' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_log.user_ip',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',
			)
		),
		'user_name' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_log.user_name',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',
			)
		),
		'user_spider' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_log.user_spider',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'old_url;;;;1-1-1, new_url, referer, referer_extern, user_browser, user_ip, user_name, user_spider')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>
