<head>
    <base href="/">
    <meta charset="UTF-8">
    <script src="node_modules/jquery/dist/jquery.js"></script>
    <script src="node_modules/easy-autocomplete/dist/jquery.easy-autocomplete.min.js"></script>
    <link rel="stylesheet" href="node_modules/easy-autocomplete/dist/easy-autocomplete.min.css">
</head>

<?php /** @var \App\DTO\OldDTO $oldClient */ $oldClient = $data['oldClient'] ?>
<form method="POST">

    <div>
        Street: <input type="text" name="street" class="street" required/>
    </div>

    <div>
        First Name: <input type="text" name="first_name"  />
    </div>

    <div>
        Last Name: <input type="text" name="last_name" />
    </div>

    <div>
        Phone: <input type="text" name="phone" required value="<?= $oldClient->getPhone() ?>" />
    </div>

    <div>
        Street Number: <input type="text" name="street_number">
    </div>

    <div>
        Remark: <input type="text" name="remark" value="<?= $oldClient->getRemark() ?>" />
    </div>

    <div>
        Nickname: <input type="text" name="nickname" value="<?= $oldClient->getName() ?>">
    </div>

    <div>
        ClientIp: <input type="text" name="client_ip" value="<?= $oldClient->getClientIp() ?>">
    </div>

    <div>
        Description: <input type="text" name="description" >
    </div>

    <div>
        <input type="hidden" name="abonament" value="12">
    </div>

    <div>
        <input type="hidden" name="town" value="1">
    </div>

    <div>
        <input type="hidden" name="csrf_token" class="csrf_token" value="<?= $data['csrf_token'] ?>">
    </div>

    <input type="submit" name="add_new_client">
</form>

<div>
    <p>Street: <?= $oldClient->getStreet() ?></p>
    <p>Name: <?= $oldClient->getName() ?></p>
    <p>Phone: <?= $oldClient->getPhone() ?></p>
    <p>Notes: <?= $oldClient->getNotes() ?></p>
    <p>Remark: <?= $oldClient->getRemark() ?></p>
    <p>LastInvoicePaid: <span class="a"><?= $oldClient->getLastInvoicePaid() ?></span></p>
    <p>StopService: <span class="b"><?= $oldClient->getStopService() ?></span></p>
    <p>StopService2: <?= $oldClient->getStopService2() ?></p>
    <p>Progress: <?= $oldClient->getProgress() ?></p>
    <p>ClientIp: <?= $oldClient->getClientIp() ?></p>
    <p>Disabled: <?= $oldClient->getDisabled() ?></p>
</div>

<script>
    $(document).ready(() => {
        let csrfToken = $('.csrf_token').val()

        if ( $('.a').text() === '' ) {
            return
        }

        if ( $('.b').text() === '' ) {
            return
        }

        $.ajax({
            url: 'getTownStreets/' + 1 + '/' + csrfToken,
            type: 'GET',
        }).then((res) => {
            let responce = JSON.parse(res)

            if (responce['status'] === 'success') {
                let streets = responce['responce']['streets']

                let streetOptions = {
                    data: streets,
                    getValue: "name",
                    list: {
                        match: {
                            enabled: true
                        }
                    },
                    template: {
                        type: "custom",
                        method: (value, item) => {
                            return "<span>" + item.name + "</span>"
                        }
                    }
                }

                $('.street').easyAutocomplete(streetOptions)
            }
        })

    })
</script>

