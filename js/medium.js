(function ($, Drupal, MediumEditor) {
'use strict';

/**
 * @file
 * Defines Medium as a Drupal editor.
 */

/**
 * Define editor methods.
 */
if (Drupal.editors) Drupal.editors.medium = {

    attach: function (element, format) {
  //console.log($field.value());
  var $field = $('#' + element.id);
  $field.wrap('<div class="editable-wrapper" />');
  $field.parent().append('<div class="medium-editor-container">' + $field.val() + '</div>');
  $field.parent().parent().parent().find('label').hide();
  $field.hide();
  var editor = new MediumEditor('.editable-wrapper');
  console.log(Drupal.editors.medium);
  },
  detach: function (element, format, trigger) {
    return MediumEditor.detach(element);
  },
  onChange: function (element, callback) {
  }
};
// When the form is submitted, copy the Medium editor contents
      // to the related textarea.
      $('form').submit(function(event) {
        $('.editable').each(function() {
          var editorKey = $field.val().parent().attr('data-editor-key');
          var editorValue = Drupal.editors.medium[editorKey].serialize();
          alert( "$field.val()).val()" );
          $field.val().val(editorValue["element-0"].value);
        });
      });
})(jQuery, Drupal, MediumEditor);
