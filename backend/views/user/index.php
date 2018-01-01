<?php
/**
 * Created by PhpStorm.
 * User: YFan
 * Date: 2017/12/24
 * Time: 10:33
 */
//用户列表首页
?>

<table class="table table-bordered table-hover">
    <tr>
        <th>编号</th>
        <th>用户名</th>
        <th>头像</th>
        <th>邮箱</th>
        <th>状态</th>
        <th>注册时间</th>
        <th>最后登陆的时间</th>
        <th>最后登陆的ip</th>
        <th>角色</th>
        <th>操作</th>
    </tr>
    <?php foreach ($users as $user):?>
        <tr>
            <td><?=$user->id?></td>
            <td><?=$user->username?></td>
            <td><?= !empty($user->head)?"<img class='img-thumbnail' width='50' height='50' src='$user->head'>":'' ?></td>
            <td><?=$user->email?></td>
            <td><?=$user->status == 1 ? '启用': '禁用'?></td>
            <td><?=isset($user->created_at)?date("Y-m-d H:i:s",$user->created_at):''?></td>
            <td><?=isset($user->last_login_time)?date("Y-m-d H:i:s",$user->last_login_time):''?></td>
            <td><?=$user->last_login_ip?></td>
            <td>
                <?php
                foreach ($user->roles as $role){
                    echo $role.'<br/>';
                }
                ?>
            </td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['user/edit','id'=>$user->id])?>" class="btn btn-primary">修改</a>
                <a href="<?=\yii\helpers\Url::to(['user/delete','id'=>$user->id])?>"class="btn btn-danger" >删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<a href="<?=\yii\helpers\Url::to(['user/add'])?>" class="btn btn-primary">添加</a>
