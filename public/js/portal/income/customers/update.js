var customerUpdate = {
    importId: null,
    timer: null,
    applying: false,

    upload: function () {
        var file = $('#file')[0].files[0];

        if (!file) {
            Biblioteca.notificaciones('Debe seleccionar un archivo CSV.', 'Actualización clientes', 'warning');
            return;
        }

        var data = new FormData(document.getElementById('form-customer-update'));
        var button = $('#btn-upload');

        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Cargando...');

        $.ajax({
            url: '/Admin/customer-update/upload',
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            dataType: 'json',

            success: function (response) {
                if (!response.success) {
                    Biblioteca.notificaciones(response.message, 'Actualización clientes', 'error');
                    return;
                }

                customerUpdate.importId = response.import_id;
                customerUpdate.applying = false;

                $('#process-card').show();
                $('#btn-apply,#btn-cancel-import,#btn-errors').hide();
                $('#process-error').hide();

                customerUpdate.startPolling();
            },

            error: function (xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Ocurrió un error cargando el archivo.';

                Biblioteca.notificaciones(message, 'Actualización clientes', 'error');
            },

            complete: function () {
                button.prop('disabled', false).html('<i class="fa fa-upload"></i> Cargar y validar archivo');
            }
        });
    },

    startPolling: function () {
        customerUpdate.stopPolling();
        customerUpdate.loadStatus();

        customerUpdate.timer = setInterval(function () {
            customerUpdate.loadStatus();
        }, 3000);
    },

    stopPolling: function () {
        if (customerUpdate.timer) {
            clearInterval(customerUpdate.timer);
            customerUpdate.timer = null;
        }
    },

    loadStatus: function () {
        if (!customerUpdate.importId) return;

        $.ajax({
            url: '/Admin/customer-update/' + customerUpdate.importId + '/status',
            type: 'GET',
            dataType: 'json',

            success: function (response) {
                if (response.success) customerUpdate.renderStatus(response.data);
            }
        });
    },

    renderStatus: function (data) {
        $('#process-status').text(customerUpdate.translateStatus(data.status));
        $('#total-rows').text(customerUpdate.numberFormat(data.total_rows));
        $('#valid-rows').text(customerUpdate.numberFormat(data.valid_rows));
        $('#error-rows').text(customerUpdate.numberFormat(data.error_rows));
        $('#updated-rows').text(customerUpdate.numberFormat(data.updated_rows));

        var progress = parseInt(data.progress || 0, 10);

        $('#process-progress').css('width', progress + '%').text(progress + '%');

        if (data.status === 'READY' && parseInt(data.error_rows || 0, 10) > 0) {
            $('#btn-errors')
                .attr('href', '/Admin/customer-update/' + customerUpdate.importId + '/errors')
                .show();
        } else {
            $('#btn-errors').hide();
        }

        if (data.status === 'PENDING' || data.status === 'VALIDATING') {
            customerUpdate.applying = false;
            $('#btn-apply,#btn-cancel-import').hide();
        }

        if (data.status === 'READY') {
            if (!customerUpdate.applying) {
                customerUpdate.stopPolling();

                if (parseInt(data.valid_rows || 0, 10) > 0) {
                    $('#btn-apply').prop('disabled', false).show();
                } else {
                    $('#btn-apply').hide();
                }

                $('#btn-cancel-import').prop('disabled', false).show();
            } else {
                $('#process-status').text('Iniciando actualización de clientes...');
                $('#btn-apply,#btn-cancel-import,#btn-errors').hide();
            }
        }

        if (data.status === 'APPLY_QUEUED') {
            customerUpdate.applying = true;
            $('#process-status').text('Iniciando actualización de clientes...');
            $('#btn-apply,#btn-cancel-import,#btn-errors').hide();
        }

        if (data.status === 'APPLYING') {
            customerUpdate.applying = true;
            $('#btn-apply,#btn-cancel-import,#btn-errors').hide();
        }

        if (data.status === 'COMPLETED') {
            customerUpdate.applying = false;
            customerUpdate.stopPolling();
            $('#btn-apply,#btn-cancel-import,#btn-errors').hide();

            Biblioteca.notificaciones(
                'La actualización de clientes terminó correctamente.',
                'Actualización clientes',
                'success'
            );

            setTimeout(function () {
                window.location.reload();
            }, 1500);
        }

        if (data.status === 'CANCELLED') {
            customerUpdate.applying = false;
            customerUpdate.stopPolling();
            $('#btn-apply,#btn-cancel-import,#btn-errors').hide();

            setTimeout(function () {
                window.location.reload();
            }, 1000);
        }

        if (data.status === 'VALIDATION_FAILED' || data.status === 'APPLY_FAILED') {
            customerUpdate.applying = false;
            customerUpdate.stopPolling();
            $('#btn-apply,#btn-cancel-import,#btn-errors').hide();

            $('#process-error')
                .text(data.error_message || 'Ocurrió un error durante el proceso.')
                .show();
        }
    },

    apply: function () {
        if (!customerUpdate.importId) return;

        swal({
            title: 'Actualización masiva de clientes',
            text: 'Se actualizarán ' + $('#valid-rows').text() + ' registros válidos. ¿Desea continuar?',
            icon: 'warning',
            buttons: {
                cancel: true,
                confirm: {
                    text: 'Actualizar',
                    value: true,
                    visible: true
                }
            }
        }).then(function (value) {
            if (!value) return;

            customerUpdate.applying = true;

            $('#btn-apply,#btn-cancel-import,#btn-errors').hide();
            $('#process-error').hide();
            $('#process-status').text('Iniciando actualización de clientes...');
            $('#process-progress').css('width', '0%').text('0%');
            $('#updated-rows').text('0');

            customerUpdate.startPolling();

            $.ajax({
                url: '/Admin/customer-update/' + customerUpdate.importId + '/apply',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: $('input[name="_token"]').val()
                },

                success: function (response) {
                    if (!response.success) {
                        customerUpdate.applying = false;
                        customerUpdate.stopPolling();
                        $('#btn-apply,#btn-cancel-import').prop('disabled', false).show();

                        Biblioteca.notificaciones(response.message, 'Actualización clientes', 'error');
                    }
                },

                error: function (xhr) {
                    customerUpdate.applying = false;
                    customerUpdate.stopPolling();

                    var message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Ocurrió un error iniciando la actualización.';

                    Biblioteca.notificaciones(message, 'Actualización clientes', 'error');
                }
            });
        });
    },

    cancelImport: function () {
        if (!customerUpdate.importId) return;

        swal({
            title: 'Cancelar importación',
            text: 'Los datos validados no serán aplicados a los clientes. El CSV original permanecerá almacenado como histórico. ¿Desea cancelar?',
            icon: 'warning',
            buttons: {
                cancel: true,
                confirm: {
                    text: 'Sí, cancelar',
                    value: true,
                    visible: true
                }
            }
        }).then(function (value) {
            if (!value) return;

            $('#btn-cancel-import,#btn-apply').prop('disabled', true);

            $.ajax({
                url: '/Admin/customer-update/' + customerUpdate.importId + '/cancel',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: $('input[name="_token"]').val()
                },

                success: function (response) {
                    if (!response.success) {
                        $('#btn-cancel-import,#btn-apply').prop('disabled', false);
                        Biblioteca.notificaciones(response.message, 'Actualización clientes', 'error');
                        return;
                    }

                    customerUpdate.stopPolling();
                    customerUpdate.applying = false;
                    $('#btn-apply,#btn-cancel-import,#btn-errors').hide();
                    $('#process-status').text('Proceso cancelado');

                    Biblioteca.notificaciones(
                        'El proceso fue cancelado correctamente.',
                        'Actualización clientes',
                        'success'
                    );

                    setTimeout(function () {
                        window.location.reload();
                    }, 1200);
                },

                error: function (xhr) {
                    $('#btn-cancel-import,#btn-apply').prop('disabled', false);

                    var message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Ocurrió un error cancelando el proceso.';

                    Biblioteca.notificaciones(message, 'Actualización clientes', 'error');
                }
            });
        });
    },

    translateStatus: function (status) {
        var statuses = {
            'PENDING': 'Pendiente',
            'VALIDATING': 'Validando archivo',
            'READY': 'Validación finalizada - listo para actualizar',
            'APPLY_QUEUED': 'Iniciando actualización',
            'APPLYING': 'Actualizando clientes',
            'COMPLETED': 'Proceso finalizado',
            'CANCELLED': 'Proceso cancelado',
            'VALIDATION_FAILED': 'Error validando archivo',
            'APPLY_FAILED': 'Error actualizando clientes'
        };

        return statuses[status] || status;
    },

    numberFormat: function (value) {
        return parseInt(value || 0, 10).toLocaleString('es-CO');
    },

    init: function () {
        $('body').on('click', '#btn-upload', customerUpdate.upload);
        $('body').on('click', '#btn-apply', customerUpdate.apply);
        $('body').on('click', '#btn-cancel-import', customerUpdate.cancelImport);

        var activeImportId = $('#active-import-id').val();
        var activeImportStatus = $('#active-import-status').val();

        if (activeImportId) {
            customerUpdate.importId = parseInt(activeImportId, 10);
            customerUpdate.applying = activeImportStatus === 'APPLY_QUEUED' || activeImportStatus === 'APPLYING';

            $('#process-card').show();
            $('#process-error').hide();

            customerUpdate.startPolling();
        }
    }
};

$(function () {
    customerUpdate.init();
});
