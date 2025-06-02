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
import('lib.pkp.classes.plugins.GenericPlugin');

class ContextEnhancerPlugin extends GenericPlugin {

    /**
     * @copydoc Plugin::register()
     */
    function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled($mainContextId)) {

            HookRegistry::register('Schema::get::context', [$this, 'addToSchema']);
            

        }
        return $success;
    }

    /**
     * Extend the context entity's schema with an aditionals properties
     */
    public function addToSchema(string $hookName, array $args)
    {
      $schema = $args[0]; /** @var stdClass */
      $schema->properties->journalKeywords = (object) [
          'type' => 'string',
          'multilingual' => true,
          'validation' => ['nullable'],
      ];
      $schema->properties->ownerType = (object) [
          'type' => 'string',
          'validation' => ['nullable'],
      ];
      $schema->properties->journalDDH = (object) [
          'type' => 'string',
          'validation' => ['nullable'],
      ];
      $schema->properties->journalDOAJ = (object) [
          'type' => 'string',
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
      $schema->properties->openAuthorship = (object) [
          'type' => 'boolean',
          'validation' => ['nullable'],
      ];
      return false;
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    function getDisplayName() {
        return __('plugins.generic.contextEnhancer.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    function getDescription() {
        return __('plugins.generic.contextEnhancer.description');
    }

    /**
     * @copydoc Plugin::getActions()
     */
    function getActions($request, $verb) {
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
    function manage($args, $request) {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();

                AppLocale::requireComponents(LOCALE_COMPONENT_APP_COMMON, LOCALE_COMPONENT_PKP_MANAGER);
                $templateMgr = TemplateManager::getManager($request);
                $templateMgr->registerPlugin('function', 'plugin_url', array($this, 'smartyPluginUrl'));

                $this->import('ContextEnhancerSettingsForm');
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

}
