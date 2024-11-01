jQuery(document).ready(function($){
    $('.color-field').wpColorPicker();
});
function Wptabs_ShowTaxo( value ){
	data = {
		action: 'taxonomiesResult',
		key_post : value
	};
	jQuery.post(ajaxurl,data,function(response){
		jQuery(".WpTabTaxonomy").html(response);
	});
}
