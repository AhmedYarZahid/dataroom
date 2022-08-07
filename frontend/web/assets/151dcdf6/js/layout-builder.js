if (!window.can) {
    alert('An error occurred while loading, please try again later or contact administrator.');
    throw new Error("Couldn't find CanJS library, cannot proceed");
}

var can = window.can; // just to pacify IDE
var layoutBuilder = new can.Map();

layoutBuilder.config = new can.Map({
    translations: {}
});
layoutBuilder.globalState = new can.Map({
    isCollapsed: false
});
layoutBuilder.faIcons = new can.List(fontAwesomeIconsList);
layoutBuilder.getRandomIcon = function() {
    return this.faIcons[Math.floor(Math.random() * this.faIcons.length)]
};

layoutBuilder.attr('state', 'init');

layoutBuilder.attr('layoutsOrder', [
    'one-tenth',
    'one-sixth',
    'one-fifth',
    'one-fourth',
    'one-third',
    'two-fifths',
    'one-half',
    'three-fifths',
    'two-thirds',
    'three-fourths',
    'four-fifths',
    'full-width'
]);

layoutBuilder.attr('menu', new can.Map());
layoutBuilder.attr('sectionBlocks', new can.List());
layoutBuilder.attr('undoStack', new can.List());
layoutBuilder.attr('redoStack', new can.List());

layoutBuilder.menu.attr('layoutBlocks', [
    { type: 'section', title: 'Section', shortTitle: 'Section' },
    { type: 'full-width', title: 'Full width column', shortTitle: '1/1' },
    { type: 'one-half', title: 'Half width column', shortTitle: '1/2' },
    { type: 'one-third', title: '33% width column', shortTitle: '1/3' },
    { type: 'one-fourth', title: '25% width column', shortTitle: '1/4' },
    { type: 'two-thirds', title: '66% width column', shortTitle: '2/3' },
    { type: 'three-fourths', title: '75% width column', shortTitle: '3/4' },
    { type: 'one-fifth', title: '20% width column', shortTitle: '1/5' },
    { type: 'two-fifths', title: '40% width column', shortTitle: '2/5' },
    { type: 'three-fifths', title: '60% width column', shortTitle: '3/5' },
    { type: 'four-fifths', title: '80% width column', shortTitle: '4/5' },
    { type: 'one-sixth', title: '16% width column', shortTitle: '1/6' },
    { type: 'one-tenth', title: '10% width column', shortTitle: '1/10' }
]);

layoutBuilder.menu.attr('contentBlocks', [
    { type: 'text-block', title: 'Text Block', shortTitle: 'Text' },
    { type: 'icon-list', title: 'Icon List', shortTitle: 'Icon List' },
    { type: 'separator', title: 'Separator', shortTitle: 'Separator' },
    { type: 'icon-box', title: 'Icon Box', shortTitle: 'Icon Box' },
    { type: 'button', title: 'Button', shortTitle: 'Button' },
    { type: 'table', title: 'Table', shortTitle: 'Table' },
    { type: 'image', title: 'Image', shortTitle: 'Image' },
    { type: 'video', title: 'Video', shortTitle: 'Video' },
    { type: 'slideshow', title: 'Slider', shortTitle: 'Slider' },
    { type: 'team-member', title: 'Team Member', shortTitle: 'Team Member' },
    { type: 'dynamic-block', title: 'Dynamic block', shortTitle: 'Dynamic' }
]);

layoutBuilder.menu.attr('codeBlocks', [
    { type: 'css', title: 'CSS', shortTitle: 'CSS' },
    { type: 'js', title: 'Javascript', shortTitle: 'JS' }
]);

layoutBuilder.menu.getBlockByType = function(type) {
    var found = null;

    this.layoutBlocks.forEach(function(i) {
        if (i.type === type) {
            found = i;
        }
    });

    if (!found) {
        this.contentBlocks.forEach(function(i) {
            if (i.type === type) {
                found = i;
            }
        });
    }

    if (!found) {
        this.codeBlocks.forEach(function(i) {
            if (i.type === type) {
                found = i;
            }
        });
    }

    return found;
};

layoutBuilder.Block = can.Construct.extend({
    init: function(blockData) {
        var data = blockData.meta || blockData;

        this.id = layoutBuilder.getNewId();

        this.state = new can.Map({
            isCollapsed: false
        });
        this.tmp = new can.Map({});
        this.meta = new can.Map({});

        // Try to translate titles on change
        this.meta.bind('change', function(e, attr, how, value, oldValue) {
            var attrsToBeTranslated = ['title', 'shortTitle'],
                translatedValue;

            if (attrsToBeTranslated.indexOf(attr) === -1) {
                return;
            }

            if (layoutBuilder.hasTranslation(value)) {
                translatedValue = layoutBuilder.t(value);
                // Check if there's no cross-connection to avoid infinite loop
                if (!layoutBuilder.hasTranslation(translatedValue)) {
                    this.attr(attr, translatedValue);
                } else {
                    console.warn('There is a cross-connection between translations of', value, 'and', translatedValue,
                        '\nPotentially can cause infinite loop therefore the translation cancelled');
                }
            }
        });

        // Init meta attributes
        if (data instanceof can.Map) {
            this.meta.attr(data.attr());
        } else {
            this.meta.attr(data);
        }
    },
    hook: function(hookName) {
        var that = this;

        return $.Deferred(function() {
            if (typeof that[hookName] === 'function') {
                that[hookName].apply(that);
            }

            this.resolve();
        });
    },
    edit: function() {
        new layoutBuilder.Editor(this);
    },
    save: function() {
        // Should be implemented if needed
    },
    getData: function() {
        return this.meta.attr();
    },
    toggleCollapse: function(state) {
        if (typeof state !== 'boolean') {
            state = !this.state.isCollapsed;
        }

        this.state.attr('isCollapsed', state);
    }
});

