<?php
/**
* Initialize the system
*/
define('TL_MODE', 'BE');
require_once('../../initialize.php');

class AutocompleterTextfieldResponder extends Controller
{
	public function __construct()
	{
		parent::__construct();
		
		$this->tbl = $this->Input->get('tbl');
		$this->fld = $this->Input->get('fld');
		
		// little validation
		if(!preg_match('~^[a-z0-9_\-]+$~i',$this->tbl)) die('ERROR 201');
		if(!preg_match('~^[a-z0-9_\-]+$~i',$this->fld)) die('ERROR 202');
		
		// load the DCA
		$this->loadDataContainer($this->tbl);
		
		// get options from options_callback
		if(is_array($GLOBALS['TL_DCA'][$this->tbl]['fields'][$this->fld]['options_callback']))
		{
			if (!is_object($GLOBALS['TL_DCA'][$this->tbl]['fields'][$this->fld]['options_callback'][0]))
			{
				$this->import($GLOBALS['TL_DCA'][$this->tbl]['fields'][$this->fld]['options_callback'][0]);
			}
		
			$arrOptions = $this->$GLOBALS['TL_DCA'][$this->tbl]['fields'][$this->fld]['options_callback'][0]->$GLOBALS['TL_DCA'][$this->tbl]['fields'][$this->fld]['options_callback'][1]($this,$this->Input->post('value'));
		}
		
		// TODO: support options and foreignKey parameters too		
		
		$arrRet = array();
		foreach($arrOptions as $id => $val)
		{
			$arrRet[] = array('id'=>$id,'value'=>$val);
		}
		echo json_encode($arrRet);
		
		
		$this->import('Database');
	}
}

$x = new AutocompleterTextfieldResponder();
?>
