tinyMCE.init({
  mode : "textareas",
  relative_urls : false,
  theme : "advanced",
  // width : "500",
  // height : "500",
  plugins : "inlinepopups,table,GZFileManager,fullscreen",
  theme_advanced_buttons1 : "GZFileManager,fullscreen,bold,italic,underline,strikethrough,tablecontrols,separator,fontsizeselect,fontselect,forecolor,backcolorpicker,justifyleft,justifycenter,justifyright,separator,image,link,unlink,code,separator,undo,redo",
  theme_advanced_buttons2 : "",
  theme_advanced_buttons3 : "",
  theme_advanced_toolbar_location : "top",
  theme_advanced_toolbar_align : "left",
  theme_advanced_statusbar_location : "bottom",
  theme_advanced_resizing : true,
  table_styles : "Header 1=header1;Header 2=header2;Header 3=header3",
	table_cell_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Cell=tableCel1",
	table_row_styles : "Header 1=header1;Header 2=header2;Header 3=header3;Table Row=tableRow1",
	table_cell_limit : 100,
	table_row_limit : 5,
	table_col_limit : 5,
  extended_valid_elements : "a[name|href|target|title],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],p[style]"
});