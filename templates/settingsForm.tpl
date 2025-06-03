{**
* plugins/importexport/contextEnhancer/templates/settingsForm.tpl
*
* Copyright (c) 2014-2020 Simon Fraser University
* Copyright (c) 2003-2020 John Willinsky
* Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
*
* ContextEnhancer plugin settings
*
*}
<script type="text/javascript">
    $(function () {ldelim}
            // Attach the form handler.
            $('#contextEnhancerSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
    {rdelim});
</script>
<form class="pkp_form" id="contextEnhancerSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
    {csrf}
    {fbvFormArea id="contextEnhancerSettingsFormArea"}

            
            {fbvFormSection for="journalDOI" title="plugins.generic.contextEnhancer.manager.settings.journalDoi"}
                    {fbvElement type="text" id="journalDOI" value=$journalDOI label="plugins.generic.contextEnhancer.manager.settings.journalDoi.description" size=$fbvStyles.size.MEDIUM}     
            {/fbvFormSection} 
            
            
            {fbvFormSection for="publisherLocation" title="plugins.generic.contextEnhancer.manager.settings.publisherLocation"}
            {translate key="plugins.generic.contextEnhancer.manager.settings.publisherName" publisherName=$publisherName}
                    {fbvElement type="select" label="plugins.generic.contextEnhancer.manager.settings.publisherLocation.description" name="publisherLocation" id="publisherLocation" defaultLabel="" defaultValue="" from=$countries selected=$publisherLocation translate="0" size=$fbvStyles.size.MEDIUM}
            {/fbvFormSection}
            
            {fbvFormSection for="journalKeywords" title="plugins.generic.contextEnhancer.manager.settings.keywords"}
                {fbvElement type="text" label="plugins.generic.contextEnhancer.manager.settings.keywords.description" multilingual="true" name="journalKeywords" id="journalKeywords" value=$journalKeywords  size=$fbvStyles.size.LARGE}               
            {/fbvFormSection}
            
             
            {fbvFormSection list=true title="plugins.generic.contextEnhancer.manager.settings.journalPolicy"}   
                {if $peerReviewUsed}
                        {assign var="checked" value=true}
                {else}
                        {assign var="checked" value=false}
                {/if}
                {fbvElement type="checkbox" name="peerReviewUsed" id="peerReviewUsed" checked=$checked label="plugins.generic.contextEnhancer.manager.settings.peerReviewUsed"}
            {/fbvFormSection}
    {/fbvFormArea}
    {fbvFormButtons submitText="common.save"}
    <p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</form>
