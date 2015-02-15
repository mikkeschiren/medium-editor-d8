(function ($, Drupal, MediumEditor) {
  'use strict';
  // @todo: make this more useful with attach/detach adn dynamic toolbar
  var $field = $('.form-textarea');
  // Styles not used now, placed here as placeholder.
  var styles = $field.attr('style');
  if (typeof styles != 'undefined') {
    styles = ' style="' + styles + '"';
  }
  // Create an wrapper and placeholder. Medium uses a div, not textarea.
  $field.wrap('<div class="editable-wrapper"/>');
  var $parentfild = $field.parent();
  $parentfild.prepend('<div class="medium-editable" ' + styles + ' data-placeholder="' + $field.attr('placeholder') + '">' + $field.val() + '</div>');
  // Hide the textarea, we don't use it for editing.
  $field.hide();
  var setting = $('#edit-settings-toolbar').val();
  setting.replace(/ /g, '');
  var settingSplit = setting.split(', ');

  return new MediumEditor('.editable-wrapper', {
    buttons: settingSplit,
    diffLeft: 0,
    diffTop: -10
  });

})(jQuery, Drupal, MediumEditor);
