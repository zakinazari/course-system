'use strict';

document.addEventListener('DOMContentLoaded', function () {

  // Init custom option check
  if (window.Helpers && window.Helpers.initCustomOptionCheck) {
    window.Helpers.initCustomOptionCheck();
  }

  // Flatpickr
  const flatPickrList = [].slice.call(document.querySelectorAll('.flatpickr-validation'));
  if (flatPickrList.length) {
    flatPickrList.forEach(flatPickr => {
      if (flatPickr) {
        flatpickr(flatPickr, {
          allowInput: true,
          monthSelectorType: 'static'
        });
      }
    });
  }

  // Bootstrap validation
  const bsValidationForms = document.querySelectorAll('.needs-validation');
  Array.prototype.slice.call(bsValidationForms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      } else {
        alert('Submitted!!!');
      }
      form.classList.add('was-validated');
    }, false);
  });

  // FormValidation examples
  const formValidationExamples = document.getElementById('formValidationExamples');
  if (!formValidationExamples) return; // فرم وجود ندارد، کل کد زیر را اجرا نکن

  const formValidationSelect2El = formValidationExamples.querySelector('[name="formValidationSelect2"]') ? 
    jQuery(formValidationExamples.querySelector('[name="formValidationSelect2"]')) : null;
  const formValidationTechEl = formValidationExamples.querySelector('[name="formValidationTech"]') ? 
    jQuery(formValidationExamples.querySelector('[name="formValidationTech"]')) : null;
  const formValidationLangEl = formValidationExamples.querySelector('[name="formValidationLang"]') || null;
  const formValidationHobbiesEl = formValidationExamples.querySelector('.selectpicker') ? 
    jQuery(formValidationExamples.querySelector('.selectpicker')) : null;

  const tech = [
    'ReactJS','Angular','VueJS','Html','Css','Sass','Pug','Gulp','Php','Laravel','Python','Bootstrap','Material Design','NodeJS'
  ];

  const fv = FormValidation.formValidation(formValidationExamples, {
    fields: {
      formValidationName: { validators: { notEmpty: {message:'Please enter your name'} } },
      formValidationEmail: { validators: { notEmpty:{message:'Please enter your email'}, emailAddress:{message:'Invalid email'} } },
      formValidationPass: { validators: { notEmpty: { message: 'Please enter your password' } } },
      formValidationConfirmPass: { validators: { notEmpty:{message:'Please confirm password'}, identical:{compare: function(){ return formValidationExamples.querySelector('[name="formValidationPass"]')?.value; }, message:'Password mismatch'} } },
      formValidationDob: { validators: { notEmpty:{message:'Please select your DOB'}, date:{format:'YYYY/MM/DD', message:'Invalid date'} } },
      formValidationSelect2: { validators: { notEmpty:{message:'Please select your country'} } },
      formValidationTech: { validators: { notEmpty:{message:'Please select technology'} } },
      formValidationLang: { validators: { notEmpty:{message:'Please add your language'} } },
      formValidationHobbies: { validators: { notEmpty:{message:'Please select your hobbies'} } },
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({ eleValidClass:'', rowSelector: 'row' }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });

  // Flatpickr DOB
  const dobEl = formValidationExamples.querySelector('[name="formValidationDob"]');
  if (dobEl) {
    flatpickr(dobEl, {
      enableTime: false,
      dateFormat: 'Y/m/d',
      onChange: function () { fv.revalidateField('formValidationDob'); }
    });
  }

  // Select2 (Country)
  if (formValidationSelect2El) {
    formValidationSelect2El.wrap('<div class="position-relative"></div>');
    formValidationSelect2El.select2({
      placeholder:'Select country',
      dropdownParent: formValidationSelect2El.parent()
    }).on('change.select2', function () {
      fv.revalidateField('formValidationSelect2');
    });
  }

  // Typeahead
  if (formValidationTechEl) {
    const substringMatcher = function(strs) {
      return function findMatches(q, cb) {
        const matches = [];
        const substrRegex = new RegExp(q, 'i');
        $.each(strs, function(i,str){ if(substrRegex.test(str)) matches.push(str); });
        cb(matches);
      };
    };
    formValidationTechEl.typeahead({ hint: !isRtl, highlight:true, minLength:1 }, { name:'tech', source: substringMatcher(tech) });
  }

  // Tagify
  if (formValidationLangEl) {
    const langTagify = new Tagify(formValidationLangEl);
    formValidationLangEl.addEventListener('change', function(){ fv.revalidateField('formValidationLang'); });
  }

  // Bootstrap select
  if (formValidationHobbiesEl) {
    formValidationHobbiesEl.on('changed.bs.select', function(){ fv.revalidateField('formValidationHobbies'); });
  }

});