layoutBuilder.SectionBlock = layoutBuilder.Block.extend({
    init: function(data) {
        layoutBuilder.Block.prototype.init.call(this, data);

        this.layoutBlocks = new can.List();
        this.settings = new can.Map({
            bgColor: '#ffffff',
            bgImageRepeat: 'no-repeat',
            bgVideo: new can.Map({
                autoplay: 1,
                loop: 1,
                rel: 0,
                showinfo: 0,
                controls: 0,
                disablekb: 1,
                volume: 0
            })
        });
        // To avoid ambiguousness in template
        this.sectionSettings = this.settings;

        this.formTpl = 'section-form';
        this.viewTpl = 'section-view';
        this.meta.unbind('type');

        if (data) {
            if (Array.isArray(data.layoutBlocks)) {
                data.layoutBlocks.forEach(function(layoutBlockData) {
                    this.addLayoutBlock(layoutBlockData);
                }.bind(this));
            }

            if (data.settings) {
                this.settings.attr(data.settings);
            }
        }

        this.openImagePicker = layoutBuilder.openImagePicker;

        this.bindAddLayoutBlockHandler();
        layoutBuilder.bindUndoWatcher(this.layoutBlocks, this.settings);
    },
    getData: function() {
        var data = {
            meta: this.meta.attr(),
            settings: this.settings.attr(),
            layoutBlocks: []
        };

        this.layoutBlocks.forEach(function(layoutBlock) {
            data.layoutBlocks.push(layoutBlock.getData());
        });

        return data;
    },
    setData: function(data) {
        if (data && data.settings) {
            this.settings.attr(data.settings);
        }
    },
    copy: function() {
        layoutBuilder.sectionBlocks.push(new layoutBuilder.SectionBlock(this.getData()));
    },
    changeOrder: function(afterSection) {
        var curIndex = layoutBuilder.sectionBlocks.indexOf(this),
            afterIndex;

        layoutBuilder.sectionBlocks.splice(curIndex, 1);
        afterIndex = layoutBuilder.sectionBlocks.indexOf(afterSection);

        if (afterIndex > -1) {
            layoutBuilder.sectionBlocks.splice(afterIndex + 1, 0, this);
        } else {
            layoutBuilder.sectionBlocks.unshift(this);
        }
    },
    addLayoutBlock: function(layoutBlockData) {
        var layoutBlock = new layoutBuilder.LayoutBlock(layoutBlockData);

        this.layoutBlocks.push(layoutBlock);

        return layoutBlock;
    },
    removeLayoutBlock: function(layoutBlock) {
        var index = this.layoutBlocks.indexOf(layoutBlock);

        if (index > -1) {
            this.layoutBlocks.splice(index, 1);
        } else {
            console.warn('Cannot delete content block. No such block', layoutBlock);
        }
    },
    sortLayoutBlock: function(layoutBlock, afterLayoutBlock, toSection) {
        var newIndex;

        // Dragged from another list
        if (this !== toSection) {
            this.removeLayoutBlock(layoutBlock);
        } else {
            toSection.removeLayoutBlock(layoutBlock);
        }

        if (!afterLayoutBlock) {
            toSection.layoutBlocks.unshift(layoutBlock);
        } else {
            newIndex = toSection.layoutBlocks.indexOf(afterLayoutBlock) + 1;
            toSection.layoutBlocks.splice(newIndex, 0, layoutBlock);
        }
    },
    bindAddLayoutBlockHandler: function () {
        this.layoutBlocks.bind('add', function() {
            setTimeout(function() {
                layoutBuilder.applySortableToContent();
            }.bind(this), 0);
        }.bind(this));
    },
    onEdit: function() {
        layoutBuilder.applyFileuploader($('.layout-bg-image'), {
            onSuccess: function(files, response) {
                if (response && response.url) {
                    this.settings.attr('bgImage', response.url);
                }
            }.bind(this)
        });

        this.settings.bgVideo.bind('url', function(e, newValue) {
            var matches;

            newValue = String(newValue);
            if (newValue.match(/youtube\.com/gi)) {
                // Change url from youtube.com/watch?v=videoId to youtube.com/embed/videoId
                matches = newValue.match(/(\?|&)v=([^&\n\r]+)/);
                if (matches) {
                    this.attr('url', 'https://www.youtube.com/embed/' + matches[2]);
                }
            }
        });
    },
    onImageChosen: function(src) {
        this.settings.attr('bgImage', src);
    },
    clearBgImage: function() {
        this.settings.attr('bgImage', '');
    },
    getYoutubeSettings: function() {
        return 'autoplay=' + this.settings.bgVideo.autoplay +
        '&loop=' + this.settings.bgVideo.loop +
        '&controls=' + this.settings.bgVideo.controls +
        '&rel=' + this.settings.bgVideo.rel +
        '&showinfo=' + this.settings.bgVideo.showinfo +
        '&disablekb=' + this.settings.bgVideo.disablekb,
        '&volume=' + this.settings.bgVideo.volume;
    }
});

layoutBuilder.LayoutBlock = layoutBuilder.Block.extend({
    init: function(data) {
        layoutBuilder.Block.prototype.init.apply(this, arguments);

        this.contentBlocks = new can.List();
        this.settings = new can.Map({
            bgEnabled: true,
            bgColor: '#ffffff',
            bgImageRepeat: 'no-repeat',
            ajax: new can.Map({
                enabled: false,
                url: ''
            })
        });

        this.tmp.ajax = new can.Map({
            success: 0,
            didMakeRequest: false,
            contentType: '',
            fieldsAvailable: new can.List()
        });

        // To avoid ambiguousness in template
        this.layoutSettings = this.settings;

        this.formTpl = 'layout-form';
        this.viewTpl = 'layout-view';
        this.meta.unbind('type');

        if (data) {
            if (Array.isArray(data.contentBlocks)) {
                data.contentBlocks.forEach(function(blockData) {
                    this.addContentBlock(blockData);
                }.bind(this));
            }

            if (data.settings) {
                this.settings.attr(data.settings);
            }
        }

        this.openImagePicker = layoutBuilder.openImagePicker;

        this.checkAjaxUrl();

        this.bindAddContentBlockHandler();
        layoutBuilder.bindUndoWatcher(this.contentBlocks, this.settings, this.meta);
    },
    edit: function() {
        this.editor = new layoutBuilder.Editor(this);

        if (this.settings) {
            this.applyFileuploader();
        }

        this.settings.bind('bgEnabled', function(e, newValue) {
            if (newValue) {
                this.applyFileuploader();
            }
        }.bind(this));
    },
    getData: function() {
        var data = {
                meta: this.meta.attr(),
                settings: this.settings.attr(),
                contentBlocks: []
            };

        this.contentBlocks.forEach(function(contentBlock) {
            data.contentBlocks.push(contentBlock.getData());
        });

        return data;
    },
    setData: function(data) {
        if (data && data.settings) {
            this.settings.attr(data.settings);
        }
    },
    copy: function() {
        var section = layoutBuilder.getSectionByLayout(this),
            newLayout = new layoutBuilder.LayoutBlock(this.getData());

        if (section) {
            section.layoutBlocks.push(newLayout);
        }
    },
    changeType: function(data) {
        this.meta.attr(data.attr());
    },
    widen: function() {
        var index = layoutBuilder.layoutsOrder.indexOf(this.meta.type),
            newType,
            newLayout;

        if (index < layoutBuilder.layoutsOrder.length) {
            newType = layoutBuilder.layoutsOrder[index + 1];
            newLayout = layoutBuilder.menu.getBlockByType(newType);

            if (newLayout) {
                this.changeType(newLayout);
            }
        }
    },
    shorten: function() {
        var index = layoutBuilder.layoutsOrder.indexOf(this.meta.type),
            newType,
            newLayout;

        if (index > 0) {
            newType = layoutBuilder.layoutsOrder[index - 1];
            newLayout = layoutBuilder.menu.getBlockByType(newType);

            if (newLayout) {
                this.changeType(newLayout);
            }
        }
    },
    addContentBlock: function(blockData) {
        var blockType = blockData.type || blockData.meta.type,
            newBlock;

        if (layoutBuilder.contentBlocksConstructors[blockType]) {
            newBlock = new layoutBuilder.contentBlocksConstructors[blockType](blockData);
        } else {
            newBlock = new layoutBuilder.ContentBlock(blockData);
        }

        this.contentBlocks.push(newBlock);

        return newBlock;
    },
    removeContentBlock: function(contentBlock) {
        var index = this.contentBlocks.indexOf(contentBlock);

        if (index > -1) {
            this.contentBlocks.splice(index, 1);
        } else {
            console.warn('Cannot delete content block. No such block', contentBlock);
        }
    },
    sortContentBlock: function(contentBlock, afterContentBlock, toLayout) {
        var newIndex;

        // Dragged from another list
        if (this !== toLayout) {
            this.removeContentBlock(contentBlock);
        } else {
            toLayout.removeContentBlock(contentBlock);
        }

        if (!afterContentBlock) {
            toLayout.contentBlocks.unshift(contentBlock);
        } else {
            newIndex = toLayout.contentBlocks.indexOf(afterContentBlock) + 1;
            toLayout.contentBlocks.splice(newIndex, 0, contentBlock);
        }
    },
    bindAddContentBlockHandler: function () {
        this.contentBlocks.bind('add', function() {
            setTimeout(function() {
                layoutBuilder.applySortableToContent();
            }.bind(this), 0);
        }.bind(this));
    },
    applyFileuploader: function() {
        layoutBuilder.applyFileuploader($('.layout-bg-image'), {
            onSuccess: function(files, response) {
                if (response && response.url) {
                    this.settings.attr('bgImage', response.url);
                }
            }.bind(this)
        });
    },
    onImageChosen: function(src) {
        this.settings.attr('bgImage', src);
    },
    clearBgImage: function() {
        this.settings.attr('bgImage', '');
    },
    checkAjaxUrl: function() {
        if (!this.settings.ajax.enabled) {
            return;
        }

        this.tmp.ajax.attr('didMakeRequest', true);

        $.ajax({
            url: this.settings.ajax.url,
            jsonp: "callback",
            dataType: "jsonp",
            success: function(response) {
                this.tmp.ajax.attr('success', true);
                this.tmp.ajax.fieldsAvailable.splice(0);

                if (Array.isArray(response)) {
                    if (!response.length) {
                        this.tmp.ajax.attr('contentType', 'empty array');
                        return;
                    }

                    if (can.isPlainObject(response[0])) {
                        this.tmp.ajax.attr('contentType', 'array of objects');

                        Object.keys(response[0]).forEach(function(field) {
                            this.tmp.ajax.fieldsAvailable.push(field);
                        }.bind(this));
                    } else {
                        this.tmp.ajax.attr('contentType', 'array of text');
                    }
                } else if (can.isPlainObject(response)) {
                    this.tmp.ajax.attr('contentType', 'object');

                    Object.keys(response).forEach(function(field) {
                        this.tmp.ajax.fieldsAvailable.push(field);
                    }.bind(this));
                } else {
                    this.tmp.ajax.attr('contentType', 'text');
                }
            }.bind(this),
            error: function() {
                this.tmp.ajax.attr('success', false);
            }.bind(this)
        });
    }
});

