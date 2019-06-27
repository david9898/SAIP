<section id="client_basic">

    <form class="prevent_submit" method="POST">
        <div class="form-style-5">
            <form class="prevent_submit" method="POST">
                <fieldset class="second_form_fieldset">
                    <div>
                        <legend><span class="number">1</span> Добави нов служител </legend>
                        <input type="text" name="first_name" placeholder="Име *">
                        <input type="text" name="last_name" placeholder="Фамилия *">
                        <input type="text" name="phone" placeholder="Телефон *">
                        <input type="text" name="username" placeholder="Потребителско име *">
                        <input type="password" name="password" placeholder="Парола *">
                        <input type="password" name="repeat_password" placeholder="Повтори паролата *">

                        <label for="role">Роля:</label>
                        <select id="role" name="role">
                            <option selected value="none">-</option>
                            <option value="1">Служител</option>
                            <option value="2">Админ</option>
                        </select>
                    </div>
                </fieldset>
                <input type="hidden" class="csrf_token" name="csrf_token" value="<?= $data['csrf_token'] ?>">
                <input type="submit" class="add_staff" name="add_staff" value="Добави" />
            </form>
        </div>
    </form>

</section>