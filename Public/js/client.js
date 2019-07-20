window.onload = function () {
    moment.locale('bg')

    sessionStorage.setItem('client-part', 'info')

    addNextPayment()
    addPayment()
    makePaymentsToDates()
    chooseBill()
    billTo()
    updateStartData()
    showPart()
    changePartView()
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

        calculateTime()
    })
}

function calculateTime() {
    let activeBills = $('.ready-payment').toArray().length

    chooseBills(activeBills)

    document.getElementById('bills').value = activeBills

}

async function addNextPayment() {
    let billTemplate       = await $.get(baseUrl + 'Public/templatesHbs/addBillTemplate.hbs')

    $('.add_bill_button').on('click', function (e) {
        e.preventDefault()

        let lastTime     = $('.last_bill_to').attr('last_bill_to')

        if ( lastTime === undefined ) {
            lastTime  = $('#bills').attr('lastTime')
            let price = $('#bills').attr('price')

            renderBill(billTemplate, lastTime, price)

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
                lastTime = new Date() / 1000

                renderBill(billTemplate, lastTime, price)
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

        if ( !$(currentBill).hasClass('active') ) {
            $(currentBill).addClass('ready-payment')
        }
    }

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

function showPart() {
    let part = sessionStorage.getItem('client-part')

    if ( part === 'info' ) {
        $('.add_payment').css('display', 'none')
        $('.client_info').css('display', 'block')
    }

    if ( part === 'payments' ) {
        $('.client_info').css('display', 'none')
        $('.add_payment').css('display', 'block')
    }
}

function changePartView() {
    $('.client-info-btn').on('click', function () {
        sessionStorage.setItem('client-part', 'info')

        $(this).removeClass('unselected-part-view')
        $(this).addClass('selected-part-view')
        $('.client-payment-btn').removeClass('selected-part-view')
        $('.client-payment-btn').addClass('unselected-part-view')

        showPart()
    })

    $('.client-payment-btn').on('click', function () {
        sessionStorage.setItem('client-part', 'payments')

        $(this).removeClass('unselected-part-view')
        $(this).addClass('selected-part-view')
        $('.client-info-btn').removeClass('selected-part-view')
        $('.client-info-btn').addClass('unselected-part-view')

        showPart()
    })
}