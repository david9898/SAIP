<section class="add_street">

    <form method="POST">
        <div class="form-style-5">

            <form method="POST">
                <fieldset class="second_form_fieldset">

                    <div>
                        <legend><span class="number">1</span> Добави улица </legend>
                        <input type="text" name="name" placeholder="Име на Улицата *">
                        <label for="town">Град:</label>
                        <select name="townId">
                            <option selected value="none">-</option>
                            <?php foreach ( $data['towns'] as $town ): ?>
                                <option value="<?= $town->getId() ?>"><?= $town->getName() ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </fieldset>
                <input type="hidden" class="csrf_token" name="csrf_token" value="<?= $data['csrf_token'] ?>">
                <input type="submit" class="add_street" name="add_street" value="Добави" />
            </form>

        </div>
    </form>

</section>