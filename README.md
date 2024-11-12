# WI TinyMCE config plugin

A Joomla 4 system plugin adding more power to the tinyMCE editor.

# Features:
1. Search for [current_template]/css/tinymce.css and, if found, add it as editor stylesheet when tinyMCE is used. This allows the user to see the true colors, sizes and fonts while creating their text contents.
2. Search for [current_template]/css/tinymce.json. This file may contain a custom setup configuration which will be appended to default tinyMCE configuration. This allows developers to specify e.g. template-specific paragraph or text styles.

# Building
Make sure [NodeJS](https://nodejs.org/en/download/) is installed, then download or clone the repository: 
`git clone https://github.com/jbjhjm/wi_tinymce_config.git`

Change to development directory and run `npm install`.

Use `grunt build` to create a plugin installer package.
Use `grunt stage` or `grunt watch:stage` to copy files to a local joomla installation (default path is ../staging/).
