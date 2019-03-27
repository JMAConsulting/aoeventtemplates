<?php
define('TEMPLATE_ID', 327);

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

function aoeventtemplates_civicrm_buildAmount($pageType, &$form, &$amount) {
  $zeroTemplates = [
    'Community Awareness',
    'SLO Evidence Based Programs',
    'SLO Health & Fitness',
    'SLO Recreation',
    'SLO Skill Building',
    'SLO Support Groups - Facilitated',
    'SLO Support Groups - Meetup',
    'Workshop Community Training',
  ];
  if (get_class($form) == "CRM_Event_Form_Registration_Register") {
    $templateId = civicrm_api3('Event', 'get', [
      'id' => $form->_eventId,
      'return.custom_' . TEMPLATE_ID => 1,
    ])['values'][$form->_eventId]['custom_' . TEMPLATE_ID];
    if ($templateId) {
      $template = getEventTemplates($templateId);
      if (in_array($template, $zeroTemplates)) {
        $form->assign('zeroPrice', TRUE);
        foreach ($amount as $key => &$val) {
          $val['is_display_amounts'] = 0;
          foreach ($val['options'] as $pid => &$pf) {
            $pf['amount'] = 0.00;
          }
        }
      }
    }
  }
  if (get_class($form) == "CRM_Event_Form_Participant" && $pageType == 'event') {
    $eventTypes = CRM_Core_OptionGroup::values('event_type');
    $eventType = CRM_Core_DAO::singleValueQuery("SELECT event_type_id FROM civicrm_event WHERE id = {$form->_eventId}");
    if (array_search($eventTypes[$eventType], $zeroTemplates) && !empty($amount)) {
      $form->assign('zeroPrice', TRUE);
      foreach ($amount as $key => &$val) {
        $val['is_display_amounts'] = 0;
        foreach ($val['options'] as $pid => &$pf) {
          $pf['amount'] = 0.00;
        }
      }
    }
  }
}

function aoeventtemplates_civicrm_links($op, $objectName, $objectId, &$links, &$mask, &$values) {
  if ($op == "event.manage.list") {
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    if (!array_search('senior_staff', $roles) && !array_search('administrator', $roles)) {
      unset($links[3]);
    }
  }
}

