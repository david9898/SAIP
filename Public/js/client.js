window.onload = function () {
    chooseBills()
    addPayment()
}

function chooseBills() {
    document.getElementById('bills').addEventListener('input', function (e) {
        let price      = e.target.getAttribute('price')
        let bills      = e.target.value
        let finalPrice = price * bills

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