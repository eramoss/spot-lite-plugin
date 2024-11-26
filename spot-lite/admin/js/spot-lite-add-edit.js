(function ($) {
  'use strict';

  const wp_data = spot_lite_add_edit;
  let $body = $('body');
  let $photos_section = $('#photos-section');
  let $drop_area = $('#drop-area');
  let $file_input = $('#file-input');
  let $activities_section = $('#activities-section');
  const participants_map = create_participants_map(wp_data.existing_participants);

  let photo_index = $('.photo-item').length;

  const handle_files = (e) => {
    const files = e.target.files || e.originalEvent.dataTransfer.files;
    Array.from(files).forEach(upload_file);
  };

  $(document).ready(function () {
    $body = $('body');
    $photos_section = $('#photos-section');
    $drop_area = $('#drop-area');
    $file_input = $('#file-input');
    $activities_section = $('#activities-section');
    create_participants_datalist(wp_data.existing_participants);

    $('#add-activity').on('click', add_activity);
    $('#add-photo').on('click', add_photo);

    $body.on('click', '.remove-activity', remove_activity);
    $body.on('click', '.remove-photo', remove_photo);

    initialize_drag_drop();

    $file_input.on('change', handle_files);
  });

  function create_participants_map(participants) {
    return participants.reduce((map, participant) => {
      map[participant.name] = participant;
      return map;
    }, {});
  }

  function create_participants_datalist(participants) {
    const $datalist = $('<datalist id="participants-list"></datalist>');
    participants.forEach(participant => {
      $datalist.append(`<option value="${participant.name}"></option>`);
    });
    $body.append($datalist);
  }

  function add_activity() {
    console.log('add_activity');
    const index = $activities_section.children().length;
    const html = get_activity_html(index);
    $activities_section.append(html);

    const $new_item = $activities_section.children().last();
    const $participant_input = $new_item.find('.participant-input');
    const $birth_date_input = $new_item.find('.birth-date-input');

    $participant_input.on('input', function () {
      const participant = participants_map[$participant_input.val()];
      $birth_date_input.val(participant ? participant.birth_date : '');
    });
  }

  function get_activity_html(index) {
    return `
      <div class="activity-item">
          <div class='participant-container'>
            <div class="input-group">
              <label>Aluno:</label>
              <input type="text" autocomplete="off" name="activities[${index}][participant_name]" list="participants-list" class="participant-input">
            </div>
            <div class="input-group">
              <label>Data de nascimento do aluno:</label>
              <input type="date" name="activities[${index}][participant_birth_date]" class="birth-date-input">
            </div>
          </div>
          <label>Descrição da atividade:</label>
          <textarea name="activities[${index}][description]"></textarea>
          <button type="button" class="remove-activity">Remove</button>
      </div>
    `;
  }

  function remove_activity() {
    $(this).closest('.activity-item').remove();
  }

  function add_photo() {
    const html = get_photo_html(photo_index);
    $photos_section.append(html);

    const $new_item = $photos_section.children().last();
    $new_item.find('.remove-photo').on('click', function () {
      $new_item.remove();
    });

    photo_index++;
  }

  function get_photo_html(index) {
    return `
      <div class="photo-item">
        <input type="hidden" name="photos[${index}][id]" value="">
        <label>Foto URL:</label>
        <input type="text" name="photos[${index}][url]" class="photo-url">
        <button type="button" class="remove-photo button-link">Remover</button>
      </div>
    `;
  }

  function remove_photo() {
    $(this).closest('.photo-item').remove();
  }

  function initialize_drag_drop() {
    const prevent_defaults = (e) => {
      e.preventDefault();
      e.stopPropagation();
    };

    const highlight_drop_area = () => {
      $drop_area.addClass('highlight');
    };

    const unhighlight_drop_area = () => {
      $drop_area.removeClass('highlight');
    };


    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event_name => {
      $drop_area.on(event_name, prevent_defaults);
    });

    ['dragenter', 'dragover'].forEach(event_name => {
      $drop_area.on(event_name, highlight_drop_area);
    });

    ['dragleave', 'drop'].forEach(event_name => {
      $drop_area.on(event_name, unhighlight_drop_area);
    });

    $drop_area.on('drop', handle_files);
    $drop_area.on('click', function () {
      $file_input.click();
    });
  }

  async function upload_file(file) {
    if (!file.type.startsWith('image/')) {
      alert('Only image files are allowed!');
      return;
    }

    const reader = new FileReader();
    reader.onload = async function () {
      const photo = reader.result;
      const link = await upload_to_wordpress(photo, file);

      const html = `
        <div class="photo-item">
          <input type="hidden" name="photos[${photo_index}][id]" value="">
          <label>Foto URL:</label>
          <input type="text" name="photos[${photo_index}][url]" class="photo-url" value="${link}" readonly>
          <button type="button" class="remove-photo button-link">Remover</button>
        </div>
      `;
      $photos_section.append(html);

      photo_index++;
    };
    reader.readAsArrayBuffer(file);
  }

  async function upload_to_wordpress(image, file) {
    try {
      const response = await $.ajax({
        url: wp_data.rest_url,
        type: 'POST',
        data: image,
        processData: false,
        headers: {
          'Content-Type': file.type,
          'X-WP-Nonce': wp_data.nonce,
          'content-disposition': 'attachment; filename=' + file.name
        }
      });
      return response.source_url;
    } catch (error) {
      console.error(error);
      return '';
    }
  }

})(jQuery);
