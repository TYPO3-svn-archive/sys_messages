<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2004 Christian Jul Jensen <christian(at)jul(dot)net>
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
	* @author Christian Jul Jensen <christian(at)jul(dot)net>
	*/
	 
	 
	class tx_sysworkflows_definition {


		function getWorkflowTypes(&$BE_USER) {
			$wfTypes = array();
				if ($BE_USER->isAdmin()) {
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'sys_workflows', 'sys_workflows.pid=0', '', 'sys_workflows.title');
				} else {
					$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
					'sys_workflows.*',
						'sys_workflows',
						"sys_workflows_algr_mm",
						'be_groups',
						"AND be_groups.uid IN (".($BE_USER->groupList?$BE_USER->groupList:0).")
						AND sys_workflows.pid=0
						AND sys_workflows.hidden=0",
						'sys_workflows.uid',
						'sys_workflows.title' );
				}
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$wfTypes['wf_'.$row['uid']] = $row['title'];
				}
				return $wfTypes;
		}

		function anyUserWorkFlows($table) {
			$count = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->queryUserWorkFlows($table,'count(*)'));
			return $count['count(*)']?true:false;
		}
		
		function getUserWorkFlows($table) {
			$res = $this->queryUserWorkFlows($table,'uid,title,tablename,tablename_ver,tablename_del,tablename_move');
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$tmpRow['uid'] = $row['uid'];
				$tmpRow['title'] = $row['title'];
				if('pages'==$table) {
					$tmpRow['new inside'] = t3lib_div::inList($row['tablename'],$table);
				}
				$tmpRow['new after'] = t3lib_div::inList($row['tablename'],$table);
				$tmpRow['version'] = t3lib_div::inList($row['tablename_ver'],$table);
				$tmpRow['delete'] = t3lib_div::inList($row['tablename_del'],$table);
				$tmpRow['move'] = t3lib_div::inList($row['tablename_move'],$table);
				$rows[] = $tmpRow;
				unset($tmpRow);
			}
			return $rows;
		}

		function getWorkFlow($uid,$checkTable='',$checkAction='',$checkUser='create') {
#			debug(debug_backtrace(),'debug_backtrace()',__LINE__,__FILE__);//julle
			$GLOBALS['TYPO3_DB']->debugOutput = 1;
			$res = $this->queryUserWorkFlows($checkTable,'*',$uid,$checkAction,$checkUser);
#			debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery,'$GLOBALS[\'TYPO3_DB\']->debug_lastBuiltQuery',__LINE__,__FILE__);//julle

			return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}

		function queryUserWorkFlows($table='',$fields='*',$uid='',$checkAction='',$checkUser='create') {
			if($table) {
				if(!$checkAction || substr(trim($checkAction),0,3)=='new') {
					$tableChecks[] = $GLOBALS['TYPO3_DB']->listQuery('tablename',$table,'sys_workflows');
				}
				if(!$checkAction || $checkAction=='version') {
					$tableChecks[] = $GLOBALS['TYPO3_DB']->listQuery('tablename_ver',$table,'sys_workflows');
				}
				if(!$checkAction || $checkAction=='delete') {
					$tableChecks[] = $GLOBALS['TYPO3_DB']->listQuery('tablename_del',$table,'sys_workflows');
				}
				if(!$checkAction || $checkAction=='move') {
					$tableChecks[] = $GLOBALS['TYPO3_DB']->listQuery('tablename_move',$table,'sys_workflows');
				}
				if(is_array($tableChecks)) {
					$extraClauses .= ' AND ('.implode(' OR ',$tableChecks).')';
				}
			}
			if($uid) {
				$extraClauses = ' AND sys_workflows.uid='.$uid;
			}

			if(!$checkUser || $GLOBALS['BE_USER']->isAdmin()) {
				return $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, 'sys_workflows', 'sys_workflows.pid=0 AND sys_workflows.hidden=0'.$extraClauses, '', 'sys_workflows.title');
			} else {
				$checkFields = array('create' => 'allowed_groups', 'edit' => 'target_groups', 'review' => 'sys_workflows_rvuser_mm', 'publish' => 'sys_workflows_pubuser_mm');
				$checkField = $checkFields[$checkUser];
				if(substr($checkField,-6)=='groups') {
					$groups = explode(',',$GLOBALS['BE_USER']->groupList?$GLOBALS['BE_USER']->groupList:0);
					foreach($groups as $group) {
						$access_clause[] = $GLOBALS['TYPO3_DB']->listQuery($checkField,$group,'sys_workflows');
					}
					return $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							 $fields,
							 'sys_workflows',
							 '('.implode(' OR ',$access_clause).') '.'AND sys_workflows.pid=0	AND sys_workflows.hidden=0'.$extraClauses,
							 '', 
							 'sys_workflows.title'
							 );
			
				} else {
#					$access_clause = $GLOBALS['TYPO3_DB']->listQuery($checkField,$GLOBALS['BE_USER']->user['uid'],'sys_workflows');
				return $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
							 $fields,
							 'sys_workflows',
							 $checkField,
							 'be_users',
							 ' AND be_users.uid='.$GLOBALS['BE_USER']->user['uid'].' AND sys_workflows.pid=0	AND sys_workflows.hidden=0'.$extraClauses,
							 '', 
							 'sys_workflows.title'
							 );
			

				}
			}
		}
	}
	
//load XCLASS?
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sys_workflows/class.tx_sysworkflows_definition.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sys_workflows/class.tx_sysworkflows_definition.php']);
	}
	 
?>
