<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 1999-2004 Kasper Skaarhoj (kasper@typo3.com)
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
	* @author Kasper Skårhøj <kasperYYYY@typo3.com>
	* @author Christian Jul Jensen <christian(at)jul(dot)net>
	*					Revritten for the TYPO3 3.8.0 revision of the taskcenter
	*/
	class tx_taskcenterrootlist extends mod_user_task {
		 
		/**
		* Creates the configuration for the tab menu in the left
		*
		* @return array/void  proper configuration for the menu or nothing if the user is not admin
		*/
		function overview_main() {
			if ($this->BE_USER->isAdmin()) {
				return $this->mkMenuConfig($this->headLInk('tx_taskcenterrootlist'));
			}
		}
		 
		/**
		* Creates the content for the right side of the taskcenter when module is selected.
		*
		* @return string  (HTML)
		*/
		function main() {
			return htmlspecialchars($this->headLInk('tx_taskcenterrootlist', 1)). '<br />'. $this->urlInIframe($this->backPath.'db_list.php?id=0',1);
		}
	}
	 
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/taskcenter_rootlist/class.tx_taskcenterrootlist.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/taskcenter_rootlist/class.tx_taskcenterrootlist.php']);
	}
?>
