<?php

/**
 * @file ContextEnhancerSettingsForm.inc.php
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ContextEnhancerSettingsForm
 * @ingroup plugins_generic_contextenhancer
 *
 * @brief Form for journal managers to modify Context Enhancer plugin settings
 */

// $Id$


import('lib.pkp.classes.form.Form');

class ContextEnhancerSettingsForm extends Form {

        const CONFIG_VARS = array(
		'journalDOI' => 'string',
		'publisherLocation' => 'string',
		'peerReviewUsed' => 'bool',
		'journalKeywords' => 'string',
	);
        
        const MULTILINGUAL = array(
            'journalKeywords'
        );
        
	/** @var int */
	var $_contextId;

	/** @var object */
	var $_plugin;
        
        /** @var context **/
        var $_context;
        
	/**
	 * Constructor
	 * @param $plugin object
	 * @param $contextId int
	 */
	function __construct($plugin, $context) {
		$this->_plugin = $plugin;
                $this->_context = $context;

		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
                $this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Initialize form data.
	 */
	function initData() {
		$this->_data = array();
		$context = $this->_context;
		foreach (self::CONFIG_VARS as $configVar => $type) {
			$this->_data[$configVar] = $context->getSetting($configVar);
		}
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array_keys(self::CONFIG_VARS));
	}

        /**
	 * @copydoc Form::fetch()
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager($request);
                
                
                $isoCodes = new \Sokil\IsoCodes\IsoCodesFactory();
		$countries = array();
		foreach ($isoCodes->getCountries() as $country) {
			$countries[$country->getAlpha2()] = $country->getLocalName();
		}
		asort($countries);
                
                $templateMgr->assign('publisherName', $this->_context->getData('publisherInstitution'));  
		$templateMgr->assign('countries', $countries);                
		$templateMgr->assign('pluginName', $this->_plugin->getName());
                $templateMgr->assign('applicationName', Application::get()->getName());
		return parent::fetch($request, $template, $display);
	}
        
        /**
	 * @copydoc Form::execute()
	 */
	function execute(...$functionArgs) {
                
                $context = $this->_context;
                
                foreach (self::CONFIG_VARS as $configVar => $type) {                    
                    if(in_array($configVar, self::MULTILINGUAL)){
                        $context->setData($configVar, $this->getData($configVar, null));   
                    } else {
                        $context->setData($configVar, $this->getData($configVar));  
                    }
                }                
                parent::execute(...$functionArgs);
                
		$contextDao = DAORegistry::getDAO('JournalDAO'); /* @var $contextDao JournalDAO */
		$contextDao->updateObject($context);
	}
}

?>
