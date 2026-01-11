'use strict';

document.addEventListener('DOMContentLoaded', function () {

  // -----------------------------
  // Flat Picker
  // -----------------------------
  const flatpickrIds = [
    '.flatpickr-date',
    '.flatpickr-date1',
    '.flatpickr-date2',
    '.flatpickr-date3',
    '#flatpickr-time',
    '#flatpickr-datetime',
    '#flatpickr-multi',
    '#flatpickr-range',
    '#flatpickr-inline',
    '#flatpickr-human-friendly',
    '#flatpickr-disabled-range'
  ];

  flatpickrIds.forEach(selector => {
    const el = document.querySelector(selector);
    if (el) {
      flatpickr(el, { dateFormat: 'Y-m-d', enableTime: el.id.includes('time') || el.id.includes('datetime') });
    }
  });

  // -----------------------------
  // Bootstrap Daterange Picker
  // -----------------------------
  const bsPickers = [
    '#bs-rangepicker-basic',
    '#bs-rangepicker-single',
    '#bs-rangepicker-time',
    '#bs-rangepicker-range',
    '#bs-rangepicker-week-num',
    '#bs-rangepicker-dropdown'
  ];

  bsPickers.forEach(sel => {
    const el = $(sel);
    if (el.length) {
      const opts = {};
      switch(sel) {
        case '#bs-rangepicker-single': opts.singleDatePicker = true; break;
        case '#bs-rangepicker-time': opts.timePicker = true; opts.timePickerIncrement = 30; opts.locale = { format: 'MM/DD/YYYY h:mm A' }; break;
        case '#bs-rangepicker-range':
          opts.ranges = {
            Today: [moment(), moment()],
            Yesterday: [moment().subtract(1,'days'), moment().subtract(1,'days')],
            'Last 7 Days': [moment().subtract(6,'days'), moment()],
            'Last 30 Days': [moment().subtract(29,'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
          };
          break;
        case '#bs-rangepicker-week-num': opts.showWeekNumbers = true; break;
        case '#bs-rangepicker-dropdown': opts.showDropdowns = true; break;
      }
      opts.opens = isRtl ? 'left' : 'right';
      el.daterangepicker(opts);
    }
  });

  // -----------------------------
  // jQuery Timepicker
  // -----------------------------
  const timepickers = [
    '#timepicker-basic',
    '#timepicker-min-max',
    '#timepicker-disabled-times',
    '#timepicker-format',
    '#timepicker-step',
    '#timepicker-24hours'
  ];

  timepickers.forEach(sel => {
    const el = $(sel);
    if (el.length) {
      const config = { orientation: isRtl ? 'r' : 'l' };
      if(sel === '#timepicker-min-max') { config.minTime='2:00pm'; config.maxTime='7:00pm'; config.showDuration=true; }
      if(sel === '#timepicker-disabled-times') { config.disableTimeRanges=[['12am','3am'],['4am','4:30am']]; }
      if(sel === '#timepicker-format') { config.timeFormat='H:i:s'; }
      if(sel === '#timepicker-step') { config.step=15; }
      if(sel === '#timepicker-24hours') { config.show='24:00'; config.timeFormat='H:i:s'; }
      el.timepicker(config);
    }
  });

  // -----------------------------
  // Color Picker (Pickr)
  // -----------------------------
  const pickrSelectors = [
    {selector:'#color-picker-classic', theme:'classic', defaultColor:'rgba(102,108,232,1)'},
    {selector:'#color-picker-monolith', theme:'monolith', defaultColor:'rgba(40,208,148,1)'},
    {selector:'#color-picker-nano', theme:'nano', defaultColor:'rgba(255,73,97,1)'}
  ];

  pickrSelectors.forEach(p => {
    const el = document.querySelector(p.selector);
    if(el) {
      pickr.create({
        el: el,
        theme: p.theme,
        default: p.defaultColor,
        swatches: ['rgba(102,108,232,1)','rgba(40,208,148,1)','rgba(255,73,97,1)','rgba(255,145,73,1)','rgba(30,159,242,1)'],
        components: {
          preview:true, opacity:true, hue:true,
          interaction:{hex:true, rgba:true, hsla:true, hsva:true, cmyk:true, input:true, clear:true, save:true}
        }
      });
    }
  });

});
