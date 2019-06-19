$(document).ready(() => {
    renderPagination()
    changePage()
})

function renderPagination() {
    let pages      = Number($('.davos_pagination').attr('pages'))
    let activePage = Number($('.davos_pagination').attr('active_page'))

    if ( pages <= 5 ) {
        $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span"><</span></a>')
        for (let i=1;i<=pages;i++) {
            if ( i === activePage ) {
                $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span active_page">' + i + '</span></a>')
            }else {
                $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">' + i + '</span></a>')
            }
        }
        $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">></span></a>')
    }else {
        $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span"><</span></a>')
        if ( activePage === 1 ) {
            $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span active_page">1</span></a>')
        }else {
            $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">1</span></a>')
        }
        if ( activePage - 3 >= 2 ) {
            $('.davos_pagination').append('<span>...</span>')
            $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">' + (activePage - 2) + '</span></a>')
            $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">' + (activePage - 1) + '</span></a>')
            $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span active_page">' + (activePage) + '</span></a>')
        }else {
            if ( activePage === 1 ) {

            }else {
                for (let i = 2; i <= activePage; i++) {
                    if (activePage === i) {
                        $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span active_page">' + i + '</span></a>')
                    } else {
                        $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">' + i + '</span></a>')
                    }
                }
            }
        }

        if ( activePage + 3 < pages ) {
            $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">' + (activePage + 1) + '</span></a>')
            $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">' + (activePage + 2) + '</span></a>')
            $('.davos_pagination').append('<span>...</span>')
        }else {
            if ( activePage !== pages ) {
                for (let i = activePage + 1; i < pages; i++) {
                    $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">' + i + '</span></a>')
                }
            }
        }

        if ( activePage !== pages ) {
            $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">' + pages + '</span></a>')
            $('.davos_pagination').append('<a href="#"><span class="davos_pagination_span">></span></a>')
        }
    }

    changePage()
}

function changePage() {
    $('.davos_pagination_span').on('click', function () {
        let pages        = $('.davos_pagination').attr('pages')
        let selectPage   = $(this).text()

        $('.davos_pagination').remove()

        $('.clients_pagination').append('<div class="davos_pagination" active_page="' + selectPage + '" pages="' + pages + '"></div>')

        renderPagination()
    })
}