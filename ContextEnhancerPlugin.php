<?php

/**
 * @file plugins/generic/contextEnhancer/ContextEnhancerPlugin.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ContextEnhancerPlugin
 * @ingroup plugins_generic_contextenhancer
 *
 * @brief Journal Metadata Exchange Format plugin class
 */

namespace APP\plugins\generic\contextEnhancer;

use APP\core\Application;
use APP\template\TemplateManager;
use APP\plugins\generic\contextEnhancer\ContextEnhancerSettingsForm;
use PKP\core\JSONMessage;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use PKP\facades\Locale;

import('lib.pkp.classes.plugins.GenericPlugin');

class ContextEnhancerPlugin extends GenericPlugin {

    const CONFIG_VARS = array(
        'journalDOI' => 'string',
        'publisherLocation' => 'string',
        'peerReviewUsed' => 'bool',
        'journalKeywords' => 'string',
    );
    const MULTILINGUAL = array(
        'journalKeywords'
    );

    /**
     * @copydoc Plugin::register()
     */
    function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled($mainContextId)) {

            //adds new metadata to context schema
            Hook::add('Schema::get::context', [$this, 'addToSchema']);

            //injects context object with specific metadata. Enhancing the displayed informations.
            Hook::add('TemplateManager::display', [$this, 'injectContextObject']);
        }
        return $success;
    }

    /**
     * Extend the context entity's schema with an aditionals properties
     */
    public function addToSchema(string $hookName, array $args) {
        $schema = $args[0];/** @var stdClass */
        $schema->properties->journalKeywords = (object) [
                    'type' => 'string',
                    'multilingual' => true,
                    'validation' => ['nullable'],
        ];
        $schema->properties->journalDOI = (object) [
                    'type' => 'string',
                    'validation' => ['nullable'],
        ];
        $schema->properties->publisherLocation = (object) [
                    'type' => 'string',
                    'validation' => ['nullable'],
        ];
        $schema->properties->peerReviewUsed = (object) [
                    'type' => 'boolean',
                    'validation' => ['nullable'],
        ];
        return false;
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName() {
        return __('plugins.generic.contextEnhancer.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription() {
        return __('plugins.generic.contextEnhancer.description');
    }

    /**
     * @copydoc Plugin::getActions()
     */
    public function getActions($request, $verb) {
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        return array_merge(
                $this->getEnabled() ? array(
            new LinkAction(
                    'settings',
                    new AjaxModal(
                            $router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
                            $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
            ),
                ) : array(),
                parent::getActions($request, $verb)
        );
    }

    /**
     * @copydoc Plugin::manage()
     */
    public function manage($args, $request) {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();

                $templateMgr = TemplateManager::getManager($request);
                $templateMgr->registerPlugin('function', 'plugin_url', array($this, 'smartyPluginUrl'));

                $form = new ContextEnhancerSettingsForm($this, $context);
                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new JSONMessage(true);
                    }
                } else {
                    $form->initData();
                }
                return new JSONMessage(true, $form->fetch($request));
        }
        return parent::manage($args, $request);
    }

    public function injectContextObject($hookName, $args) {
        $templateMgr = $args[0];
        $template = $args[1];

        $request = Application::get()->getRequest();
        $context = $request->getContext();
        $contextId = $context->getId();

        // Get the currentContext object from template manager
        $currentContext = $templateMgr->getTemplateVars('currentContext');
        $currentLocale = Locale::getLocale();

        // You can specify the template page where you want to do the change
//        if ($template !== "frontend/pages/about.tpl") return false;


        if ($currentContext) {
            $aboutText = $currentContext->getLocalizedSetting('about');

            if ($aboutText === null) $aboutText = "";

            // get the specific data from context object and add variables to the description
            foreach (self::CONFIG_VARS as $configVar => $type) {
                if($type == "bool"){
                    $loadedData = (bool) $context->getData($configVar);
                    $loadedData = $loadedData ? __('plugins.generic.contextEnhancer.settings.yes') : __('plugins.generic.contextEnhancer.settings.no');
                } elseif (in_array($configVar, self::MULTILINGUAL)) {
                    $loadedData = $context->getData($configVar, $currentLocale);

                } else {
                    $loadedData = $context->getData($configVar);
                }

                /* Publisher location */
                if ($configVar == "publisherLocation" && $loadedData) {
                    $isoCodes = new \Sokil\IsoCodes\IsoCodesFactory();
                    $loadedData = $loadedData ? $isoCodes->getCountries()->getByAlpha2($loadedData)->getLocalName() : null;
                }

                if($loadedData){
                    $aboutText .= "<p>".__('plugins.generic.contextEnhancer.settings.'.$configVar) . " " . $loadedData . "</p>";
                }
            }


            // Content update inside object
            $currentContext->setData('about', $aboutText, $currentLocale);
        }

        // Assign whole updated object to template
        $templateMgr->assign(array(
            'currentContext' => $currentContext,
        ));

        return false;
    }

}
