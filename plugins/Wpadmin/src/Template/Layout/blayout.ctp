<!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= templateDefault(getPluginConfig('project.name') . '后台管理系统', 'wpadmin后台管理系统') ?></title>
        <!-- zui -->
        <link href="/wpadmin/lib/zui/css/zui.min.css" rel="stylesheet">
        <link href="/wpadmin/lib/datetimepicker/jquery.datetimepicker.css" rel="stylesheet">
        <link href="/wpadmin/css/base.css" rel="stylesheet">
        <?= $this->fetch('static') ?>
    </head>
    <body>
        <!-- header -->
        <div id="main-content">
            <div id="page-content">
                <div class="page-main" style="margin-top: 10px;">
                    <?= $this->fetch('content') ?>
                </div>
            </div>
        </div>

        <!-- 在此处挥洒你的创意 -->
        <!-- jQuery (ZUI中的Javascript组件依赖于jQuery) -->
        <script src="/wpadmin/js/jquery.js"></script>
        <!-- ZUI Javascript组件 -->
        <script src="/wpadmin/lib/zui/js/zui.min.js"></script>
        <script src="/wpadmin/lib/datetimepicker/jquery.datetimepicker.js"></script>
        <script src="/wpadmin/lib/layer/layer.js"></script>
        <script src="/wpadmin/lib/layer/extend/layer.ext.js"></script>
        <script src="/wpadmin/js/global.js"></script>
        <script>
            $(function () {
                $('#left-bar').add('#main-content').height($(window).height() - $('header').height());
                $(window).bind('resize', function () {
                    $('#left-bar').add('#main-content').height($(window).height() - $('header').height());
                });
                $('.header-tooltip').tooltip();
                $('#left-menu ul.nav-primary ul.nav li.active').parents('li').addClass('active show');
                $('#left-menu ul.nav-primary ul.nav li.active').parents('li').find('i.icon-chevron-right').addClass('icon-rotate-90');
                $('#switch-left-bar').on('click', function () {
                    $('#left-bar').toggleClass('hide');
                    var width = 200;
                    if ($('#left-bar').hasClass('hide')) {
                        width = 0;
                    }
//                    $('#main-content').width($(window).width() - width);
                });

                $('.img-thumbnail').each(function () {
                    if ($(this).find('img').attr('src')) {
                        $(this).removeClass('input-img');
                    }
                });
            });
        </script>
        <?= $this->fetch('script') ?>
    </body>
</html>