<?php
	
class tx_kickstarter_sectionbase {
	
	/* instance of the main Kickstarter Wizard class (class.tx_kickstarter_wizard.php) */
	var $wizard;
	
	/* instance of the Kickstarter Compilefiles class (class.tx_kickstarter_compilefiles.php) */
	var $compilefiles;
	
	/* Unique ID of this section (used in forms and data processing) */
	var $sectionID = 'uniqueID';
	
	/* renders the wizard for this section */
	function render_wizard() {
	}
	
	/* renders the code for this section */
	function render_extPart() {
	}
	
	function &process_hook($hookName, &$data) {
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter'][$this->sectionID][$hookName])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kickstarter'][$this->sectionID][$hookName] as $_funcRef) {
	$data =& t3lib_div::callUserFunction($_funcRef, $data, $this);
			}
		}
		return $data;
	}

	function renderCheckBox($prefix,$value,$defVal=0)	{
		if (!isset($value))	$value=$defVal;
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<input type="hidden" name="'.$this->piFieldName("wizArray_upd").$prefix.'" value="0"><input type="checkbox" name="'.$this->piFieldName("wizArray_upd").$prefix.'" value="1"'.($value?" CHECKED":"").' onClick="'.$onCP[1].'"'.$this->wop($prefix).'>';
	}
	function renderTextareaBox($prefix,$value)	{
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<textarea name="'.$this->piFieldName("wizArray_upd").$prefix.'" style="width:600px;" rows="10" wrap="OFF" onChange="'.$onCP[1].'" title="'.htmlspecialchars("WOP:".$prefix).'"'.$this->wop($prefix).'>'.t3lib_div::formatForTextarea($value).'</textarea>';
	}
	function renderStringBox($prefix,$value,$width=200)	{
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<input type="text" name="'.$this->piFieldName("wizArray_upd").$prefix.'" value="'.htmlspecialchars($value).'" style="width:'.$width.'px;" onChange="'.$onCP[1].'"'.$this->wop($prefix).'>';
	}
	function renderRadioBox($prefix,$value,$thisValue)	{
		$onCP = $this->getOnChangeParts($prefix);
		return $this->wopText($prefix).$onCP[0].'<input type="radio" name="'.$this->piFieldName("wizArray_upd").$prefix.'" value="'.$thisValue.'"'.(!strcmp($value,$thisValue)?" CHECKED":"").' onClick="'.$onCP[1].'"'.$this->wop($prefix).'>';
	}
	function renderSelectBox($prefix,$value,$optValues)	{
		$onCP = $this->getOnChangeParts($prefix);
		$opt=array();
		$isSelFlag=0;
		foreach($optValues as $k=>$v)	{
			$sel = (!strcmp($k,$value)?" SELECTED":"");
			if ($sel)	$isSelFlag++;
			$opt[]='<option value="'.htmlspecialchars($k).'"'.$sel.'>'.htmlspecialchars($v).'</option>';
		}
		if (!$isSelFlag && strcmp("",$value))	$opt[]='<option value="'.$value.'" SELECTED>'.htmlspecialchars("CURRENT VALUE '".$value."' DID NOT EXIST AMONG THE OPTIONS").'</option>';
		return $this->wopText($prefix).$onCP[0].'<select name="'.$this->piFieldName("wizArray_upd").$prefix.'" onChange="'.$onCP[1].'"'.$this->wop($prefix).'>'.implode("",$opt).'</select>';
	}

	function whatIsThis($str)	{
		return ' <a href="#" title="'.htmlspecialchars($str).'" style="cursor:help" onClick="alert('.$GLOBALS['LANG']->JScharCode($str).');return false;">(What is this?)</a>';
	}
	function renderStringBox_lang($fieldName,$ffPrefix,$piConf)	{
		$content = $this->renderStringBox($ffPrefix."[".$fieldName."]",$piConf[$fieldName])." [English]";
		if (count($this->selectedLanguages))	{
			$lines=array();
			foreach($this->selectedLanguages as $k=>$v) {
				$lines[]=$this->renderStringBox($ffPrefix."[".$fieldName."_".$k."]",$piConf[$fieldName."_".$k])." [".$v."]";
			}
			$content.=$this->textSetup("",implode("<BR>",$lines));
		}
		return $content;
	}

	function textSetup($header,$content)	{
		return ($header?"<strong>".$header."</strong><BR>":"")."<blockquote>".trim($content)."</blockquote>";
	}
	function resImg($name,$p='align="center"',$pre="<BR>",$post="<BR>")	{
		if ($this->dontPrintImages)	return "<BR>";
		$imgRel = $this->path_resources().$name;
		$imgInfo = @getimagesize(PATH_site.$imgRel);
		return $pre.'<img src="'.$this->siteBackPath.$imgRel.'" '.$imgInfo[3].($p?" ".$p:"").' vspace=5 border=1 style="border:solid 1px;">'.$post;
	}
	function resIcon($name,$p="")	{
		if ($this->dontPrintImages)	return "";
		$imgRel = $this->path_resources("icons/").$name;
		if (!@is_file(PATH_site.$imgRel))	return "";
		$imgInfo = @getimagesize(PATH_site.$imgRel);
		return '<img src="'.$this->siteBackPath.$imgRel.'" '.$imgInfo[3].($p?" ".$p:"").'>';
	}
	function path_resources($subdir="res/")	{
		return substr(t3lib_extMgm::extPath("kickstarter"),strlen(PATH_site)).$subdir;
	}
	function getOnChangeParts($prefix)	{
		$md5h=t3lib_div::shortMd5($this->piFieldName("wizArray_upd").$prefix);
		return array('<a name="'.$md5h.'"></a>',"setFormAnchorPoint('".$md5h."');");
	}

	function wop($prefix)	{
		return ' title="'.htmlspecialchars("WOP: ".$prefix).'"';
	}
	function wopText($prefix)	{
		return $this->printWOP?'<font face="verdana,arial,sans-serif" size=1 color=#999999>'.htmlspecialchars($prefix).':</font><BR>':'';
	}
	function catHeaderLines($lines,$k,$v,$altHeader="",$index="")	{
					$lines[]='<tr'.$this->bgCol(1).'><td><strong>'.$this->fw($v[0]).'</strong></td></tr>';
					$lines[]='<tr'.$this->bgCol(2).'><td>'.$this->fw($v[1]).'</td></tr>';
					$lines[]='<tr><td></td></tr>';
		return $lines;
	}
	function linkCurrentItems($cat)	{
		$items = $this->wizard->wizArray[$cat];
		$lines=array();
		$c=0;
		if (is_array($items))	{
			foreach($items as $k=>$conf)	{
				$lines[]='<strong>'.$this->linkStr($conf["title"]?$conf["title"]:"<em>Item ".$k."</em>",$cat,'edit:'.$k).'</strong>';
				$c=$k;
			}
		}
		if (!t3lib_div::inList("save,ts,TSconfig,languages",$cat) || !count($lines))	{
			$c++;
			if (count($lines))	$lines[]='';
			$lines[]=$this->linkStr('Add new item',$cat,'edit:'.$c);
		}
		return $this->fw(implode("<BR>",$lines));
	}
	function linkStr($str,$wizSubCmd,$wizAction)	{
		return '<a href="#" onClick="
			document.'.$this->varPrefix.'_wizard[\''.$this->piFieldName("wizSubCmd").'\'].value=\''.$wizSubCmd.'\';
			document.'.$this->varPrefix.'_wizard[\''.$this->piFieldName("wizAction").'\'].value=\''.$wizAction.'\';
			document.'.$this->varPrefix.'_wizard.submit();
			return false;">'.$str.'</a>';
	}
	function bgCol($n,$mod=0)	{
		$color = $this->color[$n-1];
		if ($mod)	$color = t3lib_div::modifyHTMLcolor($color,$mod,$mod,$mod);
		return ' bgColor="'.$color.'"';
	}
	function regNewEntry($k,$index)	{
		if (!is_array($this->wizard->wizArray[$k][$index]))	{
			$this->wizard->wizArray[$k][$index]=array();
		}
	}
	function bwWithFlag($str,$flag)	{
		if ($flag)	$str = '<strong>'.$str.'</strong>';
		return $str;
	}
	/**
	 * Getting link to this page + extra parameters, we have specified
	 *
	 * @param	array		Additional parameters specified.
	 * @return	string		The URL
	 */
	function linkThisCmd($uPA=array())	{
	  $url = t3lib_div::linkThisScript($uPA);
	  return $url;
	}

	/**
	 * Font wrap function; Wrapping input string in a <span> tag with font family and font size set
	 *
	 * @param	string		Input value
	 * @return	string		Wrapped input value.
	 */
	function fw($str)	{
		return '<span style="font-family:verdana,arial,sans-serif; font-size:10px;">'.$str.'</span>';
	}


	function piFieldName($key)	{
		return $this->varPrefix."[".$key."]";
	}
	function cmdHiddenField()	{
		return '<input type="hidden"  name="'.$this->piFieldName("cmd").'" value="'.htmlspecialchars($this->currentCMD).'">';
	}

	function preWrap($str)	{
		$str = str_replace(chr(9),"&nbsp;&nbsp;&nbsp;&nbsp;",htmlspecialchars($str));
		$str = '<pre>'.$str.'</pre>';
		return $str;
	}
	
}
?>