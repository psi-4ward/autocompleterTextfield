<?php


/**
 * Class AutocompleterTextfieldWidget
 *
 * Provide methods to handle autocompleting text fields.
 * @copyright  4ward.media 2011
 * @author     Christoph Wiechert
 */

class AutocompleterTextfieldWidget extends Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget';

	/**
	 * Contents
	 * @var array
	 */
	protected $arrContents = array();


	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'maxlength':
				$this->arrAttributes[$strKey] = ($varValue > 0) ? $varValue : '';
				break;

			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			default:
				parent::__set($strKey, $varValue);
			break;
		}
	}

	/**
	 * Trim values
	 * @param mixed
	 * @return mixed
	 */
	protected function validator($varInput)
	{
		if (is_array($varInput))
		{
			return parent::validator($varInput);
		}

		return parent::validator(trim($varInput));
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		// Hide the Punycode format (see #2750)
		if ($this->rgxp == 'email' || $this->rgxp == 'url')
		{
			$this->varValue = $this->idnaDecode($this->varValue);
		}
		
		// load autocompleter 
		$GLOBALS['TL_JAVASCRIPT']['autocompleter']  = 'plugins/autocompleter/js/ac_compress.src.js';
		$GLOBALS['TL_CSS']['autocompleter'] 		= 'plugins/autocompleter/css/auto_completer.css';
		$GLOBALS['TL_JAVASCRIPT']['autocompleterHelper'] = 'system/modules/autocompleterTextfield/html/helper.js';

		// try to guess MultiColumnWizard usage
		$mcw = false;
		if(preg_match("~^([^\[]+)\[(\d+)\]\[([^\]]+)\]$~i",$this->strName,$erg))
		{
			// this is a MultiColumnWizard, try to guess the fieldname
			$this->strField = $erg[1];
			$mcw=$erg[3];
		}
		
		// Store ID (array-key) instead of the value from the textfield 
		if($this->storeId)
		{
			// get value to the ID saved in $this->varValue
			$strValue = '';
			foreach($this->options as $v)
			{
				if($v['value'] == $this->varValue)
				{
					$strValue = $v['label'];
					break;
				}
			}
			
			
			return  '<input type="hidden" name="'.$this->strName.'" value="'.specialchars($this->varValue).'"/>'
					.'<input type="text" id="ctrl_'.$this->strId.'" class="tl_text'.(strlen($this->strClass) ? ' ' . $this->strClass : '')
					.' autocompleterTextfield" value="'.specialchars($strValue).'"'.$this->getAttributes().' onfocus="Backend.getScrollOffset();"'
					.(($mcw)?' multicolumnwizard="'.$mcw.'"':'') 
					.'>'
					.$this->wizard;
		}
		else
		{
			return '<input type="text" name="'.$this->strName.'" id="ctrl_'.$this->strId.'" class="tl_text'.(strlen($this->strClass) ? ' ' . $this->strClass : '')
					.' autocompleterTextfield" value="'.specialchars($this->varValue).'"'.$this->getAttributes().' onfocus="Backend.getScrollOffset();"'
					.(($mcw)?' multicolumnwizard="'.$mcw.'"':'')
					.'>'
					.$this->wizard;
		}

	}
}

?>