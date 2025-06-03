# Context Enhancer plugin

About
-----
An OJS plugin that allows additional data to be added to the journal’s context and
its publications to be shown on the frontend.
This works independet from hooks which allows easy compatibility with OJS versions withot any dependcy on themes.

# Installation
 clone the plugin into  OJS generic plugins folder

```
cd ${OJS}/plugins/generic
git clone https://github.com/munipress/contextEnhancer
Goto  OJS  Backend e.g. http://<localhost>/index.php/<my.journal>/management/settings/website#plugins
Active the plugin: Context enhancer [X]
Click on the plugin settings and activate the needed metadata fields

```
## Customization
### Prequisites
- Basic PHP Skills are required, php template smarty language is helpful.
### Steps
#### Adding variables to the context settings table.

- Define  the name of the fields required in [ContextEnhancerSettingsForm](https://github.com/munipress/contextEnhancer/blob/stable-3_3_0/ContextEnhancerSettingsForm.inc.php) in `CONFIG_VARS`
- Use `MULTILINGUAL`, if the field need to be multilingual.
- Add defined variables above to the context schema in the method `addToSchema` in [ContextEnhancerPlugin](https://github.com/munipress/contextEnhancer/blob/stable-3_3_0/ContextEnhancerPlugin.inc.php)

#### Injecting the variables into the current context object
- Info: Method `injectContextObject` injects the metadata to the context settings table
- The conditional statement comparing [template name](https://github.com/withanage/contextEnhancer/blob/stable-3_3_0/ContextEnhancerPlugin.inc.php)  e.g. `if ($template !== "frontend/pages/about.tpl") ` adds the  information to the needed template.
- Specify the name of the template page, you require if you need to, or the method will be modifying the context object all the time
- Use context variable for adding the new metadata. Plugin needs existing context variables (like: “description”, “about” , “authorInformation”, or even “customHeaders” and many others.)

#### Customized displaying of the variables
Current implementation inside method  `injectContextObject` adds the variables into  the description  of the context.
Use any available template variables to inject these  objects into the required page.

Important: Injection only depends on the availability of the template variables, not any OJS hooks.

```php
            // get the specific data from context object and add variables to the description
            foreach (self::CONFIG_VARS as $configVar => $type) {

                if($type == "bool"){
                    $loadedData = $loadedData ? __('plugins.generic.contextEnhancer.settings.yes') : __('plugins.generic.contextEnhancer.settings.no');
                } elseif (in_array($configVar, self::MULTILINGUAL)) {
                    $loadedData = $context->getData($configVar, $currentLocale);
                    
                } else {
                    $loadedData = $context->getData($configVar);
                }
                
                if($loadedData){
                    $about .= "<p>".__('plugins.generic.contextEnhancer.settings.'.$configVar) . " " . $loadedData;
                }
            }

```



License
-------
This plugin is licensed under the GNU General Public License v3. See the file LICENSE for the complete terms of this license.

System Requirements
-------------------
OJS 3.3 or greater.
PHP 8.2 or greater.

## Support
Support for OJS 3.3.0

### Developed by
- Radek Gomola (Lead, Masaryk University Press)
- Dulip Withanage (TIB)
- Rob Arnold (Ubiquity Press)

### Additional information
- This template plugin was developed in PKP Sprint 2025, Oslo, Norway based on the previous work for CRAFT-OA Diamond plugins made by Radek Gomola.
