<nav class="menu" id="left-menu"  data-toggle="menu" >
    <ul class="nav nav-primary">
        <li><a href="<?=PROJ_PREFIX?>/index/index">
                <i class="icon-home"></i> 主面板<i style="float: right"  class="icon-undo"></i></a></li>
        <?php foreach ($menus as $menu): ?>
            <?php if ($menu['pid'] == '0' && $menu['is_menu'] == '1'): ?>
                <li><a href="#"><i class="<?= $menu['class'] ?>"></i><?= $menu['name'] ?></a>
                    <?php if (isset($menu['children'])): ?>
                        <ul class="nav">
                            <?php foreach ($menu['children'] as $sub_menu): ?>
                                <?php if ($sub_menu['is_menu'] == '1'): ?>
                                    <li <?php if ($url == $sub_menu['node']): ?>class="active"
                                                                                <?php elseif ($active == $sub_menu['node']): ?>class="active"<?php endif; ?>>
                                        <a href="<?= $sub_menu['node'] ?>"><i class="<?= $sub_menu['class'] ?>">
                                            </i><?= $sub_menu['name'] ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
<!--        <li>
            <?//= $this->cell('Wpadmin.inbox::menu') ?>
        </li>-->
    </ul>
</nav>