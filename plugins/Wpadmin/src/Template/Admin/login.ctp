<!doctype html>
<html lang="zh"><head>
        <meta charset="utf-8">
        <title><?=getPluginConfig('project.namfe')?>后台管理登录</title>
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link href="/wpadmin/lib/zui/css/zui.min.css" rel="stylesheet">
    </head>
    <body class=" theme-blue">
        <style type="text/css">
            #line-chart {
                height:300px;
                width:800px;
                margin: 0px auto;
                margin-top: 1em;
            }
            .navbar-default .navbar-brand, .navbar-default .navbar-brand:hover { 
                color: #fff;
            }
            .login-box{
                width: 400px;
            }
        </style>
        <div class="navbar navbar-inverse" role="navigation">
            <div class="navbar-header">
                <a class="" href="index.html"><span class="navbar-brand">
                    <span class="fa fa-paper-plane"></span>
                        <?=  templateDefault(getPluginConfig('project.name'),'wpadmin后台管理系统')?>
                    </span>
                </a>
            </div>
            <div class="navbar-collapse collapse" style="height: 1px;">

            </div>
        </div>
    </div>
    <div class="container"> 
        <?= $this->Flash->render() ?>
    </div>
    <div class="login-box center-block">
        <div class="panel panel-default">
            <p class="panel-heading no-collapse">后台登录</p>
            <div class="panel-body">
                <form action="" method="post">
                    <input type="hidden" name="_csrf_token" value="" />
                    <div class="form-group">
                        <label for="username">用户名</label>
                        <input type="text" id="username" class="form-control span12" name="username" value="" required="required" />
                    </div>
                    <div class="form-group">
                        <label for="password">密码</label>
                        <input type="password" id="password" class="form-control span12 form-control" name="password" required="required" />
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">登录</button>
                    <label class="_remember_me"><input type="checkbox" name="_remember_me" value="on">记住我</label>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
        <p class="pull-right" style=""><a href="#" target="blank" style="font-size: .75em; margin-top: .25em;">联系管理员</a></p>
        <p><a href="reset-password.html">忘记密码?</a></p>
    </div>

</body>
</html>
