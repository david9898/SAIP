<section class="add_abonament_section">

    <form method="POST">
        <div class="form-style-5">

            <form method="POST">
                <fieldset class="second_form_fieldset">

                    <div>
                        <legend><span class="number">1</span> Добави абонамент </legend>
                        <input type="text" name="name" placeholder="Име на абонамента *">
                        <input type="text" name="price" placeholder="Цена *">
                        <textarea name="description" placeholder="Описание" maxlength="255"></textarea>
                    </div>

                </fieldset>
                <input type="hidden" class="csrf_token" name="csrf_token" value="<?= $data['csrf_token'] ?>">
                <input type="submit" class="add_abonament" name="add_abonament" value="Добави" />
            </form>

        </div>
    </form>

</section>