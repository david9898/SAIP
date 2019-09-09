$(document).ready(() => {
    changeView()
    addStaff()
    editStaff()
    magnificPopUp()
    updateStaff()
    disableStaff()
})

function changeView() {
    $('.all_staff_view').on('click', function () {
        $('#all_staff').css('display', 'block')
        $('#register_staff').css('display', 'none')
    })

    $('.add_staff_view').on('click', function () {
        $('#register_staff').css('display', 'block')
        $('#all_staff').css('display', 'none')
    })
}

function addStaff() {
    $('.add_staff').on('click', function (e) {
        e.preventDefault()

        let body = {
            'first_name': $('.first_name').val(),
            'last_name': $('.last_name').val(),
            'phone': $('.phone').val(),
            'username': $('.username').val(),
            'password': $('.password').val(),
            'repeat_password': $('.repeat_password').val(),
            'roles': $('.roles').val(),
            'csrf_token': $('.csrf_token').val()
        }

        $.ajax({
            url: baseUrl + 'addStaff',
            type: 'POST',
            data: {body: JSON.stringify(body)}
        }).then((res) => {
            let responce = JSON.parse(res)

            if ( responce['status'] === 'success' ) {
                location.reload()
            }else {
                toastr.error(responce['description'])
            }
        })
    })
}

async function editStaff() {

    $('.edit_staff').on('click', function () {
        let csrfToken  = $('.csrf_token').val()
        let customerId = $(this).attr('staffId')
        $('.update_staff').attr('staffId', customerId)

        $.ajax({
            url: baseUrl + 'getOneCustomer/' + customerId + '/' + csrfToken,
            type: 'GET'
        }).then((res) => {
            let responce     = JSON.parse(res)

            let rolesOptions = $('#edit_staff select option').toArray()

            for ( let roleOption of rolesOptions ) {
                $(roleOption).removeAttr('selected')
            }

            if ( responce['status'] === 'success' ) {
                $('#edit_staff .first_name').val(responce['staff']['firstName'])
                $('#edit_staff .last_name').val(responce['staff']['lastName'])
                $('#edit_staff .phone').val(responce['staff']['phone'])
                $('#edit_staff .username').val(responce['staff']['username'])

                for ( let roleName of responce['staff']['roles'] ) {
                    for ( let roleOption of rolesOptions ) {
                        if ( $(roleOption).text() === roleName ) {
                            $(roleOption).attr('selected', 'true')
                        }
                    }
                }


            }else {
                toastr.error(responce['description'])
            }
        })
    })
}

function magnificPopUp() {
    $('.edit_staff').magnificPopup({
        type: 'inline',
        midClick: true
    })
}

function updateStaff() {
    $('.update_staff').on('click', function (e) {
        e.preventDefault()

        let body = {
            'id': $(this).attr('staffId'),
            'first_name': $('.first_name').val(),
            'last_name': $('.last_name').val(),
            'phone': $('.phone').val(),
            'username': $('.username').val(),
            'password': $('.password').val(),
            'repeat_password': $('.repeat_password').val(),
            'roles': $('#role_edit').val(),
            'csrf_token': $('.csrf_token').val()
        }


        $.ajax({
            url: baseUrl + 'updateStaff',
            type: 'PUT',
            data: {body: JSON.stringify(body)}
        }).then((res) => {
            let responce = JSON.parse(res)

            if ( responce['status'] === 'success' ) {
                $.magnificPopup.close()

                location.reload()
            }else {
                toastr.error(responce['description'])
            }
        })

    })
}

function disableStaff() {
    $('.delete_staff').on('click', function () {
        let body = {
            'id': $(this).attr('staffId'),
            'csrf_token': $('.csrf_token').val()
        }

        swal({
            title: "Сигурен ли сте че искате да премахте служителя?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: baseUrl + 'deleteStaff',
                    type: 'PUT',
                    data: {body: JSON.stringify(body)}
                }).then((res) => {
                    let responce = JSON.parse(res)

                    if ( responce['status'] === 'success' ) {
                        toastr.success('Успешно премахнахте потребителя!')
                        $('.all_staff #' + responce['staffId']).fadeOut()
                    }else {
                        toastr.error(responce['description'])
                    }
                })
            } else {

            }
        });
    })
}