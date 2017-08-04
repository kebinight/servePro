<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Group'), ['action' => 'edit', $group->Id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Group'), ['action' => 'delete', $group->Id], ['confirm' => __('Are you sure you want to delete # {0}?', $group->Id)]) ?> </li>
        <li><?= $this->Html->link(__('List Group'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Group'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="group view large-9 medium-8 columns content">
    <h3><?= h($group->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Name') ?></th>
            <td><?= h($group->name) ?></td>
        </tr>
        <tr>
            <th><?= __('Remark') ?></th>
            <td><?= h($group->remark) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($group->Id) ?></td>
        </tr>
        <tr>
            <th><?= __('Ctime') ?></th>
            <td><?= h($group->ctime) ?></td>
        </tr>
        <tr>
            <th><?= __('Utime') ?></th>
            <td><?= h($group->utime) ?></td>
        </tr>
    </table>
</div>