layoutBuilder.ContentBlock = layoutBuilder.Block.extend({
    init: function(data) {
        layoutBuilder.Block.prototype.init.apply(this, arguments);

        this.settings = new can.Map();
        this.content = new can.Map();
        this.aux = new can.Map();

        // To avoid ambiguousness in template
        this.blockSettings = this.settings;

        this.formTpl = this.meta.type + '-form';
        this.viewTpl = this.meta.type + '-view';
        this.previewTpl = this.meta.type + '-preview';

        this.meta.bind('type', function(e, newValue) {
            this.formTpl = newValue + '-form';
            this.viewTpl = newValue + '-view';
        });

        this.setData(data);
        layoutBuilder.bindUndoWatcher(this.content);
    },
    getData: function() {
        return {
            meta: this.meta.attr(),
            content: this.content.attr(),
            settings: this.settings.attr()
        };
    },
    setData: function(data) {
        if (data && data.content) {
            this.content.attr(data.content);
        }

        if (data && data.settings) {
            this.settings.attr(data.settings);
        }
    },
    copy: function() {
        var layout = layoutBuilder.getLayoutByContentBlock(this),
            newContentBlock = new layoutBuilder.ContentBlock(this.getData());

        if (layout) {
            layout.contentBlocks.push(newContentBlock);
        }
    },
    editSettings: function() {
        var settingsBlock = new layoutBuilder.ContentBlock({
            type: 'content-block-settings',
            title: this.meta.title,
            shortTitle: this.meta.title
        });

        settingsBlock.save = function () {
            this.settings.attr(settingsBlock.settings.attr());
        }.bind(this);

        settingsBlock.settings.attr(this.settings.attr());
        this.settingsEditor = new layoutBuilder.Editor(settingsBlock);
    }
});

layoutBuilder.Editor = can.Construct.extend({
    init: function(block) {
        layoutBuilder.attr('state', 'edit');

        this.block = block;
        this.contentBackup = this.block.getData();

        this.getContainer().append(can.view('editor-modal-tpl', { editor: this, block: this.block }));
        this.el = this.getContainer().children(':last-child');
        this.el.modal('show');

        this.el.on('hidden.bs.modal', function() {
            this.block.hook('onEditorClear').then(function() {
                if (!this.saved) {
                    this.block.setData(this.contentBackup);
                }

                this.clear();
            }.bind(this));
        }.bind(this));

        this.block.hook('onEdit');
    },
    save: function() {
        this.block.save();
        this.saved = true;
        this.el.modal('hide');
    },
    close: function() {
        this.el.modal('hide');
    },
    clear: function() {
        this.block = null;
        this.el.remove();

        layoutBuilder.attr('state', 'view');

        if (this.saved) {
            layoutBuilder.addUndoItem();
        }
    },
    getContainer: function() {
        return $('#editors-container');
    }
});

layoutBuilder.ImagePicker = layoutBuilder.ContentBlock.extend({
    edit: function() {
        this.images = new can.List();
        this.editor = new layoutBuilder.Editor(this);

        $.getJSON(layoutBuilder.config.getImagesUrl, function(images) {
            if (!Array.isArray(images)) {
                return;
            }

            this.images.splice(0, this.images.length);
            images.forEach(function(image) {

                this.images.push(new can.Map(image));
            }.bind(this));
        }.bind(this));
    },
    setImage: function(image) {
        this.content.attr('src', image.url);
    }
});

layoutBuilder.openImagePicker = function(block) {
    var imagePicker = new layoutBuilder.ImagePicker({
        meta: {
            type: 'image-picker',
            title: 'Choose Image'
        },
        content: {
            src: ''
        }
    });

    setTimeout(function() {
        layoutBuilder.applyFileuploader($('.image-picker-wrapper .uploadfile'), {
            onSuccess: function(files, response) {
                if (response && response.url) {
                    imagePicker.content.attr('src', response.url);
                }
            }.bind(this)
        });
    }.bind(this), 0);

    imagePicker.content.bind('src', function(e, newValue) {
        if (newValue) {
            if (block && typeof block.onImageChosen === 'function') {
                block.onImageChosen.call(block, newValue);
            }

            imagePicker.editor.close();
        }
    }.bind(this));

    imagePicker.onEditorClear = function() {
        if (block && typeof block.onImagePickerClose === 'function') {
            block.onImagePickerClose.call(block);
        }
    };

    imagePicker.edit();
};

// Content blocks constructors
layoutBuilder.contentBlocksConstructors = {};

layoutBuilder.contentBlocksConstructors['text-block'] = layoutBuilder.ContentBlock.extend({
    edit: function() {
        this.editor = new layoutBuilder.Editor(this);

        layoutBuilder.applyHtmlEditor(this.getTextElem());
    },
    save: function() {
        this.content.attr('text', this.getTextElem().val());
    },
    onEditorClear: function() {
        this.getTextElem().redactor('core.destroy');
        this.editor = null;
    },
    getTextElem: function() {
        return this.editor.el.find('.text');
    }
});

