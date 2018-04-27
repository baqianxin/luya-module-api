<?php

use yii\db\Migration;

/**
 * Class m180416_065756_api_auth_user
 */
class m180416_065756_admin_user_api_auth extends Migration
{
    public $userTable = 'admin_user_api_auth';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        // Check if the table exists
        if ($this->db->schema->getTableSchema($this->userTable, true) === null) {
            $this->createTable($this->userTable, [
                'id' => $this->primaryKey(),
                'admin_id' => $this->integer()->unique()->notNull(),
                'email' => $this->string()->notNull(),
                'username' => $this->string(32)->notNull(),
                'app_key' => $this->string(32)->notNull(),
                'api_token' => $this->string()->notNull(),
                'app_secret' => $this->string()->notNull(),
                'app_secret_reset_token' => $this->string(),
                'allowance'=>$this->integer(11)->defaultValue(0)->comment('剩余的允许的请求数量'),
                'allowance_updated_at'=>$this->integer(11)->defaultValue(0)->comment('有效期UNIX时间戳数'),
                'status' => $this->smallInteger()->notNull()->defaultValue(10),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
            ], $tableOptions);
            /**
                参数1：设置本表的外键名（可自定义）。
                参数2：本表表名。
                参数3：本表中与外表关联的字段名称。如果有多个字段，以逗号分隔或使用一个数组。
                参数4：外表表名。
                参数5：外表中与本表关联的字段名称。如果有多个字段，以逗号分隔或使用一个数组。
                参数6：删除选项，可选，默认为空。可选的类型有 RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL。
                参数7：更新选项，可选，默认为空。可选的类型有 RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL。
                注意：参数3和参数5的字段类型必须一致，否则执行失败。
             */
            $this->addForeignKey('admin_id_f_key',$this->userTable,'admin_id','admin_user','id');
        }
    }
    public function down()
    {
        if ($this->db->schema->getTableSchema($this->userTable, true) !== null) {
            $this->dropTable($this->userTable);
        }
    }
}
