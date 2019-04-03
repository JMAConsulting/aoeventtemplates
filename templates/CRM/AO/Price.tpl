{literal}
<script type="text/javascript">
CRM.$( function($) {
  var eventType = '{/literal}{$eventTypeID}{literal}';
  if (eventType) {
    hidePriceSet(eventType);
  }
   
  $('#event_id').change(function() {
    var eventid = $(this).val();
    if (eventid) {
      CRM.api3('Event', 'getvalue', {
        "return": "event_type_id",
        "id": eventid
      }).done(function(result) {
        hidePriceSet(result.result);
      });
    }
  });

  function hidePriceSet(type) {
    var validtypes = ['8','18','9','10','19','20','21','22'];
    if ($.inArray(type, validtypes) !='-1') {
      $( document ).ajaxComplete(function( event, xhr, settings ) {
        $('.price-field-amount').html('$ 0.00');
      });
    }
  }
});
</script>
{/literal}