layoutBuilder.contentBlocksConstructors['icon-list'] = layoutBuilder.ContentBlock.extend({
    getData: function() {
        var data = {
            meta: this.meta.attr(),
            content: this.content.attr()
        };

        data.content.listItems = [];
        this.content.listItems.forEach(function(listItem) {
            data.content.listItems.push(listItem.getData());
        });

        return data;
    },
    setData: function(data) {
        this.content.attr('listItems', new can.List());

        if (data && data.content) {
            this.content.attr('iconPosition', data.content.iconPosition || 'left');
            this.content.attr('titleColor', data.content.titleColor || 'grey');

            if (Array.isArray(data.content.listItems)) {
                data.content.listItems.forEach(function(listItem) {
                    this.content.listItems.push(new this.ListItem(listItem));
                }.bind(this));
            }
        } else {
            // Add a couple items by default
            this.addListItem();
            this.addListItem();
        }
    },
    onEdit: function() {
        this.applySortableToListItems();

        this.content.listItems.bind('add', function() {
            setTimeout(function() {
                this.applySortableToListItems();
            }.bind(this), 0);
        }.bind(this));
    },
    addListItem: function() {
        this.content.listItems.push(new this.ListItem({
            meta: {
                type: 'icon-list-item',
                title: 'List Item'
            },
            content: {
                title: layoutBuilder.t('List item'),
                text: '',
                icon: layoutBuilder.getRandomIcon(),
                isLink: false,
                href: ''
            }
        }));
    },
    editListItem: function(listItem) {
        listItem.edit();
    },
    removeListItem: function(listItem) {
        var index = this.content.listItems.indexOf(listItem);

        if (index > -1) {
            this.content.listItems.splice(index, 1);
        }
    },
    moveListItem: function(listItem, afterListItem) {
        var curIndex = this.content.listItems.indexOf(listItem),
            afterIndex;

        this.content.listItems.splice(curIndex, 1);
        afterIndex = this.content.listItems.indexOf(afterListItem);

        if (afterIndex > -1) {
            this.content.listItems.splice(afterIndex + 1, 0, listItem);
        } else {
            this.content.listItems.unshift(listItem);
        }
    },
    applySortableToListItems: function() {
        var that = this;

        $('.icon-list:not(:data(ui-sortable))').sortable({
            appendTo: '#content',
            stop: function(e, ui) {
                var li = ui.item.data('listItem'),
                    prevLi = ui.item.prev().data('listItem');

                that.moveListItem(li, prevLi);
            }
        });
    },
    ListItem: layoutBuilder.ContentBlock.extend({
        icons: layoutBuilder.faIcons,
        onEdit: function() {
            this.content.bind('icon', function() {
                this.markAsActive();
            }.bind(this));

            this.markAsActive();
        },
        edit: function() {
            this.editor = new layoutBuilder.Editor(this);

            layoutBuilder.applyHtmlEditor(this.getTextElem());
        },
        save: function() {
            this.content.attr('text', this.getTextElem().val());
        },
        onEditorClear: function() {
            this.getTextElem().redactor('core.destroy');
            this.editor = null;
        },
        getTextElem: function() {
            return this.editor.el.find('.text');
        },
        onEnterKey: function(block, el, e) {
            if (e.keyCode === 13) {
                block.editor.save();
            }
        },
        setIcon: function(icon) {
            this.content.attr('icon', icon);
        },
        markAsActive: function() {
            $('.icon.active').removeClass('active');
            $('.icon.' + this.content.icon).addClass('active');
        }
    })
});

layoutBuilder.contentBlocksConstructors['separator'] = layoutBuilder.ContentBlock.extend({
    init: function() {
        var defaultType = 'line';

        layoutBuilder.ContentBlock.prototype.init.apply(this, arguments);

        this.bindType();
        this.bindHeightValidator();

        if (!this.content.type) {
            this.content.attr('type', defaultType);
        }
    },
    bindType: function() {
        this.content.bind('type', function() {
            this.aux.attr('isLine', this.content.type === 'line');
        }.bind(this));
    },
    bindHeightValidator: function() {
        this.content.bind('height', function(e, newVal) {
            var height = Number(newVal),
                defaultHeight = 10;

            if (isNaN(height) || height <= 0) {
                this.content.attr('height', defaultHeight);
            }
        }.bind(this));
    }
});

layoutBuilder.contentBlocksConstructors['icon-box'] = layoutBuilder.ContentBlock.extend({
    icons: layoutBuilder.faIcons,
    onEdit: function() {
        if (!this.content.icon) {
            this.content.attr('icon', layoutBuilder.getRandomIcon());
        }

        this.content.bind('icon', function() {
            this.markAsActive();
        }.bind(this));

        this.markAsActive();
    },
    edit: function() {
        this.editor = new layoutBuilder.Editor(this);

        layoutBuilder.applyHtmlEditor(this.getTextElem());
    },
    save: function() {
        this.content.attr('text', this.getTextElem().val());
    },
    onEditorClear: function() {
        this.getTextElem().redactor('core.destroy');
        this.editor = null;
    },
    getTextElem: function() {
        return this.editor.el.find('.text');
    },
    onEnterKey: function(block, el, e) {
        if (e.keyCode === 13) {
            block.editor.save();
        }
    },
    setIcon: function(icon) {
        this.content.attr('icon', icon);
    },
    markAsActive: function() {
        $('.icon.active').removeClass('active');
        $('.icon.' + this.content.icon).addClass('active');
    }
});

layoutBuilder.contentBlocksConstructors['button'] = layoutBuilder.ContentBlock.extend({
    icons: layoutBuilder.faIcons,
    setData: function(data) {
        if (data && data.content) {
            this.content.attr(data.content);
        } else {
            this.content.attr({
                titleColor: '#ffffff',
                buttonColor: '#3c8dbc',
                alignment: 'left',
                iconPosition: 'left',
                size: 'def'
            });
        }
    },
    onEdit: function() {
        if (!this.content.icon) {
            this.content.attr('icon', layoutBuilder.getRandomIcon());
        }

        this.content.bind('icon', function() {
            this.markAsActive();
        }.bind(this));

        this.markAsActive();
    },
    onEnterKey: function(block, el, e) {
        if (e.keyCode === 13) {
            block.editor.save();
        }
    },
    setIcon: function(icon) {
        this.content.attr('icon', icon);
    },
    markAsActive: function() {
        $('.icon.active').removeClass('active');
        $('.icon.' + this.content.icon).addClass('active');
    }
});

