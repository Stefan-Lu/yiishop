<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m171229_021807_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->notNull()->comment('菜单名称'),
            'parent_id'=>$this->integer()->notNull()->comment('上级菜单'),
            'route'=>$this->string(50)->comment('路由地址'),
            'level'=>$this->smallInteger(1)->notNull()->comment('层级'),
            'sort'=>$this->integer(11)->notNull()->comment('排序'),

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
