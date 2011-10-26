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
		$GLOBALS['TL_JAVASCRIPT']['autocompleter']  = 'system/modules/autocompleterTextfield/html/js/ac_compress.src.js';
		$GLOBALS['TL_CSS']['autocompleter'] 		= 'system/modules/autocompleterTextfield/html/css/auto_completer.css';
		$GLOBALS['TL_JAVASCRIPT']['autocompleterHelper'] = 'system/modules/autocompleterTextfield/html/js/helper.js';
		
		if($this->storeId)
		{
			$arrOptions = array();
			
			// TODO: support options and foreignKey parameters too
			
			// get options from options_callback
			if(!$this->strField)
			{
				// probalby this is a MultiColumnWizard, try to guess the fieldname
				$this->strField = substr($this->strName,0,strpos($this->strName, '[')); 
			}
			
			if(is_array($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['options_callback']))
			{
				if (!is_object($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['options_callback'][0]))
				{
					$this->import($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['options_callback'][0]);
				}
			
				$arrOptions = $this->$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['options_callback'][0]->$GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['options_callback'][1]($this);
			}
			
			return  '<input type="hidden" name="'.$this->strName.'" value="'.specialchars($this->varValue).'"/>'
					.'<input type="text" id="ctrl_'.$this->strId.'" class="tl_text'.(strlen($this->strClass) ? ' ' . $this->strClass : '')
					.' autocompleterTextfield" value="'.specialchars($arrOptions[$this->varValue]).'"'.$this->getAttributes().' onfocus="Backend.getScrollOffset();">'
					.$this->wizard;
		}
		else
		{
			return '<input type="text" name="'.$this->strName.'" id="ctrl_'.$this->strId.'" class="tl_text'.(strlen($this->strClass) ? ' ' . $this->strClass : '')
					.' autocompleterTextfield" value="'.specialchars($this->varValue).'"'.$this->getAttributes().' onfocus="Backend.getScrollOffset();">'
					.$this->wizard;
		}

	}
}

?>