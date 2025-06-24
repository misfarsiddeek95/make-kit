'use strict';

(function ($) {
  'use strict';

  // World

  $('#world').vectorMap({
    map: 'world_en',
    backgroundColor: null,
    borderColor: null,
    borderOpacity: 0.5,
    borderWidth: 1,
    color: '#1d87e4',
    enableZoom: true,
    hoverColor: '#1d87e4',
    hoverOpacity: 0.7,
    normalizeFunction: 'linear',
    selectedColor: '#faa800',
    selectedRegions: ['au', 'ca'],
    showTooltip: true
  });

  // USA
  $('#usa').vectorMap({
    map: 'usa_en',
    backgroundColor: null,
    borderColor: null,
    borderOpacity: 0.5,
    borderWidth: 1,
    color: '#34a853',
    enableZoom: true,
    hoverColor: '#34a853',
    hoverOpacity: 0.7,
    normalizeFunction: 'linear',
    selectedColor: '#faa800',
    selectedRegions: null,
    showTooltip: true
  });

  // Europe
  $('#europe').vectorMap({
    map: 'europe_en',
    backgroundColor: null,
    borderColor: null,
    borderOpacity: 0.5,
    borderWidth: 1,
    color: '#e53935',
    enableZoom: true,
    hoverColor: '#e53935',
    hoverOpacity: 0.7,
    normalizeFunction: 'linear',
    selectedColor: '#faa800',
    selectedRegions: null,
    showTooltip: true
  });
})(jQuery);
