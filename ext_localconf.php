<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	// Include the userfunction
t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_cagrel_pi1.php','','',1);

	// set new default for external urls
$GLOBALS['TCA']['pages']['columns']['urltype']['config']['default'] = '0';

?>