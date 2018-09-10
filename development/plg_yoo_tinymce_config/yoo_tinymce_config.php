<?php
<%= phpbanner %>

// jimport( 'joomla.plugin.plugin');
// jimport( 'joomla.html.parameter');

class plgSystemYoo_tinymce_config extends JPlugin
{

	function onAfterDispatch()
	{
		$app = JFactory::GetApplication();
		$doc = JFactory::GetDocument();

		if($app->isAdmin()) {

			$db = JFactory::GetDbo();
			$db->setQuery('SELECT template FROM #__template_styles WHERE client_id=0 AND home=1');
			$defaultTmpl = $db->loadResult();

			$templatePath = '/templates/'.$defaultTmpl.'/';
			$assetsPath = '/plugins/system/yoo_tinymce_config/assets/';

			$customSetup = "{}";

			if(file_exists(JPATH_ROOT . $templatePath.'css/tinymce.json')) {
				$jsonFile = $templatePath.'css/tinymce.json';
			} else if(file_exists(JPATH_ROOT . $assetsPath.'tinymce.json')) {
				$jsonFile = $assetsPath.'tinymce.json';
			} else {
				$jsonFile = false;
			}
			if($jsonFile) {
				$customSetup = file_get_contents(JPATH_ROOT . $jsonFile);
				if(!is_object(json_decode($customSetup))) $customSetup = "{_error:'invalid json data!'}";
			}

			if(file_exists(JPATH_ROOT . $templatePath.'css/tinymce.css')) {
				$cssFile = $templatePath.'css/tinymce.css';
			} else if(file_exists(JPATH_ROOT . $assetsPath.'tinymce.css')) {
				$cssFile = $assetsPath.'tinymce.css';
			} else {
				$cssFile = false;
			}
			if($cssFile) $cssFile = 'content_css : "'.$cssFile.'",';


			$js = "
			var tinyMCEoptions = Joomla.getOptions('plg_editor_tinymce');
			if(tinyMCEoptions) {
				tinyMCEoptions.tinyMCE.default = jQuery.extend(tinyMCEoptions.tinyMCE.default,{
					{$cssFile}
					plugins : 'autolink,lists,colorpicker,paste,link,code,image,wordcount,autosave,codesample,contextmenu',
					codesample_languages: [
						{text: 'HTML/XML', value: 'markup'},
						{text: 'JavaScript', value: 'javascript'},
						{text: 'CSS', value: 'css'},
						{text: 'PHP', value: 'php'},
						{text: 'Ruby', value: 'ruby'},
						{text: 'Python', value: 'python'},
						{text: 'Java', value: 'java'},
						{text: 'C', value: 'c'},
						{text: 'C#', value: 'csharp'},
						{text: 'C++', value: 'cpp'}
					],
					preview_styles : 'font-family font-size font-weight font-style text-decoration text-transform color',
					forced_root_block : false,
					setup : function(editor) {
						editor.on('init', function () {
							// this.addShortcut('alt+shift+m', '', function () {});
							this.shortcuts.remove('ctrl+s');
							this.shortcuts.remove('esc');
						});
					}
				},{$customSetup});
			}
			";
			$doc->addScriptDeclaration($js);


			$css = "
			.com_zoo div.repeat-elements li.repeatable-element div.repeatable-content { float:left; width: 97%; }
			";
			$doc->addStyleDeclaration($css);

		}
	}

}
?>
