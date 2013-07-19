function _(s) {
	return typeof l10n[s] != 'undefined' ? l10n[s] : s;
}
function __(str){
	return i18n.gettext(str);
}

function test(param) {
	var a = __("Hello world, testing jsgettext");
console.log(a);
console.log(__(__('Test string')));
console.log(__('Test string'));
	var reg1 = /"[a-z]+"/i;
	var reg2 = /[a-z]+\+\/"aa"/i;
	var s1 = __('string 1: single quotes');
console.log(s1);
	var s2 = __("string 2: double quotes");
console.log(s2);
	var s3 = __("/* comment in string */");
console.log(s3);
	var s4 = __("regexp in string: /[a-z]+/i");
console.log(s4);

	//var s7 = T_("string 2: \"escaped double quotes\"");
	var s8 = __('string 2: \'escaped single quotes\'');
	//var s9 = T_('¡¡¿¿Texto con açentos, eñes, etcétera??!!');
	//alert(i18n.gettext("some key")); // alerts "some value"
	// "string in comment"
	//;

	/**
	 * multiple
	 * lines
	 * comment
	 * _("Hello world from comment")
	 */
}
