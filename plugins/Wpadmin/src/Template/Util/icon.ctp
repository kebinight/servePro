<!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- zui -->
        <link href="/wpadmin/lib/zui/css/zui.min.css" rel="stylesheet">
        <style>
            article{
                width: 800px;
                margin: 0px auto auto auto;
            }
            #iconsExample ul {padding: 0;}
            #iconsExample li
            {
                cursor: pointer;
                float: left;
                width: 150px;
                line-height: 25px;
                list-style: none;
                padding: 2px 10px;
                white-space: nowrap;
                transition: all .3s;
            }
            #iconsExample li a {color: #333}
            #iconsExample li a:hover {text-decoration: none}
            #iconsExample li a > i {display: inline-block; width: 20px}
            #iconsExample li:hover {background-color: #d5f1d7; transform: scale(1.2);}

        </style>
    </head>
    <body>
        <article>
            <p>在ZUI中提供了<span class="icons-count">329</span>个图标：</p>
            <div class="example" id="iconsExample"><ul class="clearfix">
                    <?php foreach($iconArr as $key=>$icon):?>
                    <li><a class="icon-pick" href="#" data-val="icon-<?=$key?>"> <i class="icon-<?=$key?>"> <?=$key?></i></a></li>
                    <?php endforeach; ?>
        </article>
        <!-- 在此处挥洒你的创意 -->
        <!-- jQuery (ZUI中的Javascript组件依赖于jQuery) -->
        <script src="/wpadmin/js/jquery.js"></script>
        <!-- ZUI Javascript组件 -->
        <script src="/wpadmin/lib/zui/js/zui.min.plus.js"></script>
        <script src="/wpadmin/lib/layer/layer.js"></script>
        <script>
            $(function () {
                $('.icon-pick').on('click', function () {
                    var path = $(this).data('val');
                    parent.choiceIcon(path);
                });
            });
            //
        </script>
    </body>
</html>