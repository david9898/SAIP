

    <?php /** @var \App\DTO\OldDTO $oldClient */ ?>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <table border="1">
        <tr>
            <th>id</th>
            <th>street</th>
            <th>name</th>
            <th>phone</th>
            <th>notes</th>
            <th>remark</th>
            <th>lastInvoicePaid</th>
            <th>stopService</th>
            <th>stopService2</th>
            <th>progress</th>
            <th>clientIp</th>
            <th>disabled</th>
        </tr>

        <?php foreach ($data['oldClients'] as $oldClient): ?>
            <tr>
                <td><?= $oldClient->getId() ?></td>
                <td><?= $oldClient->getStreet() ?></td>
                <td><?= $oldClient->getName() ?></td>
                <td><?= $oldClient->getPhone() ?></td>
                <td><?= $oldClient->getNotes() ?></td>
                <td><?= $oldClient->getRemark() ?></td>
                <td><?= $oldClient->getLastInvoicePaid() ?></td>
                <td><?= $oldClient->getStopService() ?></td>
                <td><?= $oldClient->getStopService2() ?></td>
                <td><?= $oldClient->getProgress() ?></td>
                <td><?= $oldClient->getClientIp() ?></td>
                <td><?= $oldClient->getDisabled() ?></td>
                <td><a href="editOldClient/<?= $oldClient->getId() ?>">Edit</a> </td>
            </tr>
        <?php endforeach; ?>
    </table>

