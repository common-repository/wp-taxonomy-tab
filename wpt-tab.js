jQuery(document).ready(function(){
	jQuery('.wpt-tabtitles li:first-child a').addClass('active');
	jQuery('.wpt-maincontenttab .wpt-contenttab:first-child').addClass('active');
	var autoHeight = jQuery('.wpt-maincontenttab').height();//alert(autoHeight);
});
function TabDisplay( active ){
	var ParentID = jQuery(active).attr('data-parent');	
	var ChildTitleID = jQuery(active).attr('id');
	jQuery('#'+ParentID+' .wpt-tabtitles li a').removeClass("active");
	jQuery('#'+ParentID+' #'+ChildTitleID).addClass("active");
	
	var tabID = jQuery(active).attr('href');	
	jQuery('#'+ParentID+' .wpt-contenttab').removeClass('active');
	jQuery('#'+ParentID+' '+tabID).addClass('active');
}
function pagiNation( href ){	
	var PaginationActive = jQuery( href ).attr('href');
	var newP = jQuery(href).attr('data-parent'); 
	var MainParent = jQuery(href).attr('data-mainparent');
	var CurrentPosition = jQuery(href).attr('id');
	
	jQuery('#'+MainParent+' #'+newP+' .wpt-pagination li a').removeClass('active');
	jQuery('#'+MainParent+' #'+newP+' .wpt-pagination li a#'+CurrentPosition).addClass('active');
	jQuery('#'+MainParent+' #'+newP+' .tab-sect .wpt-content').removeClass('wpt-active');
	jQuery('#'+MainParent+' #'+newP+' .tab-sect .wpt-content'+PaginationActive).addClass('wpt-active');	
}