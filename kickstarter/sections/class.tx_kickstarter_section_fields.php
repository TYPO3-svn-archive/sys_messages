<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2001-2004 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * @author	Kasper Sk�rh�j <kasperYYYY@typo3.com>
 */

require_once(t3lib_extMgm::extPath("kickstarter")."class.tx_kickstarter_sectionbase.php");
 
class tx_kickstarter_section_fields extends tx_kickstarter_sectionbase {
  var $sectionID = 'fields';

	/**
	 * Renders the form in the kickstarter; this was add_cat_fields()
	 */
	function render_wizard() {
		$lines=array();

		$action = explode(":",$this->wizard->modData["wizAction"]);
		if ($action[0]=="edit")	{
			$this->regNewEntry($this->sectionID,$action[1]);
			$lines = $this->catHeaderLines($lines,$this->sectionID,$this->wizard->options[$this->sectionID],"&nbsp;",$action[1]);
			$piConf = $this->wizArray[$this->sectionID][$action[1]];
			$ffPrefix='['.$this->sectionID.']['.$action[1].']';

		}


				// Header field
			$optValues = array(
				"tt_content" => "Content (tt_content)",
				"fe_users" => "Frontend Users (fe_users)",
				"fe_groups" => "Frontend Groups (fe_groups)",
				"be_users" => "Backend Users (be_users)",
				"be_groups" => "Backend Groups (be_groups)",
				"tt_news" => "News (tt_news)",
				"tt_address" => "Address (tt_address)",
				"pages" => "Pages (pages)",
			);

			foreach($GLOBALS['TCA'] as $tablename => $tableTCA) {
				if(!$optValues[$tablename]) {
					$optValues[$tablename] = $GLOBALS['LANG']->sL($tableTCA['ctrl']['title']).' ('.$tablename.')';
				}
			}

			$subContent = "<strong>Which table:<BR></strong>".
					$this->renderSelectBox($ffPrefix."[which_table]",$piConf["which_table"],$optValues).
					$this->whatIsThis("Select the table which should be extended with these extra fields.");
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).
				'<input type="hidden" name="'.$this->piFieldName("wizArray_upd").$ffPrefix.'[title]" value="'.($piConf["which_table"]?$optValues[$piConf["which_table"]]:"").'"></td></tr>';





				// PRESETS:
			$selPresetBox=$this->presetBox($piConf["fields"]);

				// FIelds
			$c=array(0);
			$this->usedNames=array();
			if (is_array($piConf["fields"]))	{
				$piConf["fields"] = $this->cleanFieldsAndDoCommands($piConf["fields"],$this->sectionID,$action[1]);

					// Do it for real...
				reset($piConf["fields"]);
				while(list($k,$v)=each($piConf["fields"]))	{
					$c[]=$k;
					$subContent=$this->renderField($ffPrefix."[fields][".$k."]",$v);
					$lines[]='<tr'.$this->bgCol(2).'><td>'.$this->fw("<strong>FIELD:</strong> <em>".$v["fieldname"]."</em>").'</td></tr>';
					$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
				}
			}


				// New field:
			$k=max($c)+1;
			$v=array();
			$lines[]='<tr'.$this->bgCol(2).'><td>'.$this->fw("<strong>NEW FIELD:</strong>").'</td></tr>';
			$subContent=$this->renderField($ffPrefix."[fields][".$k."]",$v,1);
			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';


			$lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw("<BR><BR>Load preset fields: <BR>".$selPresetBox).'</td></tr>';

