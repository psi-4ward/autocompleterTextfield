<?php
/**
* Initialize the system
*/
define('TL_MODE', 'BE');
define('BYPASS_TOKEN_CHECK',true);
require_once('../../initialize.php');

class AutocompleterTextfieldResponder extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
		
		
		// little validation
		$this->tbl = $this->Input->get('tbl');
		$this->fld = $this->Input->get('fld');
		$this->mcw = $this->Input->get('mcw');
		if(!preg_match('~^[a-z0-9_\-]+$~i',$this->tbl)) die('ERROR 201');
		if(!preg_match('~^[a-z0-9_\-]+$~i',$this->fld)) die('ERROR 202');
		
		// load the DCA
		$this->loadDataContainer($this->tbl);
		if($this->mcw)
		{
			if(!preg_match('~^[a-z0-9_\-]+$~i',$this->mcw)) die('ERROR 203');
			$dca = $GLOBALS['TL_DCA'][$this->tbl]['fields'][$this->fld]['eval']['columnFields'][$this->mcw];
		}
		else
		{
			$dca = $GLOBALS['TL_DCA'][$this->tbl]['fields'][$this->fld];
		}
		
		// let the Controller::prepareForWidget calc the options
		$temp = $this->prepareForWidget($dca, $this->fld, '', null, $this->tbl);
		
		// reformat options for Autocompleter-JS and unique values
		$arrRet = array();
		$arrVals = array();
		foreach($temp['options'] as $val)
		{
			if(in_array($val['label'],$arrVals)) continue;
			$arrRet[] = array('id'=>$val['value'],'value'=>$val['label']);
			$arrVals[] = $val['label'];
		}
		unset($temp);

		// filter the array to return only matching elements
		$search = $this->Input->post('value');
		$arrRet = array_filter($arrRet,function($val) use($search){
			return strripos($val['value'], $search) !== false;
		});
		
		echo json_encode(array_values($arrRet));
		
	}
}

$x = new AutocompleterTextfieldResponder();
?>
