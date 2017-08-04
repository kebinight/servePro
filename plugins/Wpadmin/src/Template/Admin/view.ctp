<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Admin'), ['action' => 'edit', $admin->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Admin'), ['action' => 'delete', $admin->id], ['confirm' => __('Are you sure you want to delete # {0}?', $admin->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Admin'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Admin'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="admin view large-9 medium-8 columns content">
    <h3><?= h($admin->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Username') ?></th>
            <td><?= h($admin->username) ?></td>
        </tr>
        <tr>
            <th><?= __('Password') ?></th>
            <td><?= h($admin->password) ?></td>
        </tr>
        <tr>
            <th><?= __('Login Ip') ?></th>
            <td><?= h($admin->login_ip) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($admin->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Ctime') ?></th>
            <td><?= h($admin->ctime) ?></td>
        </tr>
        <tr>
            <th><?= __('Utime') ?></th>
            <td><?= h($admin->utime) ?></td>
        </tr>
        <tr>
            <th><?= __('Login Time') ?></th>
            <td><?= h($admin->login_time) ?></td>
        </tr>
        <tr>
            <th><?= __('Enabled') ?></th>
            <td><?= $admin->enabled ? __('Yes') : __('No'); ?></td>
         </tr>
    </table>
</div>
