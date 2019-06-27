<?php
/**
 * @var $town      \App\DTO\TownDTO
 */

/**
 * @var $abonament \App\DTO\AbonamentDTO
 */
?>
<section id="client_basic">

    <form class="prevent_submit">
        <div class="form-style-5">
            <form class="prevent_submit">
                <fieldset class="second_form_fieldset">
                    <div>
                        <legend><span class="number">1</span> Информация за клиент</legend>
                        <input type="text" class="first_name" placeholder="Име *">
                        <input type="text" class="last_name" placeholder="Фамилия *">
                        <input type="text" class="phone" placeholder="Телефон *">
                        <input type="email" class="email" placeholder="Имейл *">

                        <label for="abonament">Абонамент:</label>
                        <select id="abonament">
                            <option selected value="none">-</option>
                            <?php foreach ( $data['abonaments'] as $abonament ): ?>
                                <option value="<?= $abonament->getId() ?>"><?= $abonament->getName() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="town">Град:</label>
                        <select id="town">
                            <option selected value="none">-</option>
                            <?php foreach ( $data['towns'] as $town ): ?>
                                <option value="<?= $town->getId() ?>"><?= $town->getName() ?></option>
                            <?php endforeach; ?>
                        </select>

                        <input type="text" class="street" placeholder="Улица *">

                        <input type="text" class="street_number" placeholder="Номер улица *">

                        <input type="text" class="neighborhood" placeholder="Квартал *">

                        <textarea placeholder="Описание на адреса" class="description"></textarea>
                    </div>
                </fieldset>
                <input type="hidden" class="csrf_token" name="csrf_token" value="<?= $data['csrf_token'] ?>">
                <input type="submit" class="add_client" value="Добави" />
            </form>
        </div>
    </form>

</section>