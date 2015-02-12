(function ($, Drupal) {
'use strict';

/**
 * @file
 * Provides a library for processing user input asynchronously.
 * Requires 'access ajax preview' permission on the server side.
 * Can be used independently of Medium.
 */

/**
 * Asynchronous user input processor.
 * Accepts data object containing input, format, and callback.
 * Executes the callback with the data object containing output and status.
 */
var xPreview = Drupal.xPreview = function(opt) {
  var settings, result;
  // Do nothing if there is no callback
  if (!opt || !opt.callback) return;
  // Set defaults
  opt.output = '';
  opt.status = false;
  // Check settings
  if (!(settings = drupalSettings.xPreview) || !settings.url) {
    opt.output = Drupal.t('Missing ajax parameters.');
  }
  // Check empty input.
  else if (!(opt.input = $.trim(opt.input))) {
    opt.status = true;
  }
  // Check cached results
  else if (result = xPreview.getCache(opt)) {
    $.extend(opt, result);
  }
  // Create a new request and return.
  else {
    return $.ajax({
      url: settings.url,
      data: {input: opt.input, format: opt.format},
      type: 'POST',
      dataType: 'json',
      success: xPreview.succes,
      error: xPreview.error,
      complete: xPreview.complete,
      opt: opt
    });
  }
  // No request is sent. Run the callback with minimum delay.
  xPreview.delay(opt);
};

/**
 * Success handler of preview request.
 */
xPreview.succes = function(response) {
  $.extend(this.opt, response);
};

/**
 * Error handler of preview request.
 */
xPreview.error = function(xhr) {
  var msg;
  if (xhr.status == 403) {
    msg = Drupal.t("You don't have permission to use ajax preview.");
  }
  else {
    msg = Drupal.t('An AJAX HTTP error occurred.') + '<br />\n';
    msg += Drupal.t('HTTP Result Code: !status', {'!status': xhr.status*1 || 0}) + '<br />\n';
    msg += Drupal.t('StatusText: !statusText', {'!statusText': Drupal.checkPlain(xhr.statusText || 'N/A')});
  }
  this.opt.output = msg;
};

/**
 * Complete handler of preview request.
 */
xPreview.complete = function(xhr) {
  var opt = this.opt;
  delete this.opt;
  opt.xhr = xhr;
  xPreview.setCache(opt);
  opt.callback.call(this, opt);
};

/**
 * Delays the execution of completion callback.
 */
xPreview.delay = function(opt) {
  setTimeout(function() {
    opt.callback(opt);
    opt = null;
  });
};

/**
 * Returns the cached result.
 */
xPreview.getCache = function(opt) {
  var cache = xPreview.cache;
  if (cache) return cache[opt.format + ' ' + opt.input];
};

/**
 * Saves the result to the cache.
 */
xPreview.setCache = function(opt) {
  // Keep only one result
  var cache = xPreview.cache = {};
  cache[opt.format + ' ' + opt.input] = {status: opt.status, output: opt.output};
};

})(jQuery, Drupal);
