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

			$assetsPath = '/plugins/system/yoo_tinymce_config/assets/';

			$customSetup = "{}";
			if(file_exists(JPATH_ROOT . $assetsPath . 'tinymce.json')) {
				$customSetup = file_get_contents(JPATH_ROOT . $assetsPath . 'tinymce.json');
				if(!is_object(json_decode($customSetup))) $customSetup = "{_error:'invalid json data!'}";
			}

			$js = "

			var tinyMCEoptions = Joomla.getOptions('plg_editor_tinymce');
			if(tinyMCEoptions) {
				tinyMCEoptions.tinyMCE.default = jQuery.extend(tinyMCEoptions.tinyMCE.default,{
					content_css : '{$assetsPath}tinymce.css',
					plugins : 'autolink,lists,save,colorpicker,link,code,paste,wordcount,autosave,contextmenu',
					preview_styles : 'font-family font-size font-weight font-style text-decoration text-transform color',
				},{$customSetup});
			}

			";

			JFactory::GetDocument()->addScriptDeclaration($js);
		}
	}

}
?>
