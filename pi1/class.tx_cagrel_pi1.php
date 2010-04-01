<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2010 Torsten Schrade (schradt@uni-mainz.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
* class.tx_cagrel_pi1
*
* create <link rel=""> tags from pages
*
* @author Torsten Schrade <schradt@uni-mainz.de>
*/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *  43: class tx_cagrel_pi1 extends tslib_pibase
 *  57:		function select_pages($content, $conf)
 *  92:		function make_relLink ($I, $conf)
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_cagrel_pi1 extends tslib_pibase {

	var $prefixID = 'tx_cagrel_pi1';
	var $scriptRelPath = 'pi1/class.tx_cagrel_pi1.php';
	var $extKey = 'cag_rel';

	/**
	 * Performs a query to get all IDs of pages below the root template which have been checked as 'link' and which
	 * are defined in the getRel attribute of the TMENUS that group the relation types together
	 *
	 * @return	string		A comma separated list of IDs from which the TMENUS are built
	 */
	function select_pages ($content,$conf) {

			if ($conf['getRel']) {

				$ids = array();
					// set the idrange from root template
				$idrange = tslib_pibase::pi_getPidList($GLOBALS[TSFE]->tmpl->rootId,$conf['recLevel']);
					// set the relations to be queried
				$rel = ' AND tx_cagrel_stdrel IN ('.$conf['getRel'].')';
					// sorting order is either the kind of rel or the sorting field if its a stylesheet
				$conf['getRel'] == '14' ? $orderBy='sorting' : $orderBy='tx_cagrel_stdrel';
					// do the query
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','pages','uid IN ('.$idrange.') AND tx_cagrel_link="1" AND hidden="0" AND deleted="0"'.$rel,'',$orderBy,'');
					// work on the result
				$rowCounter=0;
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$ids[] = $row['uid'];
					$rowCounter++;
				}
				empty($ids) ? $content = 'noresult' : $content = implode(',', $ids);

			} else {
					// a string is returned - HMENU.special.value hast to be set to something, otherwise the uid is taken as default value
				$content = 'noresult';
			}
		return $content;
	}

	/**
	 * Changes the $I['parts'] array for each item to consist of a <link> rather than an <a> tag. Relations and additional
	 * parameters are inserted. If the link is to a stylesheet, the title is dropped. The links are only inserted if no special
	 * IDs have been set or if the current page id is one of the defined ids for the link.
	 *
	 * @param	array		The array with information about the menuitem
	 * @param	array		The configuration array for the HMENU
	 *
	 * @return	array		The modified I['parts'] array
	 */
	function make_relLink ($I,$conf) {

		$pageID = intval($GLOBALS['TSFE']->page['uid']); 	/* the current page id */
		$uidList = array(); 								/* list of IDs that might be specified for the item */
		$key = $I['key'];									/* the current key of the current menu item */

		// we don't want a normal <a> tag from the TMENU this time ;)
		unset($I['parts']['ATag_begin']);
		unset($I['parts']['ATag_end']);

			// if no special relation is set, insert one of the standard relations
		if (!$conf['parentObj']->menuArr[$key]['tx_cagrel_specrel']) {

			$stdRel = Array (
				'1' => 'alternate',
				'2' => 'appendix',
				'3' => 'bookmark',
				'4' => 'chapter',
				'5' => 'contents',
				'6' => 'copyright',
				'7' => 'glossary',
				'8' => 'help',
				'9' => 'index',
				'10' => 'next',
				'11' => 'prev',
				'12' => 'section',
				'13' => 'start',
				'14' => 'stylesheet',
				'15' => 'subsection',
			);

				// set the attribute for the current item
			$r = $conf['parentObj']->menuArr[$key]['tx_cagrel_stdrel'];
			$linkRel = $stdRel[$r];

				// if no standard relation is specified, set 'section' as a default value
			if ($conf['parentObj']->menuArr[$key]['tx_cagrel_stdrel'] == 0 && !$I['val']['setRelation']) {$linkRel = $I['val']['defRelation'];}

		} else {
				// otherwise the special relation keyword is inserted
			$linkRel = htmlspecialchars($conf['parentObj']->menuArr[$key]['tx_cagrel_specrel']);
		}

			// override the relations from TS
		if ($I['val']['setRelation']) {$linkRel = htmlspecialchars($I['val']['setRelation']);}

			// use external href if doktype is external url - left in for backwards compatibility; can now be set with additional params field as well
		if ($conf['parentObj']->menuArr[$key]['doktype'] == 3) {

			$urltype = Array (
				'0'	=> '',
				'1' => 'http://',
				'2' => 'https://',
				'3' => 'mailto:',
				'4' => 'ftp://',
			);
			$url = $conf['parentObj']->menuArr[$key]['url'];
				// new href is set
			$I['linkHREF']['HREF'] = $urltype[$conf['parentObj']->menuArr[$key]['urltype']].$url;
		}

			// check for additional parameters
		if ($conf['parentObj']->menuArr[$key]['tx_cagrel_params'] || $I['val']['LinkTagParams'])  {

			// if params are set from TS, use them (thanks to Niels!)
			if ($I['val']['LinkTagParams']) {
				$params = t3lib_div::trimExplode(';', $I['val']['LinkTagParams']);
			} else {
				$params = t3lib_div::trimExplode(';', $conf['parentObj']->menuArr[$key]['tx_cagrel_params']);
			}

				// process the parameters
			foreach ($params as $key => $value) {

					// check if parameter is an id value
				if (substr($params[$key],-1 , 1) !== '+' && intval($params[$key]) > 0) {

					$uidList[] = intval($params[$key]);
					unset($params[$key]);

					// or if its an id value with recursive option (+)
				} elseif (substr($params[$key],-1 , 1) == '+' && intval($params[$key]) > 0) {

							// get all recursive ids into an array
						$recIds = t3lib_div::trimExplode(',',tslib_pibase::pi_getPidList(intval($params[$key]), 10));
						unset($params[$key]);

							// not using array_merge ;)
						foreach ($recIds as $key => $value) {
							$uidList[] = intval($recIds[$key]);
						}

					// or if its a href attribute
				} elseif (substr($params[$key],0,5) == 'href=') {

						// just set it here, will be htmlspecialchared below
					$I['linkHREF']['HREF'] = substr($params[$key],6);
					$I['linkHREF']['HREF'] = substr($I['linkHREF']['HREF'],0,-1);

					unset($params[$key]);

					// or if its a title attribute
				} elseif (substr($params[$key],0,6) == 'title=') {

					$params[$key] = substr($params[$key],7);
					$I['parts']['title'] = htmlspecialchars(substr($params[$key],0,-1));

					unset($params[$key]);

					// or if its a comment
				} elseif (substr($params[$key],0,4) == '<!--') {

					$comment = array();

						// two part comment
					if (strpos($params[$key],'|')) {
						$comment = explode('|', $params[$key]);
							// is it the first part of a conditional comment
						if (substr($comment[0],-2) == ']>') {
							$comment[0] = substr($comment[0],4);
							$comment[0] = '<!--'.htmlspecialchars(substr($comment[0],0,-2)).']>';
							// if not treat it as the first part of a normal comment
						} else {
							$comment[0] = '<!--'.htmlspecialchars(substr($comment[0],4));
						}
							// is it the second part of a conditional comment?
						if (substr($comment[1],0,2) == '<!') {
							$comment[1] = substr($comment[1],2);
							$comment[1] = '<!'.htmlspecialchars(substr($comment[1],0,-3)).'-->';
							// if not treat it as the  second part of a normal comment
						} else {
							$comment[1] = htmlspecialchars(substr($comment[1],0,-3)).'-->';
						}
						// normal comment
					} else {
						$comment[0] = $params[$key];
						$comment[0] = substr($comment[0],4);
						$comment[0] = '<!--'.htmlspecialchars(substr($comment[0],0,-3)).'-->';
					}

					unset($params[$key]);
				}
			}
				// build a string of the remaining attributes which will be inserted
			if (count($params) != 0) {

				$addParams = str_replace('&quot;','"',htmlspecialchars(implode(' ', $params)));
				$addParams .= ' ';
			}

		}

		// check if a HMENU.special.browse is used and set relations accordingly
		if($conf['parentObj']->conf['special'] == 'browse') {

				// get the relation values from TS for override
			$curRel = t3lib_div::trimExplode('|',$I['val']['setRelation']);

				// if this doesnt match, some of the specified browse items are not there and we need to do a bit of shuffling around (for instance if we are on the first/last page in a section)
			if (count($curRel) != $conf['parentObj']->WMmenuItems) {

					// we need to know if we are on the first / last page in this section
				for ($i = 0; $i < $conf['parentObj']->WMmenuItems; $i++) {
					$sectionUids[$conf['parentObj']->menuArr[$i]['sorting']] = $conf['parentObj']->menuArr[$i]['uid'];
				}
					// take out the index bit if it's there and then sort it;
				if (in_array('index', $curRel)) {
					$index = array_search($conf['parentObj']->parent_cObj->data['pid'], $sectionUids);
					unset($sectionUids[$index]);
				}
				ksort($sectionUids);

					// are prev/next set from TS?
				$prev = array_search('prev', $curRel);
				$next = array_search('next', $curRel);

					// check if we are on the first/last page in a section - if so, next/prev relations are removed
				if ($prev || $next) {

					if ($pageID == reset($sectionUids)) {unset($curRel[$prev]);}
					if ($pageID == end($sectionUids)) {unset($curRel[$next]);}
					 // reindex the array
					$c = 0;
					foreach ($curRel as $k => $v) {
						$tmp[$c] = $curRel[$k];
						$c++;
					}
					$curRel = $tmp;
				}
			}
				// put the current relation into the link relation value
			$linkRel = $curRel[$I['key']];
		}

			// <link> item if either no special uids are set or if we are on a page that corresponds to the $uidList
		if (!$uidList || in_array($pageID, $uidList)) {

				// shift title to another variable so that it can be taken out for stylesheets
			$title= ' title="'.$I['parts']['title'].'"';
			unset($I['parts']['title']);

				// override title from TS; thx to Niels Froehling
			if ($I['val']['LinkTagTitle']) {
				$title = ' title="'.$I['val']['LinkTagTitle'].'"';
			}

				// take out title attribute completely if its a stylesheet
			if ($conf['parentObj']->menuArr[$key]['tx_cagrel_stdrel'] == 14) {
				unset($title);
			}

				// compile the <link>
			if ($comment['0']) {$I['parts']['before'] = "\t".$comment['0']."\n";} else {$I['parts']['before']='';}
			$I['parts']['before'] .= "\t".'<link rel="'.$linkRel.'" '.$addParams.'href="'.htmlspecialchars($I['linkHREF']['HREF']).'"'.$title.' />'."\n";
			if ($comment['1']) {$I['parts']['after'] = "\t".$comment['1']."\n";} else {$I['parts']['after']='';}

		} else {
			// take out the whole item if we're not on a corresponding page
			// remember: ATag_begin + ATag_end have been unset already ;)
			unset($I['parts']['title']);
		}
		return $I;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_rel/pi1/class.tx_cagrel_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_rel/pi1/class.tx_cagrel_pi1.php']);
}
?>
