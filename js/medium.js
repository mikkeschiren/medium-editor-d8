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
      //  $(this).find('textarea').val(serialize().$(this).find('.medium-editor-container').value());
       var valuetest = $(this).find('.medium-editor-container').value();
       //var valuetest = 'ffo bar';
        //$(this).find('textarea').val(valuetest);
        var textarean = $(this).find('textarea');
        $(this).find('textarea').val('foo bar');
        console.log(valuetest);
        debugger;
        //textarean.val(serialize(valuetest,value()));
       // console.log(textarea.val());
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
    var $field = $('#' + element.id);
    // Show taxtarea again
    $field.show();
    // Show label again
    $field.parent().parent().parent().find('label').show();
    // Remove divs created for medium
    $( ".editable-wrapper > div").unwrap();
    $( ".medium-editor-container" ).remove();


    //debugger;
  },
  onChange: function (element, callback) {
  }
};




})(jQuery, Drupal, MediumEditor);

