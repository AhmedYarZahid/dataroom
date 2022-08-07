<?php echo yii2mod\tree\Tree::widget([
    'id' => 'folders-tree',
    'items' => \backend\modules\document\models\Document::getRoomDocumentsTree($detailedRoomModel->roomID, null, Yii::$app->user->can('user'), Yii::$app->user->can('user'), !empty($addDatesInfo)),
    'clientOptions' => [
        'extensions' => !empty($allowManage) ? ['edit', 'dnd'] : [],
        'checkbox' => true,
        'selectMode' => 2,
        'activate' => new \yii\web\JsExpression("
                function(event, data) {
                    var node = data.node;
                    $('#document-parentid').val(node.key);
                }
            "),
        'edit' => [
            'triggerStart' => ["clickActive", "dblclick", "f2", "mac+enter", "shift+click"],
            'beforeEdit' => new \yii\web\JsExpression("
                    function(event, data) {
                        // Return false to prevent edit mode
                    }
                "),
            'edit' => new \yii\web\JsExpression("
                    function(event, data) {
                        // Editor was opened (available as data.input)
                    }
                "),
            'beforeClose' => new \yii\web\JsExpression("
                    function(event, data) {
                        // Return false to prevent cancel/save (data.input is available)
                    }
                "),
            'save' => new \yii\web\JsExpression("
                    function(event, data) {
                        var node = data.node;

                        if (data.isNew) {
                            // Create new folder using data.input.val() or return false to keep editor open
                            $.ajax({
                                url: '" . \yii\helpers\Url::to(['create-document-folder']) . "',
                                data: {
                                    parentID: node.parent.key,
                                    roomID: " . $detailedRoomModel->roomID . ",
                                    title: data.input.val()
                                },
                                method: 'GET'

                            }).done(function(key) {
                                if (!key) {
                                    // Ajax error: remove node
                                    node.remove();
                                } else {
                                    node.key = key;
                                    //node.reRegister(key, key);
                                    //node.setRefKey(key);
                                }

                            }).fail(function(result) {
                                // Ajax error: remove node
                                node.remove();

                            }).always(function() {
                                $(data.node.span).removeClass('pending');
                            });

                            // Optimistically assume that save will succeed. Accept the user input.
                            return true;
                        } else {
                            // Save data.input.val() or return false to keep editor open
                            $.ajax({
                                url: '" . \yii\helpers\Url::to(['update-document-title']) . "',
                                data: {
                                    documentID: node.key,
                                    title: data.input.val()
                                },
                                method: 'GET'

                            }).done(function(result) {
                                if (!result) {
                                    // Ajax error: reset title (and maybe issue a warning)
                                    node.setTitle(data.orgTitle);
                                }

                            }).fail(function(result) {
                                // Ajax error: reset title (and maybe issue a warning)
                                node.setTitle(data.orgTitle);

                            }).always(function() {
                                $(data.node.span).removeClass('pending');
                            });

                            // Optimistically assume that save will succeed. Accept the user input.
                            return true;
                        }
                    }
                "),
            'close' => new \yii\web\JsExpression("
                    function(event, data) {
                        // Editor was closed
                        if (data.save) {
                            // Since we started an async request, mark the node as preliminary
                            $(data.node.span).addClass('pending');
                        }
                    }
                "),
        ],
        'dnd' => [
            'focusOnClick' => true, // Focus, although draggable cancels mousedown event
            'dragStart' => new \yii\web\JsExpression("
                    function(node, data) {
                        return true;
                    }
                "),
            'dragEnter' => new \yii\web\JsExpression("
                    function(node, data) {
                        return true;
                    }
                "),
            'dragDrop' => new \yii\web\JsExpression("
                    function(node, data) {
                        if (!data.otherNode.isFolder() && !$.isNumeric(node.parent.key) && data.hitMode != 'over') {
                            alert('" . Yii::t('admin', 'You can move files only to the folders.') . "');
                            return false;
                        }

                        data.otherNode.moveTo(node, data.hitMode);

                        var tree = $('#folders-tree').fancytree('getTree');
                        var children = tree.getRootNode().children;

                        documentsHierarchy = getDocumentsHierarchy(children);

                        $(data.otherNode.span).addClass('pending');

                        $.ajax({
                            url: '" . \yii\helpers\Url::to(['set-documents-order']) . "',
                            data: {
                                documentsHierarchy: documentsHierarchy
                            },
                            method: 'POST'

                        }).done(function(result) {
                            if (!result) {
                                // Ajax error: show error
                                alert('Error occurred.');
                            }

                        }).fail(function(result) {
                            // Ajax error: show error
                            alert('Error occurred.');

                        }).always(function() {
                            $(data.otherNode.span).removeClass('pending');
                        });

                        /*var picked = (({ key, children }) => ({ key, children }))(children);
                        console.log(picked);*/
                    }
                "),
        ]
    ]
]); ?>

<?php $this->registerJs("
    function getDocumentsHierarchy(tree) {
        var parsedTree = [];
        $.each(tree, function(key, value) {
            parsedTree.push({id: value.key, parentID: value.parent.key, children: getDocumentsHierarchy(value.children)});
        });

        return parsedTree;
    }
    ", \yii\web\View::POS_HEAD); ?>

<?php $this->registerJs("

    $('body').on('click', '#remove-node', function(event) {
        var tree = $('#folders-tree').fancytree('getTree'),
        node = tree.getActiveNode();

        if (!node) {
          alert('" . Yii::t('admin', 'Please choose a folder/document.') . "');
          return;
        } else if (!confirm('" . Yii::t('admin', 'Are you sure you want to remove selected folder/document? All childs will be completely removed also!') . "')) {
            return;
        }
        searchIDs = tree.getSelectedNodes();

        if(searchIDs.length < 1){
            searchIDs.push(node);
        }



        searchIDs.forEach(function(node){

            var children = node.children;
            if (children!==null)node.parent.addChildren(children,node.getNextSibling());

            $.ajax({
                url: '" . \yii\helpers\Url::to(['delete-document']) . "',
                data: {
                    documentID: node.key,
                },
                method: 'GET'

            }).done(function(result) {
                if (!result) {
                    // Ajax error: return to previous state
                    $(data.node.span).removeClass('pending');
                }

            }).fail(function(result) {
                // Ajax error: return to previous state
                $(data.node.span).removeClass('pending');

            }).always(function() {
                node.remove();
            });
        });
    });





    $('body').on('click', '#add-child-folder', function() {
        var node = $('#folders-tree').fancytree('getActiveNode');

        if (!node) {
          alert('" . Yii::t('admin', 'Please choose a parent folder.') . "');
          return;
        } else if (!node.isFolder()) {
            alert('" . Yii::t('admin', 'Please choose a folder (not document).') . "');
            return false;
        }

        node.editCreateNode('child', {
            title: '" . Yii::t('admin', 'New folder') . "',
            folder: true,
            checkbox: false
        });
    });

    $('body').on('click', '#add-sibling-folder', function() {
        var node = $('#folders-tree').fancytree('getActiveNode');

        if (!node) {
          alert('" . Yii::t('admin', 'Please choose a folder/document.') . "');
          return;
        }

        node.editCreateNode('after', {
            title: '" . Yii::t('admin', 'New folder') . "',
            folder: true,
            checkbox: false,
        });
    });

    $('body').on('click', '#download-document', function() {
        var node = $('#folders-tree').fancytree('getActiveNode');

        if (!node) {
          alert('" . Yii::t('admin', 'Please choose a document.') . "');
          return false;
        } else if (node.isFolder()) {
            alert('" . Yii::t('admin', 'Please choose a document (not folder).') . "');
            return false;
        }

        window.open(node.data.downloadLink);
    });

    $('body').on('click', '#download-selected-documents', function() {
        var selNodes = $('#folders-tree').fancytree('getTree').getSelectedNodes();

        if (!selNodes.length) {
          alert('" . Yii::t('admin', 'Please choose a document(s).') . "');
          return false;
        }

        var selKeys = $.map(selNodes, function(node) {
            return node.key;
        });

        var downloadUrl = '" . (Yii::$app->id == 'app-frontend' ? 'dataroom/companies/download-all-documents' : 'dataroom/companies/room/download-all-documents'). "';
        window.open(UrlManager.createUrl(downloadUrl, {roomID: " . $detailedRoomModel->id . ", idList: selKeys}));
    });

    $('body').on('click', '#update-document', function() {
        var node = $('#folders-tree').fancytree('getActiveNode');

        if (!node) {
          alert('" . Yii::t('admin', 'Please choose a document.') . "');
          return false;
        } else if (node.isFolder()) {
            alert('" . Yii::t('admin', 'Please choose a document (not folder).') . "');
            return false;
        }

        window.location = node.data.updateLink;
    });
    $('#btnSelectAll').click(function(){
            $('#folders-tree').fancytree('getTree').visit(function(node){
                node.setSelected(true);
            });
            return false;
        });
    "); ?>