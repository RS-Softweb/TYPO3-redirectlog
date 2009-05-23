<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE')	{
	t3lib_extMgm::insertModuleFunction(
		'web_info',		
		'tx_redirectlog_modfunc1',
		t3lib_extMgm::extPath($_EXTKEY).'modfunc1/class.tx_redirectlog_modfunc1.php',
		'LLL:EXT:redirectlog/locallang_db.xml:moduleFunction.tx_redirectlog_modfunc1'
	);
}

$TCA['tx_redirectlog_urls'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_urls',		
		'label'     => 'old_url',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_redirectlog_urls.gif',
	),
);

$TCA['tx_redirectlog_log'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:redirectlog/locallang_db.xml:tx_redirectlog_log',		
		'label'     => 'old_url',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_redirectlog_log.gif',
	),
);
?>