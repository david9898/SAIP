<?php
    /** @var $client \App\DTO\ClientDTO */
?>

<section>

    <div class="clients_navigation">
        <div class="box">
            <a href="client/add" class="btn btn-white btn-animation-1">Добави клиент <i class="fas fa-user-plus"></i></a>
        </div>
    </div>

    <div class="search_clients">
        <div class="col-auto">
            <label class="sr-only" for="inlineFormInputGroup">Search</label>
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-search"></i></div>
                </div>
                <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Търси">
            </div>
        </div>
    </div>

    <div id="clients_list">

        <table class="table-fill">
            <thead>
            <tr>
                <th class="text-left">Име</th>
                <th class="text-left">Фамилия</th>
                <th class="text-left">Град</th>
                <th class="text-left">Улица</th>
                <th class="text-left">Платежност</th>
                <th class="text-left">Абонамент</th>
            </tr>
            </thead>
            <tbody class="table-hover">
            </tbody>
        </table>

    </div>
    <input type="hidden" value="<?= $data['csrf_token'] ?>" id="csrf_token">

</section>