window.onload = function () {
    moment.locale('bg')

    sessionStorage.setItem('client-part', 'info')

    addNextPayment()
    addPayment()
    makeSecondsToDates()
    chooseBill()
    showPart()
    changePartView()
    payInvoices()

}

async function addNextPayment() {
    let invoiceTemplate        = await $.get('templates/addBillTemplate.hbs')
    let lastInvoiceTime        = $('.last_invoice_time').val().split('-')
    let sum                    = $('.client_invoice_sum').toArray()
    let realSum                = $(sum[sum.length - 1]).text()

    $('.add_invoice_btn').on('click', function () {
        let year        = null
        let month       = null

        if ( lastInvoiceTime[1] < 12 ) {
            year  = lastInvoiceTime[0]
            month = Number(lastInvoiceTime[1])
        }else {
            year  = Number(lastInvoiceTime[0]) + 1
            month = 0
        }

        let nextInvoiceDateDaysInMonth = new Date(year, Number(month) + 1, 0).getDate()
        let start                      = new Date(year, month, 1)
        let end                        = new Date(year, month, nextInvoiceDateDaysInMonth)

        let obj = {
            'start': moment(start.getTime()).format('LL'),
            'end': moment(end.getTime()).format('LL'),
            'sum': realSum,
            'time': moment(new Date().getTime()).format('LL')
        }

        let template = Handlebars.compile(invoiceTemplate)
        let html     = template(obj)
        $('.bills_table tbody').append(html)

        if ( lastInvoiceTime[1] < 11 ) {
            year  = lastInvoiceTime[0]
            month = Number(lastInvoiceTime[1]) + 1
        }else {
            year  = Number(lastInvoiceTime[0]) + 1
            month = 0
        }

        lastInvoiceTime[0] = year
        lastInvoiceTime[1] = month

        chooseBill()
        makeInvoicesReady()
    })
}

function makeInvoicesReady() {
    let invoices = $('.bills_table tbody tr').toArray()

    for (let i = 0; i < invoices.length; i++) {
        let currentInvoice = invoices[i]

        $(currentInvoice).removeClass('selected')

        if ( !$(currentInvoice).hasClass('active') ) {
            $(currentInvoice).addClass('ready-payment')
        }
    }
}

function addPayment() {
    $('.add_payment').on('click', function () {
        let numInvoices = $('.ready-payment').toArray().length
        let csrfToken = $('#csrf_token').val()
        let pathArr = window.location.pathname.split('/')
        let client = pathArr[pathArr.length - 1]

        let body = {
            'numInvoices': numInvoices,
            'clientId': client,
            'csrf_token': csrfToken
        }

        $.ajax({
            type: 'POST',
            url: baseUrl + 'payInvoices',
            data: {body: JSON.stringify(body)}
        }).then((res) => {
            let responce = JSON.parse(res)

            if (responce['status'] === 'success') {
                $('.ready-payment .unpaid_invoice').text(moment(responce['time'] * 1000).format('LL'))

                let newInvoices = $('.ready-payment').toArray()
                for (let i = 0; i < newInvoices.length; i++) {
                    let currInvoice = newInvoices[i]

                    $(currInvoice).removeClass('ready-payment')
                    $(currInvoice).addClass('active')
                }

                $('#printing').printThis({
                    afterPrint: function () {
                        toastr.success('Успешно платихте фактурата')
                        $('.second_level_payment').css('display', 'none')
                        $('.add_payment').css('display', 'block')
                        $('.bills_table').css('display', 'block')
                        $('.add_invoices').css('display', 'block')
                        $('#printing').empty()
                        $('#printing').css('display', 'none')
                    }
                })
            } else {
                toastr.error(responce['description'])
            }
        })

    })
}



function makeSecondsToDates() {
    let seconds = $('.has_time').toArray()

    for (let pay of seconds) {

        let time = $(pay).text()

        if ( time === '' ) {
            $(pay).text('-')
            continue
        }

        $(pay).text(moment(time * 1000).format('LL'))
    }

}

function chooseBill() {
    let bills = $('.bills_table tbody .ready-payment').toArray()

    $('.bills_table tbody .ready-payment').on('click', function () {
        for (let i = 0; i < bills.length; i++) {
            let currentBill = bills[i]
            $(currentBill).removeClass('selected')
            $(currentBill).removeClass('ready-payment')
        }

        $(this).addClass('selected')

        for (let i = 0; i < bills.length; i++) {
            let currentBill = bills[i]

            $(currentBill).addClass('ready-payment')

            if ( $(currentBill).hasClass('selected') ) {
                $(currentBill).addClass('ready-payment')
                break
            }
        }
    })
}

