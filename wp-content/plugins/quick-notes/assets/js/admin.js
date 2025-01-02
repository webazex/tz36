window.$ = jQuery;
$(document).ready(function (){
    console.log("Quick Notes plugin active");

    function sendAjaxRequest(dataForm){
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'size_converter_action',
                security: size_converter_nonce,
                action_type: 'add_pair',
                pair: pair_data
            },
            success: function (response) {
                if(response.success === true){
                    if (container.hasClass('wbzx-error') || container.hasClass('wbzx-success')) {
                        container.removeClass('wbzx-error wbzx-success').addClass('wbzx-success');
                    }else {
                        container.addClass('wbzx-success');
                    }
                    updateTable();
                }else{
                    if (container.hasClass('wbzx-error') || container.hasClass('wbzx-success')) {
                        container.removeClass('wbzx-error wbzx-success').addClass('wbzx-error');
                    }else {
                        container.addClass('wbzx-error');
                    }

                }
                $('.form-row-result__text').text(response.data);
                $('#add-pair-form')[0].reset();
            },
            error: function () {
                alert('Error request.');
                $('#add-pair-form')[0].reset();
            }
        });
    }

    $('#note-form-00').submit(function (e) {
        e.preventDefault();

        // new FormData
        let thisFormData = new FormData(this);

        // add params
        thisFormData.append('action', 'create_note');
        thisFormData.append('security', save_note_nonce);

        // Вызываем функцию для отправки AJAX-запроса
        sendAjaxRequest(thisFormData);
    });







    //add pair
    $('#add-pair-form').on('submit', function (event) {
        event.preventDefault();
        const container = $('.form-row-result__result-container');
        let pair_data = $(this).serializeArray();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'size_converter_action',
                security: size_converter_nonce,
                action_type: 'add_pair',
                pair: pair_data
            },
            success: function (response) {
                if(response.success === true){
                    if (container.hasClass('wbzx-error') || container.hasClass('wbzx-success')) {
                        container.removeClass('wbzx-error wbzx-success').addClass('wbzx-success');
                    }else {
                        container.addClass('wbzx-success');
                    }
                    updateTable();
                }else{
                    if (container.hasClass('wbzx-error') || container.hasClass('wbzx-success')) {
                        container.removeClass('wbzx-error wbzx-success').addClass('wbzx-error');
                    }else {
                        container.addClass('wbzx-error');
                    }

                }
                $('.form-row-result__text').text(response.data);
                $('#add-pair-form')[0].reset();
            },
            error: function () {
                alert('Error request.');
                $('#add-pair-form')[0].reset();
            }
        });
    });

    function updateTable(offset = 0, currentPage = 1, perPage = 10) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'size_converter_action',
                security: size_converter_nonce,
                action_type: 'get_pairs',
                limit: 10,
                offset: offset
            },
            success: function (response) {
                if (response.success) {
                    const data = JSON.parse(response.data);
                    const tableHtml = renderTable(data);
                    $('#pairs-table tbody').html(tableHtml);
                    //render pagination
                    const paginationHtml = renderPagination(data.count, currentPage, perPage);
                    $('.wbzx-container-pagination').html(paginationHtml);
                }
            },
        });
    }

    $(document).on('click', '.result-container__close-btn', function () {
        let el = $(this).closest('.form-row-result__result-container');
        if (el.hasClass('wbzx-error') || el.hasClass('wbzx-success')) {
            el.removeClass('wbzx-error wbzx-success');
            el.removeClass('wbzx-error wbzx-error');
        }
    });

    function renderTable(data) {
        let html = '';
        if (data.pairs && Array.isArray(data.pairs) && data.pairs.length > 0) {
            data.pairs.forEach(function(pair) {
                const originalSize = pair.original_size || 'N/A';
                const convertedSize = pair.converted_size || 'N/A';

                html += `
                <tr data-original="${originalSize}" data-converted="${convertedSize}">
                    <td>${originalSize}</td>
                    <td>${convertedSize}</td>
                    <td>
                        <button class="edit-button button-primary wbzx-pair-edit" data-original="${originalSize}" data-converted="${convertedSize}">Edit</button>
                        <button class="delete-button button-secondary wbzx-pair-delete" data-original="${originalSize}" data-converted="${convertedSize}">Delete</button>
                    </td>
                </tr>
            `;
            });
        } else {
            html = `<tr><td colspan="3">No pairs found</td></tr>`;
        }

        try {
            // update
            $('#pairs-table tbody').html(html);
        } catch (error) {
            console.error('Error updating table:', error);
        }
    }


    function renderPagination(totalCount, currentPage = 1, perPage = 10) {
        const totalPages = Math.ceil(totalCount / perPage);
        if (totalPages <= 1) {
            return ''; // if only one page
        }

        let html = `<div class="wbzx-pagination" data-pp="${perPage}">`;

        // arr left if it's not first page
        if (currentPage > 1) {
            const prevOffset = (currentPage - 2) * perPage;
            html += `<button class="pagination-arrow prev" data-page="${currentPage - 1}" data-offset="${prevOffset}">&lt;</button>`;
        }

        // first page visible always
        if (currentPage > 3) {
            html += `<button class="pagination-page" data-page="1" data-offset="0">1</button>`;
            if (currentPage > 4) {
                html += '<span class="pagination-ellipsis">...</span>'; // Пропуск страниц
            }
        }

        // +1 page near current page
        for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
            const offset = (i - 1) * perPage;
            if (i === currentPage) {
                html += `<button class="pagination-page current" data-page="${i}" data-offset="${offset}">${i}</button>`;
            } else {
                html += `<button class="pagination-page" data-page="${i}" data-offset="${offset}">${i}</button>`;
            }
        }

        // visible always last page
        if (currentPage < totalPages - 2) {
            if (currentPage < totalPages - 3) {
                html += '<span class="pagination-ellipsis">...</span>'; // Пропуск страниц
            }
            const lastOffset = (totalPages - 1) * perPage;
            html += `<button class="pagination-page" data-page="${totalPages}" data-offset="${lastOffset}">${totalPages}</button>`;
        }

        // arr right if it's not last page
        if (currentPage < totalPages) {
            const nextOffset = currentPage * perPage;
            html += `<button class="pagination-arrow next" data-page="${currentPage + 1}" data-offset="${nextOffset}">&gt;</button>`;
        }

        html += '</div>';
        return html;
    }


    //pagination ajax
    $('.wbzx-container-pagination').on('click', '.pagination-page', '', function (){
        let dataPage = $(this).data('page');
        let offset = $(this).data('offset');
        $('.pagination-page').removeClass('current');
        $(this).addClass('current');
        updateTable(offset, dataPage);
    });

    //edit form
    jQuery(document).ready(function ($) {
        // Обработчик клика на кнопку "Edit"
        $(document).on('click', '.wbzx-pair-edit', function () {
            const $button = $(this);
            const $row = $button.closest('tr'); // Текущая строка
            const originalSize = $button.data('original'); // Текущее значение original_size
            const convertedSize = $button.data('converted'); // Текущее значение converted_size

            // Проверяем, нет ли уже открытой формы
            if ($row.next('.edit-form-row').length > 0) {
                return; // Если форма уже открыта, ничего не делаем
            }

            // Делаем кнопку "Edit" неактивной
            $button.prop('disabled', true);

            // Создаем форму
            const formHtml = `
            <tr class="edit-form-row">
                <td colspan="3">
                    <form class="edit-form">
                        <label>
                            Original Size:
                            <input type="text" class="edit-original" value="${originalSize}" pattern="[a-zA-Z]+" required>
                        </label>
                        <label>
                            Converted Size:
                            <input type="number" class="edit-converted" value="${convertedSize}" min="1" step="1" required>
                        </label>
                        <button type="submit" class="button-primary save-edit">Save</button>
                        <button type="button" class="button-secondary cancel-edit">Cancel</button>
                    </form>
                </td>
            </tr>
        `;

            // append
            $row.after(formHtml);
        });

        // cancel
        $(document).on('click', '.cancel-edit', function () {
            const $formRow = $(this).closest('.edit-form-row'); // Строка формы
            const $row = $formRow.prev('tr'); // Исходная строка таблицы
            $row.find('.wbzx-pair-edit').prop('disabled', false); // Активируем кнопку "Edit"
            $formRow.remove(); // Удаляем строку с формой
        });

        // submit
        $(document).on('submit', '.edit-form', function (e) {
            e.preventDefault();

            const $form = $(this);
            const $formRow = $form.closest('.edit-form-row');
            const $row = $formRow.prev('tr');

            const originalSize = $form.find('.edit-original').val();
            const convertedSize = $form.find('.edit-converted').val();

            const oldOriginalSize = $row.data('original');
            const oldConvertedSize = $row.data('converted');



            // ajax
            console.log('Saving...', { originalSize, convertedSize });
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'size_converter_action',
                    security: size_converter_nonce,
                    action_type: 'update_pair',
                    original_size: originalSize,
                    converted_size: convertedSize,
                    old_original_size: oldOriginalSize,
                    old_converted_size: oldConvertedSize,
                },
                success: function (response) {
                    if (response.success) {


                        // upd table
                        $row.attr('data-original', originalSize).attr('data-converted', convertedSize);
                        $row.find('td').eq(0).text(originalSize);
                        $row.find('td').eq(1).text(convertedSize);
                        $row.find('.wbzx-pair-edit').data('original', originalSize).data('converted', convertedSize);

                        // remove form
                        $row.find('.wbzx-pair-edit').prop('disabled', false);
                        $formRow.remove();
                    } else {
                        console.error(response.data);
                    }
                },
            });
        });
    });

    //delete pair
    $(document).on('click', '.wbzx-pair-delete', function () {
        const $button = $(this);
        const originalSize = $button.data('original');
        const convertedSize = $button.data('converted');
        const $row = $button.closest('tr');

        if (!confirm(`Are you sure you want to delete the pair "${originalSize}" -> "${convertedSize}"?`)) {
            return;
        }

        // AJAX запрос
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'size_converter_action',
                security: size_converter_nonce,
                action_type: 'delete_pair',
                original_size: originalSize,
                converted_size: convertedSize,
            },
            success: function (response) {
                if (response.success) {
                    alert(response.data.message || 'Pair successfully deleted.');
                    //removed row
                    $row.remove();
                } else {
                    alert(response.data.message || 'Failed to delete pair.');
                }
            },
            error: function (response) {
                alert('An error occurred. Please try again.');
            },
        });
    });


});