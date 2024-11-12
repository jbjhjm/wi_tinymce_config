<?php
<%= phpbanner %>


// jimport( 'joomla.plugin.plugin');
// jimport( 'joomla.html.parameter');

class plgSystemYoo_tinymce_config extends JPlugin
{

	private $assetsPath = '/plugins/system/yoo_tinymce_config/assets/';
	private $templatePath;

	function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		// $this->_plugin = JPluginHelper::getPlugin( 'system', 'yoo_tinymce_config' );
		// $this->_params = new JRegistry( $this->_plugin->params );
		$this->loadTemplatePath();
	}

	function loadTemplatePath(){
		$db = Factory::getContainer()->get(Joomla\Database\DatabaseInterface::class);
		$db->setQuery('SELECT template FROM #__template_styles WHERE client_id=0 AND home=1');
		$defaultTmpl = $db->loadResult();
		$this->templatePath = '/templates/'.$defaultTmpl.'/';
	}

	function loadThemeOptions(){
		$themeOptionsObject = "{}";

		if(file_exists(JPATH_ROOT . $this->templatePath.'css/tinymce.json')) {
			$jsonFile = $this->templatePath.'css/tinymce.json';
		} else if(file_exists(JPATH_ROOT . $this->assetsPath.'tinymce.json')) {
			$jsonFile = $this->assetsPath.'tinymce.json';
		} else {
			$jsonFile = false;
		}
		if($jsonFile) {
			$themeOptionsObject = file_get_contents(JPATH_ROOT . $jsonFile);
			if(!is_object(json_decode($themeOptionsObject))) $themeOptionsObject = "{_error:'invalid json data!'}";
		}
		return $themeOptionsObject;
	}

	function findEditorCSSFile(){
		$cssFile = false;

		if(file_exists(JPATH_ROOT . $this->templatePath.'css/tinymce.css')) {
			$cssFile = $this->templatePath.'css/tinymce.css';
		} else if(file_exists(JPATH_ROOT . $this->assetsPath.'tinymce.css')) {
			$cssFile = $this->assetsPath.'tinymce.css';
		}

		return $cssFile;
	}

	function onAfterDispatch()
	{
		$app = Factory::GetApplication();
		$doc = $app->GetDocument();

		$enable_frontend = $this->params->get('enable_frontend', '0') == '1';

		if($enable_frontend || $app->isAdmin()) {

			$themeOptionsObject = $this->loadThemeOptions();
			$customOptionsObject = $this->getCustomConfiguration();

			$customSetupFnBody = "
				editor.on('init', function () {
					// this.addShortcut('alt+shift+m', '', function () {});
					this.shortcuts.remove('ctrl+s');
					this.shortcuts.remove('esc');
				});
			";

			$mode = 'j4';
				
			if($mode==='j3') {
				$js = $this->generateJ3EditorConfigOverride($customOptionsObject, $themeOptionsObject, $customSetupFnBody);
			} else {
				$js = $this->generateJ4EditorConfigOverride($customOptionsObject, $themeOptionsObject, $customSetupFnBody);
			}


			$doc->addScriptDeclaration($js);


			$css = "
			.com_zoo div.repeat-elements li.repeatable-element div.repeatable-content { float:left; width: 97%; }
			";
			$doc->addStyleDeclaration($css);

		}
	}

	function getCustomConfiguration() {
		$cssFile = $this->findEditorCSSFile();
		if($cssFile) $cssFile = 'content_css : "'.$cssFile.'",';
		return "{
			{$cssFile}
			plugins : 'autolink,lists,colorpicker,paste,link,code,image,wordcount,charmap,autosave,textcolor,codesample,contextmenu,table',
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
			// forced_root_block should not be false, but the name of root element to use, e.g. div or p
			// forced_root_block : false,
		}";
	}

	/**
	 * Joomla 3 allows us to get the tinyMCE configuration using Joomla.getOptions('plg_editor_tinymce').
	 * This makes it easy to update it with our own customizations.
	 */
	function generateJ3EditorConfigOverride($completedCustomOptionsObject, $themeOptionsObject, $customSetupFnBody) {
		return "
			var tinyMCEoptions = Joomla.getOptions('plg_editor_tinymce');
			if(tinyMCEoptions) {
				tinyMCEoptions.tinyMCE.default = jQuery.extend(
					tinyMCEoptions.tinyMCE.default,
					{$completedCustomOptionsObject},
					{$themeOptionsObject},
					{setup: (editor)=>{ {$customSetupFnBody} } }
				);
			}
		";
	}

	/**
	 * Joomla 4 is different. tinymce configuration is bundled within general client-side joomla configuration object.
	 * This object is found in <script type="application/json" class="joomla-script-options new">...</script>.
	 * media/system/js/core.min.js will parse the contents and load them into Joomla.optionsStorage.
	 * Its data is not available synchronously, so we need a different approach.
	 */
	function generateJ4EditorConfigOverride($completedCustomOptionsObject, $themeOptionsObject, $customSetupFnBody) {
		return "
			const original = Joomla.loadOptions;
			Joomla.loadOptions = (t) => {
				original(t);
				const tinyMCEoptions = Joomla.optionsStorage.plg_editor_tinymce;
				if(t && tinyMCEoptions) {
					const originalOptions = tinyMCEoptions.tinyMCE.default;
					const customOptions = {$completedCustomOptionsObject};
					const themeOptions = {$themeOptionsObject};

					if(tinyMCEoptions) {
						const mergedOptions = jQuery.extend(tinyMCEoptions.tinyMCE.default,customOptions,themeOptions);
						mergedOptions.setup = (editor) => {
							originalOptions.setup(editor);
							{$customSetupFnBody}
						};
						tinyMCEoptions.tinyMCE.default = mergedOptions;
					}
				}
			}
		";
	}

}
?>
