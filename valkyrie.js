/**
 * Collapsible tabs for Valkyrie
 */
/* eslint-disable no-jquery/no-global-selector */
$(function() {
    var $cactions = $('#p-cactions'),
        $tabContainer = $('#p-views ul'),
        $navBtn = $('#mobile-menu-btn'),
        $searchBtn = $('#mobile-search-btn'),
        $personalBtn = $('#mobile-personal-btn'),
        $moreBtn = $('#mobile-more-btn'),
        $navContainer = $('#mw-navigation #mw-panel'),
        $searchContainer = $('#p-search'),
        $personalContainer = $('#p-personal'),
        $moreContainer = $('#mobile-navigation'),
        initialCactionsWidth = function() {
            // HACK: This depends on a discouraged feature of jQuery width().
            // The #p-cactions element is generally hidden by default, but
            // the consumers of this function need to know the width that the
            // "More" menu would consume if it were visible. This means it
            // must not return 0 if hidden, but rather virtually render it
            // and compute its width, then hide it again. jQuery width() does
            // all that for us.
            var width = $cactions.width();
            initialCactionsWidth = function() {
                return width;
            };
            return width;
        };

    // Bind callback functions to animate our drop down menu in and out
    // and then call the collapsibleTabs function on the menu
    $tabContainer
        .on('beforeTabCollapse', function() {
            var expandedWidth;
            // If the dropdown was hidden, show it
            if ($cactions.hasClass('emptyPortlet')) {
                $cactions.removeClass('emptyPortlet');
                // Now that it is visible, force-render it virtually
                // to get its expanded width, then shrink it 1px before we
                // yield from JS (which means the expansion won't be visible).
                // Then animate from the 1px to the expanded width.
                expandedWidth = $cactions.width();
                // eslint-disable-next-line no-jquery/no-animate
                $cactions.find('h3')
                    .css('width', '1px')
                    .animate({ width: expandedWidth }, 'normal');
            }
        })
        .on('beforeTabExpand', function() {
            // If we're removing the last child node right now, hide the dropdown
            if ($cactions.find('li').length === 1) {
                // eslint-disable-next-line no-jquery/no-animate
                $cactions.find('h3').animate({ width: '1px' }, 'normal', function() {
                    $(this).attr('style', '')
                        .parent().addClass('emptyPortlet');
                });
            }
        })
        .collapsibleTabs({
            expandCondition: function(eleWidth) {
                // This looks a bit awkward because we're doing expensive queries as late
                // as possible.
                var distance = $.collapsibleTabs.calculateTabDistance();
                // If there are at least eleWidth + 1 pixels of free space, expand.
                // We add 1 because .width() will truncate fractional values but .offset() will not.
                if (distance >= eleWidth + 1) {
                    return true;
                } else {
                    // Maybe we can still expand? Account for the width of the "Actions" dropdown
                    // if the expansion would hide it.
                    if ($cactions.find('li').length === 1) {
                        return distance >= eleWidth + 1 - initialCactionsWidth();
                    } else {
                        return false;
                    }
                }
            },
            collapseCondition: function() {
                var collapsibleWidth = 0,
                    doCollapse = false;

                // This looks a bit awkward because we're doing expensive queries as late
                // as possible.
                // TODO: The dropdown itself should probably "fold" to just the down-arrow
                // (hiding the text) if it can't fit on the line?

                // Never collapse if there is no overlap.
                if ($.collapsibleTabs.calculateTabDistance() >= 0) {
                    return false;
                }

                // Always collapse if the "More" button is already shown.
                if (!$cactions.hasClass('emptyPortlet')) {
                    return true;
                }

                // If we reach here, this means:
                // 1. #p-cactions is currently empty and invisible (e.g. when logged out),
                // 2. and, there is at least one li.collapsible link in #p-views, as asserted
                //    by handleResize() before calling here. Such link exists e.g. as
                //    "View history" on articles, but generally not on special pages.
                // 3. and, the left-navigation and right-navigation are overlapping
                //    each other, e.g. when making the window very narrow, or if a gadget
                //    added a lot of tabs.
                $tabContainer.children('li.collapsible').each(function(index, element) {
                    collapsibleWidth += $(element).width();
                    if (collapsibleWidth > initialCactionsWidth()) {
                        // We've found one or more collapsible links that are wider
                        // than the "More" menu would be if it were made visible,
                        // which means it is worth doing a collapsing.
                        doCollapse = true;
                        // Stop this possibly expensive loop the moment the condition is met once.
                        return false;
                    }
                });
                return doCollapse;
            }
        });

    // 思源宋体懒加载
    if (!document.getElementById('noto_serif_sc')) {
        var fontTag = document.createElement('link');
        fontTag.rel = 'stylesheet';
        fontTag.type = 'text/css';
        fontTag.id = 'noto_serif_sc';
        fontTag.href = 'https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@400;500;700&display=swap';
        document.head.appendChild(fontTag);
    }

    function hideAll(buttonID) {
        // buttonID: nav: 1, search: 2, more: 3
        buttonID = buttonID || 0;
        if (buttonID !== 1) {
            $navContainer.slideUp();
            $navContainer.data('expand', false);
        }
        if (buttonID !== 2) {
            $searchContainer.hide();
            $searchContainer.data('expand', false);
        }
        if (buttonID !== 3) {
            $personalContainer.hide();
            $personalContainer.data('expand', false);
        }
        if (buttonID !== 4) {
            $moreContainer.hide();
            $moreContainer.data('expand', false);
        }
    }

    $navBtn.click(function() {
        hideAll(1);
        var isNavMenuShown = $navContainer.data('expand') || false;
        if (isNavMenuShown) {
            $navContainer.slideUp();
        } else {
            $navContainer.slideDown();
        }
        $navContainer.data('expand', !isNavMenuShown);
    })

    $searchBtn.click(function() {
        hideAll(2);
        var isSearchShown = $searchContainer.data('expand') || false;
        $searchContainer.toggle(!isSearchShown);
        $searchContainer.data('expand', !isSearchShown);
    })

    $personalBtn.click(function() {
        hideAll(3);
        var isPersonMenuShown = $personalContainer.data('expand') || false;
        $personalContainer.toggle(!isPersonMenuShown);
        $personalContainer.data('expand', !isPersonMenuShown);
    })

    $moreBtn.click(function() {
        hideAll(4);
        var isMoreMenuShown = $moreContainer.data('expand') || false;
        $moreContainer.toggle(!isMoreMenuShown);
        $moreContainer.data('expand', !isMoreMenuShown);
    })

    // esc press
    $(document).keyup(function(e) {
        if (e.keyCode === 27) {
            hideAll();
        }
    });

    $(window).on('resize', function() {
        var win = $(this); //this = window
        if (win.width() >= 720) {
            $navContainer.show();
            $searchContainer.show();
            $personalContainer.show();
            $moreContainer.hide();
            $moreContainer.data('expand', false);
        } else {
            var isNavMenuShown = $navContainer.data('expand') || false;
            var isSearchShown = $searchContainer.data('expand') || false;
            var isPersonMenuShown = $personalContainer.data('expand') || false;
            var isMoreMenuShown = $moreContainer.data('expand') || false;
            if (!isNavMenuShown) {
                $navContainer.hide();
            }
            if (!isSearchShown) {
                $searchContainer.hide();
            }
            if (!isPersonMenuShown) {
                $personalContainer.hide();
            }
            if (!isMoreMenuShown) {
                $moreContainer.hide();
            }
        }
    });
});
