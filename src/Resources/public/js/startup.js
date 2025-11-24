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


opendxp.registerNS("opendxp.bundle.advancedObjectSearch");

opendxp.bundle.advancedObjectSearch = Class.create({
    getClassName: function() {
        return "opendxp.plugin.advancedObjectSearch";
    },

    initialize: function() {
        document.addEventListener(opendxp.events.opendxpReady, this.onOpenDxpReady.bind(this));
        document.addEventListener(opendxp.events.onPerspectiveEditorLoadPermissions, this.onPerspectiveEditorLoadPermissions.bind(this));
    },

    onOpenDxpReady: function (e){
        var perspectiveCfg = opendxp.globalmanager.get("perspective");
        var user = opendxp.globalmanager.get("user");

        var searchMenu = opendxp.globalmanager.get("layout_toolbar").searchMenu;
        if(searchMenu && perspectiveCfg.inToolbar("search.advancedObjectSearch") && user.isAllowed("bundle_advancedsearch_search")) {
            opendxp.bundle.advancedObjectSearch.helper.rebuildEsSearchMenu();
            opendxp.bundle.advancedObjectSearch.helper.initializeStatusIcon();
        }
    },

    onPerspectiveEditorLoadPermissions: function (e) {
        let context = e.detail.context;
        let menu = e.detail.menu;
        let permissions = e.detail.permissions;

        if(context == 'toolbar' && menu == 'search' &&
            permissions[context][menu].indexOf('items.advancedObjectSearch') == -1) {
            permissions[context][menu].push('items.advancedObjectSearch');
        }
    }
});

var advancedObjectSearchPlugin = new opendxp.bundle.advancedObjectSearch();

