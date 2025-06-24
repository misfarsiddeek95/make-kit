'use strict';

(function ($) {
  'use strict';

  // Peity charts

  $('#peity-chart-1').peity('line', {
    fill: ['rgba(255, 255, 255, 0.3)'],
    height: '50px',
    width: '100%'
  });

  $('#peity-chart-2').peity('bar', {
    fill: ['rgba(255, 255, 255, 0.3)'],
    height: '50px',
    width: '100%'
  });

  $('#peity-chart-3').peity('bar', {
    fill: ['rgba(255, 255, 255, 0.3)'],
    height: '50px',
    width: '100%'
  });

  $('[data-chart="peity"]').each(function () {
    var type = $(this).attr('data-type');
    $(this).peity(type);
  });

  // Chartist chart
  var chart = new Chartist.Line('#advanced', {
    labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
    series: [[15, 4, 6, 8, 5, 4, 6, 2, 3, 3], [4, 5, 3, 7, 3, 5, 5, 3, 4, 4], [1, 3, 4, 5, 6, 10, 3, 4, 5, 6]]
  }, {
    chartPadding: {
      right: 20,
      left: 0,
      top: 20,
      bottom: 0
    },
    fullWidth: true,
    low: 0,
    showArea: true
  });

  // Let's put a sequence number aside so we can use it in the event callbacks
  var seq = 0,
      delays = 80,
      durations = 500;

  // Once the chart is fully created we reset the sequence
  chart.on('created', function () {
    seq = 0;
  });

  // On each drawn element by Chartist we use the Chartist.Svg API to trigger SMIL animations
  chart.on('draw', function (data) {
    seq++;

    if (data.type === 'line') {
      // If the drawn element is a line we do a simple opacity fade in. This could also be achieved using CSS3 animations.
      data.element.animate({
        opacity: {
          // The delay when we like to start the animation
          begin: seq * delays + 1000,
          // Duration of the animation
          dur: durations,
          // The value where the animation should start
          from: 0,
          // The value where it should end
          to: 1
        }
      });
    } else if (data.type === 'label' && data.axis === 'x') {
      data.element.animate({
        y: {
          begin: seq * delays,
          dur: durations,
          from: data.y + 100,
          to: data.y,
          // We can specify an easing function from Chartist.Svg.Easing
          easing: 'easeOutQuart'
        }
      });
    } else if (data.type === 'label' && data.axis === 'y') {
      data.element.animate({
        x: {
          begin: seq * delays,
          dur: durations,
          from: data.x - 100,
          to: data.x,
          easing: 'easeOutQuart'
        }
      });
    } else if (data.type === 'point') {
      data.element.animate({
        x1: {
          begin: seq * delays,
          dur: durations,
          from: data.x - 10,
          to: data.x,
          easing: 'easeOutQuart'
        },
        x2: {
          begin: seq * delays,
          dur: durations,
          from: data.x - 10,
          to: data.x,
          easing: 'easeOutQuart'
        },
        opacity: {
          begin: seq * delays,
          dur: durations,
          from: 0,
          to: 1,
          easing: 'easeOutQuart'
        }
      });
    } else if (data.type === 'grid') {
      // Using data.axis we get x or y which we can use to construct our animation definition objects
      var pos1Animation = {
        begin: seq * delays,
        dur: durations,
        from: data[data.axis.units.pos + '1'] - 30,
        to: data[data.axis.units.pos + '1'],
        easing: 'easeOutQuart'
      };

      var pos2Animation = {
        begin: seq * delays,
        dur: durations,
        from: data[data.axis.units.pos + '2'] - 100,
        to: data[data.axis.units.pos + '2'],
        easing: 'easeOutQuart'
      };

      var animations = {};
      animations[data.axis.units.pos + '1'] = pos1Animation;
      animations[data.axis.units.pos + '2'] = pos2Animation;
      animations['opacity'] = {
        begin: seq * delays,
        dur: durations,
        from: 0,
        to: 1,
        easing: 'easeOutQuart'
      };

      data.element.animate(animations);
    }
  });

  // For the sake of the example we update the chart every time it's created with a delay of 10 seconds
  chart.on('created', function () {
    if (window.__exampleAnimateTimeout) {
      clearTimeout(window.__exampleAnimateTimeout);
      window.__exampleAnimateTimeout = null;
    }
    window.__exampleAnimateTimeout = setTimeout(chart.update.bind(chart), 120000);
  });

  // Flot chart
  var data1 = [[1, 10], [2, 5], [3, 12], [4, 6], [5, 10], [6, 7], [7, 15]];
  var data2 = [[1, 6], [2, 3], [3, 7], [4, 4], [5, 8], [6, 5], [7, 10]];

  var label1 = 'Page views';
  var label2 = 'Sales';

  var color1 = '#1d87e4';
  var color2 = tinycolor('#1d87e4').darken(15).toString();

  $.plot($('.flot-chart-example'), [{
    data: data1,
    label: label1,
    color: color1
  }, {
    data: data2,
    label: label2,
    color: color2
  }], {
    series: {
      lines: {
        show: true,
        fill: true,
        lineWidth: 1,
        fillColor: {
          colors: [{
            opacity: 1
          }, {
            opacity: 1
          }]
        }
      },
      points: {
        show: true,
        radius: 0
      },
      shadowSize: 0,
      curvedLines: {
        apply: true,
        active: true,
        monotonicFit: true
      }
    },
    grid: {
      margin: {
        top: 0,
        bottom: 0,
        left: 0,
        right: 0
      },
      color: '#aaa',
      hoverable: true,
      borderWidth: 0,
      backgroundColor: '#fff',
      labelMargin: 0,
      minBorderMargin: 0,
      axisMargin: 0
    },
    legend: {
      show: false
    },
    xaxis: {
      show: false
    },
    yaxis: {
      ticks: 0
    },
    tooltip: true,
    tooltipOpts: {
      content: '%s: %y',
      shifts: {
        x: -60,
        y: 25
      },
      defaultTheme: false
    }
  });

  // Vector map
  $('#vector-map').vectorMap({
    map: 'usa_en',
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
    selectedRegions: ['fl', 'ca', 'tx', 'ny', 'nd', 'oh'],
    showTooltip: true
  });
})(jQuery);
