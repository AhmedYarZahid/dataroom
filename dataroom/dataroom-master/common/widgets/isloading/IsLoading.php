<?php
namespace common\widgets\isloading;

use yii\base\Widget;
use yii\helpers\Json;

class IsLoading extends Widget
{

    public $text = 'O sistema está processando...';
    public $position = 'overlay'; // right | inside | overlay
    public $class = 'fa fa-refresh';
    public $tpl = '<span class=\"isloading-wrapper %wrapper%\">%text% <i class=\"%class% fa-spin\"></i></span>';
    public $disableSource = true;
    public $disableOthers = [];

    public $timeout = false;
    public $time = 1000;

    /**
     * @inheritdoc
     */
    public function run()
    {

        $options = Json::encode($this->disableOthers);

        if ($this->timeout) {
            $jsStop = 'setTimeout(function(){
    					$.isLoading("hide");
    					$(".isloading-overlay").remove();
    				}, ' . $this->time . ');';
        } else {
            $jsStop = '$.isLoading("hide");
    					$(".isloading-overlay").remove();';
        }

        $js = '
		    $(document).ajaxStart(function() {    	    	
		    	if(!$(".isloading-overlay").is(":hidden")){
		    		$.isLoading({
		    			"class": "' . $this->class . '",
		    			"text": "' . $this->text . '",
		    			"position": "' . $this->position . '",
		    			"tpl": "' . $this->tpl . '",
		    			"disableSource": ' . $this->disableSource . ',
		    			"disableOthers": ' . $options . ',
		    		});
		    	}
		    })
		    .ajaxStop(function() {
		    	if($(".isloading-overlay").is(":visible")){
		    		' . $jsStop . '
		    	}
		    });';

        $this->view->registerJs($js, \yii\web\View::POS_HEAD);

        $this->registerAssets();
    }


    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $view = $this->getView();
        IsLoadingAsset::register($view);
        IsLoadingAjaxStatusAsset::register($view);

    }
}