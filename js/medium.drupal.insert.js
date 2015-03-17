//js.
(function ($, Drupal, MediumEditor) {
  $(".form-textarea-wrapper").prepend("\
<div id='media_insert'>\n\<a href='/medium/image/modal/full_html/' class='use-ajax'  data-accepts='application/vnd.drupal-modal'>media</a></div>");
})(jQuery, Drupal, MediumEditor);
