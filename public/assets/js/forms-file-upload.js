
/**
 * File Upload
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // اطمینان از اینکه Dropzone لود شده است
  if (typeof Dropzone === 'undefined') {
    console.error('❌ Dropzone is not defined. Make sure dropzone.min.js is loaded before this file.');
    return;
  }

  // previewTemplate: Updated Dropzone default previewTemplate
  const previewTemplate = `<div class="dz-preview dz-file-preview">
<div class="dz-details">
  <div class="dz-thumbnail">
    <img data-dz-thumbnail>
    <span class="dz-nopreview">No preview</span>
    <div class="dz-success-mark"></div>
    <div class="dz-error-mark"></div>
    <div class="dz-error-message"><span data-dz-errormessage></span></div>
    <div class="progress">
      <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
    </div>
  </div>
  <div class="dz-filename" data-dz-name></div>
  <div class="dz-size" data-dz-size></div>
</div>
</div>`;

  // جلوگیری از Dropzone اتوماتیک روی تمام فرم‌ها
  Dropzone.autoDiscover = false;

  // بررسی وجود المنت
  const dropzoneElement = document.querySelector('#dropzone-multi');

  if (!dropzoneElement) {
    console.warn('⚠️ Element #dropzone-multi not found. Skipping Dropzone initialization.');
    return;
  }

  try {
    // مقداردهی Dropzone فقط در صورت وجود المنت
    const dropzoneMulti = new Dropzone(dropzoneElement, {
      previewTemplate: previewTemplate,
      parallelUploads: 1,
      maxFilesize: 5, // مگابایت
      addRemoveLinks: true
    });

    console.log('✅ Dropzone initialized successfully:', dropzoneElement);

  } catch (error) {
    console.error('❌ Dropzone initialization error:', error);
  }
});