		/* HOOK: Place a hook here, so additional output can be integrated */
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_fields'])) {
		  foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter']['add_cat_fields'] as $_funcRef) {
		    $lines = t3lib_div::callUserFunction($_funcRef, $lines, $this);
		  }
		}

		$content = '<table border=0 cellpadding=2 cellspacing=2>'.implode("",$lines).'</table>';
		return $content;
	}




	function presetBox(&$piConfFields)	{
		$_PRESETS = $this->wizard->modData["_PRESET"];

		$optValues = array();

		/* Static Presets from DB-Table are disabled. Just leave the code in here for possible future use */
		//		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'kickstarter_static_presets', '');
		//		while($presetRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
		//			$optValues[] = '<option value="'.htmlspecialchars($presetRow["fieldname"]).'">'.htmlspecialchars($presetRow["title"]." (".$presetRow["fieldname"].", type: ".$presetRow["type"].")").'</option>';
		//			if (is_array($_PRESETS) && in_array($presetRow["fieldname"],$_PRESETS))	{
		//				if (!is_array($piConfFields))	$piConfFields=array();
		//				$piConfFields[] = unserialize($presetRow["appdata"]);
		//			}
		//		}

			// Session presets:
		$ses_optValues=array();
		$sesdat = $GLOBALS["BE_USER"]->getSessionData("kickstarter");
		if (is_array($sesdat["presets"]))	{
			foreach($sesdat["presets"] as $kk1=>$vv1)	{
				if (is_array($vv1))	{
					foreach($vv1 as $kk2=>$vv2)	{
						$ses_optValues[]='<option value="'.htmlspecialchars($kk1.".".$vv2["fieldname"]).'">'.htmlspecialchars($kk1.": ".$vv2["title"]." (".$vv2["fieldname"].", type: ".$vv2["type"].")").'</option>';
						if (is_array($_PRESETS) && in_array($kk1.".".$vv2["fieldname"],$_PRESETS))	{
							if (!is_array($piConfFields))	$piConfFields=array();
							$piConfFields[] = $vv2;
						}
					}
				}
			}
		}
		if (count($ses_optValues))	{
			$optValues = array_merge($optValues,count($optValues)?array('<option value=""></option>'):array(),array('<option value="">__Fields picked up in this session__:</option>'),$ses_optValues);
		}
		if (count($optValues))		$selPresetBox = '<select name="'.$this->piFieldName("_PRESET").'[]" size='.t3lib_div::intInRange(count($optValues),1,10).' multiple>'.implode("",$optValues).'</select>';
		return $selPresetBox;
	}
	function cleanFieldsAndDoCommands($fConf,$catID,$action)	{
		$newFConf=array();
		$downFlag=0;
		foreach($fConf as $k=>$v)	{
			if ($v["type"] && trim($v["fieldname"]))	{
				$v["fieldname"] = $this->cleanUpFieldName($v["fieldname"]);

				if (!$v["_DELETE"])	{
					$newFConf[$k]=$v;
					if (t3lib_div::_GP($this->varPrefix.'_CMD_'.$v["fieldname"].'_UP_x') || $downFlag)	{
						if (count($newFConf)>=2)	{
							$lastKeys = array_slice(array_keys($newFConf),-2);

							$buffer = Array();
							$buffer[$lastKeys[1]] = $newFConf[$lastKeys[1]];
							$buffer[$lastKeys[0]] = $newFConf[$lastKeys[0]];

							unset($newFConf[$lastKeys[0]]);
							unset($newFConf[$lastKeys[1]]);

							$newFConf[$lastKeys[1]] = $buffer[$lastKeys[1]];
							$newFConf[$lastKeys[0]] = $buffer[$lastKeys[0]];
						}
						$downFlag=0;
					} elseif (t3lib_div::_GP($this->varPrefix.'_CMD_'.$v["fieldname"].'_DOWN_x'))	{
						$downFlag=1;
					}
				}

					// PRESET:
				//				if (t3lib_div::_GP($this->varPrefix.'_CMD_'.$v["fieldname"].'_SAVE_x'))	{
				//					$datArr=Array(
				//						"fieldname" => $v["fieldname"],
				//						"title" => $v["title"],
// 						"type" => $v["type"],
// 						"appdata" => serialize($v),
// 						"tstamp" => time()
// 					);

// 					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('fieldname', 'kickstarter_static_presets', 'fieldname="'.$GLOBALS['TYPO3_DB']->quoteStr($v['fieldname'], 'kickstarter_static_presets').'"');
// 					if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) || $v["_DELETE"])	{
// 						if ($v["_DELETE"])	{
// 							$GLOBALS['TYPO3_DB']->exec_DELETEquery('kickstarter_static_presets', 'fieldname="'.$GLOBALS['TYPO3_DB']->quoteStr($v['fieldname'], 'kickstarter_static_presets').'"');
// 						} else {
// 							$GLOBALS['TYPO3_DB']->exec_UPDATEquery('kickstarter_static_presets', 'fieldname="'.$GLOBALS['TYPO3_DB']->quoteStr($v['fieldname'], 'kickstarter_static_presets').'"', $datArr);
// 						}
// 					} else {
// 						$GLOBALS['TYPO3_DB']->exec_INSERTquery("kickstarter_static_presets", $datArr);
// 					}
// 				}
			} else {
			  //				unset($this->wizArray[$catID][$action]["fields"][$k]);
			  //				unset($fConf[$k]);
			}
		}
		//		debug($newFConf);
		$this->wizArray[$catID][$action]["fields"] = $newFConf;
		$sesdat = $GLOBALS["BE_USER"]->getSessionData("kickstarter");
		$sesdat["presets"][$this->extKey."-".$catID."-".$action]=$newFConf;
		$GLOBALS["BE_USER"]->setAndSaveSessionData("kickstarter",$sesdat);

#debug($newFConf);
		return $newFConf;
	}


	function returnName($extKey,$type,$suffix="")	{
		if (substr($extKey,0,5)=="user_")	{
			$extKey = substr($extKey,5);
			switch($type)	{
				case "class":
					return "user_".str_replace("_","",$extKey).($suffix?"_".$suffix:"");
				break;
				case "tables":
				case "fields":
				case "fields":
					return "user_".str_replace("_","",$extKey).($suffix?"_".$suffix:"");
				break;
				case "module":
					return "u".str_replace("_","",$extKey).$suffix;
				break;
			}
		} else {
			switch($type)	{
				case "class":
					return "tx_".str_replace("_","",$extKey).($suffix?"_".$suffix:"");
				break;
				case "tables":
				case "fields":
				case "fields":
					return "tx_".str_replace("_","",$extKey).($suffix?"_".$suffix:"");
				break;
				case "module":
					return "tx".str_replace("_","",$extKey).$suffix;
				break;
			}
		}
	}


	function addOtherExtensionTables($optValues)	{
		if (is_array($this->wizArray["tables"]))	{
			foreach($this->wizArray["tables"] as $k=>$info)	{
				if (trim($info["tablename"]))	{
					$tableName = $this->returnName($this->extKey,"tables",trim($info["tablename"]));
					$optValues[$tableName]="Extension table: ".$info["title"]." (".$tableName.")";
				}
			}
		}
		return $optValues;
	}
	function cleanUpFieldName($str)	{
		$fieldName = ereg_replace("[^[:alnum:]_]","",strtolower($str));
		if (!$fieldName || t3lib_div::inList($this->reservedTypo3Fields.",".$this->mysql_reservedFields,$fieldName) || in_array($fieldName,$this->usedNames))	{
			$fieldName.=($fieldName?"_":"").t3lib_div::shortmd5(microtime());
		}
		$this->usedNames[]=$fieldName;
		return $fieldName;
	}


	function renderField($prefix,$fConf,$dontRemove=0)	{
		$onCP = $this->getOnChangeParts($prefix."[fieldname]");
		$fieldName = $this->renderStringBox($prefix."[fieldname]",$fConf["fieldname"]).
			(!$dontRemove?" (Remove:".$this->renderCheckBox($prefix."[_DELETE]",0).')'.
				'<input type="image" hspace=2 src="'.$this->siteBackPath.TYPO3_mainDir.'gfx/pil2up.gif" name="'.$this->varPrefix.'_CMD_'.$fConf["fieldname"].'_UP" onClick="'.$onCP[1].'">'.
				'<input type="image" hspace=2 src="'.$this->siteBackPath.TYPO3_mainDir.'gfx/pil2down.gif" name="'.$this->varPrefix.'_CMD_'.$fConf["fieldname"].'_DOWN" onClick="'.$onCP[1].'">'.
				'<input type="image" hspace=2 src="'.$this->siteBackPath.TYPO3_mainDir.'gfx/savesnapshot.gif" name="'.$this->varPrefix.'_CMD_'.$fConf["fieldname"].'_SAVE" onClick="'.$onCP[1].'" title="Save this field setting as a preset.">':'');

		$fieldTitle = ((string)$fConf["type"] != 'passthrough') ? $this->renderStringBox_lang("title",$prefix,$fConf) : '';
		$typeCfg = "";

			// Sorting
		$optValues = array(
			"" => "",
			"input" => "String input",
			"input+" => "String input, advanced",
			"textarea" => "Text area",
			"textarea_rte" => "Text area with RTE",
			"textarea_nowrap" => "Text area, No wrapping",
			"check" => "Checkbox, single",
			"check_4" => "Checkbox, 4 boxes in a row",
			"check_10" => "Checkbox, 10 boxes in two rows (max)",
			"link" => "Link",
			"date" => "Date",
			"datetime" => "Date and time",
			"integer" => "Integer, 10-1000",
			"select" => "Selectorbox",
			"radio" => "Radio buttons",
			"rel" => "Database relation",
			"files" => "Files",
			"none" => "Not editable, only displayed",
			"passthrough" => "[Passthrough]",
		);
		$typeCfg.=$this->renderSelectBox($prefix."[type]",$fConf["type"],$optValues);
		$typeCfg.=$this->renderCheckBox($prefix."[excludeField]",isset($fConf["excludeField"])?$fConf["excludeField"]:1)." Is Exclude-field ".$this->whatIsThis("If a field is marked 'Exclude-field', users can edit it ONLY if the field is specifically listed in one of the backend user groups of the user.\nIn other words, if a field is marked 'Exclude-field' you can control which users can edit it and which cannot.")."<BR>";

		$fDetails="";
		switch((string)$fConf["type"])	{
			case "input+":
				$typeCfg.=$this->resImg("t_input.png",'','');

				$fDetails.=$this->renderStringBox($prefix."[conf_size]",$fConf["conf_size"],50)." Field width (5-48 relative, 30 default)<BR>";
				$fDetails.=$this->renderStringBox($prefix."[conf_max]",$fConf["conf_max"],50)." Max characters<BR>";
				$fDetails.=$this->renderCheckBox($prefix."[conf_required]",$fConf["conf_required"])."Required<BR>";
				$fDetails.=$this->resImg("t_input_required.png",'hspace=20','','<BR><BR>');

				$fDetails.=$this->renderCheckBox($prefix."[conf_varchar]",$fConf["conf_varchar"])."Create VARCHAR, not TINYTEXT field (if not forced INT)<BR>";

				$fDetails.=$this->renderCheckBox($prefix."[conf_check]",$fConf["conf_check"])."Apply checkbox<BR>";
				$fDetails.=$this->resImg("t_input_check.png",'hspace=20','','<BR><BR>');

				$optValues = array(
					"" => "",
					"date" => "Date (day-month-year)",
					"time" => "Time (hours, minutes)",
					"timesec" => "Time + seconds",
					"datetime" => "Date + Time",
					"year" => "Year",
					"int" => "Integer",
					"int+" => "Integer 0-1000",
					"double2" => "Floating point, x.xx",
					"alphanum" => "Alphanumeric only",
					"upper" => "Upper case",
					"lower" => "Lower case",
				);
				$fDetails.="<BR>Evaluate value to:<BR>".$this->renderSelectBox($prefix."[conf_eval]",$fConf["conf_eval"],$optValues)."<BR>";
				$fDetails.=$this->renderCheckBox($prefix."[conf_stripspace]",$fConf["conf_stripspace"])."Strip space<BR>";
				$fDetails.=$this->renderCheckBox($prefix."[conf_pass]",$fConf["conf_pass"])."Is password field<BR>";
				$fDetails.=$this->resImg("t_input_password.png",'hspace=20','','<BR><BR>');

				$fDetails.="<BR>";
				$fDetails.=$this->renderRadioBox($prefix."[conf_unique]",$fConf["conf_unique"],"G")."Unique in whole database<BR>";
				$fDetails.=$this->renderRadioBox($prefix."[conf_unique]",$fConf["conf_unique"],"L")."Unique inside parent page<BR>";
				$fDetails.=$this->renderRadioBox($prefix."[conf_unique]",$fConf["conf_unique"],"")."Not unique (default)<BR>";
				$fDetails.="<BR>";
				$fDetails.=$this->renderCheckBox($prefix."[conf_wiz_color]",$fConf["conf_wiz_color"])."Add colorpicker wizard<BR>";
				$fDetails.=$this->resImg("t_input_colorwiz.png",'hspace=20','','<BR><BR>');
				$fDetails.=$this->renderCheckBox($prefix."[conf_wiz_link]",$fConf["conf_wiz_link"])."Add link wizard<BR>";
				$fDetails.=$this->resImg("t_input_link2.png",'hspace=20','','<BR><BR>');
			break;
			case "input":
				$typeCfg.=$this->resImg("t_input.png",'','');

				$fDetails.=$this->renderStringBox($prefix."[conf_size]",$fConf["conf_size"],50)." Field width (5-48 relative, 30 default)<BR>";
				$fDetails.=$this->renderStringBox($prefix."[conf_max]",$fConf["conf_max"],50)." Max characters<BR>";
				$fDetails.=$this->renderCheckBox($prefix."[conf_required]",$fConf["conf_required"])."Required<BR>";
				$fDetails.=$this->resImg("t_input_required.png",'hspace=20','','<BR><BR>');

				$fDetails.=$this->renderCheckBox($prefix."[conf_varchar]",$fConf["conf_varchar"])."Create VARCHAR, not TINYTEXT field<BR>";
			break;
			case "textarea":
			case "textarea_nowrap":
				$typeCfg.=$this->resImg("t_textarea.png",'','');

				$fDetails.=$this->renderStringBox($prefix."[conf_cols]",$fConf["conf_cols"],50)." Textarea width (5-48 relative, 30 default)<BR>";
				$fDetails.=$this->renderStringBox($prefix."[conf_rows]",$fConf["conf_rows"],50)." Number of rows (height)<BR>";
				$fDetails.="<BR>";
				$fDetails.=$this->renderCheckBox($prefix."[conf_wiz_example]",$fConf["conf_wiz_example"])."Add wizard example<BR>";
				$fDetails.=$this->resImg("t_textarea_wiz.png",'hspace=20','','<BR><BR>');
			break;
			case "textarea_rte":
				$typeCfg.=$this->resImg($fConf["conf_rte"]!="tt_content"?"t_rte.png":"t_rte2.png",'','');

				$optValues = array(
					"tt_content" => "Transform content like the Content Element 'Bodytext' field (default/old)",
					"basic" => "Typical basic setup (new 'Bodytext' field based on CSS stylesheets)",
					"moderate" => "Moderate transform of images and links",
					"none" => "No transformation at all",
					"custom" => "Custom"
				);
				$fDetails.="<BR>Rich Text Editor Mode:<BR>".$this->renderSelectBox($prefix."[conf_rte]",$fConf["conf_rte"],$optValues)."<BR>";
				if ((string)$fConf["conf_rte"]=="custom")	{
					$optValues = array(
						"cut" => array("Cut button"),
						"copy" => array("Copy button"),
						"paste" => array("Paste button"),
						"formatblock" => array("Paragraph formatting","<DIV>, <P>"),
						"class" => array("Character formatting","<SPAN>)"),
						"fontstyle" => array("Font face","<FONT face=>)"),
						"fontsize" => array("Font size","<FONT size=>)"),
						"textcolor" => array("Font color","<FONT color=>"),
						"bold" => array("Bold","<STRONG>, <B>"),
						"italic" => array("italic","<EM>, <I>"),
						"underline" => array("Underline","<U>"),
						"left" => array("Left align","<DIV>, <P>"),
						"center" => array("Center align","<DIV>, <P>"),
						"right" => array("Right align","<DIV>, <P>"),
						"orderedlist" => array("Ordered bulletlist","<OL>, <LI>"),
						"unorderedlist" => array("Unordered bulletlist","<UL>, <LI>"),
						"outdent" => array("Outdent block","<BLOCKQUOTE>"),
						"indent" => array("Indent block","<BLOCKQUOTE>"),
						"link" => array("Link","<A>"),
						"table" => array("Table","<TABLE>, <TR>, <TD>"),
						"image" => array("Image","<IMG>"),
						"line" => array("Ruler","<HR>"),
						"user" => array("User defined",""),
						"chMode" => array("Edit source?","")
					);
					$subLines=array();
					$subLines[]='<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><strong>'.$this->fw("Button name:").'</strong></td>
						<td><strong>'.$this->fw("Tags allowed:").'</strong></td>
					</tr>';
					foreach($optValues as $kk=>$vv)	{
						$subLines[]='<tr>
							<td>'.$this->renderCheckBox($prefix."[conf_rte_b_".$kk."]",$fConf["conf_rte_b_".$kk]).'</td>
							<td>'.$this->resIcon($kk.".png").'</td>
							<td>'.$this->fw($vv[0]).'</td>
							<td>'.$this->fw(htmlspecialchars($vv[1])).'</td>
						</tr>';
					}
					$fDetails.='<table border=0 cellpadding=2 cellspacing=2>'.implode("",$subLines).'</table><BR>';

					$fDetails.="<BR><strong>Define specific colors:</strong><BR>
						<em>Notice: Use only HEX-values for colors ('blue' should be #0000ff etc.)</em><BR>";
					for($a=1;$a<4;$a++)	{
						$fDetails.="Color #".$a.": ".$this->renderStringBox($prefix."[conf_rte_color".$a."]",$fConf["conf_rte_color".$a],70)."<BR>";
					}
					$fDetails.=$this->resImg("t_rte_color.png",'','','<BR><BR>');

					$fDetails.=$this->renderCheckBox($prefix."[conf_rte_removecolorpicker]",$fConf["conf_rte_removecolorpicker"])."Hide colorpicker<BR>";
					$fDetails.=$this->resImg("t_rte_colorpicker.png",'hspace=20','','<BR><BR>');

					$fDetails.="<BR><strong>Define classes:</strong><BR>";
					for($a=1;$a<7;$a++)	{
						$fDetails.="Class Title:".$this->renderStringBox($prefix."[conf_rte_class".$a."]",$fConf["conf_rte_class".$a],100).
							"<BR>CSS Style: {".$this->renderStringBox($prefix."[conf_rte_class".$a."_style]",$fConf["conf_rte_class".$a."_style"],250)."}".
						"<BR>";
					}
					$fDetails.=$this->resImg("t_rte_class.png",'','','<BR><BR>');

#					$fDetails.=$this->renderCheckBox($prefix."[conf_rte_removePdefaults]",$fConf["conf_rte_removePdefaults"])."<BR>";
					$optValues = array(
						"0" => "",
						"1" => "Hide Hx and PRE from Paragraph selector.",
						"H2H3" => "Hide all, but H2,H3,P,PRE",
					);
					$fDetails.="<BR>Hide Paragraph Items:<BR>".$this->renderSelectBox($prefix."[conf_rte_removePdefaults]",$fConf["conf_rte_removePdefaults"],$optValues)."<BR>";
					$fDetails.=$this->resImg("t_rte_hideHx.png",'hspace=20','','<BR><BR>');

					$fDetails.="<BR><strong>Misc:</strong><BR>";
//					$fDetails.=$this->renderCheckBox($prefix."[conf_rte_custom_php_processing]",$fConf["conf_rte_custom_php_processing"])."Custom PHP processing of content<BR>";
					$fDetails.=$this->renderCheckBox($prefix."[conf_rte_div_to_p]",isset($fConf["conf_rte_div_to_p"])?$fConf["conf_rte_div_to_p"]:1).htmlspecialchars("Convert all <DIV> to <P>")."<BR>";
				}

				$fDetails.="<BR>";
				$fDetails.=$this->renderCheckBox($prefix."[conf_rte_fullscreen]",isset($fConf["conf_rte_fullscreen"])?$fConf["conf_rte_fullscreen"]:1)."Fullscreen link<BR>";
				$fDetails.=$this->resImg("t_rte_fullscreen.png",'hspace=20','','<BR><BR>');

				if (t3lib_div::inList("moderate,basic,custom",$fConf["conf_rte"]))	{
					$fDetails.="<BR>";
					$fDetails.=$this->renderCheckBox($prefix."[conf_rte_separateStorageForImages]",isset($fConf["conf_rte_separateStorageForImages"])?$fConf["conf_rte_separateStorageForImages"]:1)."Storage of images in separate folder (in uploads/[extfolder]/rte/)<BR>";
				}
				if (t3lib_div::inList("moderate,custom",$fConf["conf_rte"]))	{
					$fDetails.="<BR>";
					$fDetails.=$this->renderCheckBox($prefix."[conf_mode_cssOrNot]",isset($fConf["conf_mode_cssOrNot"])?$fConf["conf_mode_cssOrNot"]:1)."Use 'ts_css' transformation instead of 'ts_images-ts-reglinks'<BR>";
				}
			break;
			case "check":
				$typeCfg.=$this->resImg("t_input_link.png",'','');
				$fDetails.=$this->renderCheckBox($prefix."[conf_check_default]",$fConf["conf_check_default"])."Checked by default<BR>";
			break;
			case "select":
			case "radio":
				if ($fConf["type"]=="radio")	{
					$typeCfg.=$this->resImg("t_radio.png",'','');
				} else	{
					$typeCfg.=$this->resImg("t_sel.png",'','');
				}
				$fDetails.="<BR><strong>Define values:</strong><BR>";
				$subLines=array();
					$subLines[]='<tr>
						<td valign=top>'.$this->fw("Item label:").'</td>
						<td valign=top>'.$this->fw("Item value:").'</td>
					</tr>';
				$nItems = $fConf["conf_select_items"] = isset($fConf["conf_select_items"])?t3lib_div::intInRange(intval($fConf["conf_select_items"]),0,20):4;
				for($a=0;$a<$nItems;$a++)	{
					$subLines[]='<tr>
						<td valign=top>'.$this->fw($this->renderStringBox_lang("conf_select_item_".$a,$prefix,$fConf)).'</td>
						<td valign=top>'.$this->fw($this->renderStringBox($prefix."[conf_select_itemvalue_".$a."]",isset($fConf["conf_select_itemvalue_".$a])?$fConf["conf_select_itemvalue_".$a]:$a,50)).'</td>
					</tr>';
				}
				$fDetails.='<table border=0 cellpadding=2 cellspacing=2>'.implode("",$subLines).'</table><BR>';
				$fDetails.=$this->renderStringBox($prefix."[conf_select_items]",$fConf["conf_select_items"],50)." Number of values<BR>";

				if ($fConf["type"]=="select")	{
					$fDetails.=$this->renderCheckBox($prefix."[conf_select_icons]",$fConf["conf_select_icons"])."Add a dummy set of icons<BR>";
					$fDetails.=$this->resImg("t_select_icons.png",'hspace=20','','<BR><BR>');

					$fDetails.=$this->renderStringBox($prefix."[conf_relations]",t3lib_div::intInRange($fConf["conf_relations"],1,1000),50)." Max number of relations<BR>";
					$fDetails.=$this->renderStringBox($prefix."[conf_relations_selsize]",t3lib_div::intInRange($fConf["conf_relations_selsize"],1,50),50)." Size of selector box<BR>";

					$fDetails.=$this->renderCheckBox($prefix."[conf_select_pro]",$fConf["conf_select_pro"])."Add pre-processing with PHP-function<BR>";
				}
			break;
			case "rel":
				if ($fConf["conf_rel_type"]=="group" || !$fConf["conf_rel_type"])	{
					$typeCfg.=$this->resImg("t_rel_group.png",'','');
				} elseif(intval($fConf["conf_relations"])>1)	{
					$typeCfg.=$this->resImg("t_rel_selmulti.png",'','');
				} elseif(intval($fConf["conf_relations_selsize"])>1)	{
					$typeCfg.=$this->resImg("t_rel_selx.png",'','');
				} else {
					$typeCfg.=$this->resImg("t_rel_sel1.png",'','');
				}


				$optValues = array(
					"pages" => "Pages table, (pages)",
					"fe_users" => "Frontend Users, (fe_users)",
					"fe_groups" => "Frontend Usergroups, (fe_groups)",
					"tt_content" => "Content elements, (tt_content)",
					"_CUSTOM" => "Custom table (enter name below)",
					"_ALL" => "All tables allowed!",
				);
				if ($fConf["conf_rel_type"]!="group")	{unset($optValues["_ALL"]);}
				$optValues = $this->addOtherExtensionTables($optValues);
				$fDetails.="<BR>Create relation to table:<BR>".$this->renderSelectBox($prefix."[conf_rel_table]",$fConf["conf_rel_table"],$optValues)."<BR>";
				if ($fConf["conf_rel_table"]=="_CUSTOM")	$fDetails.="Custom table name: ".$this->renderStringBox($prefix."[conf_custom_table_name]",$fConf["conf_custom_table_name"],200)."<BR>";

				$optValues = array(
					"group" => "Field with Element Browser",
					"select" => "Selectorbox, select global",
					"select_cur" => "Selectorbox, select from current page",
					"select_root" => "Selectorbox, select from root page",
					"select_storage" => "Selectorbox, select from storage page",
				);
				$fDetails.="<BR>Type:<BR>".$this->renderSelectBox($prefix."[conf_rel_type]",$fConf["conf_rel_type"]?$fConf["conf_rel_type"]:"group",$optValues)."<BR>";
				if (t3lib_div::intInRange($fConf["conf_relations"],1,1000)==1 && $fConf["conf_rel_type"]!="group")	{
					$fDetails.=$this->renderCheckBox($prefix."[conf_rel_dummyitem]",$fConf["conf_rel_dummyitem"])."Add a blank item to the selector<BR>";
				}

				$fDetails.=$this->renderStringBox($prefix."[conf_relations]",t3lib_div::intInRange($fConf["conf_relations"],1,1000),50)." Max number of relations<BR>";
				$fDetails.=$this->renderStringBox($prefix."[conf_relations_selsize]",t3lib_div::intInRange($fConf["conf_relations_selsize"],1,50),50)." Size of selector box<BR>";
				$fDetails.=$this->renderCheckBox($prefix."[conf_relations_mm]",$fConf["conf_relations_mm"])."True M-M relations (otherwise commalist of values)<BR>";


				if ($fConf["conf_rel_type"]!="group")	{
					$fDetails.="<BR>";
					$fDetails.=$this->renderCheckBox($prefix."[conf_wiz_addrec]",$fConf["conf_wiz_addrec"])."Add 'Add record' link<BR>";
					$fDetails.=$this->renderCheckBox($prefix."[conf_wiz_listrec]",$fConf["conf_wiz_listrec"])."Add 'List records' link<BR>";
					$fDetails.=$this->renderCheckBox($prefix."[conf_wiz_editrec]",$fConf["conf_wiz_editrec"])."Add 'Edit record' link<BR>";
					$fDetails.=$this->resImg("t_rel_wizards.png",'hspace=20','','<BR><BR>');
				}
			break;
			case "files":
				if ($fConf["conf_files_type"]=="images")	{
					$typeCfg.=$this->resImg("t_file_img.png",'','');
				} elseif ($fConf["conf_files_type"]=="webimages")	{
					$typeCfg.=$this->resImg("t_file_web.png",'','');
				} else {
					$typeCfg.=$this->resImg("t_file_all.png",'','');
				}

				$optValues = array(
					"images" => "Imagefiles",
					"webimages" => "Web-imagefiles (gif,jpg,png)",
					"all" => "All files, except php/php3 extensions",
				);
				$fDetails.="<BR>Extensions:<BR>".$this->renderSelectBox($prefix."[conf_files_type]",$fConf["conf_files_type"],$optValues)."<BR>";

				$fDetails.=$this->renderStringBox($prefix."[conf_files]",t3lib_div::intInRange($fConf["conf_files"],1,1000),50)." Max number of files<BR>";
				$fDetails.=$this->renderStringBox($prefix."[conf_max_filesize]",t3lib_div::intInRange($fConf["conf_max_filesize"],1,1000,500),50)." Max filesize allowed (kb)<BR>";
				$fDetails.=$this->renderStringBox($prefix."[conf_files_selsize]",t3lib_div::intInRange($fConf["conf_files_selsize"],1,50),50)." Size of selector box<BR>";
				$fDetails.=$this->resImg("t_file_size.png",'','','<BR><BR>');
//				$fDetails.=$this->renderCheckBox($prefix."[conf_files_mm]",$fConf["conf_files_mm"])."DB relations (very rare choice, normally the commalist is fine enough)<BR>";
				$fDetails.=$this->renderCheckBox($prefix."[conf_files_thumbs]",$fConf["conf_files_thumbs"])."Show thumbnails<BR>";
				$fDetails.=$this->resImg("t_file_thumb.png",'hspace=20','','<BR><BR>');
			break;
			case "integer":
				$typeCfg.=$this->resImg("t_integer.png",'','');
			break;
			case "check_4":
			case "check_10":
				if ((string)$fConf["type"]=="check_4")	{
					$typeCfg.=$this->resImg("t_check4.png",'','');
				} else {
					$typeCfg.=$this->resImg("t_check10.png",'','');
				}
				$nItems= t3lib_div::intInRange($fConf["conf_numberBoxes"],1,10,(string)$fConf["type"]=="check_4"?4:10);
				$fDetails.=$this->renderStringBox($prefix."[conf_numberBoxes]",$nItems,50)." Number of checkboxes<BR>";

				for($a=0;$a<$nItems;$a++)	{
					$fDetails.="<BR>Label ".($a+1).":<BR>".$this->renderStringBox_lang("conf_boxLabel_".$a,$prefix,$fConf);
				}
			break;
			case "date":
				$typeCfg.=$this->resImg("t_date.png",'','');
			break;
			case "datetime":
				$typeCfg.=$this->resImg("t_datetime.png",'','');
			break;
			case "link":
				$typeCfg.=$this->resImg("t_link.png",'','');
			break;
		}

		if ($fConf["type"])	$typeCfg.=$this->textSetup("",$fDetails);

		$content='<table border=0 cellpadding=0 cellspacing=0>
			<tr><td valign=top>'.$this->fw("Field name:").'</td><td valign=top>'.$this->fw($fieldName).'</td></tr>
			<tr><td valign=top>'.$this->fw("Field title:").'</td><td valign=top>'.$this->fw($fieldTitle).'</td></tr>
			<tr><td valign=top>'.$this->fw("Field type:").'</td><td valign=top>'.$this->fw($typeCfg).'</td></tr>
		</table>';
		return $content;
	}




	/**
	 * Renders the extension PHP codee
	 */
	function render_extPart($k,$config,$extKey) {
		$WOP="[fields][".$k."]";
		$tableName=$config["which_table"];
	#	$tableName = $this->returnName($extKey,"fields",$tableName);
#		$prefix = "tx_".str_replace("_","",$extKey)."_";
		$prefix = $this->returnName($extKey,"fields")."_";

		$DBfields=array();
		$columns=array();
		$ctrl=array();
		$enFields=array();

		if (is_array($config["fields"]))	{
			reset($config["fields"]);
			while(list($i,$fConf)=each($config["fields"]))	{
				$fConf["fieldname"] = $prefix.$fConf["fieldname"];
				$this->makeFieldTCA($DBfields,$columns,$fConf,$WOP."[fields][".$i."]",$tableName,$extKey);
			}
		}

		if ($tableName=="tt_address")	$this->EM_CONF_presets["dependencies"][]="tt_address";
		if ($tableName=="tt_news")	$this->EM_CONF_presets["dependencies"][]="tt_news";
		if (t3lib_div::inList("tt_content,fe_users,fe_groups",$tableName))	$this->EM_CONF_presets["dependencies"][]="cms";

		$createTable = $this->wrapBody('
			#
			# Table structure for table \''.$tableName.'\'
			#
			CREATE TABLE '.$tableName.' (
		', ereg_replace(",[[:space:]]*$","",implode(chr(10),$DBfields)), '

			);
		');
		$this->ext_tables_sql[]=chr(10).$createTable.chr(10);


			// Finalize ext_tables.php:
		$this->ext_tables[]=$this->wrapBody('
			$tempColumns = Array (
				', implode(chr(10),$columns)	,'
			);
		');


		list($typeList) = $this->implodeColumns($columns);
		$applyToAll=1;
		if (is_array($this->wizArray["pi"]))	{
			reset($this->wizArray["pi"]);
			while(list(,$fC)=each($this->wizArray["pi"]))	{
				if ($fC["apply_extended"]==$k)	{
					$applyToAll=0;
					$this->_apply_extended_types[$k]=$typeList;
				}
			}
		}
		$this->ext_tables[]=$this->sPS('
			t3lib_div::loadTCA("'.$tableName.'");
			t3lib_extMgm::addTCAcolumns("'.$tableName.'",$tempColumns,1);
			'.($applyToAll?'t3lib_extMgm::addToAllTCAtypes("'.$tableName.'","'.$typeList.'");':'').'
		');
	}





	function implodeColumns($columns)	{
		reset($columns);
		$outems=array();
		$paltems=array();
		$c=0;
		$hiddenFlag=0;
		$titleDivFlag=0;
		while(list($fN)=each($columns))	{
			if (!$hiddenFlag || !t3lib_div::inList("starttime,endtime,fe_group",$fN))	{
				$outTem = array($fN,"","","","");
				$outTem[3] = $this->_typeP[$fN];
				if ($c==0)	$outTem[4]="1-1-1";
				if ($fN=="title")	{
					$outTem[4]="2-2-2";
					$titleDivFlag=1;
				} elseif ($titleDivFlag)	{
					$outTem[4]="3-3-3";
					$titleDivFlag=0;
				}
				if ($fN=="hidden")	{
					$outTem[2]="1";
					$hiddenFlag=1;
				}
				$outems[] = str_replace(",","",str_replace(chr(9),";",trim(str_replace(";","",implode(chr(9),$outTem)))));
				$c++;
			} else {
				$paltems[]=$fN;
			}
		}
		return array(implode(", ",$outems),implode(", ",$paltems));
	}
	function makeFieldTCA(&$DBfields,&$columns,$fConf,$WOP,$table,$extKey)	{
		if (!(string)$fConf["type"])	return;
		$id = $table."_".$fConf["fieldname"];
#debug($fConf);

		$configL=array();
		$t = (string)$fConf["type"];
		switch($t)	{
			case "input":
			case "input+":
				$isString =1;
				$configL[]='"type" => "input",	'.$this->WOPcomment('WOP:'.$WOP.'[type]');
				$configL[]='"size" => "'.t3lib_div::intInRange($fConf["conf_size"],5,48,30).'",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_size]');
				if (intval($fConf["conf_max"]))	$configL[]='"max" => "'.t3lib_div::intInRange($fConf["conf_max"],1,255).'",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_max]');

				$evalItems=array();
				if ($fConf["conf_required"])	{$evalItems[0][] = "required";			$evalItems[1][] = $WOP.'[conf_required]';}

				if ($t=="input+")	{
					$isString = !$fConf["conf_eval"] || t3lib_div::inList("alphanum,upper,lower",$fConf["conf_eval"]);
					if ($fConf["conf_varchar"] && $isString)		{$evalItems[0][] = "trim";			$evalItems[1][] = $WOP.'[conf_varchar]';}
					if ($fConf["conf_eval"]=="int+")	{
						$configL[]='"range" => Array ("lower"=>0,"upper"=>1000),	'.$this->WOPcomment('WOP:'.$WOP.'[conf_eval] = int+ results in a range setting');
						$fConf["conf_eval"]="int";
					}
					if ($fConf["conf_eval"])		{$evalItems[0][] = $fConf["conf_eval"];			$evalItems[1][] = $WOP.'[conf_eval]';}
					if ($fConf["conf_check"])	$configL[]='"checkbox" => "'.($isString?"":"0").'",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_check]');

					if ($fConf["conf_stripspace"])		{$evalItems[0][] = "nospace";			$evalItems[1][] = $WOP.'[conf_stripspace]';}
					if ($fConf["conf_pass"])		{$evalItems[0][] = "password";			$evalItems[1][] = $WOP.'[conf_pass]';}
					if ($fConf["conf_unique"])	{
						if ($fConf["conf_unique"]=="L")		{$evalItems[0][] = "uniqueInPid";			$evalItems[1][] = $WOP.'[conf_unique] = Local (unique in this page (PID))';}
						if ($fConf["conf_unique"]=="G")		{$evalItems[0][] = "unique";			$evalItems[1][] = $WOP.'[conf_unique] = Global (unique in whole database)';}
					}

					$wizards =array();
					if ($fConf["conf_wiz_color"])	{
						$wizards[] = trim($this->sPS('
							'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_color]').'
							"color" => Array(
								"title" => "Color:",
								"type" => "colorbox",
								"dim" => "12x12",
								"tableStyle" => "border:solid 1px black;",
								"script" => "wizard_colorpicker.php",
								"JSopenParams" => "height=300,width=250,status=0,menubar=0,scrollbars=1",
							),
						'));
					}
					if ($fConf["conf_wiz_link"])	{
						$wizards[] = trim($this->sPS('
							'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_link]').'
							"link" => Array(
								"type" => "popup",
								"title" => "Link",
								"icon" => "link_popup.gif",
								"script" => "browse_links.php?mode=wizard",
								"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
							),
						'));
					}
					if (count($wizards))	{
						$configL[]=trim($this->wrapBody('
							"wizards" => Array(
								"_PADDING" => 2,
								',implode(chr(10),$wizards),'
							),
						'));
					}
				} else {
					if ($fConf["conf_varchar"])		{$evalItems[0][] = "trim";			$evalItems[1][] = $WOP.'[conf_varchar]';}
				}

				if (count($evalItems))	$configL[]='"eval" => "'.implode(",",$evalItems[0]).'",	'.$this->WOPcomment('WOP:'.implode(" / ",$evalItems[1]));

				if (!$isString)	{
					$DBfields[] = $fConf["fieldname"]." int(11) DEFAULT '0' NOT NULL,";
				} elseif (!$fConf["conf_varchar"])		{
					$DBfields[] = $fConf["fieldname"]." tinytext NOT NULL,";
				} else {
					$varCharLn = (intval($fConf["conf_max"])?t3lib_div::intInRange($fConf["conf_max"],1,255):255);
					$DBfields[] = $fConf["fieldname"]." ".($varCharLn>$this->charMaxLng?'var':'')."char(".$varCharLn.") DEFAULT '' NOT NULL,";
				}
			break;
			case "link":
				$DBfields[] = $fConf["fieldname"]." tinytext NOT NULL,";
				$configL[]=trim($this->sPS('
					"type" => "input",
					"size" => "15",
					"max" => "255",
					"checkbox" => "",
					"eval" => "trim",
					"wizards" => Array(
						"_PADDING" => 2,
						"link" => Array(
							"type" => "popup",
							"title" => "Link",
							"icon" => "link_popup.gif",
							"script" => "browse_links.php?mode=wizard",
							"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
						)
					)
				'));
			break;
			case "datetime":
			case "date":
				$DBfields[] = $fConf["fieldname"]." int(11) DEFAULT '0' NOT NULL,";
				$configL[]=trim($this->sPS('
					"type" => "input",
					"size" => "'.($t=="datetime"?12:8).'",
					"max" => "20",
					"eval" => "'.$t.'",
					"checkbox" => "0",
					"default" => "0"
				'));
			break;
			case "integer":
				$DBfields[] = $fConf["fieldname"]." int(11) DEFAULT '0' NOT NULL,";
				$configL[]=trim($this->sPS('
					"type" => "input",
					"size" => "4",
					"max" => "4",
					"eval" => "int",
					"checkbox" => "0",
					"range" => Array (
						"upper" => "1000",
						"lower" => "10"
					),
					"default" => 0
				'));
			break;
			case "textarea":
			case "textarea_nowrap":
				$DBfields[] = $fConf["fieldname"]." text NOT NULL,";
				$configL[]='"type" => "text",';
				if ($t=="textarea_nowrap")	{
					$configL[]='"wrap" => "OFF",';
				}
				$configL[]='"cols" => "'.t3lib_div::intInRange($fConf["conf_cols"],5,48,30).'",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_cols]');
				$configL[]='"rows" => "'.t3lib_div::intInRange($fConf["conf_rows"],1,20,5).'",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rows]');
				if ($fConf["conf_wiz_example"])	{
					$wizards =array();
					$wizards[] = trim($this->sPS('
						'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_example]').'
						"example" => Array(
							"title" => "Example Wizard:",
							"type" => "script",
							"notNewRecords" => 1,
							"icon" => t3lib_extMgm::extRelPath("'.$extKey.'")."'.$id.'/wizard_icon.gif",
							"script" => t3lib_extMgm::extRelPath("'.$extKey.'")."'.$id.'/index.php",
						),
					'));

					$cN = $this->returnName($extKey,"class",$id."wiz");
					$this->writeStandardBE_xMod(
						$extKey,
						array("title"=>"Example Wizard title..."),
						$id.'/',
						$cN,
						0,
						$id."wiz"
					);
					$this->addFileToFileArray($id."/wizard_icon.gif",t3lib_div::getUrl(t3lib_extMgm::extPath("kickstarter")."res/notfound.gif"));

					$configL[]=trim($this->wrapBody('
						"wizards" => Array(
							"_PADDING" => 2,
							',implode(chr(10),$wizards),'
						),
					'));
				}
			break;
			case "textarea_rte":
				$DBfields[] = $fConf["fieldname"]." text NOT NULL,";
				$configL[]='"type" => "text",';
				$configL[]='"cols" => "30",';
				$configL[]='"rows" => "5",';
				if ($fConf["conf_rte_fullscreen"])	{
					$wizards =array();
					$wizards[] = trim($this->sPS('
						'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_fullscreen]').'
						"RTE" => Array(
							"notNewRecords" => 1,
							"RTEonly" => 1,
							"type" => "script",
							"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
							"icon" => "wizard_rte2.gif",
							"script" => "wizard_rte.php",
						),
					'));
					$configL[]=trim($this->wrapBody('
						"wizards" => Array(
							"_PADDING" => 2,
							',implode(chr(10),$wizards),'
						),
					'));
				}

				$rteImageDir = "";
				if ($fConf["conf_rte_separateStorageForImages"] && t3lib_div::inList("moderate,basic,custom",$fConf["conf_rte"]))	{
					$this->EM_CONF_presets["createDirs"][]=$this->ulFolder($extKey)."rte/";
					$rteImageDir = "|imgpath=".$this->ulFolder($extKey)."rte/";
				}

				$transformation="ts_images-ts_reglinks";
				if ($fConf["conf_mode_cssOrNot"] && t3lib_div::inList("moderate,custom",$fConf["conf_rte"]))	{
					$transformation="ts_css";
				}


				switch($fConf["conf_rte"])	{
					case "tt_content":
						$typeP = 'richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]';
					break;
					case "moderate":
						$typeP = 'richtext[*]:rte_transform[mode='.$transformation.''.$rteImageDir.']';
					break;
					case "basic":
						$typeP = 'richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css'.$rteImageDir.']';
						$this->ext_localconf[]=trim($this->wrapBody("
								t3lib_extMgm::addPageTSConfig('

									# ***************************************************************************************
									# CONFIGURATION of RTE in table \"".$table."\", field \"".$fConf["fieldname"]."\"
									# ***************************************************************************************

									",trim($this->slashValueForSingleDashes(str_replace(chr(9),"  ",$this->sPS("
										RTE.config.".$table.".".$fConf["fieldname"]." {
											hidePStyleItems = H1, H4, H5, H6
											proc.exitHTMLparser_db=1
											proc.exitHTMLparser_db {
												keepNonMatchedTags=1
												tags.font.allowedAttribs= color
												tags.font.rmTagIfNoAttrib = 1
												tags.font.nesting = global
											}
										}
									")))),"
								');
						",0));
					break;
					case "none":
						$typeP = 'richtext[*]';
					break;
					case "custom":
						$enabledButtons=array();
						$traverseList = explode(",","cut,copy,paste,formatblock,class,fontstyle,fontsize,textcolor,bold,italic,underline,left,center,right,orderedlist,unorderedlist,outdent,indent,link,table,image,line,user,chMode");
						$HTMLparser=array();
						$fontAllowedAttrib=array();
						$allowedTags_WOP = array();
						$allowedTags=array();
						while(list(,$lI)=each($traverseList))	{
							$nothingDone=0;
							if ($fConf["conf_rte_b_".$lI])	{
								$enabledButtons[]=$lI;
								switch($lI)	{
									case "formatblock":
									case "left":
									case "center":
									case "right":
										$allowedTags[]="div";
										$allowedTags[]="p";
									break;
									case "class":
										$allowedTags[]="span";
									break;
									case "fontstyle":
										$allowedTags[]="font";
										$fontAllowedAttrib[]="face";
									break;
									case "fontsize":
										$allowedTags[]="font";
										$fontAllowedAttrib[]="size";
									break;
									case "textcolor":
										$allowedTags[]="font";
										$fontAllowedAttrib[]="color";
									break;
									case "bold":
										$allowedTags[]="b";
										$allowedTags[]="strong";
									break;
									case "italic":
										$allowedTags[]="i";
										$allowedTags[]="em";
									break;
									case "underline":
										$allowedTags[]="u";
									break;
									case "orderedlist":
										$allowedTags[]="ol";
										$allowedTags[]="li";
									break;
									case "unorderedlist":
										$allowedTags[]="ul";
										$allowedTags[]="li";
									break;
									case "outdent":
									case "indent":
										$allowedTags[]="blockquote";
									break;
									case "link":
										$allowedTags[]="a";
									break;
									case "table":
										$allowedTags[]="table";
										$allowedTags[]="tr";
										$allowedTags[]="td";
									break;
									case "image":
										$allowedTags[]="img";
									break;
									case "line":
										$allowedTags[]="hr";
									break;
									default:
										$nothingDone=1;
									break;
								}
								if (!$nothingDone)	$allowedTags_WOP[] = $WOP.'[conf_rte_b_'.$lI.']';
							}
						}
						if (count($fontAllowedAttrib))	{
							$HTMLparser[]="tags.font.allowedAttribs = ".implode(",",$fontAllowedAttrib);
							$HTMLparser[]="tags.font.rmTagIfNoAttrib = 1";
							$HTMLparser[]="tags.font.nesting = global";
						}
						if (count($enabledButtons))	{
							$typeP = 'richtext['.implode("|",$enabledButtons).']:rte_transform[mode='.$transformation.''.$rteImageDir.']';
						}

						$rte_colors=array();
						$setupUpColors=array();
						for ($a=1;$a<=3;$a++)	{
							if ($fConf["conf_rte_color".$a])	{
								$rte_colors[$id.'_color'.$a]=trim($this->sPS('
									'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_color'.$a.']').'
									'.$id.'_color'.$a.' {
										name = Color '.$a.'
										value = '.$fConf["conf_rte_color".$a].'
									}
								'));
								$setupUpColors[]=trim($fConf["conf_rte_color".$a]);
							}
						}

						$rte_classes=array();
						for ($a=1;$a<=6;$a++)	{
							if ($fConf["conf_rte_class".$a])	{
								$rte_classes[$id.'_class'.$a]=trim($this->sPS('
									'.$this->WOPcomment('WOP:'.$WOP.'[conf_rte_class'.$a.']').'
									'.$id.'_class'.$a.' {
										name = '.$fConf["conf_rte_class".$a].'
										value = '.$fConf["conf_rte_class".$a."_style"].'
									}
								'));
							}
						}

						$PageTSconfig= Array();
						if ($fConf["conf_rte_removecolorpicker"])	{
							$PageTSconfig[]="	".$this->WOPcomment('WOP:'.$WOP.'[conf_rte_removecolorpicker]');
							$PageTSconfig[]="disableColorPicker = 1";
						}
						if (count($rte_classes))	{
							$PageTSconfig[]="	".$this->WOPcomment('WOP:'.$WOP.'[conf_rte_class*]');
							$PageTSconfig[]="classesParagraph = ".implode(", ",array_keys($rte_classes));
							$PageTSconfig[]="classesCharacter = ".implode(", ",array_keys($rte_classes));
							if (in_array("p",$allowedTags) || in_array("div",$allowedTags))	{
								$HTMLparser[]="	".$this->WOPcomment('WOP:'.$WOP.'[conf_rte_class*]');
								if (in_array("p",$allowedTags))	{$HTMLparser[]="p.fixAttrib.class.list = ,".implode(",",array_keys($rte_classes));}
								if (in_array("div",$allowedTags))	{$HTMLparser[]="div.fixAttrib.class.list = ,".implode(",",array_keys($rte_classes));}
							}
						}
						if (count($rte_colors))		{
							$PageTSconfig[]="	".$this->WOPcomment('WOP:'.$WOP.'[conf_rte_color*]');
							$PageTSconfig[]="colors = ".implode(", ",array_keys($rte_colors));

							if (in_array("color",$fontAllowedAttrib) && $fConf["conf_rte_removecolorpicker"])	{
								$HTMLparser[]="	".$this->WOPcomment('WOP:'.$WOP.'[conf_rte_removecolorpicker]');
								$HTMLparser[]="tags.font.fixAttrib.color.list = ,".implode(",",$setupUpColors);
								$HTMLparser[]="tags.font.fixAttrib.color.removeIfFalse = 1";
							}
						}
						if (!strcmp($fConf["conf_rte_removePdefaults"],1))	{
							$PageTSconfig[]="	".$this->WOPcomment('WOP:'.$WOP.'[conf_rte_removePdefaults]');
							$PageTSconfig[]="hidePStyleItems = H1, H2, H3, H4, H5, H6, PRE";
						} elseif ($fConf["conf_rte_removePdefaults"]=="H2H3")	{
							$PageTSconfig[]="	".$this->WOPcomment('WOP:'.$WOP.'[conf_rte_removePdefaults]');
							$PageTSconfig[]="hidePStyleItems = H1, H4, H5, H6";
						} else {
							$allowedTags[]="h1";
							$allowedTags[]="h2";
							$allowedTags[]="h3";
							$allowedTags[]="h4";
							$allowedTags[]="h5";
							$allowedTags[]="h6";
							$allowedTags[]="pre";
						}


						$allowedTags = array_unique($allowedTags);
						if (count($allowedTags))	{
							$HTMLparser[]="	".$this->WOPcomment('WOP:'.implode(" / ",$allowedTags_WOP));
							$HTMLparser[]='allowTags = '.implode(", ",$allowedTags);
						}
						if ($fConf["conf_rte_div_to_p"])	{
							$HTMLparser[]="	".$this->WOPcomment('WOP:'.$WOP.'[conf_rte_div_to_p]');
							$HTMLparser[]='tags.div.remap = P';
						}
						if (count($HTMLparser))	{
							$PageTSconfig[]=trim($this->wrapBody('
								proc.exitHTMLparser_db=1
								proc.exitHTMLparser_db {
									',implode(chr(10),$HTMLparser),'
								}
							'));
						}

						$finalPageTSconfig=array();
						if (count($rte_colors))		{
							$finalPageTSconfig[]=trim($this->wrapBody('
								RTE.colors {
								',implode(chr(10),$rte_colors),'
								}
							'));
						}
						if (count($rte_classes))		{
							$finalPageTSconfig[]=trim($this->wrapBody('
								RTE.classes {
								',implode(chr(10),$rte_classes),'
								}
							'));
						}
						if (count($PageTSconfig))		{
							$finalPageTSconfig[]=trim($this->wrapBody('
								RTE.config.'.$table.'.'.$fConf["fieldname"].' {
								',implode(chr(10),$PageTSconfig),'
								}
							'));
						}
						if (count($finalPageTSconfig))	{
							$this->ext_localconf[]=trim($this->wrapBody("
								t3lib_extMgm::addPageTSConfig('

									# ***************************************************************************************
									# CONFIGURATION of RTE in table \"".$table."\", field \"".$fConf["fieldname"]."\"
									# ***************************************************************************************

								",trim($this->slashValueForSingleDashes(str_replace(chr(9),"  ",implode(chr(10).chr(10),$finalPageTSconfig)))),"
								');
							",0));
						}
					break;
				}
				$this->_typeP[$fConf["fieldname"]]	= $typeP;
			break;
			case "check":
			case "check_4":
			case "check_10":
				$configL[]='"type" => "check",';
				if ($t=="check")	{
					$DBfields[] = $fConf["fieldname"]." tinyint(3) unsigned DEFAULT '0' NOT NULL,";
					if ($fConf["conf_check_default"])	$configL[]='"default" => 1,	'.$this->WOPcomment('WOP:'.$WOP.'[conf_check_default]');
				} else {
					$DBfields[] = $fConf["fieldname"]." int(11) unsigned DEFAULT '0' NOT NULL,";
				}
				if ($t=="check_4" || $t=="check_10")	{
					$configL[]='"cols" => 4,';
					$cItems=array();
#					$aMax = ($t=="check_4"?4:10);
					$aMax = intval($fConf["conf_numberBoxes"]);
					for($a=0;$a<$aMax;$a++)	{
//						$cItems[]='Array("'.($fConf["conf_boxLabel_".$a]?str_replace("\\'","'",addslashes($this->getSplitLabels($fConf,"conf_boxLabel_".$a))):'English Label '.($a+1).'|Danish Label '.($a+1).'|German Label '.($a+1).'| etc...').'", ""),';
						$cItems[]='Array("'.addslashes($this->getSplitLabels_reference($fConf,"conf_boxLabel_".$a,$table.".".$fConf["fieldname"].".I.".$a)).'", ""),';
					}
					$configL[]=trim($this->wrapBody('
						"items" => Array (
							',implode(chr(10),$cItems),'
						),
					'));
				}
			break;
			case "radio":
			case "select":
				$configL[]='"type" => "'.($t=="select"?"select":"radio").'",';
				$notIntVal=0;
				$len=array();
				for($a=0;$a<t3lib_div::intInRange($fConf["conf_select_items"],1,20);$a++)	{
					$val = $fConf["conf_select_itemvalue_".$a];
					$notIntVal+= t3lib_div::testInt($val)?0:1;
					$len[]=strlen($val);
					if ($fConf["conf_select_icons"] && $t=="select")	{
						$icon = ', t3lib_extMgm::extRelPath("'.$extKey.'")."'."selicon_".$id."_".$a.".gif".'"';
										// Add wizard icon
						$this->addFileToFileArray("selicon_".$id."_".$a.".gif",t3lib_div::getUrl(t3lib_extMgm::extPath("kickstarter")."res/wiz.gif"));
					} else $icon="";
//					$cItems[]='Array("'.str_replace("\\'","'",addslashes($this->getSplitLabels($fConf,"conf_select_item_".$a))).'", "'.addslashes($val).'"'.$icon.'),';
					$cItems[]='Array("'.addslashes($this->getSplitLabels_reference($fConf,"conf_select_item_".$a,$table.".".$fConf["fieldname"].".I.".$a)).'", "'.addslashes($val).'"'.$icon.'),';
				}
				$configL[]=trim($this->wrapBody('
					'.$this->WOPcomment('WOP:'.$WOP.'[conf_select_items]').'
					"items" => Array (
						',implode(chr(10),$cItems),'
					),
				'));
				if ($fConf["conf_select_pro"] && $t=="select")	{
					$cN = $this->returnName($extKey,"class",$id);
					$configL[]='"itemsProcFunc" => "'.$cN.'->main",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_select_pro]');

					$classContent= $this->sPS('
						class '.$cN.' {
							function main(&$params,&$pObj)	{
/*								debug("Hello World!",1);
								debug("\$params:",1);
								debug($params);
								debug("\$pObj:",1);
								debug($pObj);
	*/
									// Adding an item!
								$params["items"][]=Array($pObj->sL("Added label by PHP function|Tilf�jet Dansk tekst med PHP funktion"), 999);

								// No return - the $params and $pObj variables are passed by reference, so just change content in then and it is passed back automatically...
							}
						}
					');

					$this->addFileToFileArray("class.".$cN.".php",$this->PHPclassFile($extKey,"class.".$cN.".php",$classContent,"Class/Function which manipulates the item-array for table/field ".$id."."));

					$this->ext_tables[]=$this->sPS('
						'.$this->WOPcomment('WOP:'.$WOP.'[conf_select_pro]:').'
						if (TYPO3_MODE=="BE")	include_once(t3lib_extMgm::extPath("'.$extKey.'")."'.'class.'.$cN.'.php");
					');
				}

				$numberOfRelations = t3lib_div::intInRange($fConf["conf_relations"],1,100);
				if ($t=="select")	{
					$configL[]='"size" => '.t3lib_div::intInRange($fConf["conf_relations_selsize"],1,100).',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_relations_selsize]');
					$configL[]='"maxitems" => '.$numberOfRelations.',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_relations]');
				}

				if ($numberOfRelations>1 && $t=="select")	{
					if ($numberOfRelations*4 < 256)	{
						$DBfields[] = $fConf["fieldname"]." varchar(".($numberOfRelations*4).") DEFAULT '' NOT NULL,";
					} else {
						$DBfields[] = $fConf["fieldname"]." text NOT NULL,";
					}
				} elseif ($notIntVal)	{
					$varCharLn = t3lib_div::intInRange(max($len),1);
					$DBfields[] = $fConf["fieldname"]." ".($varCharLn>$this->charMaxLng?'var':'')."char(".$varCharLn.") DEFAULT '' NOT NULL,";
				} else {
					$DBfields[] = $fConf["fieldname"]." int(11) unsigned DEFAULT '0' NOT NULL,";
				}
			break;
			case "rel":
				if ($fConf["conf_rel_type"]=="group")	{
					$configL[]='"type" => "group",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_type]');
					$configL[]='"internal_type" => "db",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_type]');
				} else {
					$configL[]='"type" => "select",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_type]');
				}

				if ($fConf["conf_rel_type"]!="group" && $fConf["conf_relations"]==1 && $fConf["conf_rel_dummyitem"])	{
					$configL[]=trim($this->wrapBody('
						'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_dummyitem]').'
						"items" => Array (
							','Array("",0),','
						),
					'));
				}

				if (t3lib_div::inList("tt_content,fe_users,fe_groups",$fConf["conf_rel_table"]))		$this->EM_CONF_presets["dependencies"][]="cms";

				if ($fConf["conf_rel_table"]=="_CUSTOM")	{
					$fConf["conf_rel_table"]=$fConf["conf_custom_table_name"]?$fConf["conf_custom_table_name"]:"NO_TABLE_NAME_AVAILABLE";
				}

				if ($fConf["conf_rel_type"]=="group")	{
					$configL[]='"allowed" => "'.($fConf["conf_rel_table"]!="_ALL"?$fConf["conf_rel_table"]:"*").'",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_table]');
					if ($fConf["conf_rel_table"]=="_ALL")	$configL[]='"prepend_tname" => 1,	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_table]=_ALL');
				} else {
					switch($fConf["conf_rel_type"])	{
						case "select_cur":
							$where="AND ".$fConf["conf_rel_table"].".pid=###CURRENT_PID### ";
						break;
						case "select_root":
							$where="AND ".$fConf["conf_rel_table"].".pid=###SITEROOT### ";
						break;
						case "select_storage":
							$where="AND ".$fConf["conf_rel_table"].".pid=###STORAGE_PID### ";
						break;
						default:
							$where="";
						break;
					}
					$configL[]='"foreign_table" => "'.$fConf["conf_rel_table"].'",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_table]');
					$configL[]='"foreign_table_where" => "'.$where.'ORDER BY '.$fConf["conf_rel_table"].'.uid",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_rel_type]');
				}
				$configL[]='"size" => '.t3lib_div::intInRange($fConf["conf_relations_selsize"],1,100).',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_relations_selsize]');
				$configL[]='"minitems" => 0,';
				$configL[]='"maxitems" => '.t3lib_div::intInRange($fConf["conf_relations"],1,100).',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_relations]');

				if ($fConf["conf_relations_mm"])	{
					$mmTableName=$id."_mm";
					$configL[]='"MM" => "'.$mmTableName.'",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_relations_mm]');
					$DBfields[] = $fConf["fieldname"]." int(11) unsigned DEFAULT '0' NOT NULL,";

					$createTable = $this->sPS("
						#
						# Table structure for table '".$mmTableName."'
						# ".$this->WOPcomment('WOP:'.$WOP.'[conf_relations_mm]')."
						#
						CREATE TABLE ".$mmTableName." (
						  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
						  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
						  tablenames varchar(30) DEFAULT '' NOT NULL,
						  sorting int(11) unsigned DEFAULT '0' NOT NULL,
						  KEY uid_local (uid_local),
						  KEY uid_foreign (uid_foreign)
						);
					");
					$this->ext_tables_sql[]=chr(10).$createTable.chr(10);
				} elseif (t3lib_div::intInRange($fConf["conf_relations"],1,100)>1 || $fConf["conf_rel_type"]=="group") {
					$DBfields[] = $fConf["fieldname"]." blob NOT NULL,";
				} else {
					$DBfields[] = $fConf["fieldname"]." int(11) unsigned DEFAULT '0' NOT NULL,";
				}

				if ($fConf["conf_rel_type"]!="group")	{
					$wTable=$fConf["conf_rel_table"];
					$wizards =array();
					if ($fConf["conf_wiz_addrec"])	{
						$wizards[] = trim($this->sPS('
							'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_addrec]').'
							"add" => Array(
								"type" => "script",
								"title" => "Create new record",
								"icon" => "add.gif",
								"params" => Array(
									"table"=>"'.$wTable.'",
									"pid" => "###CURRENT_PID###",
									"setValue" => "prepend"
								),
								"script" => "wizard_add.php",
							),
						'));
					}
					if ($fConf["conf_wiz_listrec"])	{
						$wizards[] = trim($this->sPS('
							'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_listrec]').'
							"list" => Array(
								"type" => "script",
								"title" => "List",
								"icon" => "list.gif",
								"params" => Array(
									"table"=>"'.$wTable.'",
									"pid" => "###CURRENT_PID###",
								),
								"script" => "wizard_list.php",
							),
						'));
					}
					if ($fConf["conf_wiz_editrec"])	{
						$wizards[] = trim($this->sPS('
							'.$this->WOPcomment('WOP:'.$WOP.'[conf_wiz_editrec]').'
							"edit" => Array(
								"type" => "popup",
								"title" => "Edit",
								"script" => "wizard_edit.php",
								"popup_onlyOpenIfSelected" => 1,
								"icon" => "edit2.gif",
								"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
							),
						'));
					}
					if (count($wizards))	{
						$configL[]=trim($this->wrapBody('
							"wizards" => Array(
								"_PADDING" => 2,
								"_VERTICAL" => 1,
								',implode(chr(10),$wizards),'
							),
						'));
					}
				}
			break;
			case "files":
				$configL[]='"type" => "group",';
				$configL[]='"internal_type" => "file",';
				switch($fConf["conf_files_type"])	{
					case "images":
						$configL[]='"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_type]');
					break;
					case "webimages":
						$configL[]='"allowed" => "gif,png,jpeg,jpg",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_type]');
					break;
					case "all":
						$configL[]='"allowed" => "",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_type]');
						$configL[]='"disallowed" => "php,php3",	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_type]');
					break;
				}
				$configL[]='"max_size" => '.t3lib_div::intInRange($fConf["conf_max_filesize"],1,1000,500).',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_max_filesize]');

				$this->EM_CONF_presets["uploadfolder"]=1;

				$ulFolder = 'uploads/tx_'.str_replace("_","",$extKey);
				$configL[]='"uploadfolder" => "'.$ulFolder.'",';
				if ($fConf["conf_files_thumbs"])	$configL[]='"show_thumbs" => 1,	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_thumbs]');

				$configL[]='"size" => '.t3lib_div::intInRange($fConf["conf_files_selsize"],1,100).',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files_selsize]');
				$configL[]='"minitems" => 0,';
				$configL[]='"maxitems" => '.t3lib_div::intInRange($fConf["conf_files"],1,100).',	'.$this->WOPcomment('WOP:'.$WOP.'[conf_files]');

				$DBfields[] = $fConf["fieldname"]." blob NOT NULL,";
			break;
			case "none":
				$DBfields[] = $fConf["fieldname"]." tinytext NOT NULL,";
				$configL[]=trim($this->sPS('
					"type" => "none",
				'));
			break;
			case "passthrough":
				$DBfields[] = $fConf["fieldname"]." tinytext NOT NULL,";
				$configL[]=trim($this->sPS('
					"type" => "passthrough",
				'));
			break;
			default:
				debug("Unknown type: ".(string)$fConf["type"]);
			break;
		}

		if ($t=="passthrough")	{
			$columns[$fConf["fieldname"]] = trim($this->wrapBody('
				"'.$fConf["fieldname"].'" => Array (		'.$this->WOPcomment('WOP:'.$WOP.'[fieldname]').'
					"config" => Array (
						',implode(chr(10),$configL),'
					)
				),
			',2));
		} else {
			$columns[$fConf["fieldname"]] = trim($this->wrapBody('
				"'.$fConf["fieldname"].'" => Array (		'.$this->WOPcomment('WOP:'.$WOP.'[fieldname]').'
					"exclude" => '.($fConf["excludeField"]?1:0).',		'.$this->WOPcomment('WOP:'.$WOP.'[excludeField]').'
					"label" => "'.addslashes($this->getSplitLabels_reference($fConf,"title",$table.".".$fConf["fieldname"])).'",		'.$this->WOPcomment('WOP:'.$WOP.'[title]').'
					"config" => Array (
						',implode(chr(10),$configL),'
					)
				),
			',2));
		}
	}
	function ulFolder($eKey)	{
		return "uploads/tx_".str_replace("_","",$eKey)."/";
	}
	function fieldIsRTE($fC)	{
		return !strcmp($fC["type"],"textarea_rte") &&
						($fC["conf_rte"]=="basic" ||
						(t3lib_div::inList("custom,moderate",$fC["conf_rte"]) && $fC["conf_mode_cssOrNot"])
						);
	}

}

// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_fields.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/sections/class.tx_kickstarter_section_fields.php']);
}


?>