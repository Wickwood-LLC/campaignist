/**
 * @file
 * Fullcalendar View plugin JavaScript file.
 */

(function($, Drupal) {
  var textFile = null,
  makeTextFile = function (text) {
    var data = new Blob([text], {type: 'text/plain'});
    if (textFile !== null) {
      window.URL.revokeObjectURL(textFile);
    }
    textFile = window.URL.createObjectURL(data);
    return textFile;
  };

  Drupal.behaviors.fullcalendarView = {
    attach: function(context, settings) {
      $('.campaignist-send-mail-button', context).click(function(){
        $form = $(this).closest("form");
        let recipient = $form.find('input[name="recipient"]').val();
        let subject = $form.find('input[name="subject"]').val();
        // .replace(/(\r\n|\n|\r)/gm,"%0D%0A")
        let body = $form.find('input[name="body"]').val().replace(/ /gm,"%20");
        let mail = $form.find('textarea[name="mail"]').val();
        $form.attr('action', 'mailto:' + recipient + '?subject=' + subject + '&body=' + body);
        $form.submit();

        $download_link = $(this).siblings('.download-link');
        $download_link.attr('href', makeTextFile(mail));
        $download_link.show();
        $download_link.trigger( "click" );
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
