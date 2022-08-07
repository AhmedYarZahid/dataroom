window.layoutBuilderRegisterAdditionalObjects = [
    function() {
        var layoutBuilder = this;

        layoutBuilder.contentBlocksConstructors['header'] = layoutBuilder.ContentBlock.extend({
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

        layoutBuilder.menu.contentBlocks.push({
            type: 'header',
            title: 'Bandeau image',
            shortTitle: 'Bandeau image',
            menuIcon: '/images/layout-builder/header.png'
        });
    }
];

window.layoutBuilderProjectStyles = [
    {
        title: 'Header with dash',
        className: 'tp-aja-header-with-dash'
    },
    {
        title: 'Subheader',
        className: 'tp-aja-subheader'
    },
    {
        title: 'Light text',
        className: 'tp-aja-text-light'
    }
];

window.layoutBuilderProjectColors = ['#394652', '#5a6d7f', '#92b6c7', '#1b4164', '#5e839b', '#2d3135', '#ffffff'];