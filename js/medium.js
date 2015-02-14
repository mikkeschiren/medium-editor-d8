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
  if (format.editor == 'medium') {
    // hack to hide textarea
    var $field = $('#' + element.id);
    var styles = $field.attr('style');
    if (typeof styles != 'undefined') {
        styles = ' style="' + styles + '"';
    }
    $field.wrap('<div class="editable-wrapper"/>');
    var $w = $field.parent();
    $w.prepend('<div class="medium-editable" ' + styles + ' data-placeholder="'+$field.attr('placeholder')+'">' + $field.val()+'</div>');
    $field.hide();

    // catch the value for the submit
    $('form').submit(function(){
      $('.editable-wrapper').each(function(){
        var values = $(this).find('.medium-editable').html();
        $(this).find('textarea').attr( 'data-editor-value-is-changed', 'true' );
        $(this).find('textarea').val(values);
      });

    });
    // Get buttons to use from configuration.
    toolbar = format.editorSettings.toolbar;
    return new MediumEditor('.editable-wrapper', {
      buttons: toolbar,
      diffLeft: 0,
      diffTop: -10
    });
    }
  },
  detach: function (element, format, trigger) {
    var $field = $('#' + element.id);
    // Show taxtarea again
    $field.show();
    // Show label again
    $field.parent().parent().parent().find('label').show();
    // Remove divs created for medium
    $( ".editable-wrapper > div").unwrap();
    $( ".medium-editable" ).remove();


    //debugger;
  },
  onChange: function (element, callback) {
  }
};




})(jQuery, Drupal, MediumEditor);

