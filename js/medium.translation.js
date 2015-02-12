(function ($, Drupal, MediumEditor) {
'use strict';

/**
 * @file
 * Translates Medium core strings.
 */

/**
 * Override MediumEditor translation with Drupal translation.
 */
MediumEditor.dt = MediumEditor.t;
MediumEditor.t = function(str, tokens) {
  return MediumEditor.i18n[str] ? MediumEditor.dt(str, tokens) : Drupal.t(str, tokens);
};

/**
 * Translation strings of Medium core library.
 * Triggering javascript translation by including the strings here.
 */

/*
Drupal.t('Bold')
Drupal.t('Italic')
Drupal.t('Underline')
Drupal.t('Strikethrough')
Drupal.t('Quote')
Drupal.t('Code')
Drupal.t('Bulleted list')
Drupal.t('Numbered list')
Drupal.t('Link')
Drupal.t('Image')
Drupal.t('Undo')
Drupal.t('Redo')
Drupal.t('Heading !n')
Drupal.t('Close')
Drupal.t('Tag editor - @tag')
Drupal.t('OK')
Drupal.t('Cancel')
Drupal.t('URL')
Drupal.t('Text')
Drupal.t('Width x Height')
Drupal.t('Alternative text')
Drupal.t('Browse')
*/

})(jQuery, Drupal, MediumEditor);
