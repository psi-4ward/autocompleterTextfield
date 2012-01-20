if(typeof MultiColumnWizard != 'undefined')
	MultiColumnWizard.addOperationClickCallback('copy', initAutocompleterTextfields);

window.addEvent('domready',function(){
	// Init autocompleter for textfields
	initAutocompleterTextfields();

});


/**
 * Searches all input-fields with class autocompleterTextfield
 * and initialize the Autocompleter
 */
function initAutocompleterTextfields(el, row)
{
	$$('input.autocompleterTextfield').each(function(el){
		new AutocompleterTextfield(el);
	});
}

/**
 * Class for handling the Autocompleter
 */
var AutocompleterTextfield = new Class({
	
	/**
	 * Init the class
	 */
	initialize: function(el){
		el.addEvent('keydown',function(e){
			if(e.key == 'enter')
				return false;
		});
		
		// Init autocompleter
		var tbl = el.getParent('form').getElement('input[name=FORM_SUBMIT]').get('value');
		var tmp = el.get('id').match(/^ctrl_(.*)_row\d+_/);
		var fld = (tmp) ? tmp[1] : el.get('id').substr(5);
		var mcw = el.get('multicolumnwizard') ? '&mcw='+el.get('multicolumnwizard') : ''; 
		
	    this.autocompleter = new Autocompleter.Request.JSON(el, 'system/modules/autocompleterTextfield/ajax.php?tbl='+tbl+'&fld='+fld+mcw, {
	        'postVar': 'value',
	        'injectChoice':this.generateChoice,
	        'autoSubmit':false,
	        'minLength':3,
	        'delay':500,
	        'onSelection':function(inp,el,sel,val,x){
	        	var hiddenField = inp.getParent().getElement('input[type=hidden]');
				if(hiddenField != null)
				{
					hiddenField.set('value',el.retrieve('itemID'))
				}
				else
				{
					inp.set('value',el.get('text'))
				}
				return false;
	    	}.bind(this)
	    });

	},

	/**
	 * Generate each choice
	 * this represents the Autocompleter object
	 */
	generateChoice: function(val){
	    var el = new Element('li').set('html',this.markQueryValue(val.value)).store('itemID',val.id);
	    el.inputValue = val.value;
	    this.addChoiceEvents(el);
	    el.inject(this.choices);
	}
	
	
});
