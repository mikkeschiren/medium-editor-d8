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
  },
  detach: function (element, format, trigger) {
    return MediumEditor.detach(element);
  },
  onChange: function (element, callback) {
  }
};
})(jQuery, Drupal, MediumEditor);
