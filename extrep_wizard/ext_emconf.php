<?php

########################################################################
# Extension Manager/Repository config file for ext: 'extrep_wizard'
# 
# Auto generated 12-02-2003 22:05
# 
# Manual updates:
# Only the data in the array - anything else is removed by next write
########################################################################

$EM_CONF[$_EXTKEY] = Array (
	'title' => 'Extension Repository Kickstarter',
	'description' => 'Wizard interface for kickstarting new extension development. Interfaces with EM and the extension \'extrep\' if loaded.',
	'category' => 'module',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => 0,
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Kasper Skårhøj',
	'author_email' => 'kasper@typo3.com',
	'author_company' => 'Curby Soft Multimedia',
	'private' => 0,
	'download_password' => '',
	'version' => '0.1.2',	// Don't modify this! Managed automatically during upload to repository.
	'_md5_values_when_last_written' => 'a:90:{s:12:"ext_icon.gif";s:4:"1bf7";s:14:"ext_tables.sql";s:4:"90a2";s:25:"ext_tables_static+adt.sql";s:4:"1a91";s:28:"pi/class.tx_extrepwizard.php";s:4:"cf78";s:41:"pi/class.tx_extrepwizard_compilefiles.php";s:4:"79ac";s:17:"pi/icons/bold.png";s:4:"aff1";s:19:"pi/icons/center.png";s:4:"782e";s:18:"pi/icons/class.png";s:4:"0975";s:17:"pi/icons/copy.png";s:4:"430b";s:16:"pi/icons/cut.png";s:4:"1af3";s:21:"pi/icons/emoticon.png";s:4:"928c";s:21:"pi/icons/fontsize.png";s:4:"8efa";s:22:"pi/icons/fontstyle.png";s:4:"cc24";s:24:"pi/icons/formatblock.png";s:4:"9f63";s:18:"pi/icons/image.png";s:4:"8d35";s:19:"pi/icons/indent.png";s:4:"782f";s:19:"pi/icons/italic.png";s:4:"c91b";s:17:"pi/icons/left.png";s:4:"5b83";s:17:"pi/icons/line.png";s:4:"7885";s:17:"pi/icons/link.png";s:4:"fa8a";s:24:"pi/icons/orderedlist.png";s:4:"f658";s:20:"pi/icons/outdent.png";s:4:"8890";s:18:"pi/icons/paste.png";s:4:"f4cb";s:18:"pi/icons/right.png";s:4:"e998";s:18:"pi/icons/table.png";s:4:"5f9c";s:22:"pi/icons/textcolor.png";s:4:"3988";s:22:"pi/icons/underline.png";s:4:"a0ad";s:26:"pi/icons/unorderedlist.png";s:4:"cdef";s:17:"pi/icons/user.png";s:4:"8ae5";s:13:"pi/res/cm.png";s:4:"df60";s:17:"pi/res/module.png";s:4:"9c10";s:26:"pi/res/modulefunc_func.png";s:4:"af99";s:26:"pi/res/modulefunc_task.png";s:4:"5667";s:16:"pi/res/pi_ce.png";s:4:"6ac3";s:19:"pi/res/pi_cewiz.png";s:4:"57db";s:20:"pi/res/pi_header.png";s:4:"0d49";s:26:"pi/res/pi_menu_sitemap.png";s:4:"fbfc";s:16:"pi/res/pi_pi.png";s:4:"01e2";s:21:"pi/res/pi_textbox.png";s:4:"ed57";s:20:"pi/res/t_check10.png";s:4:"1d11";s:19:"pi/res/t_check4.png";s:4:"d094";s:17:"pi/res/t_date.png";s:4:"0c8b";s:21:"pi/res/t_datetime.png";s:4:"1726";s:21:"pi/res/t_file_all.png";s:4:"9018";s:21:"pi/res/t_file_img.png";s:4:"8eed";s:22:"pi/res/t_file_size.png";s:4:"f082";s:23:"pi/res/t_file_thumb.png";s:4:"56a1";s:21:"pi/res/t_file_web.png";s:4:"e722";s:24:"pi/res/t_flag_access.png";s:4:"23a4";s:25:"pi/res/t_flag_endtime.png";s:4:"2dfc";s:24:"pi/res/t_flag_hidden.png";s:4:"a0ce";s:27:"pi/res/t_flag_starttime.png";s:4:"dd68";s:18:"pi/res/t_input.png";s:4:"c430";s:24:"pi/res/t_input_check.png";s:4:"712e";s:27:"pi/res/t_input_colorwiz.png";s:4:"2a07";s:23:"pi/res/t_input_link.png";s:4:"0ca9";s:24:"pi/res/t_input_link2.png";s:4:"15ad";s:27:"pi/res/t_input_password.png";s:4:"d51a";s:27:"pi/res/t_input_required.png";s:4:"3b9f";s:20:"pi/res/t_integer.png";s:4:"537b";s:17:"pi/res/t_link.png";s:4:"a333";s:18:"pi/res/t_radio.png";s:4:"eb1e";s:22:"pi/res/t_rel_group.png";s:4:"6d4e";s:21:"pi/res/t_rel_sel1.png";s:4:"6a9e";s:25:"pi/res/t_rel_selmulti.png";s:4:"5bdb";s:21:"pi/res/t_rel_selx.png";s:4:"810e";s:24:"pi/res/t_rel_wizards.png";s:4:"9d71";s:16:"pi/res/t_rte.png";s:4:"200b";s:17:"pi/res/t_rte2.png";s:4:"d27c";s:22:"pi/res/t_rte_class.png";s:4:"786e";s:22:"pi/res/t_rte_color.png";s:4:"0d25";s:28:"pi/res/t_rte_colorpicker.png";s:4:"b69e";s:27:"pi/res/t_rte_fullscreen.png";s:4:"f043";s:23:"pi/res/t_rte_hideHx.png";s:4:"c67d";s:16:"pi/res/t_sel.png";s:4:"c49b";s:25:"pi/res/t_select_icons.png";s:4:"24c7";s:21:"pi/res/t_textarea.png";s:4:"1212";s:25:"pi/res/t_textarea_wiz.png";s:4:"cf7b";s:13:"res/clear.gif";s:4:"cc11";s:15:"res/default.gif";s:4:"475a";s:21:"res/default_black.gif";s:4:"355b";s:20:"res/default_blue.gif";s:4:"4ad7";s:21:"res/default_gray4.gif";s:4:"a25c";s:21:"res/default_green.gif";s:4:"1e24";s:22:"res/default_purple.gif";s:4:"78eb";s:19:"res/default_red.gif";s:4:"dc05";s:22:"res/default_yellow.gif";s:4:"401f";s:16:"res/notfound.gif";s:4:"1bdc";s:23:"res/notfound_module.gif";s:4:"8074";s:11:"res/wiz.gif";s:4:"02b6";}',
);

?>