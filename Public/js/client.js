window.onload = function () {
    moment.locale('bg')
    chooseBills()
    addPayment()
}

function chooseBills() {
    document.getElementById('bills').addEventListener('input', function (e) {
        let price      = e.target.getAttribute('price')
        let bills      = e.target.value
        let lastTime   = e.target.getAttribute('lastTime')
        let finalPrice = price * bills

        calculateNextPayment(lastTime, bills)

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
            
            if (diffTime > 2635200 * bills) {
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