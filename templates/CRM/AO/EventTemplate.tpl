{if $currentTemplate}
{literal}
<script type="text/javascript">
CRM.$(function($) {
  var currentTemplate = '{/literal}{$currentTemplate}{literal}';
  var waiver = 'custom_{/literal}{$smarty.const.WAIVER_5}{literal}';

  switch (currentTemplate) {
    case 'SLO Evidence Based Programs':
    case 'SLO Health & Fitness':
    case 'SLO Recreation':
      $('.crm-section _of_professionals-section').hide();
      break;
    case 'SLO Skill Building':
      $('.crm-section _of_professionals-section').hide();
      $('#editrow-'+waiver).hide();
      break;
    case 'Workshop Behaviour':
    case 'Workshop Communication':
    case 'Workshop - Other':
    case 'Workshop - Social':
      $('#editrow-'+waiver).hide();
      break;
    default:
      break;
}
});
</script>
{/literal}
{/if}