(function ($, Drupal, MEDIUM) {
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
    var settings = format.editorSettings;
    if (settings) {
      // Set format
      if (!settings.inputFormat) {
        settings.inputFormat = format.format;
      }
      return MEDIUM.attach(element, settings);
    }
  },
  detach: function (element, format, trigger) {
    return MEDIUM.detach(element);
  },
  onChange: function (element, callback) {
  },
};

})(jQuery, Drupal, MEDIUM);
