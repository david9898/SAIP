window.onload = function () {
    moment.locale('bg')
    addNextPayment()
    addPayment()
    makePaymentsToDates()
    chooseBill()
    billTo()
    updateStartData()
}

function chooseBills(bills) {
        let price      = document.getElementById('bills').getAttribute('price')
        let lastTime   = document.getElementById('bills').getAttribute('lastTime')
        let finalPrice = price * Number(bills)

        calculateNextPayment(lastTime, Number(bills))

        $('.final_price_bill').text('')
        $('.final_price_bill').text(finalPrice)
}

function addPayment() {
    document.getElementById('addPayment').addEventListener('click', function (e) {
        e.preventDefault()

        swal({
            text: 'Сигурен ли сте че искате да направите плащането?',
            buttons: ['Не', 'Да']
        }).then((willDelete) => {

            if ( willDelete ) {
                if ( document.getElementById('bills').value <= 0 ) {
                    toastr.error('Трябва да изберете поне една сметка')
                    return
                }

                let pathArr = window.location.pathname.split('/')
                let client = pathArr[pathArr.length - 1]

                let body = {
                    'client': client,
                    'bills': document.getElementById('bills').value,
                    'csrf_token': document.getElementById('csrf_token').value
                }

                $.ajax({
                    type: 'POST',
                    url: baseUrl + 'addPayment',
                    data: JSON.stringify(body)
                }).then((res) => {
                    let responce = JSON.parse(res)

                    if ( responce['status'] === 'success' ) {
                        toastr.success('Успешно направено плащане')
                    }else {
                        toastr.error(responce['description'])
                    }
                })
            }
        })
    })
}

function calculateNextPayment(lastTime, bills) {
    let currentTime = Math.round(new Date() / 1000)

    if ( lastTime === 'none' ) {
        lastTime = currentTime
    }

    let diffTime    = null
    let nextPayment = null
    let stopPayment = null

    if ( Number(lastTime) >= currentTime ) {
        diffTime = lastTime - currentTime

        nextPayment = ((2635200 * bills) + diffTime) + Number(currentTime)
        stopPayment = (((2635200 * bills) + diffTime) + Number(currentTime)) + 7905600

        $('.next_payment').remove()
        $('.stop_payment').remove()
        $('.bills_form').append('<p class="next_payment">Следваща сметка на ' + moment(nextPayment * 1000).format('LL') + ' </p>')
        $('.bills_form').append('<p class="stop_payment">Ще бъде спрян на ' + moment(stopPayment * 1000).format('LL') + ' </p>')
    }else {
        diffTime = Math.abs(currentTime - lastTime)

        if ( diffTime <= 7905600 ) {
            if (diffTime > 2635200 * bills) {
                let diffInPayments = Math.abs(diffTime - (2635200 * bills))
                let diffBills = Math.abs(currentTime - (Number(lastTime) + diffInPayments))
                stopPayment = Math.abs(diffBills + 7905600 + Number(lastTime))

                $('.next_payment').remove()
                $('.stop_payment').remove()
                $('.bills_form').append('<p class="stop_payment">Ще бъде спрян на ' + moment(stopPayment * 1000).format('LL') + ' </p>')
            } else {
                let diffToPayment = currentTime - Number(lastTime)
                let billsTime = Math.abs(bills * 2635200)
                let residue = billsTime - diffToPayment
                nextPayment = residue + currentTime
                stopPayment = residue + currentTime + 7905600

                $('.next_payment').remove()
                $('.stop_payment').remove()
                $('.bills_form').append('<p class="next_payment">Следваща сметка на ' + moment(nextPayment * 1000).format('LL') + ' </p>')
                $('.bills_form').append('<p class="stop_payment">Ще бъде спрян на ' + moment(stopPayment * 1000).format('LL') + ' </p>')
            }
        }else {
            let add = diffTime - 7905600

            if (diffTime > (2635200 * bills + add)) {
                let diffInPayments = Math.abs(diffTime - (2635200 * bills))
                let diffBills = Math.abs(currentTime - (Number(lastTime) + diffInPayments))
                stopPayment = Math.abs(diffBills + 7905600 + Number(lastTime))

                $('.next_payment').remove()
                $('.stop_payment').remove()
                $('.bills_form').append('<p class="stop_payment">Ще бъде спрян на ' + moment((stopPayment + add) * 1000).format('LL') + ' </p>')
            } else {
                let diffToPayment = currentTime - Number(lastTime)
                let billsTime = Math.abs(bills * 2635200)
                let residue = billsTime - diffToPayment
                nextPayment = residue + currentTime
                stopPayment = residue + currentTime + 7905600

                $('.next_payment').remove()
                $('.stop_payment').remove()
                $('.bills_form').append('<p class="next_payment">Следваща сметка на ' + moment((nextPayment + add) * 1000).format('LL') + ' </p>')
                $('.bills_form').append('<p class="stop_payment">Ще бъде спрян на ' + moment((stopPayment + add) * 1000).format('LL') + ' </p>')
            }
        }
    }
}

