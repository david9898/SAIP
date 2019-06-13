<?php
/**
 * @var $town      \App\DTO\TownDTO
 */

/**
 * @var $abonament \App\DTO\AbonamentDTO
 */
?>
<section>

    <form>
        <div class="form-style-5">
            <form>
                <fieldset class="second_form_fieldset">
                    <div>
                        <legend><span class="number">1</span> Информация за клиент</legend>
                        <input type="text" name="field1" placeholder="Име *">
                        <input type="text" name="field1" placeholder="Фамилия *">
                        <input type="text" name="field1" placeholder="Телефон *">
                        <input type="email" name="field2" placeholder="Имейл *">

                        <label for="abonament">Абонамент:</label>
                        <select id="abonament" name="field4">
                            <option selected value="none">-</option>
                            <?php foreach ( $data['abonaments'] as $abonament ): ?>
                                <option value="<?= $abonament->getId() ?>"><?= $abonament->getName() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="town">Град:</label>
                        <select id="town" name="field4">
                            <option selected value="none">-</option>
                            <?php foreach ( $data['towns'] as $town ): ?>
                                <option value="<?= $town->getId() ?>"><?= $town->getName() ?></option>
                            <?php endforeach; ?>
                        </select>

                        <input type="text" name="field2" placeholder="Улица *">

                        <input type="text" name="field2" placeholder="Квартал">

                        <textarea name="field3" placeholder="Описание на адреса *"></textarea>
                    </div>
                </fieldset>
                <input type="hidden" class="csrf_token" name="csrf_token" value="<?= $data['csrf_token'] ?>">
                <input type="submit" value="Добави" />
            </form>
        </div>
    </form>

</section>