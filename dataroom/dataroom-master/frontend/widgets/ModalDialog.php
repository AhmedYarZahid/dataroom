<?php

namespace frontend\widgets;

use Yii;
use \yii\bootstrap\Modal;
use yii\web\View;

/**
 * Modal Dialog widget give possibility to open modal popup, load content from remote url and allow form submission.
 * You can open modal using javascript as following:
 *
 * ```js
 * var options = {
 *     title: "' . Yii::t('app', 'Login') . '",
 *     body: "' . Yii::t('app', 'Please wait while loading form...') . '",
 *     url: "link-to-get-login-form"
 * };
 *
 * modalDialog.open(options);
 * ```
 *
 * Response from server should be in json and have following format:
 *
 * ```php
 * return [
 *     'status' => 'ok', // or 'ko' to close modal dialog
 *     'content' => $this->renderPartial('login-form', ['model' => $model])
 * ];
 * ```
 *
 * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
 */
class ModalDialog extends \yii\bootstrap\Widget
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Render bootstrap modal popup
        Modal::begin([
            'id' => 'bootstrap-modal-dialog',
            'header' => '<h4 class="modal-title"></h4>',
            'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">' . Yii::t('app', 'Close') . '</a>',
            'size' => Modal::SIZE_DEFAULT
        ]);
        Modal::end();

        // Register JS
        Yii::$app->getView()->registerJs(
            '
            //var modalDialog = {};
            modalDialog = {};

            modalDialog.contentUrl = null;
            modalDialog.closeTimeoutID = null;

            modalDialog.loadContent = function(_url) {
                if (typeof(_url) == "string") modalDialog.contentUrl = _url;

                $.ajax({
                    url: modalDialog.contentUrl,
                    data: $(this).serialize(),
                    type: "POST",
                    dataType: "json",
                    success: function(data) {
                        $("#bootstrap-modal-dialog .modal-body").html(data.content);
                        $("#bootstrap-modal-dialog").modal("show");

                        if (data.status == "ok") {
                            $("#bootstrap-modal-dialog .modal-body form").submit(modalDialog.loadContent);
                        } else {
                            modalDialog.closeTimeoutID = setTimeout(function() {
                                $("#bootstrap-modal-dialog").modal("hide");
                            }, 4000);
                        }
                    }
                });

                return false;
            };

            modalDialog.setOptions = function(options) {
                if (options.title != undefined) {
                    $("#bootstrap-modal-dialog .modal-title").html(options.title);
                }
                if (options.body != undefined) {
                    $("#bootstrap-modal-dialog .modal-body").html(options.body);
                }
                if (options.footer != undefined) {
                    $("#bootstrap-modal-dialog .modal-footer").html(options.footer);
                }

                if (options.onHide != undefined) {
                    $("#bootstrap-modal-dialog").on("hide.bs.modal", function (e) {
                        if (modalDialog.closeTimeoutID != null) {
                            clearTimeout(modalDialog.closeTimeoutID);
                            modalDialog.closeTimeoutID = null;
                        }
                        eval(options.onHide);
                    });
                } else {
                    $("#bootstrap-modal-dialog").on("hide.bs.modal", function (e) {});
                }

                if (options.onShow != undefined) {
                    $("#bootstrap-modal-dialog").on("shown.bs.modal", function (e) {
                        eval(options.onShow);
                    });
                }
            };

            modalDialog.show = function() {
                $("#bootstrap-modal-dialog").modal("show");
            };

            modalDialog.open = function(options) {
                modalDialog.setOptions(options);
                modalDialog.show();

                if (options.url != undefined) {
                    modalDialog.loadContent(options.url);
                }
            };

            $("#bootstrap-modal-dialog").on("shown.bs.modal", function () {
                // Focus on first input element
                $("#bootstrap-modal-dialog .modal-body").find("input[type=text], textarea").first().focus();
            });
        ', View::POS_READY);
    }
}