layoutBuilder.contentBlocksConstructors['table'] = layoutBuilder.ContentBlock.extend({
    init: function() {
        var defaultRows = 3,
            i;

        layoutBuilder.ContentBlock.prototype.init.apply(this, arguments);

        // Init rows and cols objects
        if (!this.content.rowsData || !this.content.colsMeta) {
            this.content.attr('rowsData', new can.List());
            this.content.attr('colsMeta', new can.List());

            for (i = 0; i < defaultRows; i++) {
                this.addRow();
                this.addCol();
            }
        }
    },
    setData: function(data) {
        if (data && data.content) {
            this.content.attr(data.content);

            if (data.content.colsMeta && this.content.rowsData && this.content.colsMeta) {
                this.content.attr('colsMeta', data.content.colsMeta);
                this.content.attr('rowsData', data.content.rowsData);

                // Bind column `meta` object to every cell
                this.content.rowsData.forEach(function(row) {
                    row.cols.forEach(function(cell, index) {
                        cell.attr('meta', this.content.colsMeta[index]);
                    }.bind(this));
                }.bind(this));
            }
        }
    },
    onEdit: function() {
        this.aux.attr('colsLastIndex', this.content.colsMeta.length - 1);

        this.content.colsMeta.bind('change', function() {
            this.aux.attr('colsLastIndex', this.content.colsMeta.length - 1);
        }.bind(this));
    },
    getRowsNum: function() {
        return this.content.rowsData.length;
    },
    getColsNum: function() {
        var colsNum = 0;

        if (this.content.rowsData.length) {
            colsNum = this.content.rowsData[0].cols.length;
        }

        return colsNum;
    },
    getRowByCol: function(col) {
        var foundRow = null;

        this.content.rowsData.forEach(function(row) {
            if (row.cols.indexOf(col) > -1) {
                foundRow = row;
            }
        }.bind(this));

        return foundRow;
    },
    addRow: function() {
        var row = new can.Map({
                meta: new can.Map(),
                cols: new can.List()
            }),
            colsNum = this.getColsNum(),
            i;

        for (i = 0; i < colsNum; i++) {
            row.cols.push(new can.Map({
                meta: this.content.colsMeta[i]
            }));
        }

        this.content.rowsData.push(row);
    },
    addCol: function() {
        var colMeta = new can.Map();

        if (!this.content.rowsData.length) {
            this.addRow();
        }

        this.content.colsMeta.push(colMeta);

        this.content.rowsData.forEach(function(row) {
            row.cols.push(new can.Map({
                meta: colMeta
            }));
        });
    },
    editColStyles: function(colMeta) {
        this.editStyles(colMeta, 'Column styles');
    },
    editRowStyles: function(col) {
        var row = this.getRowByCol(col);
        this.editStyles(row.meta, 'Row styles');
    },
    editStyles: function(metaObject, title) {
        var tablePartStyles = new layoutBuilder.ContentBlock({
            type: 'table-part-styles',
            title: title,
            shortTitle: title
        });

        tablePartStyles.save = function() {
            metaObject.attr(tablePartStyles.content.attr());
            metaObject.attr('stylesClasses', this.getStylesClasses(metaObject));
        }.bind(this);

        tablePartStyles.content.attr(metaObject.attr());
        tablePartStyles.edit();
    },
    getStylesClasses: function(meta) {
        var classes = [];

        if (meta.alignment) {
            classes.push('text-' + meta.alignment);
        }

        if (meta.isBold) {
            classes.push('font-weight-bold');
        }

        if (meta.isItalic) {
            classes.push('font-italic');
        }

        return classes.join(' ');
    },
    getCellStyleClasses: function() {
        console.log('getCellStyleClasses', arguments)
    },
    removeRow: function(row) {
        var rowIndex = this.content.rowsData.indexOf(row);

        if (rowIndex > -1 && this.content.rowsData.length > 1) {
            this.content.rowsData.splice(rowIndex, 1);
        }
    },
    removeRowByCol: function(col) {
        var row = this.getRowByCol(col);

        if (row) {
            this.removeRow(row);
        }
    },
    removeCol: function(col) {
        var colIndex = this.content.colsMeta.indexOf(col);

        if (colIndex > -1 && this.content.colsMeta.length > 1) {
            this.content.colsMeta.splice(colIndex, 1);

            this.content.rowsData.forEach(function(row) {
                row.cols.splice(colIndex, 1);
            });
        }
    },
    moveRow: function(col, direction) {
        var row = this.getRowByCol(col),
            curIndex,
            newIndex;

        if (!row) {
            return;
        }

        curIndex = this.content.rowsData.indexOf(row);
        if (curIndex === -1) {
            return;
        }

        if (direction === 'up') {
            if (curIndex > 1) {
                newIndex = curIndex - 1;
            } else {
                newIndex = 0;
            }
        } else {
            if (curIndex < this.content.rowsData.length - 1) {
                newIndex = curIndex + 1;
            } else {
                newIndex = this.content.rowsData.length - 1;
            }
        }

        this.content.rowsData.splice(curIndex, 1);
        this.content.rowsData.splice(newIndex, 0, row);
    },
    moveCol: function(col, direction) {
        var curIndex = this.content.colsMeta.indexOf(col),
            newIndex;

        if (curIndex === -1) {
            return;
        }

        if (direction === 'left') {
            if (curIndex > 1) {
                newIndex = curIndex - 1;
            } else {
                newIndex = 0;
            }
        } else {
            if (curIndex < this.content.colsMeta.length - 1) {
                newIndex = curIndex + 1;
            } else {
                newIndex = this.content.colsMeta.length - 1;
            }
        }

        this.content.colsMeta.splice(curIndex, 1);
        this.content.colsMeta.splice(newIndex, 0, col);

        this.content.rowsData.forEach(function(row) {
            var col = row.cols.splice(curIndex, 1)[0];

            row.cols.splice(newIndex, 0, col);
        });
    },
    editCell: function(cell) {
        var textObject = new layoutBuilder.contentBlocksConstructors['text-block']({
            meta: {
                type: 'text-block',
                title: 'Cell data',
                shortTitle: 'Cell'
            },
            content: {
                text: cell.text
            }
        });

        textObject.onEditorClear = function() {
            var isSaved = this.editor.saved;

            layoutBuilder.contentBlocksConstructors['text-block'].prototype.onEditorClear.apply(this, arguments);

            if (isSaved) {
                cell.attr('text', this.content.text);
            }
        };

        textObject.edit();
    }
});

layoutBuilder.contentBlocksConstructors['image'] = layoutBuilder.ContentBlock.extend({
    edit: function() {
        this.editor = new layoutBuilder.Editor(this);

        layoutBuilder.applyFileuploader($('.image-block .uploadfile'), {
            onSuccess: function(files, response) {
                if (response && response.url) {
                    this.content.attr('src', response.url);
                }
            }.bind(this)
        });
    },
    onImageChosen: function(src) {
        this.content.attr('src', src);
    },
    openImagePicker: layoutBuilder.openImagePicker
});

layoutBuilder.contentBlocksConstructors['video'] = layoutBuilder.ContentBlock.extend({

});

layoutBuilder.contentBlocksConstructors['slideshow'] = layoutBuilder.ContentBlock.extend({
    init: function() {
        layoutBuilder.ContentBlock.prototype.init.apply(this, arguments);

        this.maxImagesNum = 100;
        this.defaultSlideDuration = 5;
        this.minSlideDuration = 1;
        this.maxSlideDuration = 100;

        // Init rows and cols objects
        if (!this.content.images) {
            this.content.attr({
                images: new can.List(),
                slideDuration: this.defaultSlideDuration
            });
        }

        this.getSlideDuration = can.compute(function() {
            return this.content.slideDuration * 1000;
        }.bind(this));
    },
    onEdit: function() {
        this.getSliderElem().carousel('pause');

        this.content.bind('slideDuration', function(e, newValue) {
            var duration = Number(newValue);

            if (isNaN(duration)) {
                this.content.attr('slideDuration', this.defaultSlideDuration);
            } else if (duration < this.minSlideDuration) {
                this.content.attr('slideDuration', this.minSlideDuration);
            } else if (duration > this.maxSlideDuration) {
                this.content.attr('slideDuration', this.maxSlideDuration);
            }
        }.bind(this));

        this.applySortableToSlides();
    },
    onEditorClear: function() {
        this.getSliderElem().carousel('cycle');
        this.editor = null;
    },
    getSliderElem: function() {
        return $('#carousel-' + this.id);
    },
    moveSlide: function(slide, afterSlide) {
        var curIndex = this.content.images.indexOf(slide),
            afterIndex;

        this.content.images.splice(curIndex, 1);
        afterIndex = this.content.images.indexOf(afterSlide);

        if (afterIndex > -1) {
            this.content.images.splice(afterIndex + 1, 0, slide);
        } else {
            this.content.images.unshift(slide);
        }
    },
    applySortableToSlides: function() {
        var that = this;

        $('.slides-list:not(:data(ui-sortable))').sortable({
            appendTo: '#content',

            stop: function(e, ui) {
                var li = ui.item.data('slide'),
                    prevLi = ui.item.prev().data('slide');

                that.moveSlide(li, prevLi);
            }
        });
    },
    editImageCaption: function(image) {
        var textObject = new layoutBuilder.contentBlocksConstructors['text-block']({
            meta: {
                type: 'text-block',
                title: 'Image Caption',
                shortTitle: 'Caption'
            },
            content: {
                text: image.caption
            }
        });

        textObject.onEditorClear = function() {
            if (this.editor.saved) {
                image.attr('caption', this.content.text);
            }

            layoutBuilder.contentBlocksConstructors['text-block'].prototype.onEditorClear.apply(this, arguments);
        };

        textObject.edit();
    },
    addImage: function(src) {
        if (this.content.images.length < this.maxImagesNum) {
            this.content.images.push(new can.Map({
                src: src
            }));
        }
    },
    editImage: function(image) {
        this.tmp.attr('updatingImage', image);
        this.openImagePicker(this);
    },
    removeImage: function(image) {
        var index = this.content.images.indexOf(image);

        if (index > -1) {
            this.content.images.splice(index, 1);
        }
    },
    onImageChosen: function(src) {
        if (this.tmp.updatingImage) {
            this.tmp.updatingImage.attr('src', src);
        } else {
            this.addImage(src);
        }
    },
    onImagePickerClose: function() {
        this.tmp.attr('updatingImage', null);
    },
    openImagePicker: layoutBuilder.openImagePicker
});

