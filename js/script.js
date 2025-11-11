var $ = jQuery, prefix = 'cm_'
    , printError = (elem, msg) => {
        elem?.append('<p class="js-error text-danger">' + msg + '</p>');
    }, removeErrors = () => {
        $('.js-error')?.remove();
    }

jQuery(function ($) {

    $(document).on('input change', 'input, select, textarea', function () {
        $(this).closest('.mb-3, .form-group, .col-md-6')?.find('.js-error')?.remove();
        $(this).parent()?.find('.js-error')?.remove();
    });
    $(document).on('click change', 'input[type="radio"], input[type="checkbox"]', function () {
        $(this).closest('.form-group, .mb-3')?.find('.js-error')?.remove();
        $(this).parent()?.find('.js-error')?.remove();
    });
    
    $(document).on('submit', '.js-cm-login-form', function (e) {
        e.preventDefault();
        var form = $(this), isValid = true;
        removeErrors();
        if (form.find('[name="email"]').val() == '') {
            printError(form.find('[name="email"]').parent(), 'The email is required.');
            isValid = false;
        }
        if (form.find('[name="password"]').val() == '') {
            printError(form.find('[name="password"]').parent().parent(), 'The password is required.');
            isValid = false;
        }
        if (!isValid) {
            return;
        }
        form.find('button[type="submit"]').attr('disabled', true).text('Hang on...');
        $.ajax({
            url: cm_obj.ajax_url,
            type: 'post',
            data: form.serialize(),
            success: function (response) {
                if (response.success) {
                    form.find('button[type="submit"]').before('<p class="text-dark text-center js-error">' + response.data?.success?.msg + '</p>');
                    setTimeout(() => {
                        // location.href = response.data?.success?.path;
                        location.reload();
                    }, 1000);
                } else {
                    $.each(response.data.errors, function (index, item) {
                        var inputElement = form.find('[name="' + index + '"]');
                        if (item) {
                            isValid ? inputElement.focus() : '';
                            printError(index == 'password' ? inputElement.parent().parent() : inputElement.parent(), item);
                            isValid = false;
                            return;
                        }
                    });
                    if (response?.data?.errors?.msg) {
                        form.find('button[type="submit"]').before('<p class="js-error text-danger">' + response.data.errors.msg + '</p>');
                    }
                }
            }, error: function (response) {
                form.find('button[type="submit"]').before('<p class="js-error text-danger">Could not connect to server. Please try again.</p>');
            }, complete: function () {
                form.find('button[type="submit"]').attr('disabled', false).text('Sign In');
            }
        });
    });

    $(document).on('click', '.toggle-visible-eye', function (e) {
        e.preventDefault();
        var pass = $(this).siblings('[name="password"], [name="confirm_password"]');
        if (pass.attr('type') == 'text') {
            pass.attr('type', 'password');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            pass.attr('type', 'text');
            $(this).addClass('fa-eye-slash').removeClass('fa-eye');
        }
    });

    $(document).on('submit', '.js-forget-password', function (e) {
        e.preventDefault(); removeErrors();
        var form = $(this), formData = form.serializeArray(), email = form.find('[name="email"]');
        if (!email.val()) {
            printError(email.parent(), 'The email is required.');
            email.focus();
            return;
        }

        form.find('button[type="submit"]').attr('disabled', true).text('Hang on...');
        $.ajax({
            url: cm_obj.ajax_url,
            type: 'post',
            data: formData,
            success: function (response) {
                if (response.success) {
                    email.parent().append('<p class="text-success js-error">' + (response?.data?.msg || "We have sent you a link to set new password on your email address.") + '</p>');
                    email.val('');
                    setTimeout(() => {
                        location.href = response?.data?.path || "/sign-in";
                    }, 6000);
                } else {
                    email.focus();
                    printError(email.parent(), response?.data?.msg || 'Could not connect to server. Please try again.');
                }
            }, error: function () {
                printError(email.parent(), 'Could not connect to server. Please try again.');
            }, complete: function () {
                form.find('button[type="submit"]').attr('disabled', false).text('Sign In');
            }
        });
    });

    $(document).on('submit', '.js-change-password', function (e) {
        e.preventDefault();
        var form = $(this), isValid = true, pwd = form.find('[name="password"]').val(), cpwd = form.find('[name="confirm_password"]').val();
        removeErrors();
        if (!$.trim(pwd)) {
            printError(form.find('[name="password"]').parent().parent(), 'The password is required.');
            form.find('[name="password"]').focus();
            isValid = false;
        }
        if (!$.trim(cpwd)) {
            printError(form.find('[name="confirm_password"]').parent().parent(), 'The confirm password is required.');
            isValid ? form.find('[name="confirm_password"]').focus() : '';
            isValid = false;
        }

        if (isValid && cpwd !== pwd) {
            printError(form.find('[name="confirm_password"]').parent().parent(), 'The confirm password does not match.');
            form.find('[name="confirm_password"]').focus();
            isValid = false;
        }

        if (!isValid) {
            return;
        }
        form.find('button[type="submit"]').attr('disabled', true).text('Hang on...');
        $.ajax({
            url: cm_obj.ajax_url,
            type: 'post',
            data: form.serialize(),
            success: function (response) {
                if (response.success) {
                    form.find('button[type="submit"]').before('<p class="text-dark text-center js-error">' + response.data?.success?.msg + '</p>');
                    setTimeout(() => {
                        // location.href = response.data?.success?.path;
                        location.reload();
                    }, 6000);
                } else {
                    $.each(response.data.errors, function (index, item) {
                        var inputElement = form.find('[name="' + index + '"]');
                        if (item) {
                            isValid ? inputElement.focus() : '';
                            printError(index == 'password' ? inputElement.parent().parent() : inputElement.parent(), item);
                            isValid = false;
                            return;
                        }
                    });
                    if (response?.data?.errors?.msg) {
                        form.find('button[type="submit"]').before('<p class="js-error text-danger">' + response.data.errors.msg + '</p>');
                    }
                }
            }, error: function (response) {
                form.find('button[type="submit"]').before('<p class="js-error text-danger">Could not connect to server. Please try again.</p>');
            }, complete: function () {
                form.find('button[type="submit"]').attr('disabled', false).text('Sign In');
            }
        });

    });

    const selectWrapper = document.querySelector('.select-wrapper');
    const select = document.querySelector('#location');

    select?.addEventListener('click', () => {
        selectWrapper.classList.toggle('active');
    });

    if ($('.location-images').length) {
        let magicGrid = new MagicGrid({
            container: ".location-images",
            animate: true,
            gutter: 20,
            static: true,
            useMin: true,
        });
        
        magicGrid.listen();
        window.addEventListener("load", () => {
            magicGrid.positionItems();
        });
    }

    let currentPage = window.location.pathname.split("/").pop();
    if (!currentPage) currentPage = "index.html";

    $(document).ready(function () {
        let currentPage = window.location.pathname.split("/").filter(Boolean).pop();

        $(".sidebar ul li a").each(function () {
            let href = $(this).attr("href") || "";

            let hrefLast = href.split("/").filter(Boolean).pop();

            if (hrefLast === currentPage) {
                $(this).addClass("active");
            } else {
                $(this).removeClass("active");
            }

            if (!hrefLast && !currentPage) {
                $(this).addClass("active");
            }
        });
    });

    const site_menus = $('.elementor-location-header nav ul').eq(0);

    if (site_menus.length) {
        ids = 0;
        site_menus.each(function () {
            const $menu = $(this);

            if (cm_obj.is_auth) {
                $menu.append(`
                <li class="e-n-menu-item">
                    <ul class="nav navbar-nav align-items-center mb-0">
                        <li class="nav-item nav-profile dropdown">
                            <a class="nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown"
                                id="profileDropdown">
                                <img src="${cm_obj.url}img/dummy-profile.webp" alt="profile" class="profile-img" />
                                <span class="profile-name ms-2 me-1">Issam</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown header-a-drop"
                                aria-labelledby="profileDropdown">
                                <a class="dropdown-item" href="${cm_obj.site_url}client/profile/"><i class="fa-solid fa-user me-2"></i>Profile</a>
                                <form method="post">
                                    <input type="hidden" name="casamedia_user_logout" value="1" />
                                    <button type="submit" class="dropdown-item js-cm-user-logout" href="javascript:void(0);"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>`);
            } else {
                const $lastItem = $menu.children('li').last();
                
                if ($lastItem.length) {
                    const $clone = $lastItem.clone();
                    
                    $clone.find('a')
                    .attr('href', '/sign-in/')
                    .html('<span class="e-n-menu-title-text">Sign In</span>');

                    $clone.addClass('custom-item');
                    $clone.find('div').attr('id', 'cm-id-' + ids++);
                    
                    $menu.append($clone);
                }
                
            }
        });
    }

    $(document).on('change', '[name="map_locations"]', function () {
        
    })
});