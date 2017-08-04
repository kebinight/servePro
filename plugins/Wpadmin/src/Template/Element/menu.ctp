<?php
$menus = Cake\Cache\Cache::read('admin_menus');
if ($menus === false) {
    $Menu = \Cake\ORM\TableRegistry::get('menu');
    $menus = $Menu->find()->hydrate(false)->where('status = 1')->orderDesc('rank')->toArray();
    $menus = Admin\Utils\Util::getMenu($menus);
    Cake\Cache\Cache::write('admin_menus', $menus);
}
$controller = strtolower($this->request->param('controller'));
$action = strtolower($this->request->param('action'));
$url = '/admin/' . $controller . '/' . $action;
$active = null;
foreach ($menus as $key => $value) {
    if (isset($value['child'])) {
        foreach ($value['child'] as $sub_menu) {
            if ($sub_menu['node'] == $url) {
                break;
            }
            if (isset($sub_menu['child'])) {
                foreach ($sub_menu['child'] as $v) {
                    if ($v['node'] == $url) {
                        $active = $sub_menu['node'];
                    }
                }
            }
        }
    }
}
?>
<nav class="menu" id="left-menu"  data-toggle="menu" >
    <ul class="nav nav-primary">
        <li><a href="/">
                <i class="icon-home"></i> 主面板<i style="float: right"  class="icon-undo"></i></a></li>
        <?php foreach ($menus as $menu): ?>
            <?php if ($menu['pid'] == '0'): ?>
                <li><a href="#"><i class="<?= $menu['class'] ?>"></i><?= $menu['name'] ?></a>
                    <?php if (isset($menu['child'])): ?>
                        <ul class="nav">
                            <?php foreach ($menu['child'] as $sub_menu): ?>
                                <li <?php if ($url == $sub_menu['node']): ?>class="active"
                                    <?php elseif ($active == $sub_menu['node']): ?>class="active"<?php endif; ?>>
                                    <a href="<?= $sub_menu['node'] ?>"><i class="<?= $sub_menu['class'] ?>">
                                        </i><?= $sub_menu['name'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
<!--        <li>
            <a href=""><i class="icon-tasks"></i> 消息中心
                <span class="label label-badge label-danger pull-right">2</span>
            </a>
        </li>-->
    </ul>
</nav>