function showPart() {
    let part = sessionStorage.getItem('client-part')

    if ( part === 'info' ) {
        $('.client_account_status').css('display', 'none')
        $('.client_invoices').css('display', 'none')
        $('.client_payments').css('display', 'none')
        $('.client_info').css('display', 'block')

        return
    }

    if ( part === 'payments' ) {
        $('.client_account_status').css('display', 'none')
        $('.client_invoices').css('display', 'none')
        $('.client_info').css('display', 'none')
        $('.client_payments').css('display', 'block')

        return
    }

    if ( part === 'invoices' ) {
        $('.client_account_status').css('display', 'none')
        $('.client_info').css('display', 'none')
        $('.client_payments').css('display', 'none')
        $('.client_invoices').css('display', 'block')

        return
    }

    if ( part === 'statement' ) {
        $('.client_info').css('display', 'none')
        $('.client_payments').css('display', 'none')
        $('.client_invoices').css('display', 'none')
        $('.client_account_status').css('display', 'block')

        return
    }
}

function changePartView() {
    $('.client-info-btn').on('click', function () {
        sessionStorage.setItem('client-part', 'info')

        $(this).removeClass('unselected-part-view')
        $(this).addClass('selected-part-view')
        $('.client-invoices-btn').removeClass('selected-part-view')
        $('.client-invoices-btn').addClass('unselected-part-view')
        $('.client-payment-btn').removeClass('selected-part-view')
        $('.client-payment-btn').addClass('unselected-part-view')
        $('.client-statement-btn').removeClass('selected-part-view')
        $('.client-statement-btn').addClass('unselected-part-view')

        showPart()
    })

    $('.client-payment-btn').on('click', function () {
        sessionStorage.setItem('client-part', 'payments')

        $(this).removeClass('unselected-part-view')
        $(this).addClass('selected-part-view')
        $('.client-info-btn').removeClass('selected-part-view')
        $('.client-info-btn').addClass('unselected-part-view')
        $('.client-invoices-btn').removeClass('selected-part-view')
        $('.client-invoices-btn').addClass('unselected-part-view')
        $('.client-statement-btn').removeClass('selected-part-view')
        $('.client-statement-btn').addClass('unselected-part-view')

        showPart()
    })

    $('.client-invoices-btn').on('click', function () {
        sessionStorage.setItem('client-part', 'invoices')

        $(this).removeClass('unselected-part-view')
        $(this).addClass('selected-part-view')
        $('.client-info-btn').removeClass('selected-part-view')
        $('.client-info-btn').addClass('unselected-part-view')
        $('.client-payment-btn').removeClass('selected-part-view')
        $('.client-payment-btn').addClass('unselected-part-view')
        $('.client-statement-btn').removeClass('selected-part-view')
        $('.client-statement-btn').addClass('unselected-part-view')

        showPart()
    })

    $('.client-statement-btn').on('click', function () {
        sessionStorage.setItem('client-part', 'statement')

        $(this).removeClass('unselected-part-view')
        $(this).addClass('selected-part-view')
        $('.client-info-btn').removeClass('selected-part-view')
        $('.client-info-btn').addClass('unselected-part-view')
        $('.client-payment-btn').removeClass('selected-part-view')
        $('.client-payment-btn').addClass('unselected-part-view')
        $('.client-invoices-btn').removeClass('selected-part-view')
        $('.client-invoices-btn').addClass('unselected-part-view')

        showPart()
    })
}

async function payInvoices() {
    let printTemplate = await $.get('templates/printTemplate.hbs')

    $('.add_invoices').on('click', function () {
        $('.client_invoices .bills_table').css('display', 'none')
        $('.client_invoices #printing').css('dispplay', 'block')
        let name         = $('.first_name').text() + ' ' + $('.last_name').text()
        let price        = $('.client_invoice_sum').toArray()[0].textContent
        let timePayment  = moment(new Date().getTime()).format('LL')
        let readyPayment = $('.ready-payment').toArray()[$('.ready-payment').toArray().length - 1]
        let endPayment   = $(readyPayment).children('.end_time').text()
        let address      = $('.address').text()
        let numInvoices  = $('.ready-payment').toArray().length

        let data = {
            'name': name,
            'price': Number(price) * Number(numInvoices),
            'timePayment': timePayment,
            'timeToEnd': endPayment,
            'address': address
        }

        $('#printing').css('display', 'block')

        $(this).css('display', 'none')
        $('.second_level_payment').css('display', 'flex')

        $('.refuse_invoices').on('click', function () {
            $('.second_level_payment').css('display', 'none')
            $('#printing').css('display', 'none')
            $('.add_payment').css('display', 'block')
            $('.bills_table').css('display', 'block')
            $('.add_invoices').css('display', 'block')
            $('#printing').empty()
        })

        let template = Handlebars.compile(printTemplate)
        let html     = template(data)
        $('#printing').append(html)
    })

}

