$(document).ready(async () => {
    sessionStorage.setItem('listClients', 20)

    let clientTemplate = await $.get(baseUrl + 'templates/clientTemplate.hbs')
    onScroll(clientTemplate)
    searchFriends(clientTemplate)
    seeClient()
})

let isActiveEvent = true

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

                let xhr = new XMLHttpRequest()
                xhr.onreadystatechange = function () {
                    if ( xhr.readyState === XMLHttpRequest.DONE ) {
                        let responce = (JSON.parse(xhr.responseText))

                        if ( responce['status'] === 'success' ) {

                            if ( responce['clients'] !== undefined ) {
                                for (let client of responce['clients']) {
                                    let template = Handlebars.compile(clientTemplate)
                                    if ( client['paid'] !== null ) {
                                        client['paid'] = Math.floor((client['paid'] - (new Date() / 1000)) / 86400)

                                        if ( client['paid'] <= -91 ) {
                                            client['payment'] = 'delay'
                                            client['paid'] = -91
                                        }else if ( client['paid'] > -91 && client['paid'] < 0 ) {
                                            client['payment'] = 'overdue'
                                        }else if ( client['paid'] > 0 ) {
                                            client['payment'] = 'paid'
                                        }
                                    }
                                    let html = template(client)
                                    $('.table-hover').append(html)
                                }

                                let newList = Number(sessionStorage.getItem('listClients')) + 20

                                sessionStorage.setItem('listClients', newList)

                                if (responce['clients'].length === 20) {
                                    isActiveEvent = true
                                }
                                seeClient()
                            }
                        }else {
                            toastr.error(responce['description'])
                        }
                    }
                }

                if ( searchText !== '' ) {
                    xhr.open('GET', baseUrl + 'searchFriends/' + csrfToken + '/' + firstRes + '/' + searchText)
                }else {
                    xhr.open('GET', baseUrl + 'getMoreClients/' + csrfToken + '/' + firstRes)
                }
                xhr.send(null)

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

        let xhr = new XMLHttpRequest()
        xhr.onreadystatechange = function () {
            if ( xhr.readyState === XMLHttpRequest.DONE ) {
                let responce = JSON.parse(xhr.responseText)

                if ( responce['status'] === 'success' ) {
                    if ( responce['clients'] !== undefined ) {
                        $('.table-hover').empty()
                        for (let client of responce['clients']) {
                            let template = Handlebars.compile(clientTemplate)
                            let html = template(client)
                            $('.table-hover').append(html)
                        }
                        seeClient()
                    }else {
                        $('.table-hover').empty()
                        $('.table-hover').append('<div>Няма намерени резултати</div>')
                    }
                }else {
                    toastr.error(responce['description'])
                }
            }
        }

        if ( pattern !== '' ) {
            xhr.open('GET', baseUrl + 'searchFriends/' + csrfToken + '/' + firstResult + '/' + pattern)
        }else {
            xhr.open('GET', baseUrl + 'searchFriends/' + csrfToken + '/' + firstResult)
        }
        xhr.send(null)

    })
}

function seeClient() {
    $('.see_client').on('click', function () {
        window.location.href = baseUrl + 'client/' + $(this).attr('client')
    })
}