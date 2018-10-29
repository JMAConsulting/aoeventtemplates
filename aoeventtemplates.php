<?php
define('TEMPLATE_ID', 4);
define('WAIVER_5', 322);

require_once 'aoeventtemplates.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function aoeventtemplates_civicrm_config(&$config) {
  _aoeventtemplates_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function aoeventtemplates_civicrm_xmlMenu(&$files) {
  _aoeventtemplates_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function aoeventtemplates_civicrm_install() {
  _aoeventtemplates_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function aoeventtemplates_civicrm_uninstall() {
  _aoeventtemplates_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function aoeventtemplates_civicrm_enable() {
  _aoeventtemplates_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function aoeventtemplates_civicrm_disable() {
  _aoeventtemplates_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function aoeventtemplates_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _aoeventtemplates_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function aoeventtemplates_civicrm_managed(&$entities) {
  _aoeventtemplates_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function aoeventtemplates_civicrm_caseTypes(&$caseTypes) {
  _aoeventtemplates_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function aoeventtemplates_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _aoeventtemplates_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_buildForm
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function aoeventtemplates_civicrm_buildForm($formName, &$form) {
  if ($formName == "CRM_Event_Form_Registration_Register") {
    $templateId = civicrm_api3('Event', 'get', [
      'id' => $form->_eventId,
      'return.custom_' . TEMPLATE_ID => 1,
    ])['values'][$form->_eventId]['custom_' . TEMPLATE_ID];
    if ($templateId) {
      $template = getEventTemplates($templateId);
      $form->assign('currentTemplate', $template);
      CRM_Core_Region::instance('page-body')->add(array(
        'template' => 'CRM/AO/EventTemplate.tpl',
      ));
    }
  }
}

function getEventTemplates($id) {
  $template = CRM_Core_DAO::singleValueQuery("SELECT template_title FROM civicrm_event WHERE is_template = 1 AND id = %1", ['1' => [$id, 'Integer']]);
  return $template;
}

/**
 * Implementation of hook_civicrm_postProcess
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
 */
function aoeventtemplates_civicrm_postProcess($formName, &$form) {
  if ($formName == "CRM_Event_Form_ManageEvent_EventInfo" && !empty($form->getVar('_templateId'))) {
    CRM_Core_Session::singleton()->set('eventTemplateId', $form->getVar('_templateId'));
  }
}

/**
 * Implementation of hook_civicrm_postSave
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postSave
 */
function aoeventtemplates_civicrm_postSave_civicrm_event($dao) {
  $templateId = CRM_Core_Session::singleton()->get('eventTemplateId');
  if ($templateId) {
    civicrm_api3('CustomValue', 'create', [
      'entity_id' => $dao->id,
      'custom_' . TEMPLATE_ID => $templateId,
    ]);
    CRM_Core_Session::singleton()->set('eventTemplateId', '');
  }
}