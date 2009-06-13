<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Rene <typo3@rs-softweb.de>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   56: class tx_redirectlog_pi1
 *   89:     function init($ref)
 *  110:     function main($param,$ref)
 *  198:     function checkError($param)
 *  210:     function getError($headertype)
 *  224:     function isValid($starttime, $endtime)
 *  243:     function fillArray($query)
 *  268:     function clearArray()
 *  281:     function getMailTemplate($headertype)
 *  316:     function sendMail($request_url,$replace_url,$headertype)
 *  369:     function get_page_url($pid,$addParams=array())
 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
include_once(PATH_tslib.'class.tslib_content.php');

/**
 * Plugin 'Redirection' for the 'redirectlog' extension.
 *
 * @author	Rene <typo3@rs-softweb.de>
 * @package	TYPO3
 * @subpackage	tx_redirectlog
 */
class tx_redirectlog_pi1 {
	var $prefixId      = 'tx_redirectlog_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_redirectlog_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'redirectlog';	// The extension key.
	var $pi_checkCHash = true;

	var $debug = False;

	var $headertext = array(
		'301'=>'HTTP/1.1 301 Moved Permanently',
		'302'=>'HTTP/1.1 302 Moved Temporarily',
		'403'=>'HTTP/1.1 403 Forbidden',
		'404'=>'HTTP/1.1 404 Not Found',
		'503'=>'HTTP/1.1 503 Service Unavailable'
	);
	var $arrReplacement = array (
		'Request' => array(),
		'Replace' => array(),
		'Header' => array()
	);
	var $server_name = '';
	var $server_path = '';

	var $fields_1_select = 'old_url,new_url,new_pageid,header,partitial,starttime,endtime';
	var $fields_1_where = 'hidden=0 AND deleted=0';


