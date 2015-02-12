(function ($, Drupal, MediumEditor) {
'use strict';

/**
 * @file
 * Defines Ajax Preview button for Medium.
 */

/**
 * Register buttons.
 */
MediumEditor.registerButtons('Medium.xpreview', function() {
  return {
    xpreview: {
      id: 'xpreview',
      label: Drupal.t('Preview'),
      cname: 'ficon-preview',
      code: MediumEditor.xPreview
    }
  };
});

/**
 * Previews editor content asynchronously.
 */
var MediumEditorXP = MediumEditor.xPreview = function(E) {
  E.toggleButtonsDisabled();
  E.togglePreview();
  if (E.previewing) {
    E.setPreviewContent('<div class="loading">' + Drupal.t('Loading...') + '</div>');
    E.previewXHR = Drupal.xPreview({
      input: E.getContent(),
      format: E.settings.inputFormat,
      callback: MediumEditorXP.complete,
      E: E
    });
  }
};

/**
 * Complete handler of ajax preview.
 */
MediumEditorXP.complete = function(opt) {
  var E = opt.E, success = opt.status, output = opt.output;
  if (E.previewing) {
    if (!success) output = MediumEditorXP.wrapMsg(output);
    E.setPreviewContent(output);
    // Attach behaviors
    if (success && output) {
      Drupal.attachBehaviors(E.previewEl, window.drupalSettings);
    }
    E.previewXHR = null;
  }
};

/**
 * Formats a preview message.
 */
MediumEditorXP.wrapMsg = function(msg, type) {
  return '<div class="messages messages--' + (type || 'error') + '">' + msg + '</div>';
};

})(jQuery, Drupal, MediumEditor);
