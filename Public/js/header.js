$(document).ready(function () {
    showAdminPanel()
})

function showAdminPanel() {
    $('.dropdown p').on('click', function () {
        let div = $('.dropdown div')

        if ( div.hasClass('start_panel') ) {
            $('.dropdown div').removeClass('start_panel')
            $('.dropdown div').addClass('dropdown-content')
        }else {
            $('.dropdown div').addClass('start_panel')
            $('.dropdown div').removeClass('dropdown-content')
        }
    })
}