	/**
	 * Initiates some objects and vars
	 *
	 * @param	array		Parent object
	 * @return	void
	 */
	function init($ref) {
		// create helping objects
		$this->ref = $ref;
		$this->ref->newCObj();
		$this->ref->initTemplate();
		// get the extension-manager configuration
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		$this->debug = $this->extConf['debug'];
		// set vars
		$this->server_name = 'http://'.$_SERVER['SERVER_NAME'];
		// initiate db object
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * The main method of the PlugIn
	 *
	 * @param	array		$param - Array with link params
	 * @param	array		$ref - Parent object
	 * @return	void
	 */
	function main($param,$ref)	{
//		var_dump($ref);
		$this->init($ref);
		if ($this->debug) {t3lib_div::debug($param);}

		// first check for special sites (header code 403 or 503)
		$headertype = $this->checkError($param);

		// if special site requested
		if (in_array($headertype, array('403', '503'))) {
			// get the redirections from db
			$this->getError($headertype);

			$request_url = $param['currentUrl'];
			$replace_url = $this->arrReplacement['Replace'][0];
		} else {
			// get the redirections from db
			$query = $this->db->exec_SELECTquery($this->fields_1_select, 'tx_redirectlog_urls', $this->fields_1_where );
			$this->fillArray($query);

			$request_url = $param['currentUrl'];

			$replace_url = str_replace($this->arrReplacement['Request'],$this->arrReplacement['Replace'],$request_url);
			$replace_url = strtolower($replace_url);

			for ($i=0; $i < count($this->arrReplacement['Request']); $i++)
			{
				$pos = strpos($request_url,$this->arrReplacement['Request'][$i]);
				if (($pos === false))
				{
					$umleitung=false;
					$headertype='404';
				} else {
					$umleitung=true;
					$headertype=$this->arrReplacement['Header'][$i];
					break;
				}
			}

			if (!($umleitung)) {
				$this->clearArray();
				$this->getError($headertype);
				$replace_url = $this->arrReplacement['Replace'][0];
			}
		}

		if ((in_array($headertype, array('301', '302'))) && ($this->extConf['enable_warning_redirect'])) {
			$this->sendMail($request_url,$replace_url,$headertype);
			sleep(1);
		} elseif ((in_array($headertype, array('403', '404'))) && ($this->extConf['enable_warning_notfound'])) {
			$this->sendMail($request_url,$replace_url,$headertype);
		}

		switch ($headertype) {
			case '301':
			case '302':
			case '503':
				$headertext = $this->headertext[$headertype];
				header("$headertext");
				header("Location:$this->server_name$replace_url");
				header("Connection: close");
				break;
			case '403':
			case '404':
				$headertext = $this->headertext[$headertype];
				header("$headertext");
				$handle = fopen($this->server_name.$replace_url, "r");
				if ($handle) {
					while (!feof($handle)) {
						$buffer = fgets($handle);
						$buffer = str_replace('###URL###',$this->server_name.$request_url,$buffer);
						$buffer = str_replace('###REFERER###',($_SERVER['HTTP_REFERER']!='') ? $_SERVER['HTTP_REFERER'] : '"..."',$buffer);
						echo $buffer;
					}
					fclose ($handle);
				}
				header("Connection: close");
				break;
		}
	}


	/**
	 * Checks for special error codes (header 403 or 503)
	 *
	 * @param	array		Array with link params
	 * @return	string		The sugested errorcode
	 */
	function checkError($param) {
		if (!(in_Array('0',$param['pageAccessFailureReasons']['fe_group']))) {
			return '403';
		}
	}

	/**
	 * Get vars for special error codes (header 403, 404 or 503)
	 *
	 * @param	array		Array with link params
	 * @return	void
	 */
	function getError($headertype) {
			$query = $this->db->exec_SELECTquery($this->fields_1_select, 'tx_redirectlog_urls', $this->fields_1_where.' AND old_url='.$headertype.'' );
			$this->fillArray($query);
			if ($this->debug) {t3lib_div::debug($this->arrReplacement);}
	}


	/**
	 * This function checks if a url replacement is valid
	 *
	 * @param	integer		The starttime of this row
	 * @param	integer		The endtime of this row
	 * @return	boolean		True if valid, False if not valid
	 */
	function isValid($starttime, $endtime) {
		if ( ($starttime == 0 && $endtime == 0 ) ||
			 ($starttime == 0 && date('Y-m-d',$endtime) >= date('Y-m-d',strtotime(date("Y-m-d"))) ) ||
			 (date('Y-m-d',$starttime) <= date('Y-m-d',strtotime(date("Y-m-d"))) && $endtime == 0 ) ||
			 (date('Y-m-d',$starttime) <= date('Y-m-d',strtotime(date("Y-m-d"))) && date('Y-m-d',$endtime) >= date('Y-m-d',strtotime(date("Y-m-d"))) ) )
		{
			return True;
		} else {
			return False;
		}

	}

	/**
	 * This function fills array with the replacements
	 *
	 * @param	pointer		Pointer to the Query
	 * @return	void
	 */
	function fillArray($query) {
		$count = $this->db->sql_num_rows($query);
		if ($count > 0) {
			for ($i=0;$i<$count;$i++) {
				$row = $this->db->sql_fetch_assoc($query);
				if ($this->isValid($row['starttime'],$row['endtime'])) {
					$this->arrReplacement['Request'][] = $row['old_url'];
					if ($row['partitial']== 1) {
						$this->arrReplacement['Replace'][] = $row['new_url'];
					} elseif ($row['new_pageid']>0) {
						$this->arrReplacement['Replace'][] = '/'.$this->get_page_url($row['new_pageid']);
					} else {
						$this->arrReplacement['Replace'][] = $row['new_url'];
					}
					$this->arrReplacement['Header'][] = $row['header'];
				}
			}
		}
	}

	/**
	 * This function clears array with the replacements
	 *
	 * @return	void
	 */
	function clearArray() {
		$this->arrReplacement['Request']=array();
		$this->arrReplacement['Replace']=array();
		$this->arrReplacement['Header']=array();
	}


	/**
	 * Gets the templates for mails
	 *
	 * @param	string		The headertype
	 * @return	boolean		True if template found, False if template not found or incomplete
	 */
	function getMailTemplate($headertype) {
		// Get the Mail templates.
		if(strlen($this->extConf['mail_templates_file']) < 1) $this->extConf['mail_templates_file'] = t3lib_extMgm::siteRelPath($this->extKey).'res/'.'redirect_mail.tmpl';
		$this->templateCode = $this->ref->cObj->fileResource($this->extConf['mail_templates_file']);
		// Get the subparts from the Mail template.
		if($this->templateCode) {
			$this->mailTemplate['all'] = $this->ref->cObj->getSubpart($this->templateCode, '###TEMPLATE_MAIL_'.$headertype.'###');
			$this->mailTemplate['subject'] = $this->ref->cObj->getSubpart($this->mailTemplate['all'], '###MAIL_SUBJECT###');
			$this->mailTemplate['text'] = $this->ref->cObj->getSubpart($this->mailTemplate['all'], '###MAIL_TEXT###');
			if ($this->debug) {t3lib_div::debug($this->mailTemplate);}
		} else {
			if ($this->debug) {t3lib_div::debug('No template code found!');}
		}
		if (($this->mailTemplate['subject']=='') || ($this->mailTemplate['text']=='')) {
			if (($this->mailTemplate['subject']=='') && ($this->debug)) {
				$this->mailTemplate['subject'] = $this->extKey.' - WARNING/DEBUG - No subject in the template found for header '.$this->headertext[$headertype];
			}
			if (($this->mailTemplate['text']=='') && ($this->debug)) {
				$this->mailTemplate['text'] = $this->extKey.' - WARNING/DEBUG - No bodytext in the template found for header '.$this->headertext[$headertype];
			}
			return False;
		} else {
			return True;
		}
	}


	/**
	 * Generates and sends email to the given email or the admins adress
	 *
	 * @param	string		The requested url
	 * @param	string		The replaced url
	 * @param	string		The header type
	 * @return	void
	 */
	function sendMail($request_url,$replace_url,$headertype) {
		$timestamp = time();

		if ($this->extConf['sender_mail']=='') {
			$sender = $GLOBALS['TYPO3_CONF_VARS']['BE']['warning_email_addr'];
		} elseif ($this->extConf['sender_name']=='') {
			$sender = $this->extConf['sender_mail'];
		} else {
			$sender = $this->extConf['sender_name'].' <'.$this->extConf['sender_mail'].'>';
		}
		if ($this->extConf['recipient_mail']=='') {
			$recipient = $GLOBALS['TYPO3_CONF_VARS']['BE']['warning_email_addr'];
		} elseif ($this->extConf['recipient_name']=='') {
			$recipient = $this->extConf['recipient_mail'];
		} else {
			$recipient = $this->extConf['recipient_name'].' <'.$this->extConf['recipient_mail'].'>';
		}

		$templatefound = $this->getMailTemplate($headertype);
		if (($templatefound) || ($this->debug)) {
			$markerArray = array();
			$markerArray['###request###'] = $this->server_name.$request_url;
			$markerArray['###replace###'] = $this->server_name.$replace_url;
			$markerArray['###send_header###'] = $this->headertext[$headertype];
			$markerArray['###date###'] = date("d.m.Y",$timestamp);
			$markerArray['###time###'] = date("H:i:s",$timestamp);
			$markerArray['###referer###'] = addslashes($_SERVER['HTTP_REFERER']);
			$markerArray['###remote_browser###'] = $_SERVER['HTTP_USER_AGENT'];
			$markerArray['###remote_ip###'] = $_SERVER['REMOTE_ADDR'];
			$markerArray['###remote_name###'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			if ($this->debug) {t3lib_div::debug($markerArray);}

			$subject = $this->ref->cObj->substituteMarkerArray($this->mailTemplate['subject'], $markerArray);
			$text = $this->ref->cObj->substituteMarkerArray($this->mailTemplate['text'], $markerArray);

			$extra = "From: ".$sender."\n";
			$extra .= "X-Priority: 3\n";

			if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['forceReturnPath'] = '1') {
				mail($recipient, $subject, $text, $extra, '-f'.$GLOBALS['TYPO3_CONF_VARS']['BE']['warning_email_addr']);
			} else {
				mail($recipient, $subject, $text, $extra);
			}
		}
	}

	/**
	 * Generates an URL throw an FE Object
	 *
	 * @param	integer		page uid
	 * @param	array		additionaly parameters
	 * @return	string		URL
	 */
	function get_page_url($pid,$addParams=array()){
		global $TYPO3_CONF_VARS;

		require_once(PATH_t3lib.'class.t3lib_timetrack.php');
		if(!is_object($GLOBALS['TT'])){
			$GLOBALS['TT'] = new t3lib_timeTrack;
			$GLOBALS['TT']->start();
		}

		require_once(PATH_t3lib.'class.t3lib_page.php');
		require_once(PATH_t3lib.'class.t3lib_userauth.php');
		require_once(PATH_tslib.'class.tslib_feuserauth.php');
		require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
		require_once(PATH_t3lib.'class.t3lib_cs.php');

		$temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
		$GLOBALS['TSFE'] = new $temp_TSFEclassName(
			$TYPO3_CONF_VARS,
			$pid,
			0,
			t3lib_div::_GP('no_cache'),
			t3lib_div::_GP('cHash'),
			t3lib_div::_GP('jumpurl'),
			t3lib_div::_GP('MP'),
			t3lib_div::_GP('RDCT')
		);

		#debug($GLOBALS['TSFE']);
		$GLOBALS['TSFE']->id=$pid;
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->getPageAndRootline();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();

		$page = $GLOBALS['TSFE']->sys_page->getPage($pid);
		$url =  $GLOBALS['TSFE']->tmpl->linkData($page,"self",0,'',$overrideArray='',$addParams,$typeOverride='');
		return $url['totalURL'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/redirectlog/pi1/class.tx_redirectlog_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/redirectlog/pi1/class.tx_redirectlog_pi1.php']);
}

?>