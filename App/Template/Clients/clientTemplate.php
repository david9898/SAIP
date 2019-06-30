<?php /** @var \App\DTO\ClientDTO $client */ $client = $data['client'];?>
<section>

    <div class="client_info">
        <div>
            <p>Име: <?= $client->getFirstName() ?></p>
            <p>Фамилия: <?= $client->getLastName() ?></p>
            <p>Абонамент: <?= $client->getAbonament() ?></p>
            <p>Град: <?= $client->getTown() ?></p>
            <p>Улица: <?= $client->getStreet() ?></p>
            <p>Телефон: <?= $client->getPhone() ?></p>
            <p>Квартал: <?= $client->getNeighborhood() ?></p>
            <p>Имейл: <?= $client->getEmail() ?></p>
            <p>Име: <?= $client->getStreetNumber() ?></p>
            <p>Добавен: <?= $client->getDateRegister() ?></p>
            <p>Описание на адреса: <?= $client->getDescription() ?></p>
        </div>
    </div>

    <div class="add_payment">

        <form class="prevent_submit" method="POST">
            <div class="form-style-5">
                <form class="prevent_submit" method="POST">
                    <fieldset class="second_form_fieldset">
                        <div>
                            <legend><span class="number">1</span> Направи плащане на клиента </legend>

                            <div class="bills_form">
                                <h4>Сметки</h4>
                                <?php if ( $data['bills']['delay'] !== 'none' ): ?>
                                    <?php if ( $data['bills']['delay'] === 'no' ): ?>
                                        <p>Платил до <?= $data['bills']['paid'] ?></p>
                                    <?php else: ?>
                                        <?php foreach ($data['bills']['bills'] as $item): ?>
                                            <p>start: <?= $item['start'] ?> ::::::: end: <?= $item['end'] ?></p>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php else: ?>
                                    <p>Няма досегашни плащания</p>
                                <?php endif; ?>
                                <h6>Изберете колко сметки иска да плати клиента:</h6>
                                <input type="number" id="bills" price="<?= $client->getSum() ?>" lastTime="<?= $data['bills']['lastTime'] ?>">

                                <p>Цена: <span class="final_price_bill"><?= $client->getSum() ?></span>лв.</p>
                            </div>
                        </div>
                    </fieldset>
                    <input type="hidden" class="csrf_token" id="csrf_token" name="csrf_token" value="<?= $data['csrf_token'] ?>">
                    <input type="submit" id="addPayment" name="add_payment" value="Добави" />
                </form>
            </div>
        </form>

    </div>

</section>