layoutBuilder.contentBlocksConstructors['team-member'] = layoutBuilder.ContentBlock.extend({
    edit: function() {
        this.editor = new layoutBuilder.Editor(this);

        layoutBuilder.applyFileuploader($('.team-member-block .uploadfile'), {
            onSuccess: function(files, response) {
                if (response && response.url) {
                    this.content.attr('image', response.url);
                }
            }.bind(this)
        });
    },
    onImageChosen: function(src) {
        this.content.attr('image', src);
    },
    openImagePicker: layoutBuilder.openImagePicker
});

layoutBuilder.contentBlocksConstructors['dynamic-block'] = layoutBuilder.ContentBlock.extend({

});

layoutBuilder.CodeBlock = layoutBuilder.ContentBlock.extend({
    init: function(meta, code) {
        layoutBuilder.ContentBlock.prototype.init.apply(this, arguments);

        this.setData(code);
    },
    save: function() {
        this.content.attr('text', this.aceEditor.getValue());
    },
    getData: function() {
        return this.content.text;
    }
});

layoutBuilder.CssBlock = layoutBuilder.CodeBlock.extend({
    onEdit: function() {
        this.aceEditor = ace.edit('tp-css-code');
        this.aceEditor.getSession().setMode('ace/mode/css');
    }
});

layoutBuilder.JsBlock = layoutBuilder.CodeBlock.extend({
    onEdit: function() {
        this.aceEditor = ace.edit('tp-js-code');
        this.aceEditor.getSession().setMode('ace/mode/javascript');
    }
});

layoutBuilder.addSection = function() {
    var newSection = new layoutBuilder.SectionBlock(layoutBuilder.menu.getBlockByType('section'));

    layoutBuilder.sectionBlocks.push(newSection);

    return newSection;
};

layoutBuilder.addLayout = function(data) {
    var newLayout,
        section;

    if (data.type === 'section') {
        return layoutBuilder.addSection(data);
    }

    if (layoutBuilder.sectionBlocks.length) {
        section = layoutBuilder.sectionBlocks[layoutBuilder.sectionBlocks.length - 1];
    } else {
        section = layoutBuilder.addSection();
    }

    newLayout = new layoutBuilder.LayoutBlock(data);
    section.layoutBlocks.push(newLayout);

    return newLayout;
};

layoutBuilder.removeSection = function(sectionBlock) {
    var index = layoutBuilder.sectionBlocks.indexOf(sectionBlock);

    if (index > -1) {
        layoutBuilder.sectionBlocks.splice(index, 1);
    }
};

layoutBuilder.addContentBlock = function(data) {
    var layoutData = layoutBuilder.menu.getBlockByType('full-width'),
        section,
        layout;

    if (layoutBuilder.sectionBlocks.length) {
        section = layoutBuilder.sectionBlocks[layoutBuilder.sectionBlocks.length - 1];
    } else {
        section = layoutBuilder.addSection();
    }

    if (section.layoutBlocks.length) {
        layout = section.layoutBlocks[section.layoutBlocks.length - 1];
    } else {
        layout = section.addLayoutBlock(layoutBuilder.menu.getBlockByType('full-width'));
    }

    layout.addContentBlock(data);
};

layoutBuilder.editCodeBlock = function(data) {
    var codeBlock = layoutBuilder[data.type];

    if (!codeBlock) {
        console.warn('No code block', data);
        return;
    }

    codeBlock.edit();
};

layoutBuilder.registerHelpers = function() {
    Mustache.registerHelper('gt', function(expr, value, content) {
        if (typeof expr === 'function') {
            return expr() > value ? content : '';
        }

        return expr > value ? content : '';
    });

    Mustache.registerHelper('json', function(obj) {
        while (typeof obj === 'function') {
            obj = obj();
        }

        return JSON.stringify(obj);
    });
};

layoutBuilder.renderMenu = function() {
    $('#available-layout-blocks').html(can.view('menu-items-tpl', { items: this.menu.layoutBlocks, method: this.addLayout }));
    $('#available-content-blocks').html(can.view('menu-items-tpl', { items: this.menu.contentBlocks, method: this.addContentBlock }));
    $('#available-code-blocks').html(can.view('menu-items-tpl', { items: this.menu.codeBlocks, method: this.editCodeBlock }));
};

layoutBuilder.renderContent = function() {
    $('#content').html(can.view('content-tpl', {
        globalState: layoutBuilder.globalState,
        sectionBlocks: layoutBuilder.sectionBlocks,
        removeSection: layoutBuilder.removeSection,
        undoItems: layoutBuilder.undoStack,
        redoItems: layoutBuilder.redoStack,
        undo: layoutBuilder.undo,
        redo: layoutBuilder.redo,
        toggleCollapseAll: layoutBuilder.toggleCollapseAll
    }));

    $('.box-footer>.form-group').append(can.view('footer-tpl', {
        showPreview: layoutBuilder.showPreview
    }));

    this.applySortableToContent();

    layoutBuilder.sectionBlocks.bind('add', function() {
        setTimeout(function() {
            this.applySortableToContent();
        }.bind(this), 0);
    }.bind(this));
};

layoutBuilder.initObjects = function(inputData) {
    layoutBuilder.attr('state', 'init');

    if (!inputData || !Array.isArray(inputData.sectionBlocks)) {
        return;
    }

    layoutBuilder.sectionBlocks.splice(0);

    inputData.sectionBlocks.forEach(function(sectionData) {
        layoutBuilder.sectionBlocks.push(new layoutBuilder.SectionBlock(sectionData));
    }.bind(this));

    if (inputData.css) {
        layoutBuilder.css.content.attr('text', inputData.css);
    }

    if (inputData.js) {
        layoutBuilder.js.content.attr('text', inputData.js);
    }

    layoutBuilder.attr('state', 'view');
};

layoutBuilder.getContentJSON = function() {
    var content = {
        sectionBlocks: [],
        css: '',
        js: ''
    };

    layoutBuilder.sectionBlocks.each(function(sectionBlock) {
        content.sectionBlocks.push(sectionBlock.getData());
    });

    content.css = layoutBuilder.css.getData();
    content.js = layoutBuilder.js.getData();

    return JSON.stringify(content);
};

