<?php

namespace common\models\content;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Migration;
use yii\web\UploadedFile;

class Image extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public static function tableName()
    {
        $tableName = 'image_file';

        if (!\Yii::$app->db->getTableSchema($tableName))
        {
            $migration = new Migration();
            $migration->createTable($tableName, [
                'id' => $migration->primaryKey(),
                'note' => $migration->string(),
                'file_path' => $migration->string()->notNull(),
                'url_path' => $migration->string()->notNull(),
                'created_at' => $migration->integer()->notNull(),
                'updated_at' => $migration->integer()->notNull(),
            ]);
        }

        return $tableName;
    }

    public function behaviors()
    {
        return [TimestampBehavior::className()];
    }

    public function rules()
    {
        return [
            [['note'], 'string'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'note' => 'Заметка',
            'file_path' => 'Путь к файлу',
            'url_path' => 'Url',
            'created_at' => 'Дата создания',
            'updated_at' => 'Последнее обновление',
        ];
    }

    public function beforeDelete()
    {
        $this->deleteFile();
        return parent::beforeDelete();
    }

    public function upload()
    {
        if ($this->imageFile == null)
            return;

        $time = time();
        $fileName = md5($this->imageFile->baseName.$time).'.'.$this->imageFile->extension;
        $folder = '/var/www/admin/www/stash.madwave.ru/images/200x200/';
        $this->imageFile->saveAs($folder.$fileName);
        $this->file_path = $folder.$fileName;
        $this->url_path = '/images/200x200/'.$fileName;
    }

    public function deleteFile()
    {
        if (is_writable($this->file_path)) {
            unlink($this->file_path);
        }
    }
}