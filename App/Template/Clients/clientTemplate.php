<?php /** @var \App\DTO\ClientDTO $client */ $client = $data['client']; /** @var \App\DTO\PaymentDTO $payment */ /** @var \App\DTO\InvoiceDTO $invoice */?>

<section id="body">

    <div class="client-nav">
        <button class="client-info-btn selected-part-view">Инфо</button>
        <button class="client-invoices-btn unselected-part-view">Фактури</button>
<!--        <button class="client-payment-btn unselected-part-view">Плащания</button>-->
        <button class="client-statement-btn unselected-part-view">Състояние на акаунт</button>
    </div>

    <div class="client-data">
        <div class="client_info">
            <div class="client_info_first">
                <div><img src="images/basicImages/User.png"></div>
                <p>Име: <span class="first_name"><?= $client->getFirstName() ?></span></p>
                <p>Фамилия: <span class="last_name"><?= $client->getLastName() ?></span></p>
                <p>Абонамент: <?= $client->getAbonament() ?></p>
                <p>Град: <?= $client->getTown() ?></p>
                <p>Улица: <span class="address"><?= $client->getStreet() ?></span></p>
                <p>Телефон: <?= $client->getPhone() ?></p>
                <p>Квартал: <?= $client->getNeighborhood() ?></p>
                <p>Имейл: <?= $client->getEmail() ?></p>
                <p>Улица номер: <?= $client->getStreetNumber() ?></p>
                <p>Добавен: <span class="date_register has_time"><?= $client->getRegister() ?></span></p>
                <p>Описание на адреса: <?= $client->getDescription() ?></p>
                <p>Псевдоним: <?= $client->getNickname() ?></p>
                <p>Ремарк: <?= $client->getRemark() ?></p>
            </div>
        </div>

        <div class="client_invoices">
            <div id="printing">

            </div>

            <table class="bills_table">

                <thead>
                    <tr>
                        <th>От</th>
                        <th>До</th>
                        <th>Цена</th>
                        <th>Издадена</th>
                        <th>Платена</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($data['invoices'] as $invoice): ?>

                        <?php if ( $invoice->getTimePaid() !== null ): ?>
                            <tr class="active">
                        <?php else: ?>
                            <tr class="ready-payment">
                        <?php endif; ?>

                            <td class="has_time"><?= $invoice->getStart() ?></td>
                            <td class="has_time end_time"><?= $invoice->getEnd() ?></td>
                            <td class="client_invoice_sum"><?= $invoice->getSum() ?></td>
                            <td class="has_time"><?= $invoice->getTime() ?></td>

                            <?php if ( $invoice->getTimePaid() !== null ): ?>
                                <td class="has_time"><?= $invoice->getTimePaid() ?></td>
                                <?php else: ?>
                                <td class="unpaid_invoice">-</td>
                            <?php endif; ?>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

                <tfoot>
                    <tr>

                    </tr>
                </tfoot>

            </table>

            <br />
            <br />

            <button class="add_invoices">Плати фактурите</button>

            <div class="second_level_payment">
                <button class="refuse_invoices">Отказ</button>
                <button class="add_payment">Плащане</button>
            </div>
        </div>

        <div class="client_account_status">
            <div>status</div>
        </div>

    </div>

    <input type="hidden" class="csrf_token" id="csrf_token" name="csrf_token" value="<?= $data['csrf_token'] ?>">
    <input type="hidden" class="last_invoice_time" value="<?= $data['lastInvoice'] ?>">

</section>



