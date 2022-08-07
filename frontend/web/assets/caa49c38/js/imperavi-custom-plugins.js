if (!RedactorPlugins) var RedactorPlugins = {};

function isDomElement(obj) {
    try {
        return obj instanceof HTMLElement;
    }
    catch(e){
        return (typeof obj === 'object') &&
            (obj.nodeType === 1) && (typeof obj.style === 'object') &&
            (typeof obj.ownerDocument === 'object');
    }
}

if (Array.isArray(window.layoutBuilderProjectColors)) {
    RedactorPlugins.tpFontcolor = function() {
        return {
            init: function()
            {
                var colors = window.layoutBuilderProjectColors;

                var buttons = ['fontcolor', 'backcolor'];

                for (var i = 0; i < 2; i++) {
                    var name = buttons[i];

                    var button = this.button.add(name, this.lang.get(name));
                    var $dropdown = this.button.addDropdown(button);

                    $dropdown.width(88);
                    this.tpFontcolor.buildPicker($dropdown, name, colors);

                }
            },
            buildPicker: function($dropdown, name, colors)
            {
                var rule = (name == 'backcolor') ? 'background-color' : 'color';

                var len = colors.length;
                var self = this;
                var func = function(e)
                {
                    e.preventDefault();
                    self.tpFontcolor.set($(this).data('rule'), $(this).attr('rel'));
                };

                for (var z = 0; z < len; z++)
                {
                    var color = colors[z];

                    var $swatch = $('<a rel="' + color + '" data-rule="' + rule +'" href="#" style="float: left; font-size: 0; border: 2px solid #fff; padding: 0; margin: 0; width: 22px; height: 22px;"></a>');
                    $swatch.css('background-color', color);
                    $swatch.on('click', func);

                    $dropdown.append($swatch);
                }

                var $elNone = $('<a href="#" style="display: block; clear: both; padding: 5px; font-size: 12px; line-height: 1;"></a>').html(this.lang.get('none'));
                $elNone.on('click', $.proxy(function(e)
                {
                    e.preventDefault();
                    this.tpFontcolor.remove(rule);

                }, this));

                $dropdown.append($elNone);
            },
            set: function(rule, type)
            {
                this.inline.format('span', 'style', rule + ': ' + type + ';');
            },
            remove: function(rule)
            {
                this.inline.removeStyleRule(rule);
            }
        };
    };
}

if (Array.isArray(window.layoutBuilderProjectStyles)) {
    RedactorPlugins.tpStyles = function() {
        return {
            init: function()
            {
                var dropdown = {},
                    button = this.button.add('fontfamily', this.lang.get('Project Styles')),
                    stylesList = window.layoutBuilderProjectStyles;

                stylesList.forEach(function(style) {
                    dropdown[style.className] = {
                        title: style.title,
                        func: function() {
                            this.inline.toggleClass(style.className);
                        }
                    };
                });

                this.button.addDropdown(button, dropdown);
            }
        };
    };
}

RedactorPlugins.tpFontSize = function() {
    return {
        init: function()
        {
            var fontSizes = [
                8, 10, 12, 14, 16, 18, 24, 48, 76
            ];

            var button = this.button.add('tpFontSize', this.lang.get('Font size'));
            var dropdown = {};

            this.button.addCallback(button, function() {
                var $dropdown = $(button).data('dropdown'),
                    currentEl = this.selection.getCurrent(),
                    fontSize;

                // Remove active class from all items
                $dropdown.find('a').removeClass('redactor-dropdown-active-item');

                if (!isDomElement(currentEl)) {
                    currentEl = this.selection.getParent();

                    if (!isDomElement(currentEl)) {
                        return;
                    }
                }

                fontSize = $(currentEl).css('fontSize').replace(/[^-\d\.]/g, '');
                $dropdown.find('a.redactor-dropdown-' + fontSize).addClass('redactor-dropdown-active-item');
            }.bind(this));

            fontSizes.forEach(function(fontSize) {
                dropdown[fontSize] = {
                    title: fontSize,
                    func: function(fontSize) {
                        var currentEl = this.selection.getCurrent();

                        if (!currentEl) {
                            return;
                        }

                        this.inline.format('span', 'style', 'font-size: ' + fontSize + 'px;');
                    }
                };
            });

            this.button.addDropdown(button, dropdown);
        }
    };
};

// Translations

// EN
if ($.Redactor && $.Redactor.opts.langs['en']) {
    $.Redactor.opts.langs['en']['Project Styles'] = 'Project Styles';
    $.Redactor.opts.langs['en']['Font size'] =  'Font size';
}

// FR
if ($.Redactor && $.Redactor.opts.langs['fr']) {
    $.Redactor.opts.langs['fr']['Project Styles'] = 'Styles de projet';
    $.Redactor.opts.langs['fr']['Font size'] =  'Taille de police';
}