<?php

$TYPO3_CONF_VARS['EXTCONF']['kickstarter']['sections'] = array(
	'emconf' => array(
		'classname' => 'tx_kickstarter_section_emconf',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_emconf.php',
		'titel' => 'General info',
		'description' => 'Enter general information about the extension here: Title, description, category, author...',
	),
	'tables' => array(
		'classname' => 'tx_kickstarter_section_tables',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_tables.php',
		'titel' => 'New Database Tables',
		'description' => 'Add database tables which can be edited inside the backend. These tables will be added to the global TCA array in TYPO3.',
		'image' => 'EXT:kickstarter/icons/cm.png',
	),
	'fields' => array(
		'classname' => 'tx_kickstarter_section_fields',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_fields.php',
		'titel' => 'Extend existing Tables',
		'description' => 'Add custom fields to existing tables, such as the "pages", "tt_content", "fe_users" or "be_users" table.',
	),
	'pi' => array(
		'classname' => 'tx_kickstarter_section_pi',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_pi.php',
		'titel' => 'Frontend Plugins',
		'description' => 'Create frontend plugins. Plugins are web applications running on the website itself (not in the backend of TYPO3). The default guestbook, message board, shop, rating feature etc. are examples of plugins.',
	),
	'module' => array(
		'classname' => 'tx_kickstarter_section_module',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_module.php',
		'titel' => 'Backend Modules',
		'description' => 'Create backend modules. A module is normally recognized as the application behind one of the TYPO3 backend menuitems. Examples are the Web>Page, Web>List, User>Setup, Doc module etc. In a more loose sense, all applications integrated with existing module (see below) also belongs to the "module" category.',
	),
	'modulefunction' => array(
		'classname' => 'tx_kickstarter_section_modulefunction',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_modulefunction.php',
		'titel' => 'Integrate in existing Modules',
		'description' => 'Extend existing modules with new function-menu items. Examples are extensions such as "User>Task Center, Messaging" which adds internal messaging to TYPO3. Or "Web>Info, Page TSconfig" which shows the Page TSconfiguration for a page. Or "Web>Func, Wizards, Sort pages" which is a wizard for re-ordering pages in a folder.',
	),
	'cm' => array(
		'classname' => 'tx_kickstarter_section_cm',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_cm.php',
		'titel' => 'Clickmenu items',
		'description' => 'Adds a custom item to the clickmenus of database records. This is a very cool way to integrate small tools of your own in an elegant way!',
	),
	'sv' => array(
		'classname' => 'tx_kickstarter_section_sv',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_sv.php',
		'titel' => 'Services',
		'description' => 'Create a Services class. With a Services extension you can extend TYPO3 (or an extension which use Services) with functionality, without any changes to the code which use that service.',
	),
	'ts' => array(
		'classname' => 'tx_kickstarter_section_ts',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_ts.php',
		'titel' => 'Static TypoScript code',
		'description' => 'Adds static TypoScript Setup and Constants code - just like a static template would do.',
	),
	'tsconfig' => array(
		'classname' => 'tx_kickstarter_section_tsconfig',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_tsconfig.php',
		'titel' => 'TSconfig',
		'description' => 'Adds default Page-TSconfig or User-TSconfig. Can be used to preset options inside TYPO3.',
	),
	'phpfile' => array(
		'classname' => 'tx_kickstarter_section_phpfile',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_phpfile.php',
		'titel' => 'Custom PHP File',
		'description' => 'Here you can create plain PHP files.',
	),
	'languages' => array(
		'classname' => 'tx_kickstarter_section_languages',
		'filepath' => 'EXT:kickstarter/sections/class.tx_kickstarter_section_languages.php',
		'titel' => 'Setup languages',
		'description' => 'Start here by entering the number of system languages you want to use in your extension.',
	),
);

?>