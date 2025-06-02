<?php

/**
 * @defgroup plugins_generic_contextenhancer Context metadata enhancing Plugin
 */
 
/**
 * @file plugins/generic/contextEnhancer/index.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_contextenhancer
 * @brief Wrapper for contextEnhancer plugin.
 *
 */
require_once('ContextEnhancerPlugin.inc.php');

return new ContextEnhancerPlugin();


