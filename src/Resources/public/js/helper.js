/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */


opendxp.registerNS("opendxp.bundle.advancedObjectSearch.helper");
opendxp.bundle.advancedObjectSearch.helper = {

    rebuildEsSearchMenu: function() {

        var searchMenu = opendxp.globalmanager.get("layout_toolbar").searchMenu;

        var esSearchMenu = opendxp.globalmanager.get("bundle_advancedObjectSearch_menu");

        if(!esSearchMenu) {
            esSearchMenu = Ext.create('Ext.menu.Item', {
                text: t("bundle_advancedObjectSearch"),
                iconCls: "opendxp_bundle_nav_icon_advancedObjectSearch",
                hideOnClick: false,
                menu: {
                    cls: "opendxp_navigation_flyout",
                    shadow: false,
                    items: []
                }

            });
            searchMenu.add(esSearchMenu);

            opendxp.globalmanager.add("bundle_advancedObjectSearch_menu", esSearchMenu);
        }
        esSearchMenu.getMenu().removeAll();

        esSearchMenu.getMenu().add({
            text: t("bundle_advancedObjectSearch_new"),
            iconCls: "opendxp_bundle_nav_icon_advancedObjectSearch",
            handler: function () {
                var esSearch = new opendxp.bundle.advancedObjectSearch.searchConfigPanel();
                opendxp.globalmanager.add(esSearch.getTabId(), esSearch);
            }
        });
        esSearchMenu.getMenu().add({
            text: t("bundle_advancedObjectSearch_search"),
            iconCls: "opendxp_bundle_nav_icon_advancedObjectSearch",
            handler: function () {
                new opendxp.bundle.advancedObjectSearch.selector(opendxp.bundle.advancedObjectSearch.helper.openEsSearch);
            }
        });

        Ext.Ajax.request({
            url: "/admin/bundle/advanced-object-search/admin/load-short-cuts",
            method: "get",
            success: function (response) {
                var rdata = Ext.decode(response.responseText);

                if(rdata.entries && rdata.entries.length) {
                    esSearchMenu.getMenu().add("-");

                    for(var i = 0; i < rdata.entries.length; i++) {
                        var id = rdata.entries[i].id;
                        esSearchMenu.getMenu().add({
                            text: rdata.entries[i].name,
                            iconCls: "opendxp_bundle_nav_icon_advancedObjectSearch",
                            handler: function (id) {
                                opendxp.bundle.advancedObjectSearch.helper.openEsSearch(id);
                            }.bind(this, id)
                        });
                    }
                }

            }.bind(this)
        });
    },

    initializeStatusIcon: function() {

        var notificationMenu = opendxp.globalmanager.get("layout_toolbar")["notificationMenu"];

        if(notificationMenu) {
            var statusIcon = new Ext.menu.Item({
                text: t("bundle_advancedObjectSearch_updating_index"),
                iconCls: 'opendxp_bundle_nav_icon_advancedObjectSearch'
            });
            notificationMenu.add(statusIcon);
        }

        this.checkIndexStatus(statusIcon);
    },

    checkIndexStatus: function(statusIcon) {

        Ext.Ajax.request({
            url: "/admin/bundle/advanced-object-search/admin/check-index-status",
            method: "get",
            success: function (response) {
                var rdata = Ext.decode(response.responseText);

                if(rdata.indexUptodate === true) {
                    statusIcon.hide();
                } else {
                    statusIcon.show();
                }

                setTimeout(this.checkIndexStatus.bind(this, statusIcon), 60000);

            }.bind(this)
        });

    },

    openEsSearch: function(id, callback, filter) {
        Ext.Ajax.request({
            url: "/admin/bundle/advanced-object-search/admin/load-search",
            params: {
                id: id
            },
            method: "get",
            success: function (response) {
                var rdata = Ext.decode(response.responseText);

                if(rdata.gridConfig) {
                    rdata.gridConfig.predefinedFilter = filter;
                }

                var tabId = "opendxp_search_" + id;
                try {
                    opendxp.globalmanager.get(tabId).activate();
                }
                catch (e) {
                    var esSearch = new opendxp.bundle.advancedObjectSearch.searchConfigPanel(rdata);
                    opendxp.globalmanager.add(esSearch.getTabId(), esSearch);
                }

                if(callback) {
                    callback();
                }

            }.bind(this)
        });

    }

};
