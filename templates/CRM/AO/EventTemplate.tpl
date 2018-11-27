{if $currentTemplate}
{literal}
<script type="text/javascript">
CRM.$(function($) {
  var currentTemplate = '{/literal}{$currentTemplate}{literal}';
  var waiver = '{/literal}{$smarty.const.WAIVER_5}{literal}';
  var zeroprice = '{/literal}{$zeroPrice}{literal}';

  $('fieldset.crm-profile-name-Waivers_35 div.label').hide();
  if (zeroprice) {
    $('._siblings-content span.price-field-amount').html('');
    $('._of_professionals-content span.price-field-amount').html('');
    $('._of_volunteers-content span.price-field-amount').html('');
  }
  switch (currentTemplate) {
    case 'SLO Evidence Based Programs':
    case 'SLO Health & Fitness':
    case 'SLO Recreation':
      $('._of_professionals-section').hide();
      break;
    case 'SLO Skill Building':
      $('._of_professionals-section').hide();
      $('#editrow-'+waiver).hide();
      $('#helprow-'+waiver).hide();
      break;
    case 'Workshop Behaviour':
    case 'Workshop Communication':
    case 'Workshop - Other':
    case 'Workshop - Social':
      $('#editrow-'+waiver).hide();
      $('#helprow-'+waiver).hide();
      break;
    default:
      break;
}
});
</script>
{/literal}
{/if}
