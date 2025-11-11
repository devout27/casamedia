var prefix = 'cm', iti,
    tooltip_s = () => {
    let tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => {
        let existingTooltip = bootstrap.Tooltip.getInstance(el);
        if (existingTooltip) {
            existingTooltip.dispose();
        }
    });
    [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
}

jQuery(function ($) {

    $(".select2").select2({
        width: "100%",
        placeholder: "Select",
        allowClear: true
    });

    $('[name="cm_tags"]').inputTags();

    tooltip_s();

    if ($('[name="phone"]').length) {
        iti = window.intlTelInput(document.querySelector('[name="phone"]'), {
            initialCountry: "nl",
            separateDialCode: true,
            // onlyCountries: ["nl"],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/utils.js"
        });
    }

    let map, marker, autocomplete;
    const mapElement = document.getElementById('cm_map');
    const addressInput = document.getElementById('cm_address');
    const latInput = document.getElementById('cm_lat');
    const lngInput = document.getElementById('cm_lng');

    function initMap() {
        const defaultLocation = { lat: -34.397, lng: 150.644 };

        if (latInput.value && lngInput.value) {
            defaultLocation.lat = parseFloat(latInput.value);
            defaultLocation.lng = parseFloat(lngInput.value);
        }

        map = new google.maps.Map(mapElement, {
            zoom: 15,
            center: defaultLocation
            
        });

        marker = new google.maps.Marker({
            map: map,
            position: defaultLocation,
            draggable: true
        });

        marker.addListener('dragend', function (e) {
            latInput.value = e.latLng.lat();
            lngInput.value = e.latLng.lng();
        });

        geocoder = new google.maps.Geocoder();

        marker.addListener('dragend', function (e) {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();
            latInput.value = lat;
            lngInput.value = lng;

            geocodeLatLng(lat, lng);
        });

        map.addListener('click', function (event) {
            const lat = event.latLng.lat();
            const lng = event.latLng.lng();

            marker.setPosition(event.latLng);

            latInput.value = lat;
            lngInput.value = lng;

            geocodeLatLng(lat, lng);
        });

        autocomplete = new google.maps.places.Autocomplete(addressInput);
        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;

            map.setCenter(place.geometry.location);
            marker.setPosition(place.geometry.location);

            latInput.value = place.geometry.location.lat();
            lngInput.value = place.geometry.location.lng();
        });
    }

    function geocodeLatLng(lat, lng) {
        const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
        geocoder.geocode({ location: latlng }, (results, status) => {
            if (status === "OK" && results[0]) {
                addressInput.value = results[0].formatted_address;
            }
        });
    }


    if (mapElement) {
        initMap();
    }

    $('#cm_upload_photos').click(function (e) {
        e.preventDefault();

        const frame = wp.media({
            title: 'Select Photos',
            button: {
                text: 'Add Photos'
            },
            multiple: true
        });

        frame.on('select', function () {
            const attachments = frame.state().get('selection').map(
                attachment => attachment.toJSON()
            );

            let currentPhotos = $('#cm_photos').val();
            let photoIds = currentPhotos ? currentPhotos.split(',') : [];            

            attachments.forEach(attachment => {
                if (!photoIds.includes(attachment.id.toString())) {
                    photoIds.push(attachment.id);
                    $('.cm-select-maps-photos').before(`
                        <div class="photo-item" data-id="${attachment.id}">
                            <img width="150" height="150" src="${attachment.sizes?.thumbnail?.url || attachment.url}">
                            <button type="button" class="remove-photo" data-bs-toggle="tooltip" data-bs-title="Remove">×</button>
                        </div>
                    `);
                }
            });

            $('#cm_photos').val(photoIds.join(','));
            tooltip_s();
        });

        frame.open();
    });

    $(document).on('click', '.remove-photo', function () {
        const photoItem = $(this).parent();
        const photoId = photoItem.data('id');

        let photoIds = $('#cm_photos').val().split(',');
        photoIds = photoIds.filter(id => id != photoId);
        $('#cm_photos').val(photoIds.join(','));

        photoItem.remove();
    });

    $(document).on('click', '.js-change-client-password--', function (e) {
        e.preventDefault();
        $('.js-error').remove();
        var id = $(this).data('id');
        $('.js-change-client-password--modal .toggle-visible-eye i').removeClass('fa-eye-slash').addClass('fa-eye');
        $('.js-change-client-password--modal').find('[name="id"]').val(id);
        $('.js-change-client-password--modal').find('[name="password"]').val('').attr('type', 'password');
        $('.js-change-client-password--modal').modal('show');
    });

    $(document).on('submit', '.js-change-client-password--modal form', function (e) {
        e.preventDefault();
        var form = $(this), formdata = form.serializeArray(), isValid = true, outerPass = form.find('[name="password"]').closest('.col-12'), password = form.find('[name="password"]'), modal = $('.js-change-client-password--modal');
        $('.js-error').remove();
        if (!password.val()) {
            password.focus();
            outerPass.append('<p class="js-error text-danger">Please fill this field.</p>');
            isValid = false;
        } else if (password.val().length < 8) {
            password.focus();
            outerPass.append('<p class="js-error text-danger">The password must be greater than 8 characters.</p>');
            isValid = false;
        }

        if (!isValid) {
            return;
        }
        form.find('button[type="submit"]').attr('disabled', true);
        $.ajax({
            url: cm_ajax_ob.ajax_url,
            type: 'post',
            data: formdata,
            xhr: function (event) {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = parseInt((evt.loaded / evt.total) * 100);
                            modal.find('.progress-field').show();
                            modal.find('.progress-bar').width(percentComplete + '%');
                        }
                    }, false);
                }
                return xhr;
            },
            success: function (response) {
                if (response.success) {
                    form.find('button[type="submit"]').parent().before('<p class="js-error mt-2 text-center text-success assign-modal-msg">' + (response.data?.msg || "Password updated successfully.") + '</p>');
                    setTimeout(() => {
                        modal.modal('hide');
                    }, 1500);
                } else {
                    form.find('button[type="submit"]').parent().before('<p class="js-error mt-2 text-danger text-center assign-modal-msg">Something went wrong. Please try again.</p>');
                }
            },
            error: function (xhr, status, error) {
                form.find('button[type="submit"]').parent().before('<p class="js-error mt-2 text-danger text-center assign-modal-msg">Something went wrong. Please try again.</p>');
            },
            complete: function () {
                modal.find('.progress-field').fadeOut(200);
                setTimeout(() => {
                    form.find('button[type="submit"]').attr('disabled', false);
                    modal.find('.progress-bar').width('0%');
                }, 250);
            }
        });
    });

    $(document).on('click', '.js-change-client-email--', function (e) {
        e.preventDefault();
        $('.js-error').remove();
        var id = $(this).data('id');
        $('.js-change-client-email--modal').find('[name="id"]').val(id);
        $('.js-change-client-email--modal').find('[name="email"]').val('');
        $('.js-change-client-email--modal').modal('show');
    });

    $(document).on('submit', '.js-change-client-email--modal form', function (e) {
        e.preventDefault();
        var form = $(this), formdata = form.serializeArray(), isValid = true, email = form.find('[name="email"]'), modal = $('.js-change-client-email--modal');
        $('.js-error').remove();
        if (!$.trim(email.val())) {
            email.focus();
            email.parent().append('<p class="js-error text-danger">Please fill this field.</p>');
            isValid = false;
        }

        if (!isValid) {
            return;
        }
        form.find('button[type="submit"]').attr('disabled', true);
        $.ajax({
            url: cm_ajax_ob.ajax_url,
            type: 'post',
            data: formdata,
            xhr: function (event) {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = parseInt((evt.loaded / evt.total) * 100);
                            modal.find('.progress-field').show();
                            modal.find('.progress-bar').width(percentComplete + '%');
                        }
                    }, false);
                }
                return xhr;
            },
            success: function (response) {
                if (response.success) {
                    form.find('button[type="submit"]').parent().before('<p class="js-error mt-2 text-center text-success assign-modal-msg">' + (response.data?.msg || "Password updated successfully.") + '</p>');
                    setTimeout(() => {
                        modal.modal('hide');
                        location.reload();
                    }, 1000);
                } else {
                    form.find('button[type="submit"]').parent().before('<p class="js-error mt-2 text-danger text-center assign-modal-msg">' + (response?.data?.msg || 'Something went wrong. Please try again.') + '</p>');
                }
            },
            error: function (xhr, status, error) {
                form.find('button[type="submit"]').parent().before('<p class="js-error mt-2 text-danger text-center assign-modal-msg">Something went wrong. Please try again.</p>');
            },
            complete: function () {
                modal.find('.progress-field').fadeOut(200);
                setTimeout(() => {
                    form.find('button[type="submit"]').attr('disabled', false);
                    modal.find('.progress-bar').width('0%');
                }, 250);
            }
        });
    });

    $('.toggle-visible-eye').click(function (e) {
        e.preventDefault();
        var pass = $(this).siblings('[name="password"]');
        if (pass.attr('type') == 'text') {
            pass.attr('type', 'password');
            $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            pass.attr('type', 'text');
            $(this).find('i').addClass('fa-eye-slash').removeClass('fa-eye');
        }
    });

    $(document).on('click', '.js-change-client-details--', function (e) {
        e.preventDefault();
        var id = $(this).data('id'), role = 'client', modal = $('.js-client-edit--modal');
        modal.find('.js-error').empty();
        modal.find('[name="id"]').val(id);
        modal.find('.js-form-fields').empty();
        modal.modal('show');
        modal.find('button[type="submit"]').attr('disabled', true);
        $.ajax({
            url: cm_ajax_ob.ajax_url,
            type: 'post',
            xhr: function (event) {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = parseInt((evt.loaded / evt.total) * 100);
                            modal.find('.progress-field').show();
                            modal.find('.progress-bar').width(percentComplete + '%');
                        }
                    }, false);
                }
                return xhr;
            },
            data: {
                action: prefix + '_admin_get_' + role + '_details',
                id: id
            },
            success: function (response) {
                if (response.success) {
                    modal.find('.js-form-fields').html(response.data.html);
                    modal.find('button[type="submit"]').attr('disabled', false);
                    iti?.destroy();
                    iti = window.intlTelInput(document.querySelector('[name="phone"]'), {
                        // initialCountry: "ch",
                        separateDialCode: true,
                        // onlyCountries: ["ch"],
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/utils.js"
                    });
                } else {
                    modal.find('.js-form-fields').append('<p class="text-danger text-center">Something went wrong. Please try again.</p>');
                }
            },
            error: function () {
                modal.find('.js-form-fields').append('<p class="text-danger text-center">Something went wrong. Please try again.</p>');
            },
            complete: function () {
                modal.find('.progress-field').fadeOut(200);
                setTimeout(() => {
                    modal.find('.progress-bar').width('0%');
                }, 250);
            }
        });
    });

    $(document).on('submit', '.js-client-edit--modal form, .js-client-create--modal form', function (e) {
        e.preventDefault();
        var form = $(this), formdata = form.serializeArray(), isValid = true, modal = $(this).closest('.modal');
        modal.find('.js-error').remove();
        $.each(formdata, (index, item) => {
            if (!$.trim(item.value)) {
                if (isValid) {
                    $('[name="' + item.name + '"]').focus();
                }
                if (item.name === 'phone') {
                    $('[name="' + item.name + '"]').parent().parent().append('<p class="js-error text-danger">Please fill this field.</p>');
                } else {
                    $('[name="' + item.name + '"]').parent().append('<p class="js-error text-danger">This field is required.</p>');
                }
                isValid = false;
            } else if (item.name === 'phone') {
                formdata[index]['value'] = iti?.getNumber() || item.value;
            }
        });

        if (!isValid) {
            return false;
        }
        form.find('button[type="submit"]').attr('disabled', true);
        $.ajax({
            url: cm_ajax_ob.ajax_url,
            type: 'post',
            data: formdata,
            xhr: function (event) {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = parseInt((evt.loaded / evt.total) * 100);
                            modal.find('.progress-field').show();
                            modal.find('.progress-bar').width(percentComplete + '%');
                        }
                    }, false);
                }
                return xhr;
            },
            success: function (response) {
                if (response.success) {
                    form.find('button[type="submit"]').parent().prepend('<p class="text-success js-success text-center"> Details saved successfully.</p>');
                    setTimeout(() => {
                        modal.modal('hide');
                        location.reload();
                    }, 1500);
                } else {
                    if (response?.data?.errors) {
                        $.each(response.data.errors, function (index, item) {
                            var inputElement = form.find('[name="' + index + '"]');
                            if (item) {
                                isValid ? inputElement.focus() : '';
                                if (index == 'phone') {
                                    $(inputElement.parent().parent()).append('<p class="js-error text-danger">' + item + '</p>');
                                } else {
                                    $(inputElement.parent()).append('<p class="js-error text-danger">' + item + '</p>');
                                }
                                isValid = false;
                                return;
                            }
                        });
                    } else if (response?.data?.msg) {
                        form.find('button[type="submit"]').before('<p class="text-danger js-error">' + response?.data?.msg + '</p>');
                    } else {
                        form.find('button[type="submit"]').parent().prepend('<p class="text-danger js-error"> Something went wrong. Please try again.</p>');
                    }
                }
            }, error: function () {
                form.find('button[type="submit"]').parent().prepend('<p class="text-danger js-error"> Something went wrong. Please try again.</p>');
            },
            complete: function () {
                form.find('button[type="submit"]').attr('disabled', false);
                modal.find('.progress-field').fadeOut(200);
                setTimeout(() => {
                    modal.find('.progress-bar').width('0%');
                }, 250);
            }
        });
    });
});