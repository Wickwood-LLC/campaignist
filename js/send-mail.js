/**
 * @file
 * Fullcalendar View plugin JavaScript file.
 */

(function($, Drupal) {
  Drupal.behaviors.fullcalendarView = {
    attach: function(context, settings) {
      $('.campaignist-send-mail-button', context).click(function(){
        $form = $(this).closest("form");
        $form.submit();
      });

      var clipboard = new ClipboardJS('.campaignist-textfield-copier .textfield-copier-button', {
        target: function(trigger) {
          let $wrapper = $(trigger).closest(".campaignist-textfield-copier");
          return $('.value', $wrapper)[0];
        }
      });

      $('.campaignist-textfield-copier .textfield-copier-button', context).click(function(){
        let that = this;
        $(this)
          .attr("disabled", true)
          .text($(that).attr('data-copied-label'));
        setTimeout(function(){
          $(that)
            .text($(that).attr('data-regular-label'))
            .attr("disabled", false);
        }, $(this).attr('data-disable-time'));
      });
      
    }
  };
})(jQuery, Drupal);
