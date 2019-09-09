$(document).ready(async () => {
    sessionStorage.setItem('listClients', 20)

    let clientTemplate = await $.get(baseUrl + 'templates/clientTemplate.hbs')
    loadFirstClients(clientTemplate)
    onScroll(clientTemplate)
    searchFriends(clientTemplate)
    seeClient()
})

let isActiveEvent = true

function loadFirstClients(clientTemplate) {
    let firstResult = 0
    let csrfToken   = $('#csrf_token').val()

    $.ajax({
        url: baseUrl + 'getClients/' + csrfToken + '/' + firstResult ,
        type: 'GET',
    }).then((res) => {
        let responce = JSON.parse(res)

        if ( responce['status'] === 'success' ) {
            for (let client of responce['clients']) {
                let template = Handlebars.compile(clientTemplate)
                let html = template(client)
                $('.table-hover').append(html)
            }
        }else {
            toastr.error(responce['description'])
        }

        seeClient()
    })
}

function onScroll(clientTemplate) {

    window.addEventListener('scroll', function () {
        if ( isActiveEvent ) {
            let scroll      = window.scrollY
            let innerHeight = window.innerHeight
            let allHeight   = document.body.offsetHeight
            let csrfToken   = document.getElementById('csrf_token').value
            let searchText  = document.getElementById('inlineFormInputGroup').value
            let firstRes    = sessionStorage.getItem('listClients')

            if ( scroll + innerHeight >= allHeight - 400 ) {
                isActiveEvent = false
                let url = ''

                if ( searchText !== '' ) {
                    url = baseUrl + 'searchFriends/' + csrfToken + '/' + firstRes + '/' + searchText
                }else {
                    url = baseUrl + 'getClients/' + csrfToken + '/' + firstRes
                }

                $.ajax({
                    type: 'GET',
                    url: url
                }).then((res) => {
                    let responce = JSON.parse(res)

                    if ( responce['status'] === 'success' ) {

                        for (let client of responce['clients']) {
                            let template = Handlebars.compile(clientTemplate)
                            let html = template(client)
                            $('.table-hover').append(html)
                        }

                        let newList = Number(sessionStorage.getItem('listClients')) + 20

                        sessionStorage.setItem('listClients', newList)


                        if ( responce['clients'].length === 20 ) {
                            isActiveEvent = true
                        }

                        seeClient()

                        return;
                    }else {
                        toastr.error(responce['description'])

                        return;
                    }
                })
            }
        }else {
            return
        }
    })
}

function searchFriends(clientTemplate) {
    let input = document.getElementById('inlineFormInputGroup')

    input.addEventListener('input', function (e) {
        isActiveEvent   = true
        sessionStorage.setItem('listClients', 20)
        let pattern     = e.target.value
        let csrfToken   = document.getElementById('csrf_token').value
        let firstResult = 0
        let url = ''

        if ( pattern !== '' ) {
            url = baseUrl + 'searchFriends/' + csrfToken + '/' + firstResult + '/' + pattern
        }else {
            url = baseUrl + 'searchFriends/' + csrfToken + '/' + firstResult
        }

        $.ajax({
            type: 'GET',
            url: url
        }).then((res) => {
            let responce = JSON.parse(res)

            if ( responce['status'] === 'success' ) {
                if ( responce['clients'] !== undefined && responce['clients'].length !== 0 ) {
                    $('.table-hover').empty()

                    for (let client of responce['clients']) {
                        let template = Handlebars.compile(clientTemplate)
                        let html = template(client)
                        $('.table-hover').append(html)
                    }

                    seeClient()

                    return
                }else {
                    $('.table-hover').empty()
                    $('.table-hover').append('<div>Няма намерени резултати</div>')
                    return;
                }
            }else {
                toastr.error(responce['description'])

                return;
            }
        })
    })
}

function seeClient() {
    $('.see_client').on('click', function () {
        window.location.href = baseUrl + 'client/' + $(this).attr('client')
    })
}