layoutBuilder.addUndoItem = function() {
    var content = layoutBuilder.getContentJSON(),
        lastUndoItem;

    if (layoutBuilder.state !== 'view') {
        return;
    }

    lastUndoItem = layoutBuilder.undoStack.slice(-1)[0];
    if (!lastUndoItem || lastUndoItem.content !== content) {
        layoutBuilder.redoStack.splice(0);
        layoutBuilder.undoStack.push({
            content: content,
            ts: Date.now()
        });
    }
};

layoutBuilder.addRedoItem = function(undoItem) {
    this.redoStack.push(undoItem);
};

layoutBuilder.undo = function() {
    var currentState,
        savedState;

    if (layoutBuilder.undoStack.length < 2) {
        return;
    }

    try {
        currentState = layoutBuilder.undoStack.pop();
        layoutBuilder.redoStack.push(currentState);

        savedState = layoutBuilder.undoStack.slice(-1)[0];
        layoutBuilder.initObjects(JSON.parse(savedState.content));
    } catch(e) {
        console.warn('Couldn\'t parse an undo item');
    }
};

layoutBuilder.redo = function() {
    var lastRedoItem;

    if (!layoutBuilder.redoStack.length) {
        return;
    }

    try {
        lastRedoItem = layoutBuilder.redoStack.pop();

        layoutBuilder.undoStack.push(lastRedoItem);
        layoutBuilder.initObjects(JSON.parse(lastRedoItem.content));
    } catch(e) {
        console.warn('Couldn\'t parse a redo item');
    }
};

layoutBuilder.toggleCollapseAll = function() {
    layoutBuilder.globalState.attr('isCollapsed', !layoutBuilder.globalState.isCollapsed);

    layoutBuilder.getAllContentBlocks().forEach(function(contentBlock) {
        contentBlock.toggleCollapse(layoutBuilder.globalState.isCollapsed);
    });
};

layoutBuilder.getRenderedPage = function() {
    var documentFragment,
        $div;

    documentFragment = can.view('trendy-page-tpl', {
        sectionBlocks: layoutBuilder.sectionBlocks,
        css: layoutBuilder.css.content.text,
        js: layoutBuilder.js.content.text
    });

    $div = $('<div></div>');
    $div.append(documentFragment);

    return $div.html();
};

layoutBuilder.getRenderedPreview = function() {
    return can.view('trendy-page-preview-tpl', {
        sectionBlocks: layoutBuilder.sectionBlocks
    });
};

layoutBuilder.showPreview = function() {
    var $previewIframe = $('#preview'),
        $previewForm = $('#preview-form'),
        $dataInput = $previewForm.find('input[name="body"]');

    $dataInput.val(layoutBuilder.getRenderedPage());
    $previewIframe.removeClass('hidden');
    $previewForm.submit();

    $('html').addClass('preview');
};

layoutBuilder.hidePreview = function() {
    $('html').removeClass('preview');
    $('#preview').addClass('hidden');
};

layoutBuilder.bindUndoWatcher = function() {
    var i,
        obj;

    for (i = 0; i < arguments.length; i++) {
        obj = arguments[i];

        if (!obj || typeof obj.bind !== 'function') {
            console.warn('can\'t bind UndoWatcher', 'obj:', obj, 'index:', i);
            continue;
        }

        obj.bind('change', layoutBuilder.addUndoItem);
    }
};

layoutBuilder.bindCodeBlocksWatcher = function() {
    this.css.content.bind('change', function() {
        $('#page-styles').html(this.css.getData());
    }.bind(this));

    this.js.content.bind('change', function() {

    });
};

layoutBuilder.applySortableToContent = function() {
    // Sortable sections
    $('#content:not(:data(ui-sortable))').sortable({
        appendTo: '#content',
        handle: '>.header',
        start: function (e, ui) {
            layoutBuilder.attr('state', 'sorting');
        },
        stop: function(e, ui) {
            var section = ui.item.data('section'),
                prevSection = ui.item.prev().data('section');

            if (!section) {
                section = layoutBuilder.addSection();
            }

            section.changeOrder(prevSection);

            ui.item.remove();

            layoutBuilder.attr('state', 'view');
            layoutBuilder.addUndoItem();
        }
    });
    
    // Sortable layouts
    $('#content .section>.body:not(:data(ui-sortable))').sortable({
        appendTo: '#content',
        handle: '>.header',
        connectWith: '#content .section>.body',
        start: function (e, ui) {
            layoutBuilder.attr('state', 'sorting');
        },
        stop: function(e, ui) {
            // If it's an existing layout block
            var layoutBlock = ui.item.data('layout'),
                layoutType = ui.item.data('type'),
                layoutData = layoutBuilder.menu.getBlockByType(layoutType),
                prevLayoutBlock = ui.item.prev().data('layout'),
                fromSection = $(this).closest('.section').data('section'),
                toSection = ui.item.closest('.section').data('section');

            if (layoutBlock) {
                fromSection.sortLayoutBlock(layoutBlock, prevLayoutBlock, toSection);
            } else if (layoutData) {
                layoutBlock = toSection.addLayoutBlock(layoutData.attr());
                toSection.sortLayoutBlock(layoutBlock, prevLayoutBlock, toSection);
            } else {
                console.log('An error occurred while adding/sorting layout');
            }

            ui.item.remove();

            layoutBuilder.attr('state', 'view');
            layoutBuilder.addUndoItem();
        }
    });

    // Sortable content blocks
    $('#content .layout>.body:not(:data(ui-sortable))').sortable({
        appendTo: '#content',
        connectWith: '#content .layout>.body',
        start: function (e, ui) {
            layoutBuilder.attr('state', 'sorting');
        },
        stop: function(e, ui) {
            // If it's an existing content block
            var contentBlock = ui.item.data('contentBlock'),
                blockType = ui.item.data('type'),
                blockData = layoutBuilder.menu.getBlockByType(blockType),
                prevContentBlock = ui.item.prev().data('contentBlock'),
                fromLayout = $(this).closest('.layout').data('layout'),
                toLayout = ui.item.closest('.layout').data('layout');

            if (contentBlock) {
                fromLayout.sortContentBlock(contentBlock, prevContentBlock, toLayout);
            } else if (blockData) {
                contentBlock = toLayout.addContentBlock(blockData.attr());
                toLayout.sortContentBlock(contentBlock, prevContentBlock, toLayout);
            } else {
                console.log('An error occurred while adding/sorting content blocks');
            }

            ui.item.remove();

            layoutBuilder.attr('state', 'view');
            layoutBuilder.addUndoItem();
        }
    });

    // Draggable section block in menu
    $("#available-layout-blocks .item[data-type='section']:not(:data(ui-sortable))").draggable({
        helper: function () {
            var docFrag = can.view('draggable-section-helper-tpl', {});

            return docFrag.firstElementChild;
        },
        connectToSortable: '#content'
    });

    // Draggable layout blocks in menu
    $("#available-layout-blocks .item:not([data-type='section']):not(:data(ui-sortable))").draggable({
        helper: function () {
            var layoutType = $(this).data('type'),
                layoutData = layoutBuilder.menu.getBlockByType(layoutType),
                docFrag = can.view('draggable-layout-helper-tpl', layoutData);

            return docFrag.firstElementChild;
        },
        connectToSortable: '#content .section>.body'
    });

    // Draggable content blocks in menu
    $("#available-content-blocks .item:not(:data(ui-sortable))").draggable({
        helper: function () {
            var blockType = $(this).data('type'),
                blockData = layoutBuilder.menu.getBlockByType(blockType),
                docFrag = can.view('draggable-content-block-helper-tpl', blockData);

            return docFrag.firstElementChild;
        },
        connectToSortable: '#content .layout>.body'
    });
};

