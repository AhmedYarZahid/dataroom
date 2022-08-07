<?php

namespace backend\modules\comments\widgets\Comments;

use Yii;
use yii\bootstrap\Widget;
use backend\modules\comments\models\CommentBundle;
use backend\modules\comments\models\Comment;

class Comments extends Widget
{
    /**
     * @var string Type of node to apply comments to
     */
    public $nodeType;

    /**
     * @var int Node ID
     */
    public $nodeID;

    /**
     * @return mixed string|null
     */
    public function run()
    {
        $commentBundle = CommentBundle::find()->where([
            'nodeType' => $this->nodeType,
            'nodeID' => $this->nodeID
        ])->one();

        if (!$commentBundle || !$commentBundle->isActive) {
            return null;
        }

        $comments = Comment::find()->where([
            'commentBundleID' => $commentBundle->id,
            'isApproved' => 1,
        ])->all();

        $newComment = new Comment();
        $newComment->loadDefaultValues();
        $newComment->commentBundleID = $commentBundle->id;

        return $this->render('index', [
            'commentBundle' => $commentBundle,
            'comments' => $comments,
            'newComment' => $newComment,
            'commentsCount' => count($comments),
        ]);
    }
}