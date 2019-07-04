<?php /** @var \App\DTO\ClientDTO $client */ $client = $data['client']; ?>
<section>

    <div class="client_info">
        <div class="client_info_first">
            <div><img src="Public/uploads/basicImages/User.png"></div>
            <p>Име: <?= $client->getFirstName() ?></p>
            <p>Фамилия: <?= $client->getLastName() ?></p>
            <p>Абонамент: <?= $client->getAbonament() ?></p>
            <p>Град: <?= $client->getTown() ?></p>
            <p>Улица: <?= $client->getStreet() ?></p>
            <p>Телефон: <?= $client->getPhone() ?></p>
            <p>Квартал: <?= $client->getNeighborhood() ?></p>
            <p>Имейл: <?= $client->getEmail() ?></p>
            <p>Улица номер: <?= $client->getStreetNumber() ?></p>
            <p>Добавен: <span class="date_register"><?= $client->getRegister() ?></span></p>
            <p>Описание на адреса: <?= $client->getDescription() ?></p>
        </div>
    </div>

    <div class="add_payment">

        <form class="prevent_submit" method="POST">
            <div class="form-style-5">
                <form class="prevent_submit" method="POST">
                    <fieldset class="second_form_fieldset">
                        <div>
                            <div class="bills_form">
                                <h4>Сметки</h4>
                                <?php if ( $data['bills']['delay'] !== 'none' ): ?>
                                    <div class="bill_table_container">
                                        <?php if ( $data['bills']['delay'] === 'no' ): ?>
                                            <p>Платил до <span class="paid_to"><?= $data['bills']['paid'] ?></span></p>
                                        <?php else: ?>
                                            <table class="bills_table">
                                                <thead>
                                                    <tr>
                                                        <th>От</th>
                                                        <th>До</th>
                                                        <th>Цена</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php for ($i = 0;$i < count($data['bills']['bills']); $i++): ?>
                                                        <?php if ( $i === count($data['bills']['bills']) - 1 ): ?>
                                                            <tr class="active">
                                                                <td class="start_payment"><?= $data['bills']['bills'][$i]['start'] ?></td>
                                                                <td class="end_payment last_bill_to" last_bill_to="<?= $data['bills']['bills'][$i]['end'] ?>"><?= $data['bills']['bills'][$i]['end'] ?></td>
                                                                <td><?= $client->getSum() ?>лв.</td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <tr class="active">
                                                                <td class="start_payment"><?= $data['bills']['bills'][$i]['start'] ?></td>
                                                                <td class="end_payment"><?= $data['bills']['bills'][$i]['end'] ?></td>
                                                                <td><?= $client->getSum() ?>лв.</td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3">Цена: <span class="final_price_bill"><?= count($data['bills']['bills']) * $client->getSum() ?></span>лв.</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        <?php endif; ?>

                                        <?php else: ?>
                                        <p>Няма досегашни плащания</p>
                                        <div class="bill_table_container">

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button class="add_bill_button">Добави сметка</button>
                                <input type="hidden" id="bills"
                                       price="<?= $client->getSum() ?>"
                                       lastTime="<?= $data['bills']['lastTime'] ?>"
                                       value="<?= count($data['bills']['bills']) * $client->getSum() ?>">
                            </div>
                    <input type="submit" id="addPayment" name="add_payment" value="Направи плащане" />
                        </div>
                    </fieldset>
                    <input type="hidden" class="csrf_token" id="csrf_token" name="csrf_token" value="<?= $data['csrf_token'] ?>">
                </form>
            </div>
        </form>

    </div>

</section>