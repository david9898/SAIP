$(document).ready(() => {
    getStreets()
    addClient()
})

function getStreets() {
    $('#town').on('change', function () {
        let townId = $(this).val()
        let csrfToken = $('.csrf_token').val()

        $.ajax({
            url: 'getTownStreets/' + townId + '/' + csrfToken,
            type: 'GET',
        }).then((res) => {
            let responce = JSON.parse(res)

            if ( responce['status'] === 'success' ) {
                let streets       = responce['responce']['streets']
                let neighborhoods = responce['responce']['neighborhoods']

                let streetOptions = {
                    data: streets,
                    getValue: "name",
                    list: {
                        match: {
                            enabled: true
                        }
                    },
                    template: {
                        type: "custom",
                        method: (value, item) => {
                            return "<span><i class=\"fas fa-map-marker-alt\"></i> " + item.name + "</span>"
                        }
                    }
                }

                if ( neighborhoods !== undefined ) {
                    let neighborhoodOptions = {
                        data: neighborhoods,
                        getValue: "name",
                        list: {
                            match: {
                                enabled: true
                            }
                        },
                        template: {
                            type: "custom",
                            method: (value, item) => {
                                return "<span><i class=\"fas fa-map-marked\"></i> " + item.name + "</span>"
                            }
                        }
                    }
                    $('.neighborhood').fadeIn()
                    $('.neighborhood').easyAutocomplete(neighborhoodOptions)
                }else {
                    $('.neighborhood').fadeOut()
                }

                $('.street').easyAutocomplete(streetOptions)
            }else {

            }
        })
    })
}

function addClient() {
    $('.prevent_submit').submit(function (e) {
        e.preventDefault()

        let firstName   = $('.first_name').val()
        let lastName    = $('.last_name').val()
        let phone        = $('.phone').val()
        let email        = $('.email').val()
        let abonament    = $('#abonament').val()
        let town         = $('#town').val()
        let street       = $('.street').val()
        let neighborhood = $('.neighborhood').val()
        let description  = $('.description').val()
        let streetNumber = $('.street_number').val()
        let csrf_token   = $('.csrf_token').val()

        if ( neighborhood === '' ) {
            neighborhood = null
        }

        if ( description === '' ) {
            description = null
        }

        swal({
            title: 'Сигурен ли сте, че искате да добавите този потребител?',
            buttons: true,

        }).then(() => {
            let obj = {
                'first_name': firstName,
                'last_name': lastName,
                'phone': phone,
                'email': email,
                'abonament': abonament,
                'town': town,
                'street': street,
                'neighborhood': neighborhood,
                'description': description,
                'street_number': streetNumber,
                'csrf_token': csrf_token
            }

            $.ajax({
                url: baseUrl + 'addClient',
                type: 'POST',
                data: obj,
            }).then((res) => {
                console.log(res)
                let responce = JSON.parse(res)

                if ( responce['status'] === 'success' ) {
                    window.location.href = baseUrl + 'clients/1'
                }else {
                    toastr.error(responce['description'])
                }
            })
        })
    })
}