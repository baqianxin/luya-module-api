<?php

use yii\db\Migration;

/**
 * Class m180416_065756_api_auth_user
 */
class m180416_065756_api_auth_user extends Migration
{
    public $userTable = 'api_auth_user';

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
        }
    }
    public function down()
    {
        if ($this->db->schema->getTableSchema($this->userTable, true) !== null) {
            $this->dropTable($this->userTable);
        }
    }
}
