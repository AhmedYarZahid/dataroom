<?php

namespace common\models;

use common\helpers\BrowserHelper;
use common\helpers\ExecEnvironmentHelper;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "UserHistory".
 *
 * @property integer $id
 * @property integer $userID
 * @property integer $event
 * @property integer $entityID
 * @property integer $ip
 * @property string $browser
 * @property string $platform
 * @property integer $isLastAction
 * @property integer $isPiggybackLogin
 * @property string $createdDate
 *
 * @property User $user
 */
class UserHistory extends ActiveRecord
{
    const EVENT_LOGIN = 1;
    const EVENT_LOGOUT = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'UserHistory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userID', 'ip'], 'required'],
            [['userID', 'event', 'entityID', 'ip', 'isLastAction', 'isPiggybackLogin'], 'integer'],
            [['createdDate'], 'safe'],
            [['browser', 'platform'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userID' => Yii::t('app', 'User ID'),
            'event' => Yii::t('app', 'Event'),
            'entityID' => Yii::t('app', 'Entity ID'),
            'ip' => Yii::t('app', 'Ip'),
            'browser' => Yii::t('app', 'Browser'),
            'platform' => Yii::t('app', 'Platform'),
            'isLastLogin' => Yii::t('app', 'Is Last Login'),
            'isLastLogout' => Yii::t('app', 'Is Last Logout'),
            'isPiggybackLogin' => Yii::t('app', 'Is Piggyback Login'),
            'createdDate' => Yii::t('app', 'Created Date'),
            'eventLabel' => Yii::t('app', 'Event'),
            'standartIp' => Yii::t('app', 'Ip'),
        ];
    }

    /**
     * Add record to history
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $event
     * @param integer $userID
     * @param integer $entityID
     * @return bool
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public static function addHistoryRecord($event, $userID, $entityID = null)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            Yii::$app->db->createCommand()->update(self::tableName(), ['isLastAction' => 0], ['event' => $event, 'entityID' => $entityID])->execute();

            $model = new UserHistory();
            $browser = new BrowserHelper();

            $model->event = $event;
            $model->userID = $userID;
            $model->entityID = $entityID;
            $model->ip = ExecEnvironmentHelper::getUserIp(true);
            $model->browser = $browser->getBrowser() . ' ' . $browser->getVersion();
            $model->platform = $browser->getPlatform();
            $model->isLastAction = 1;
            $model->isPiggybackLogin = 0;
            $model->createdDate = date('Y-m-d H:i:s');

            if (!$model->save()) {
                throw new Exception(Yii::t('app', "Can't add record to history."));
            }

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollBack();

            throw new Exception(Yii::t('app', "Can't add record to history."));
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userID']);
    }

    public function getEventLabel()
    {
        $labels = [
            self::EVENT_LOGIN => Yii::t('app', 'Login'),
            self::EVENT_LOGOUT => Yii::t('app', 'Logout'),
        ];

        return isset($labels[$this->event]) ? $labels[$this->event] : null;
    }

    public function getStandartIp()
    {
        return long2ip($this->ip);
    }
}