function makePaymentsToDates() {
    let starts = $('.start_payment').toArray()
    let ends   = $('.end_payment').toArray()

    for (let pay of starts) {
        let time = $(pay).text()
        $(pay).text(moment(time * 1000).format('LL'))
    }

    for (let pay of ends) {
        let time = $(pay).text()
        $(pay).text(moment(time * 1000).format('LL'))
    }
}

function chooseBill() {
    let bills = $('.bills_table tbody tr').toArray()

    $('.bills_table tbody tr').on('click', function () {
        for (let i = 0; i < bills.length; i++) {
            let currentBill = bills[i]
            $(currentBill).removeClass('selected')
            $(currentBill).removeClass('active')
        }

        $(this).addClass('selected')

        for (let i = 0; i < bills.length; i++) {
            let currentBill = bills[i]

            $(currentBill).addClass('active')

            if ( $(currentBill).hasClass('selected') ) {
                $(currentBill).addClass('active')
                break
            }
        }

        calculateTime()
    })
}

function calculateTime() {
    let activeBills = $('.active').toArray().length

    chooseBills(activeBills)

    document.getElementById('bills').value = activeBills

}

async function addNextPayment() {
    let billTemplate       = await $.get(baseUrl + 'Public/templatesHbs/addBillTemplate.hbs')
    let billsTableTemplate = await $.get(baseUrl + 'Public/templatesHbs/billsTableTemplate.hbs')

    $('.add_bill_button').on('click', function (e) {
        e.preventDefault()

        let lastTime     = $('.last_bill_to').attr('last_bill_to')

        if ( lastTime === undefined ) {
            lastTime  = $('#bills').attr('lastTime')
            let price = $('#bills').attr('price')

            renderBillsTable(billsTableTemplate, lastTime, price)

            return
        }else {
            lastTime = Number(lastTime)
        }
        $('.last_bill_to').removeClass('last_bill_to')
        let currentTime  = Math.round(new Date() / 1000)
        let price        = $('#bills').attr('price')

        if ( Number(lastTime) - Number(currentTime) >= 0 ) {

            renderBill(billTemplate, lastTime, price)

        }else {
            if ( currentTime - lastTime >= 7905600 ) {

                renderBill(billTemplate, currentTime, price)

            }else {
                let nextFrom = lastTime
                let nextTo   = lastTime + 2635200

                $('.bills_table tbody').append('<tr>' +
                    '<td>' + moment(nextFrom * 1000).format('LL') + '</td>' +
                    '<td>' + moment(nextTo * 1000).format('LL') + '</td>' +
                    '<td>' + price + '</td>' +
                    '</tr>')
            }
        }
    })
}

function renderBill(billTemplate, lastTime, price) {
    lastTime = Number(lastTime)

    let obj = {
        'start': moment(lastTime * 1000).format('LL'),
        'end': moment(lastTime * 1000).add(2635200, 'seconds').format('LL'),
        'sum': price,
        'endSeconds': lastTime + 2635200
    }

    let template = Handlebars.compile(billTemplate)
    let html     = template(obj)
    $('.bills_table tbody').append(html)

    let arr = $('.bills_table tbody tr').toArray()

    for (let i = 0; i < arr.length; i++) {
        let currentBill = arr[i]

        $(currentBill).addClass('active')
    }

    chooseBill()
    calculateTime()
}

function renderBillsTable(billTableTemplate, lastTime, price) {
    lastTime = Number(lastTime)

    let obj = {
        'start': moment(lastTime * 1000).format('LL'),
        'end': moment(lastTime * 1000).add(2635200, 'seconds').format('LL'),
        'endSeconds': lastTime + 2635200,
        'sum': price
    }

    let template = Handlebars.compile(billTableTemplate)
    let html     = template(obj)
    $('.bill_table_container').append(html)

    chooseBill()
    calculateTime()
}

function billTo() {
    let paidToSeconds = Number($('.paid_to').text())

    $('.paid_to').text(moment(paidToSeconds * 1000).format('LL'))
}

function updateStartData() {
    let register    = $('.date_register').text()
    let currentTime = Math.abs(new Date() / 1000)
    let lastTime    = document.getElementById('bills').getAttribute('lastTime')

    $('.date_register').text(moment(register * 1000).format('LL'))

    if ( lastTime > currentTime ) {
        $('.client_info_first').append('<p>Статус: Платил</p>')
    }else {
        if ( currentTime - lastTime >= 7905600 ) {
            $('.client_info_first').append('<p>Статус: Спрян</p>')
        }else {
            $('.client_info_first').append('<p>Статус: Просрочен</p>')
        }
    }
}