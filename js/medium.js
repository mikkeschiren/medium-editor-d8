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
  // We attach a new editor when we are changing editor in ui.
  attach: function (element, format) {
  // Check if medium is true. @todo: is this really needed?
  if (format.editor == 'medium') {
    var $field = $('#' + element.id);
    // Styles not used now, placed here as placeholder.
    var styles = $field.attr('style');
    if (typeof styles != 'undefined') {
        styles = ' style="' + styles + '"';
    }
    // Create an wrapper and placeholder. Medium uses a div, not textarea.
    $field.wrap('<div class="editable-wrapper"/>');
    var $parentfield = $field.parent();
    $parentfield.prepend('<div class="medium-editable" ' + styles + ' data-placeholder="'+$field.attr('placeholder')+'">' + $field.val()+'</div>');
    // Hide the textarea, we don't use it for editing.
    $field.hide();
    // We need to copy the value from the div to the normal textarea on submit.
    $('form').submit(function(){
      $('.editable-wrapper').each(function(){
        var mediumText = $(this).find('.medium-editable').html();
        var textArea = $(this).find('textarea');
        // We need to set the value attribute to changed.
        textArea.attr( 'data-editor-value-is-changed', 'true' );
        textArea.val(mediumText);
      });

    });
    // Get buttons to use from configuration.
    toolbar = format.editorSettings.toolbar;
    return new MediumEditor('.medium-editable', {
      buttons: toolbar,
      diffLeft: 0,
      diffTop: -10
    });
    }
  },
  // When changing editor, we remove everything Medium.
  detach: function (element, format, trigger) {
    var $field = $('#' + element.id);
    $('.editable-wrapper').each(function(){
      var mediumText = $(this).find('.medium-editable').html();
      var textArea = $(this).find('textarea');
      // We need to set the value attribute to changed.
      textArea.attr( 'data-editor-value-is-changed', 'true' );
      textArea.val(mediumText);
    });
    // Show taxtarea again.
    $field.show();
    // Show label again.
    $field.parent().parent().parent().find('label').show();
    // Remove divs created for medium.
    $( ".editable-wrapper > div").unwrap();
    $( ".medium-editable" ).remove();
  },
  onChange: function (element, callback) {
  }
};
})(jQuery, Drupal, MediumEditor);
