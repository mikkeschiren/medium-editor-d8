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
    // hack to remove textarea
    var $field = $('#' + element.id);
    $field.wrap('<div class="editable-wrapper" />');
    $field.parent().append('<div class="medium-editor-container">' + $field.val() + '</div>');
    $field.parent().parent().parent().find('label').hide();
    $field.hide();
    // catch the value for the submit
    $('form').submit(function(){
      $('.editable-wrapper').each(function(){
        //$(this).parent().value;
        $(this).find('textarea').val($(this).find('.medium-editor-container').text());
        //debugger;
      });
    });
    // Get buttons to use from configuration.
    toolbar = format.editorSettings.toolbar;
    console.log(format.editorSettings);
    //diffleft = format.editorSettings.diffleft;
    return new MediumEditor('.editable-wrapper', {
      buttons: toolbar,
      diffLeft: 0,
      diffTop: -10
    });
    }
  },
  detach: function (element, format, trigger) {
    console.log(element);
    var $field = $('#' + element.id);
    $field.show();
    $field.parent().parent().parent().find('label').show();
    $( "div" ).remove( ".medium-editor-container" );
    //debugger;
  },
  onChange: function (element, callback) {
  }
};




})(jQuery, Drupal, MediumEditor);

