<?php /** @var \App\DTO\StaffDTO $customer */  ?>


<section id="staff">
    <div>
        <button class="add_staff_view">Добави</button>
        <button class="all_staff_view">Персонал</button>
    </div>

    <article id="register_staff">
        <form class="prevent_submit" method="POST">
            <div class="form-style-5">
                <form class="prevent_submit" method="POST">
                    <fieldset class="second_form_fieldset">
                        <div>
                            <legend><span class="number">1</span> Добави нов служител </legend>
                            <input type="text" name="first_name" class="first_name" placeholder="Име *">
                            <input type="text" name="last_name" class="last_name" placeholder="Фамилия *">
                            <input type="text" name="phone" class="phone" placeholder="Телефон *">
                            <input type="text" name="username" class="username" placeholder="Потребителско име *">
                            <input type="password" name="password" class="password" placeholder="Парола *">
                            <input type="password" name="repeat_password" class="repeat_password" placeholder="Повтори паролата *">

                            <label for="role">Роля:</label>
                            <select id="role" name="roles[]" class="roles" multiple>
                                <?php foreach ($data['roles'] as $role): ?>
                                    <option value="<?= $role->getId() ?>"><?= $role->getRoleName() ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </fieldset>
                    <input type="hidden" class="csrf_token" name="csrf_token" value="<?= $data['csrf_token'] ?>">
                    <input type="submit" class="add_staff" name="add_staff" value="Добави" />
                </form>
            </div>
        </form>
    </article>

    <article id="all_staff">
        <table border="1" class="all_staff">

            <tr>
                <th>Име</th>
                <th>Потребителско име</th>
                <th>Телефон</th>
                <th>Роли</th>
            </tr>

            <?php foreach ($data['customers'] as $customer): ?>
                <tr id="<?= $customer->getId() ?>">
                    <td><?= $customer->getFirstName() ?></td>
                    <td><?= $customer->getUsername() ?></td>
                    <td><?= $customer->getPhone() ?></td>
                    <td><?= implode(', ', $customer->getRoles()) ?></td>
                    <td><a href="#edit_staff" class="edit_staff" staffId="<?= $customer->getId() ?>">Edit</a></td>
                    <td><button class="delete_staff" staffId="<?= $customer->getId() ?>">Delete</button></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </article>

    <article id="edit_staff" class="white-popup mfp-hide">
        <form class="prevent_submit" method="POST">
            <div class="form-style-5">
                <form class="prevent_submit" method="POST">
                    <fieldset class="second_form_fieldset">
                        <div>
                            <legend><span class="number">1</span> Информация за служител </legend>
                            <input type="text" name="first_name" class="first_name" placeholder="Име *" value="">
                            <input type="text" name="last_name" class="last_name" placeholder="Фамилия *" value="">
                            <input type="text" name="phone" class="phone" placeholder="Телефон *" value="">
                            <input type="text" name="username" class="username" placeholder="Потребителско име *" value="">
                            <input type="password" name="password" class="password" placeholder="Парола *">
                            <input type="password" name="repeat_password" class="repeat_password" placeholder="Повтори паролата *">

                            <label for="role">Роля:</label>
                            <select id="role_edit" class="roles_edit" multiple>
                                <?php foreach ($data['roles'] as $role): ?>
                                    <option value="<?= $role->getId() ?>"><?= $role->getRoleName() ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </fieldset>
                    <input type="hidden" class="csrf_token" name="csrf_token" value="<?= $data['csrf_token'] ?>">
                    <input type="submit" class="update_staff" name="update_staff" value="Промени" />
                </form>
            </div>
        </form>
    </article>

</section>


