window.onload = function () {
    moment.locale('bg')
    chooseBills()
    addNextPayment()
    addPayment()
    makePaymentsToDates()
    chooseBill()
}

function chooseBills() {
        $('#bills').on('input', function () {

            let price      = document.getElementById('bills').getAttribute('price')
            let lastTime   = document.getElementById('bills').getAttribute('lastTime')
            let bills      = $('#bills').val()
            let finalPrice = price * Number(bills)

            calculateNextPayment(lastTime, Number(bills))

            $('.final_price_bill').text('')
            $('.final_price_bill').text(finalPrice)
        })
}

function addPayment() {
    document.getElementById('addPayment').addEventListener('click', function (e) {
        e.preventDefault()
        let pathArr = window.location.pathname.split('/')
        let client  = pathArr[pathArr.length - 1]

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
            console.log(res)
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
        if ( $(this).hasClass('active') ) {
            let isChange = false
            $(this).removeClass('active')

            for (let i = 0; i < bills.length; i++) {
                let currentBill = bills[i]

                if ( isChange ) {
                    $(currentBill).removeClass('active')
                }
                if ( $(this).is(currentBill) ) {
                    isChange = true
                }
            }

            calculateTime()

        }else {
            let isChange = false
            $(this).addClass('active')

            for (let i = bills.length - 1; i >= 0; i--) {
                let currentBill = bills[i]

                if ( isChange ) {
                    $(currentBill).addClass('active')
                }
                if ( $(this).is(currentBill) ) {
                    isChange = true
                }
            }

            calculateTime()
        }
    })
}

function calculateTime() {
    let activeBills = $('.active').toArray().length

    chooseBills(activeBills)
}

async function addNextPayment() {
    let billTemplate = await $.get(baseUrl + 'Public/templatesHbs/addBillTemplate.hbs')

    $('.add_bill_button').on('click', function (e) {
        e.preventDefault()

        let lastTime     = Number($('.last_bill_to').attr('last_bill_to'))

        $('.last_bill_to').removeClass('last_bill_to')
        let currentTime  = Math.round(new Date() / 1000)
        let price        = $('#bills').attr('price')

        if ( Number(lastTime) - Number(currentTime) >= 0 ) {

            renderBill(billTemplate, lastTime, price)
            chooseBill()
            calculateTime()

        }else {
            if ( currentTime - lastTime >= 7905600 ) {

                renderBill(billTemplate, currentTime, price)
                chooseBill()
                calculateTime()

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
}