layoutBuilder.bindSubmitHandler = function() {
    $('form').on('submit', function(e) {
        $('#trendypage-body').val(layoutBuilder.getRenderedPage());
        $('#trendypage-bodydata').val(layoutBuilder.getContentJSON());

        //e.stopImmediatePropagation();
    });
};

layoutBuilder.bindKeyHandlers = function() {
    // ctrl + z, ctrl + shift + z
    $(window).on('keyup', function(e) {
        var zKeyCode = 90;

        if (e.ctrlKey && e.keyCode === zKeyCode) {
            if (e.shiftKey) {
                layoutBuilder.redo();
            } else {
                layoutBuilder.undo();
            }
        }
    });
};

layoutBuilder.bindScrollHandler = function() {
    var $availableBlocksTabs = $('.available-blocks-tabs'),
        availableBlocksTabsInitialOffset = $availableBlocksTabs.offset(),
        availableBlocksTabsInitialWidth = $availableBlocksTabs.width(),
        isFixed = null;

    $(window).on('scroll', function() {
        if (window.scrollY > availableBlocksTabsInitialOffset.top) {
            if (isFixed !== true) {
                $availableBlocksTabs.addClass('fixed');
                $availableBlocksTabs.width(availableBlocksTabsInitialWidth);

                isFixed = true;
            }
        } else {
            if (isFixed !== false) {
                $availableBlocksTabs.removeClass('fixed');
                $availableBlocksTabs.width('inherit');

                isFixed = false;
            }
        }
    });
};

layoutBuilder.applyHtmlEditor = function($el) {
    var options = {
            focus: true,
            buttonSource: true,
            minHeight: 350,
            linebreaks: false,
            replaceDivs: false,
            imageUpload: this.config.uploadImageUrl,
            imageUploadFields: {},
            imageManagerJson: this.config.getImagesUrl,
            formatting: ['p', 'h1', 'h2', 'h3', 'h4'],
            plugins: ['source', 'imagemanager', 'table', 'alignment', 'fontsize', 'fontcolor', 'properties']
        },
        pageLang = $('html').attr('lang');

    // If translation exists set the language
    if ($.Redactor.opts.langs[pageLang]) {
        options.lang = pageLang;
    }

    options.imageUploadFields[this.config.csrf.param] = this.config.csrf.value;

    // New version of Imperavi ignores empty textareas created on the fly
    if (!$el.text().length) {
        $el.html('<p></p>');
    }

    $el.redactor(options);
};

layoutBuilder.applyFileuploader = function($el, inputOptions) {
    var options = {
            url: layoutBuilder.config.uploadImageUrl,
            multiple: false,
            dragDrop: true,
            fileName: 'file',
            dragDropStr: '<span>' + layoutBuilder.t('Drag & Drop Files') + '</span>',
            doneStr: layoutBuilder.t('Done'),
        };

    if (inputOptions) {
        options = can.extend(options, inputOptions);
    }

    if ($el.length) {
        $el.uploadFile(options);
    } else {
        console.warn('No element to apply file uploader');
    }
};

layoutBuilder.getNewId = function() {
    return ++this.idCounter;
};

layoutBuilder.appendCalculatedStyles = function() {
    var viewPortHeight = $(window).height(),
        stylesData = {
            fullHeight: viewPortHeight,
            threeFourths: viewPortHeight * .75,
            oneHalf: viewPortHeight * .5,
            oneFourth: viewPortHeight *.25
        };

    $('#lb-calculated-styles').remove();

    $('head').append(can.view('lb-calculated-styles-tpl', stylesData));
};

layoutBuilder.getSectionByLayout = function(layout) {
    var result = null;

    layoutBuilder.sectionBlocks.forEach(function(section) {
        if (section.layoutBlocks.indexOf(layout) > -1) {
            result = section;
        }
    });

    return result;
};

layoutBuilder.getLayoutByContentBlock = function(contentBlock) {
    var result = null;

    layoutBuilder.sectionBlocks.forEach(function(section) {
        section.layoutBlocks.forEach(function(layout) {
            if (layout.contentBlocks.indexOf(contentBlock) > -1) {
                result = layout;
            }
        });
    });

    return result;
};

layoutBuilder.getAllContentBlocks = function() {
    var result = [];

    layoutBuilder.sectionBlocks.forEach(function(section) {
        section.layoutBlocks.forEach(function(layout) {
            result.push.apply(result, layout.contentBlocks);
        });
    });

    return result;
};

layoutBuilder.registerAdditionalObjects = function() {
    if (!Array.isArray(window.layoutBuilderRegisterAdditionalObjects)) {
        return;
    }

    window.layoutBuilderRegisterAdditionalObjects.forEach(function(registerFunc) {
        if (typeof  registerFunc === 'function') {
            registerFunc.call(layoutBuilder);
        }
    });
};

// Translates a message
layoutBuilder.t = function(message) {
    if (this.config.translations && this.config.translations[message]) {
        return this.config.translations[message];
    }

    return message;
};

layoutBuilder.hasTranslation = function(message) {
    return this.t(message) !== message;
};

layoutBuilder.translateMenu = function() {
    function translateMenuItem(item) {
        item.attr('title', layoutBuilder.t(item.title));
        item.attr('shortTitle', layoutBuilder.t(item.shortTitle));
    }

    this.menu.layoutBlocks.forEach(function(item) {
        translateMenuItem(item);
    });

    this.menu.contentBlocks.forEach(function(item) {
        translateMenuItem(item);
    });
};

layoutBuilder.registerTranslation = function(originalMessage, translatedMessage) {
    if (this.config.translations) {
        if (originalMessage && translatedMessage) {
            this.config.translations[originalMessage] = translatedMessage;
        } else {
            console.log('registerTranslation: Empty message', arguments);
        }
    } else {
        console.warn('registerTranslation: No config yet');
    }
};

layoutBuilder.bindPreviewHandlers = function() {
    $('.close-preview').on('click', function() {
        layoutBuilder.hidePreview();
    });
};

layoutBuilder.init = function() {
    var inputData = {};

    this.idCounter = 0;

    try {
        this.config.attr(JSON.parse($('input[name="layoutBuilderConfig"]').val()));
        inputData = JSON.parse($('#trendypage-bodydata').val());
    } catch (e) {
        console.log('No valid page content');
    }

    layoutBuilder.translateMenu();

    layoutBuilder.attr('css', new layoutBuilder.CssBlock(layoutBuilder.menu.getBlockByType('css')));
    layoutBuilder.attr('js', new layoutBuilder.JsBlock(layoutBuilder.menu.getBlockByType('js')));
    layoutBuilder.bindCodeBlocksWatcher();

    this.bindUndoWatcher(this.sectionBlocks);

    this.registerAdditionalObjects();
    this.initObjects(inputData);

    this.addUndoItem(); // save initial state

    this.registerHelpers();
    this.renderMenu();
    this.renderContent();
    this.appendCalculatedStyles();
    this.bindSubmitHandler();
    this.bindKeyHandlers();
    this.bindScrollHandler();
    this.bindPreviewHandlers();
};

$(function() {
    layoutBuilder.init();
});
