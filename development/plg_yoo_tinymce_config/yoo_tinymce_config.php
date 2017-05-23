<?php
<%= phpbanner %>

// jimport( 'joomla.plugin.plugin');
// jimport( 'joomla.html.parameter');

class plgSystemYoo_tinymce_config extends JPlugin
{

	function onAfterDispatch()
	{
		$app = JFactory::GetApplication();

		if($app->isAdmin()) {

			$js = '

			Joomla.getOptions(\'plg_editor_tinymce\').tinyMCE.default.baseURL = ""

			';

			JFactory::GetDocument()->addScriptDeclaration($js);
		}
	}

}
?>
