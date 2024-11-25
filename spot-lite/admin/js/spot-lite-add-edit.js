(function ($) {
  'use strict';
  if (window.location.href.includes('spot-lite-add-edit') === false) {
    return;
  }
  const wpData = spot_lite_add_edit;

  $(document).ready(function () {
    const existingParticipants = wpData.existing_participants;

    const participantsMap = Object.fromEntries(
      existingParticipants.map(participant => [participant.name, participant])
    );

    const $datalist = $('<datalist id="participants-list"></datalist>');
    existingParticipants.forEach(participant => {
      $datalist.append(`<option value="${participant.name}"></option>`);
    });
    $('body').append($datalist);

    $('#add-activity').on('click', function () {
      const $section = $('#activities-section');
      const index = $section.children().length;

      const html = `
        <div class="activity-item">
          <label>Participante:</label>
          <input type="text" name="activities[${index}][participant_name]" list="participants-list" class="participant-input">
          <label>Nascimento:</label>
          <input type="date" name="activities[${index}][participant_birth_date]" class="birth-date-input">
          <label>Descrição da atividade:</label>
          <textarea name="activities[${index}][description]"></textarea>
          <button type="button" class="remove-activity">Remove</button>
        </div>`;
      $section.append(html);

      const $newItem = $section.children().last();
      const $participantInput = $newItem.find('.participant-input');
      const $birthDateInput = $newItem.find('.birth-date-input');

      $participantInput.on('input', function () {
        const participant = participantsMap[$participantInput.val()];
        if (participant) {
          $birthDateInput.val(participant.birth_date);
        } else {
          $birthDateInput.val('');
        }
      });
    });

    $(document).on('click', '.remove-activity', function () {
      $(this).closest('.activity-item').remove();
    });

    const $photosSection = $('#photos-section');
    const $dropArea = $('#drop-area');
    const $fileInput = $('#file-input');
    let photoIndex = $('.photo-item').length;

    const preventDefaults = (e) => {
      e.preventDefault();
      e.stopPropagation();
    };

    const handleFiles = (e) => {
      const files = e.target.files;
      Array.from(files).forEach(uploadFile);
    };

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      $dropArea.on(eventName, preventDefaults);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
      $dropArea.on(eventName, () => $dropArea.addClass('highlight'));
    });

    ['dragleave', 'drop'].forEach(eventName => {
      $dropArea.on(eventName, () => $dropArea.removeClass('highlight'));
    });

    $dropArea.on('drop', function (e) {
      const files = e.originalEvent.dataTransfer.files;
      handleFiles({ target: { files } });
    });

    $dropArea.on('click', function () {
      $fileInput.click();
    });

    $fileInput.on('change', handleFiles);



    const uploadFile = (file) => {
      if (!file.type.startsWith('image/')) {
        alert('Only image files are allowed!');
        return;
      }

      const reader = new FileReader();
      reader.onload = function () {
        const photo = reader.result;

        const link = upload_to_wordpress(photo, file);
        const html = `
                <div class="photo-item">
                    <input type="hidden" name="photos[${photoIndex}][id]" value="">
                    <label>Foto URL:</label>
                    <input type="text" name="photos[${photoIndex}][url]" class="photo-url" value="${link}" readonly>
                    <button type="button" class="remove-photo button-link">Remover</button>
                </div>`;
        $photosSection.append(html);

        const $newItem = $photosSection.children().last();
        $newItem.find('.remove-photo').on('click', function () {
          $newItem.remove();
        });

        photoIndex++;
      };
      reader.readAsArrayBuffer(file);
    };

    const upload_to_wordpress = (image, file) => {
      let source_url = '';
      $.ajax({
        url: wpData.rest_url,
        type: 'POST',
        data: image,
        processData: false,
        headers: {
          'Content-Type': file.type,
          'X-WP-Nonce': wpData.nonce,
          'content-disposition': 'attachment; filename=' + file.name
        },
        async: false,
        success: function (response) {
          source_url = response.source_url;
        },
        error: function (error) {
          console.error(error);
        }
      });
      return source_url;
    };

    $('#add-photo').on('click', function () {
      const html = `
            <div class="photo-item">
                <input type="hidden" name="photos[${photoIndex}][id]" value="">
                <label>Foto URL:</label>
                <input type="text" name="photos[${photoIndex}][url]" class="photo-url">
                <button type="button" class="remove-photo button-link">Remover</button>
            </div>`;
      const $element = $(html);
      $photosSection.append($element);

      const $newItem = $photosSection.children().last();
      $newItem.find('.remove-photo').on('click', function () {
        $newItem.remove();
      });

      photoIndex++;
    });

    $(document).on('click', '.remove-photo', function () {
      $(this).closest('.photo-item').remove();
    });
  });


})(jQuery);