function aoeventtemplates_civicrm_pageRun(&$page) {
  if (get_class($page) == 'CRM_Event_Page_EventInfo') {
    $feeBlock = CRM_Core_Smarty::singleton()->get_template_vars('feeBlock');
    $feeBlock['isDisplayAmount'][9] = $feeBlock['isDisplayAmount'][10] = $feeBlock['isDisplayAmount'][11] = 0;
    CRM_Core_Smarty::singleton()->assign('feeBlock', $feeBlock);
    
    CRM_Core_Resources::singleton()->addScript(
      "CRM.$(function($) {
        $('table.fee_block-table tr:nth-child(2)').hide();
        $('table.fee_block-table tr:nth-child(3)').hide();
        $('table.fee_block-table tr:nth-child(4)').hide();
        $('table.fee_block-table tr:nth-child(5)').hide();
        $('table.fee_block-table tr:nth-child(7)').hide();
        $('table.fee_block-table tr:nth-child(8)').hide();
        $('#Event_Template__14').hide();
        $('#Event_Template__25').hide();
        $('#Event_Template__331 > div.crm-accordion-wrapper > div.crm-accordion-body > table:nth-child(1)').hide();
      });"
    );
  }
  if (get_class($page) == 'CRM_Admin_Page_EventTemplate') {
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    if (!array_search('senior_staff', $roles) && !array_search('administrator', $roles)) {
      CRM_Core_Error::fatal(ts('You do not have permission to access this page.'));
    }
  }
  if (get_class($page) == "CRM_Contact_Page_View_Summary") {
    CRM_Core_Resources::singleton()->addScript(
      "CRM.$(function($) {
        $('#tab_custom_22').hide();
      });"
    ); 
  }
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
  if ($formName == "CRM_Event_Form_ManageEvent_EventInfo" && ($form->_action & CRM_Core_Action::ADD)) {
    $defaults = [
      'start_date' => date('m/d/Y'),
    ];
    $form->setDefaults($defaults);
  }

  $current_user = \Drupal::currentUser();
  $roles = $current_user->getRoles();
  // Set frozen fields.
  if ($formName == "CRM_Event_Form_ManageEvent_EventInfo" && !$form->getVar('_isTemplate')) {
    if (!array_search('administrator', $roles) && !array_search('senior_staff', $roles)) {
      $freezeElements = [
        'event_type_id',
        'default_role_id',
        'is_public',
        'is_share',
        'participant_listing_id',
      ];
      $form->freeze($freezeElements);
    }
    $form->addRule('start_date', ts('Please enter the event start date.'), 'required');
    $form->addRule('end_date', ts('Please enter the event end date'), 'required');
    //$form->addRule('start_date_time', ts('Please enter the event start time.'), 'required');
    //$form->addRule('end_date_time', ts('Please enter the event end time.'), 'required');
    CRM_Core_Resources::singleton()->addScript(
      "CRM.$(function($) {
        $( document ).ajaxComplete(function( event, xhr, settings ) {
          $('.custom_327_25-row').hide();
        });
      });"
    );
  }
  if ($formName == "CRM_Event_Form_ManageEvent_Location" && !$form->getVar('_isTemplate')) {
    $form->addRule('email[1][email]', ts('Please enter an email.'), 'required');
    $form->addRule('phone[1][phone]', ts('Please enter phone number.'), 'required');
  }
  if ($formName == "CRM_Event_Form_ManageEvent_Fee" && !$form->getVar('_isTemplate')) {
    if (!array_search('administrator', $roles) && !array_search('senior_staff', $roles)) {
      $freezeElements = [
        'is_monetary',
        'financial_type_id',
        'price_set_id',
        'is_pay_later',
        'payment_processor',
        'fee_label',
      ];
      $form->freeze($freezeElements);
    }
  }
  if ($formName == "CRM_Event_Form_ManageEvent_Registration" && !$form->getVar('_isTemplate')) {
    if ($form->_action & CRM_Core_Action::ADD) {
      $cid = CRM_Core_Session::singleton()->get('userID');
      if ($cid) {
        $details = CRM_Core_DAO::executeQuery("SELECT display_name, email FROM civicrm_contact c INNER JOIN civicrm_email e ON e.contact_id = c.id WHERE c.id = {$cid} AND e.is_primary = 1")->fetchAll()[0];
        if (!empty($details)) {
          $form->setDefaults(['confirm_from_name' => $details['display_name'], 'confirm_from_email' => $details['email']]);
        }
      }
    }
    if (!array_search('administrator', $roles) && !array_search('senior_staff', $roles)) {
      $freezeElements = [
        'registration_link_text',
        'is_multiple_registrations',
        'allow_same_participant_emails',
        'dedupe_rule_group_id',
        'expiration_time',
        'selfcancelxfer_time',
        'allow_selfcancelxfer',
        'confirm_title',
        'confirm_text',
        'confirm_footer_text',
        'thankyou_title',
        'thankyou_text',
        'thankyou_footer_text',
        'confirm_email_text',
        //'confirm_from_name',
        //'confirm_from_email',
      ];
      $form->freeze($freezeElements);
      CRM_Core_Resources::singleton()->addScript(
        "CRM.$(function($) {
           $('#registration_screen').find('table').next().hide();
           $('#is_online_registration').hide();
        });"
      );
    }
  }
  if ($formName == "CRM_Event_Form_ManageEvent_ScheduleReminders" && !$form->getVar('_isTemplate')) {
    if (!array_search('administrator', $roles) && !array_search('senior_staff', $roles)) {
      CRM_Core_Resources::singleton()->addScript(
        "CRM.$(function($) {
           $('.action-link').hide();
           $('.crm-scheduleReminders-is_active').next('td').hide();
        });"
      );
    }
  }
  if ($formName == "CRM_Friend_Form_Event" && !$form->getVar('_isTemplate')) {
    if (!array_search('administrator', $roles) && !array_search('senior_staff', $roles)) {
      $freezeElements = [
        'tf_is_active',
        'tf_title',
        'intro',
        'suggested_message',
        'general_link',
        'tf_thankyou_title',
        'tf_thankyou_text',
      ];
      $form->freeze($freezeElements);
    }
  }
  if ($formName == "CRM_PCP_Form_Event" && !$form->getVar('_isTemplate')) {
    if (!array_search('administrator', $roles) && !array_search('senior_staff', $roles)) {
      CRM_Core_Resources::singleton()->addScript(
        "CRM.$(function($) {
           $( document ).ajaxComplete(function( event, xhr, settings ) {
             $('#pcp_active').attr('disabled', true);
           });
        });"
      );
    }
  }
  if ($formName == "CRM_Event_Form_Participant") {
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => 'CRM/AO/Price.tpl',
    ));
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
    $eventId = CRM_Core_Session::singleton()->get('eventId');
    civicrm_api3('CustomValue', 'create', [
      'entity_id' => $eventId,
      'custom_' . TEMPLATE_ID => $form->getVar('_templateId'),
    ]);
  }
}

function aoeventtemplates_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($op == 'create' && $objectName == 'Event') {
    CRM_Core_Session::singleton()->set('eventId', $objectId);    
  }
}
