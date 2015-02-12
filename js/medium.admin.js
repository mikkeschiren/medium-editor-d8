(function ($, Drupal, MEDIUM) {
'use strict';

/**
 * @file
 * Manages admin UI and forms.
 */

/**
 * Add regular expression to states API.
 */
if (Drupal.states) {
  Drupal.states.Dependent.comparisons.Object = function(reference, value) {
    // RegExp definition
    if (reference.regex) {
      return (new RegExp(reference.regex, reference.flags||'')).test(value);
    }
    // Array
    else if (typeof reference.indexOf === 'function') {
      return reference.indexOf(value) != -1;
    }
  };
}

/**
 * Admin container.
 */
var Main = Drupal.mediumAdmin = {};

/**
 * Drupal behavior .
 */
Drupal.behaviors.mediumAdmin = {attach: function(context, settings) {
  var i, wrpEl, inputEl, $input, mediumset;
  if (mediumset = settings.medium) {
    // Attach toolbar widget to toolbar input fields.
    if ($.fn.sortable && mediumset.twSettings) {
      $input = $('.medium-toolbar-input', context).not('.has-medium-tw');
      if ($input.length) {
        $input.addClass('has-medium-tw');
        for (i = 0; inputEl = $input[i]; i++) {
          Main.attachTw(inputEl, mediumset.twSettings);
        }
      }
    }
    // Install demo
    if (mediumset.demoSettings) {
      if (wrpEl = $('.medium-demo', context).not('.demo-processed').addClass('demo-processed')[0]) {
        Main.createDemo(wrpEl, mediumset.demoSettings);
      }
    }
  }
}};

/**
 * Integrates toolbar widget into a toolbar input.
 */
Main.attachTw = function(inputEl, settings) {
  if (!$.fn.sortable) return;
  var i, id, el, itemElements = Main.createTwElements(settings.items, settings),
  activeItems = inputEl.value.split(/,\s*/),
  $widget = $(Main.twTemplate(settings)),
  $available = $widget.find('.medium-available-toolbar'),
  $active = $widget.find('.medium-active-toolbar');
  // Available items
  for (id in itemElements) {
    $available.append(itemElements[id]);
  }
  // Active items
  for (i = 0; i < activeItems.length; i++) {
    id = activeItems[i];
    if (el = itemElements[id]) {
      $active.append(el.className.indexOf('multi-item') != -1 ? el.cloneNode(true) : el);
    }
  }
  $active.data('input', inputEl);
  // Apply sortable
  $available.sortable($.extend({connectWith: $active}, Main.availableSortableOptions($widget)));
  $active.sortable($.extend({connectWith: $available}, Main.activeSortableOptions($widget)));
  // Remove the sortable fix(preventing slowness and placeholder issues in empty containers)
  $widget.find('.sortable_fix').remove();
  // Sync the first time.
  Main.syncInput($active);
  // Hide the input and add the widget.
  $(inputEl).parent().hide().before($widget);
  return $widget;
};

/**
 * Creates elements for a list of items.
 */
Main.createTwElements = function(items) {
  var template, id, item, el, elements = {};
  items = Main.processTwItems(items);
  for (id in items) {
    item = items[id];
    // Create the item element and set required attributes
    template = !item.code && typeof item.template === 'string' && item.template;
    el = MEDIUM.createEl(template || MEDIUM.buttonHtml(item));
    el.setAttribute('data-medium-tw-item', id);
    el.className += ' medium-tw-item';
    if (item.multiple || template && item.multiple === undefined) {
      el.className += ' multi-item';
    }
    // Set button titles as "label: tooltip"
    if (!el.title || !template) {
      el.title = item.label ? item.label + (item.tooltip ? ': ' + item.tooltip : '') : (item.tooltip || '');
    }
    elements[id] = el;
  }
  return elements;
};

/**
 * Returns complete definitions of widget items.
 */
Main.processTwItems = function(items) {
  var id, item, ret = {};
  for (id in items) {
    item = items[id];
    // Provided as a label
    if (typeof item === 'string') {
      item = {label: item};
    }
    // Item must be an object
    if (!item || typeof item !== 'object') {
      continue;
    }
    // Copy missing properties from definitions in library files.
    ret[id] = MEDIUM.extend({id: id}, MEDIUM.getButtonDefinition(id), item);
  }
  return ret;
};

/**
 * Returns the template of toolbar widget.
 */
Main.twTemplate = function() {
  return '<div class="medium-tw">\
  <div class="form-item medium-tw-available-items">\
    <label> '+ Drupal.t('Available items') + '</label>\
    <div class="bue bue-toolbar medium-available-toolbar"><span class="medium-tw-item sortable_fix"></span></div>\
  </div>\
  <div class="form-item medium-tw-active-items">\
    <label> '+ Drupal.t('Active toolbar') + '</label>\
    <div class="bue bue-toolbar medium-active-toolbar"><span class="medium-tw-item sortable_fix"></span></div>\
  </div>\
</div>';
};

/**
 * Returns ui.sortable options for available toolbar.
 */
Main.availableSortableOptions = function() {
  return $.extend(Main.commonSortableOptions(), {
    helper: function(e, $item) {
      if ($item.hasClass('multi-item')) {
        $item.data('clonedItem', $item.clone().insertAfter($item));
      }
      return $item;
    },
    receive: function(e, ui) {
      if (ui.item.hasClass('multi-item')) {
        ui.item.remove();
      }
    },
    stop: function(e, ui) {
      if (ui.item.data('clonedItem')) {
        // Not moved
        if (ui.item.parent()[0] === this) {
          ui.item.data('clonedItem').remove();
        }
        ui.item.removeData('clonedItem');
      }
    }
  });
};

/**
 * Returns ui.sortable options for active toolbar.
 */
Main.activeSortableOptions = function() {
  return $.extend(Main.commonSortableOptions(), {
    update: function(e, ui) {
      Main.syncInput($(this));
    }
  });
};

/**
 * Returns common ui.sortable options.
 */
Main.commonSortableOptions = function() {
  return {
    cancel: false,
    tolerance: 'pointer',
    items: '.medium-tw-item',
    start: function(e, ui) {
      // Placeholders not having button text causes width differences.
      if (ui.item.hasClass('has-text')) {
        ui.placeholder.width(ui.item.width());
      }
    }
  };
};

/**
 * Updates the input field of an active toolbar.
 */
Main.syncInput = function($toolbar) {
  var inputEl = $toolbar.data('input'), itemIds = $toolbar.sortable('toArray', {attribute: 'data-medium-tw-item'});
  if (inputEl) $(inputEl).val(itemIds.join(', ')).change();
};

/**
 * Create the demo editor inside the wrapper.
 */
Main.createDemo = function(wrpEl, settings) {
  var E, textarea, date = new Date();
  if (textarea = $('textarea', wrpEl)[0]) {
    if (E = MEDIUM.attach(textarea, settings)) {
      // Set load time info
      E.addContent('Editor load time: ' + (new Date() - date) + 'ms', '\n');
      // Update editor format on format select. It can be used by preview button.
      $('.filter-list', wrpEl).change(function() {
        E.settings.inputFormat = this.value;
      }).change();
    }
  }
  return E;
};

})(jQuery, Drupal, MEDIUM);
