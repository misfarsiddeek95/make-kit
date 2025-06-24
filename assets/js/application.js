'use strict';

(function ($) {
  var App = {
    // CSS classes
    CssClasses: {
      CUSTOM_SCROLLBAR: 'custom-scrollbar',
      LAYOUT: 'layout',
      LAYOUT_MOBILE: 'layout-mobile',
      LAYOUT_TABLET: 'layout-tablet',
      LAYOUT_DESKTOP: 'layout-desktop',
      LAYOUT_LEFT_SIDEBAR_COLLAPSED: 'layout-left-sidebar-collapsed',
      LAYOUT_LEFT_SIDEBAR_OPENED: 'layout-left-sidebar-opened',
      LAYOUT_RIGHT_SIDEBAR_OPENED: 'layout-right-sidebar-opened',
      LEFT_SIDEBAR_TOGGLE: 'left-sidebar-toggle',
      LEFT_SIDEBAR_COLLAPSE: 'left-sidebar-collapse',
      OVERLAY: 'site-overlay',
      RIGHT_SIDEBAR: 'site-right-sidebar',
      RIGHT_SIDEBAR_TOGGLE: 'right-sidebar-toggle',
      SIDEBAR_MENU: 'sidebar-menu',
      SIDEBAR_SUBMENU: 'sidebar-submenu'
    },

    // Options
    Options: {
      CUSTOM_SCROLLBAR: {
        distance: '4px',
        opacity: '0.2',
        height: '100%',
        size: '4px',
        touchScrollStep: 50,
        wheelStep: 10,
        width: '100%',
        barClass: 'custom-scrollbar-bar',
        railClass: 'custom-scrollbar-rail',
        wrapperClass: 'custom-scrollable-wrapper'
      },
      LEFT_SIDEBAR_MENU: {
        activeClass: 'open',
        collapseClass: 'collapse',
        collapseInClass: 'in',
        collapsingClass: 'collapsing'
      }
    },

    // Init
    init: function init() {
      this.$document = $(document);
      this.$layout = $('.' + this.CssClasses.LAYOUT);
      this.$leftSidebarMenu = $('.' + this.CssClasses.SIDEBAR_MENU);
      this.$leftSidebarToggle = $('.' + this.CssClasses.LEFT_SIDEBAR_TOGGLE);
      this.$rightSidebarToggle = $('.' + this.CssClasses.RIGHT_SIDEBAR_TOGGLE);
      this.$leftSidebarCollapse = $('.' + this.CssClasses.LEFT_SIDEBAR_COLLAPSE);
      this.$overlay = $('.' + this.CssClasses.OVERLAY);
      this.$scrollableArea = $('.' + this.CssClasses.CUSTOM_SCROLLBAR);
      this.mediaQueryListMobile = window.matchMedia('(max-width: 767px)');
      this.mediaQueryListTablet = window.matchMedia('(min-width: 768px) and (max-width: 991px)');
      this.mediaQueryListDesktop = window.matchMedia('(min-width: 992px)');

      this.initPlugins();
      this.bindEvents();
      this.handleMediaQueryChangeMobile();
      this.handleMediaQueryChangeTablet();
      this.handleMediaQueryChangeDesktop();
      this.activeMenuItem();
      this.sidebarChat();
    },

    // Bind events
    bindEvents: function bindEvents() {
      this.$leftSidebarCollapse.on('click', this.handleLeftSidebarCollapse.bind(this));
      this.$leftSidebarToggle.on('click', this.handleLeftSidebarToggle.bind(this));
      this.$overlay.on('click', this.handleLeftSidebarToggle.bind(this));
      this.$rightSidebarToggle.on('click', this.handleRightSidebarToggle.bind(this));
      this.$document.on('mouseup', this.handleOutsideClick.bind(this));
      this.mediaQueryListMobile.addListener(this.handleMediaQueryChangeMobile.bind(this));
      this.mediaQueryListTablet.addListener(this.handleMediaQueryChangeTablet.bind(this));
      this.mediaQueryListDesktop.addListener(this.handleMediaQueryChangeDesktop.bind(this));
    },

    // Right sidebar toggle
    handleRightSidebarToggle: function handleRightSidebarToggle(evt) {
      if (!this.$layout.hasClass(this.CssClasses.LAYOUT_RIGHT_SIDEBAR_OPENED)) {
        this.openRightSidebar();
      } else {
        this.closeRightSidebar();
      }
      evt.preventDefault();
    },
    openRightSidebar: function openRightSidebar() {
      this.$layout.addClass(this.CssClasses.LAYOUT_RIGHT_SIDEBAR_OPENED);
    },
    closeRightSidebar: function closeRightSidebar() {
      this.$layout.removeClass(this.CssClasses.LAYOUT_RIGHT_SIDEBAR_OPENED);
    },

    // Hide right sidebar on outside click
    handleOutsideClick: function handleOutsideClick(evt) {
      var container = $('.' + this.CssClasses.RIGHT_SIDEBAR + ', .' + this.CssClasses.RIGHT_SIDEBAR_TOGGLE);
      if (!container.is(evt.target) && container.has(evt.target).length === 0) {
        this.$layout.removeClass(this.CssClasses.LAYOUT_RIGHT_SIDEBAR_OPENED);
      }
    },

    // Left sidebar toggle
    handleLeftSidebarToggle: function handleLeftSidebarToggle(evt) {
      if (!this.$layout.hasClass(this.CssClasses.LAYOUT_LEFT_SIDEBAR_OPENED)) {
        this.openLeftSidebar();
      } else {
        this.closeLeftSidebar();
      }
      evt.preventDefault();
    },
    openLeftSidebar: function openLeftSidebar() {
      this.$layout.addClass(this.CssClasses.LAYOUT_LEFT_SIDEBAR_OPENED);
    },
    closeLeftSidebar: function closeLeftSidebar() {
      this.$layout.removeClass(this.CssClasses.LAYOUT_LEFT_SIDEBAR_OPENED);
    },

    // Left sidebar collapse
    handleLeftSidebarCollapse: function handleLeftSidebarCollapse(evt) {
      if (!this.$layout.hasClass(this.CssClasses.LAYOUT_LEFT_SIDEBAR_COLLAPSED)) {
        this.collapseLeftSidebar();
      } else {
        this.expandLeftSidebar();
      }
      evt.preventDefault();
    },
    collapseLeftSidebar: function collapseLeftSidebar() {
      this.$layout.addClass(this.CssClasses.LAYOUT_LEFT_SIDEBAR_COLLAPSED);
    },
    expandLeftSidebar: function expandLeftSidebar() {
      this.$layout.removeClass(this.CssClasses.LAYOUT_LEFT_SIDEBAR_COLLAPSED);
    },

    // Media query changing
    handleMediaQueryChangeMobile: function handleMediaQueryChangeMobile() {
      if (this.mediaQueryListMobile.matches) {
        this.$layout.addClass(this.CssClasses.LAYOUT_MOBILE);
        this.$layout.removeClass(this.CssClasses.LAYOUT_TABLET);
        this.$layout.removeClass(this.CssClasses.LAYOUT_DESKTOP);
      }
    },
    handleMediaQueryChangeTablet: function handleMediaQueryChangeTablet() {
      if (this.mediaQueryListTablet.matches) {
        this.$layout.addClass(this.CssClasses.LAYOUT_TABLET);
        this.$layout.removeClass(this.CssClasses.LAYOUT_MOBILE);
        this.$layout.removeClass(this.CssClasses.LAYOUT_DESKTOP);
      }
    },
    handleMediaQueryChangeDesktop: function handleMediaQueryChangeDesktop() {
      if (this.mediaQueryListDesktop.matches) {
        this.$layout.addClass(this.CssClasses.LAYOUT_DESKTOP);
        this.$layout.removeClass(this.CssClasses.LAYOUT_MOBILE);
        this.$layout.removeClass(this.CssClasses.LAYOUT_TABLET);
      }
    },

    // Active menu item
    activeMenuItem: function activeMenuItem() {
      $('.' + this.CssClasses.SIDEBAR_MENU + ' ul li').each(function () {
        if ($(this).hasClass('active')) {
          $(this).closest('ul').addClass('in');
          $(this).closest('ul').closest('li').addClass('open');
        }
      });
    },

    // Sidebar chat toggle
    sidebarChat: function sidebarChat() {
      $('.sidebar-chat a, .sidebar-chat-window a').on('click', function () {
        $('.sidebar-chat').toggle();
        $('.sidebar-chat-window').toggle();
      });
    },

    // Plugins
    initPlugins: function initPlugins() {
      this.metisMenu();
      this.popover();
      this.slimScroll();
      this.switchery();
      this.tooltip();
      return this;
    },
    metisMenu: function metisMenu() {
      var options = this.Options.LEFT_SIDEBAR_MENU;
      this.$leftSidebarMenu.metisMenu(options);
      return this;
    },
    popover: function popover() {
      $('[data-toggle="popover"]').popover();
    },
    slimScroll: function slimScroll() {
      var options = this.Options.CUSTOM_SCROLLBAR;
      this.$scrollableArea.slimScroll(options);
      $('.custom-scrollbar-bar').hide();
      return this;
    },
    switchery: function switchery() {
      $('.js-switch').each(function () {
        new Switchery($(this)[0], $(this).data());
      });
    },
    tooltip: function tooltip() {
      $('[data-toggle="tooltip"]').tooltip();
    }
  };
  App.init();
})(jQuery);
