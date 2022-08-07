// Inline IF helper
Handlebars.registerHelper('inline-if', function(variable, value) {
    return variable ? value : '';
});

$(function() {
    var $menuWrapper = $('#menu-wrapper'),
        menuItemsState = {},
        initialData = {},
        changedData = {},
        compiledMenuTpl;

    function getBaseUrl() {
        return location.pathname.substr(0, location.pathname.indexOf('/menu') + 5);
    }

    function cloneObject(obj) {
        return JSON.parse(JSON.stringify(obj));
    }

    function arrayObjectIndexOf(haystack, needle, prop) {
        var i;

        if (!Array.isArray(haystack)) {
            return -1;
        }

        for (i = 0; i < haystack.length; i++) {
            if (haystack[i][prop] === needle) {
                return i;
            }
        }

        return -1;
    }

    function updateInitialData(data) {
        initialData = {
            id: 'root',
            items: data
        };

        changedData = cloneObject(initialData);
    }

    function saveState() {
        menuItemsState = {};

        $menuWrapper.find('.menu-item').each(function() {
            var $this = $(this);

            menuItemsState[$this.data('menuItemId')] = $this.hasClass('open');
        });
    }

    function findMenuItem(menuItemId, data) {
        var found = null,
            i;

        if (data.id === menuItemId) {
            return data;
        }

        if (!Array.isArray(data.items)) {
            return null;
        }

        for (i = 0; i < data.items.length; i++) {
            found = findMenuItem(menuItemId, data.items[i]);
            if (found) {
                return found;
            }
        }

        return null;
    }

    function findMenuItemParent(menuItemId, data) {
        var found = null,
            i;

        if (!Array.isArray(data.items)) {
            return null;
        }

        if (arrayObjectIndexOf(data.items, menuItemId, 'id') > -1) {
            return data;
        }

        for (i = 0; i < data.items.length; i++) {
            found = findMenuItemParent(menuItemId, data.items[i]);

            if (found) {
                return found;
            }
        }

        return null;
    }

    function compileMenuTpl() {
        compiledMenuTpl = Handlebars.compile($('#menu-tree-tpl').html());
        Handlebars.registerPartial('menuTreeItem', $('#menu-tree-item-tpl').html());
    }

    function renderMenuTpl(menuData) {
        $menuWrapper.html(compiledMenuTpl({
            menuTree: menuData,
            menuItemsState: menuItemsState
        }));
    }

    function toggleOnMenuSortButtons(state) {
        $('#on-menu-sort-buttons').toggleClass('hidden', !state);
    }

    function toggleLoadingSpinner(state) {
        $menuWrapper.toggleClass('loading', state);
    }

    function getMenuItemElem(menuItemId) {
        return $('.menu-item[data-menu-item-id="' + menuItemId + '"]');
    }

    function bindSortable() {
        $menuWrapper.children('ol').nestedSortable({
            items: 'li',
            placeholder: 'sortable-placeholder',
            forcePlaceholderSize: true,
            tolerance: 'pointer',
            toleranceElement: '> .menu-item-info',

            update: function(e, ui) {
                var menuItemId = ui.item.data('menuItemId'),
                    menuItem = findMenuItem(menuItemId, changedData),
                    oldParentItem = findMenuItemParent(menuItemId, changedData),
                    newParentItem = findMenuItem(ui.item.parent().closest('.menu-item').data('menuItemId'), changedData),
                    prevItem = findMenuItem(ui.item.prev().data('menuItemId'), changedData),
                    menuItemIndex,
                    prevItemIndex;

                if (!menuItem || !oldParentItem) {
                    console.warn('Could not find menu item');
                    return;
                }

                if (!newParentItem) {
                    newParentItem = changedData;
                }

                if (!Array.isArray(newParentItem.items)) {
                    newParentItem.items = [];
                }

                // Remove from old place
                menuItemIndex = arrayObjectIndexOf(oldParentItem.items, menuItem.id, 'id');
                if (menuItemIndex > -1) {
                    oldParentItem.items.splice(menuItemIndex, 1);
                }

                // Insert to new place
                prevItemIndex = prevItem ? arrayObjectIndexOf(newParentItem.items, prevItem.id, 'id') : -1;
                newParentItem.items.splice(prevItemIndex + 1, 0, menuItem);

                // Expand destination subtree
                getMenuItemElem(newParentItem.id).toggleClass('open', true);

                refreshMenu(changedData);

                toggleOnMenuSortButtons(true);
            }
        });
    }

    function bindButtons() {
        // Collapse/expand submenu
        $menuWrapper.find('.toggler, .menu-item-label.has-items').on('click', function() {
            $(this).closest('.menu-item').toggleClass('open');
        });

        // Toggle isActive property of menu item
        $menuWrapper.find('.menu-item .toggle-active-btn').on('click', function() {
            var $this = $(this),
                menuItemId = $this.closest('.menu-item').data('menuItemId'),
                url = getBaseUrl() + '/toggle-is-active?id=' + menuItemId;

            toggleLoadingSpinner(true);
            $.get(url, null, null, 'json').then(function(res) {
                if (res.status === 'success') {
                    updateInitialData(res.data);

                    getMenuItemElem(menuItemId).find('.toggle-active-btn:first').toggleClass('active');
                } else {
                    alert('An error occurred while changing isActive property of menu item');
                }
                toggleLoadingSpinner(false);
            }, function() {
                toggleLoadingSpinner(false);
                alert('An error occurred while changing isActive property of menu item');
            });
        });

        // Delete menu item
        $menuWrapper.find('.menu-item .delete-btn').on('click', function() {
            var $this = $(this),
                menuItemId = $this.closest('.menu-item').data('menuItemId'),
                url = getBaseUrl() + '/delete?id=' + menuItemId + '&json=1';

            if (confirm($this.data('confirm'))) {
                toggleLoadingSpinner(true);

                $.post(url, null, null, 'json').then(function(res) {
                    if (res.status === 'success') {
                        updateInitialData(res.data);

                        getMenuItemElem(menuItemId).remove();
                    }

                    toggleLoadingSpinner(false);
                }, function() {
                    toggleLoadingSpinner(false);
                    alert('An error occurred while deleting menu item');
                });
            }
        });

        // Move menu item to the left
        $menuWrapper.find('.move-menu-item.left').on('click', function() {
            var $this = $(this),
                $menuItem = $this.closest('.menu-item'),
                $parentItem = $menuItem.parent().closest('.menu-item'),
                menuItemId = $menuItem.data('menuItemId'),
                menuItem = findMenuItem($menuItem.data('menuItemId'), changedData),
                parentItem = findMenuItem($parentItem.data('menuItemId'), changedData),
                grandParentItem,
                menuItemIndex,
                parentItemIndex;

            if (!parentItem) {
                console.warn('no parent item');
                return;
            }

            grandParentItem = findMenuItemParent(parentItem.id, changedData);
            if (!grandParentItem) {
                console.warn('no grand parent item');
                return;
            }

            menuItemIndex = arrayObjectIndexOf(parentItem.items, menuItemId, 'id');
            if (menuItemIndex > -1) {
                parentItem.items.splice(menuItemIndex, 1);
            }

            parentItemIndex = arrayObjectIndexOf(grandParentItem.items, parentItem.id, 'id');
            if (parentItemIndex > -1) {
                grandParentItem.items.splice(parentItemIndex + 1, 0, menuItem);
            }

            refreshMenu(changedData);

            toggleOnMenuSortButtons(true);
        });

        // Move menu item to the right
        $menuWrapper.find('.move-menu-item.right').on('click', function() {
            var $this = $(this),
                $menuItem = $this.closest('.menu-item'),
                $targetItem = $menuItem.prev(),
                $parentItem = $menuItem.parent().closest('.menu-item'),
                menuItemId = $menuItem.data('menuItemId'),
                menuItem = findMenuItem($menuItem.data('menuItemId'), changedData),
                targetItem = findMenuItem($targetItem.data('menuItemId'), changedData),
                parentItem = findMenuItem($parentItem.data('menuItemId'), changedData),
                menuItemIndex;

            if (menuItem && targetItem) {
                saveState();

                if (!parentItem) {
                    parentItem = changedData;
                }

                // Remove from old place
                menuItemIndex = arrayObjectIndexOf(parentItem.items, menuItemId, 'id');
                if (menuItemIndex > -1) {
                    parentItem.items.splice(menuItemIndex, 1);
                }

                // Check if new parent contains items
                if (!Array.isArray(targetItem.items)) {
                    targetItem.items = [];
                }

                // Insert into new place
                targetItem.items.push(menuItem);

                // Expand destination subtree
                getMenuItemElem(targetItem.id).toggleClass('open', true);

                refreshMenu(changedData);

                toggleOnMenuSortButtons(true);
            }
        });

        // Cancel sorting
        $('#on-menu-sort-buttons .cancel').on('click', function() {
            refreshMenu(initialData);
            toggleOnMenuSortButtons(false);
        });

        // Save menu order
        $('#on-menu-sort-buttons .save-order').on('click', function() {
            toggleLoadingSpinner(true);

            saveOrder().then(function(res) {
                if (res.status === 'success') {
                    updateInitialData(res.data);

                    refreshMenu(initialData);
                } else {
                    alert('An error occurred while saving menu order');
                    console.warn(res.errors);
                }

                toggleLoadingSpinner(false);
                toggleOnMenuSortButtons(false);
            }, function() {
                alert('An error occurred while saving menu order');
                toggleLoadingSpinner(false);
                toggleOnMenuSortButtons(false);
            });
        });
    }

    function saveOrder() {
        var url = getBaseUrl() + '/save-order',
            data = [];

        $menuWrapper.find('.menu-item').each(function() {
            var $this = $(this);

            data.push({
                id: $this.data('menuItemId'),
                parentId: $this.parent().closest('.menu-item', '#menu-wapper').data('menuItemId')
            });
        });

        return $.post(url, { menuItems: data }, null, 'json');
    }

    function refreshMenu(menuData) {
        saveState();
        renderMenuTpl(menuData);
        bindSortable();
        bindButtons();
    }

    function init() {
        compileMenuTpl();

        try {
            updateInitialData(JSON.parse($('#menu-tree-data').html()));
        } catch(e) {
            updateInitialData([]);
        }

        refreshMenu(initialData);
    }

    init();
});