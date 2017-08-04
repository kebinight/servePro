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
        <header>
            <nav class="navbar navbar-inverse " role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">
                        <?= templateDefault(getPluginConfig('project.name'), 'wpadmin') ?>后台管理系统
                    </a>
                </div>
                <div class="collapse navbar-collapse navbar-collapse-example">
                    <ul class="nav navbar-nav navbar-avatar navbar-right">
                        <!--                        <li>
                                                    <?//= $this->cell('Wpadmin.Inbox') ?>
                                                </li>-->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <?= $this->request->session()->read('User.admin.username'); ?>
                                <span class="thumb-small avatar inline">
                                    <?php if (!empty($this->request->session()->read('User.admin.avatar'))): ?>
                                        <?php $avatar = $this->request->session()->read('User.admin.avatar'); ?>
                                    <?php else: ?>
                                        <?php $avatar = '/wpadmin/img/avatar/avatar.jpg'; ?>
                                    <?php endif; ?>
                                    <img src="<?= $avatar; ?>" alt="Mika Sokeil" class="img-circle">
                                </span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/admin/admin/profile">修改个人信息</a></li>
                                <li class="divider"></li>
                                <li><a href="/wpadmin/admin/logout"><i class="icon icon-off"></i> 注销</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </nav>
        </header>
        <div id="left-bar" style="width: 200px">
            <?= $this->cell('Wpadmin.Menu') ?>
        </div>
        <div id="main-content">
            <div id="breadcrumb">
                <ul class="breadcrumb">
                    <a id="switch-left-bar" style=" margin-right: 10px; cursor:pointer;"><i class="icon-bars"></i></a>
                    <li>
                        <a href="<?= PROJ_PREFIX ?>/index/index"><i class="icon icon-home"></i> 主页</a>
                    </li>
                    <li>
                        <a  href="#"><?= $bread['first']['name'] ?></a>
                    </li>
                    <li>
                        <a href="#"><?= $bread['second']['name'] ?></a>
                    </li>
                </ul>
            </div>
            <div id="page-content">
                <div class="page-header">
                    <h1><?= $pageTitle ?></h1>
                </div>
                <div class="page-main" style="margin-top: 10px;">
                    <?php if (isset($NO_PERMISSION)): ?>
                        <?= $this->Flash->render('acl') ?>
                    <?php else: ?>
                        <?= $this->fetch('content') ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <audio id="bgMusic" preload="auto">
            <source src="/wpadmin/audio/didong.mp3" type="audio/mp3" />
        </audio>

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
            var audio = document.getElementById("bgMusic");
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

                /*var res = '{"code":200,"status":true,"msg":"","data":{"notices":[{"id":1,"user_name":"聊天男1","user_id":381,"notice":"反馈意见","url":"","status":3,"create_time":"2017-07-14 14:27:07","update_time":"2017-07-14 14:27:16"}]}}';
                var resjson = JSON.parse(res);
                if(resjson.status) {
                }*/
                checkNotice();
                setInterval(function() {
                    //轮询检查
                    checkNotice();
                }, 30000);
            });

            function checkNotice() {
                $.ajax({
                    url: '/index/checkAdminNotice',
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if(res.status) {
                            audio.play();
                            var notices = res.data.notices;
                            $.each(notices, function(key, value) {
                                var msg = '<span style="color: blue">' + value.user_name + '</span>进行操作：' + value.notice;
                                layer.confirm(msg, {
                                    area: ['400px', '100px'],
                                    title: '消息通知',
                                    shade: [0],
                                    offset: 'rb',
                                    btn: []
                                });
                            });
                        }
                    }
                });
            }
        </script>
        <?= $this->fetch('script') ?>
    </body>
</html>