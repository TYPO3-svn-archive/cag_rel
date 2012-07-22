<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$tempColumns = Array (
	'tx_cagrel_link' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_link',
		'config' => Array (
			'type' => 'check',
		)
	),
	'tx_cagrel_stdrel' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel',
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.0', '0'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.1', '1'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.2', '2'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.3', '3'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.4', '4'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.5', '5'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.6', '6'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.7', '7'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.8', '8'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.9', '9'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.10', '10'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.11', '11'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.12', '12'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.13', '13'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.14', '14'),
				Array('LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_stdrel.I.15', '15'),
			),
			'size' => 1,
			'maxitems' => 1,
		)
	),
	'tx_cagrel_specrel' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_specrel',
		'config' => Array (
			'type' => 'input',
			'size' => '30',
			'max' => '255',
			'eval' => 'trim,lower,nospace',
		)
	),
	'tx_cagrel_params' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:cag_rel/locallang_db.xml:pages.tx_cagrel_params',
		'config' => Array (
			'type' => 'input',
			'size' => '40',
			'max' => '255',
			'eval' => 'trim',
		)
	),
);


t3lib_div::loadTCA('pages');
t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('pages','tx_cagrel_link;;;;1-1-1, tx_cagrel_stdrel, tx_cagrel_specrel, tx_cagrel_params');

// Add TS
t3lib_extMgm::addStaticFile($_EXTKEY,'static/','Relational links');
?>