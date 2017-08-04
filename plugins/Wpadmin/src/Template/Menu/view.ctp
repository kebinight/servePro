<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Menu'), ['action' => 'edit', $menu->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Menu'), ['action' => 'delete', $menu->id], ['confirm' => __('Are you sure you want to delete # {0}?', $menu->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Menu'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Menu'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="menu view large-9 medium-8 columns content">
    <h3><?= h($menu->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Name') ?></th>
            <td><?= h($menu->name) ?></td>
        </tr>
        <tr>
            <th><?= __('Node') ?></th>
            <td><?= h($menu->node) ?></td>
        </tr>
        <tr>
            <th><?= __('Class') ?></th>
            <td><?= h($menu->class) ?></td>
        </tr>
        <tr>
            <th><?= __('Remark') ?></th>
            <td><?= h($menu->remark) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($menu->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Pid') ?></th>
            <td><?= $this->Number->format($menu->pid) ?></td>
        </tr>
        <tr>
            <th><?= __('Rank') ?></th>
            <td><?= $this->Number->format($menu->rank) ?></td>
        </tr>
        <tr>
            <th><?= __('Is Menu') ?></th>
            <td><?= $menu->is_menu ? __('Yes') : __('No'); ?></td>
         </tr>
        <tr>
            <th><?= __('Status') ?></th>
            <td><?= $menu->status ? __('Yes') : __('No'); ?></td>
         </tr>
    </table>
</div>
