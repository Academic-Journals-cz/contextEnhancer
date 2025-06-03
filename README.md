# Context Enhancer plugin

About
-----
An OJS plugin that allows additional data to be added to the journal’s context and
its publications to be shown on the frontend.
This works independet from hooks which allows easy compatibility with OJS versions.

# Installation
 clone the plugin into  OJS generic plugins folder

```
cd ${OJS}/plugins/generic
git clone https://github.com/munipress/contextEnhancer
Goto  OJS  Backend e.g. http://<localhost>/index.php/<my.journal>/management/settings/website#plugins
Active the plugin: Context enhancer [X]
Click on the plugin settings and activate the needed forms

```
## Customization
### Prequisites
- Basic PHP Skills are required, php template smarty language is helpful.
### Steps
#### Adding variables to the context settings table.

- Define  the name of the fields required in [ContextEnh](https://github.com/munipress/contextEnhancer/blob/stable-3_3_0/ContextEnhancerSettingsForm.inc.php) in `CONFIG_VARS`
- Use `MULTILINGUAL`, if the field needs to be multilingual.
- Add those variables to the context schema in the method `addToSchema` in [ContextEnhanceerPlugin](https://github.com/munipress/contextEnhancer/blob/stable-3_3_0/ContextEnhancerPlugin.inc.php)

#### Injecting the variables into the current context object
- Info: Method `injectContextObject` injects the metadata to the context settings table
- The conditional statement comparing [template name](https://github.com/withanage/contextEnhancer/blob/stable-3_3_0/ContextEnhancerPlugin.inc.php) `if ($template !== "frontend/pages/about.tpl") ` adds the  information to the needed templates.
- Specify  the name of the template to the pages, you require.

#### Customized displaying of the variables






License
-------
This plugin is licensed under the GNU General Public License v3. See the file LICENSE for the complete terms of this license.

System Requirements
-------------------
OJS 3.3 or greater.
PHP 7.0 or greater.

Version History
---------------


Support for OJS 3.3.0

