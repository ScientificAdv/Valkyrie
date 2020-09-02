<?php
/**
 * Valkyrie - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Skins
 */

/**
 * QuickTemplate subclass for Valkyrie
 * @ingroup Skins
 */
class ValkyrieTemplate extends BaseTemplate {

	/**
	 * Outputs the entire contents of the HTML page
	 */
	public function execute() {
		$this->data['namespace_urls'] = $this->data['content_navigation']['namespaces'];
		$this->data['view_urls'] = $this->data['content_navigation']['views'];
		$this->data['action_urls'] = $this->data['content_navigation']['actions'];
		$this->data['variant_urls'] = $this->data['content_navigation']['variants'];

		// Move the watch/unwatch star outside of the collapsed "actions" menu to the main "views" menu
		if ( $this->config->get( 'ValkyrieUseIconWatch' ) ) {
			$mode = $this->getSkin()->getUser()->isWatched( $this->getSkin()->getRelevantTitle() )
				? 'unwatch'
				: 'watch';

			if ( isset( $this->data['action_urls'][$mode] ) ) {
				$this->data['view_urls'][$mode] = $this->data['action_urls'][$mode];
				unset( $this->data['action_urls'][$mode] );
			}
		}

		// Naming conventions for Mustache parameters:
		// - Prefix "is" for boolean values.
		// - Prefix "msg-" for interface messages.
		// - Prefix "page-" for data relating to the current page (e.g. Title, WikiPage, or OutputPage).
		// - Prefix "html-" for raw HTML (in front of other keys, if applicable).
		// - Conditional values are null if absent.
		$params = [
			'html-headelement' => $this->get( 'headelement', '' ),
			'html-sitenotice' => $this->get( 'sitenotice', null ),
			'html-indicators' => $this->getIndicators(),
			'page-langcode' => $this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode(),
			'page-isarticle' => (bool)$this->data['isarticle'],

			// Remember that the string '0' is a valid title.
			// From OutputPage::getPageTitle, via ::setPageTitle().
			'html-title' => $this->get( 'title', '' ),

			'html-prebodyhtml' => $this->get( 'prebodyhtml', '' ),
			'msg-tagline' => $this->getMsg( 'tagline' )->text(),
			// TODO: mediawiki/SkinTemplate should expose langCode and langDir properly.
			'html-userlangattributes' => $this->get( 'userlangattributes', '' ),
			// From OutputPage::getSubtitle()
			'html-subtitle' => $this->get( 'subtitle', '' ),

			// TODO: Use directly Skin::getUndeleteLink() directly.
			// Always returns string, cast to null if empty.
			'html-undelete' => $this->get( 'undelete', null ) ?: null,

			// From Skin::getNewtalks(). Always returns string, cast to null if empty.
			'html-newtalk' => $this->get( 'newtalk', '' ) ?: null,

			'msg-jumptonavigation' => $this->getMsg( 'valkyrie-jumptonavigation' )->text(),
			'msg-jumptosearch' => $this->getMsg( 'valkyrie-jumptosearch' )->text(),

			// Result of OutputPage::addHTML calls
			'html-bodycontent' => $this->get( 'bodycontent' ),

			'html-printfooter' => $this->get( 'printfooter', null ),
			'html-catlinks' => $this->get( 'catlinks', '' ),
			'html-dataAfterContent' => $this->get( 'dataAfterContent', '' ),
			// From MWDebug::getHTMLDebugLog (when $wgShowDebug is enabled)
			'html-debuglog' => $this->get( 'debughtml', '' ),
			// From BaseTemplate::getTrail (handles bottom JavaScript)
			'html-printtail' => $this->getTrail(),
		];

		// TODO: Convert the rest to Mustache
		ob_start();
		?>
		<div id="mw-navigation">
			<h2><?php $this->msg( 'navigation-heading' ) ?></h2>
			<div id="mw-head">
				<nav id="mw-mobile-head">
					<div class="mobile-nav-btn-group left-section">
						<button aria-label="导航" class="mobile-nav-btn" id="mobile-menu-btn">
							<svg width="24px" height="24px" viewBox="0 0 16 16" class="bi bi-list" fill="#fff" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
							</svg>
						</button>
					</div>
					<div class="mobile-nav-btn-group right-section">
						<button aria-label="<?php $this->msg( 'search' ) ?>" class="mobile-nav-btn" id="mobile-search-btn">
							<svg width="18px" height="18px" viewBox="0 0 16 16" class="bi bi-search" fill="#fff" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
								<path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
							</svg>
						</button>
						<button aria-label="<?php $this->msg( 'personaltools' ) ?>" class="mobile-nav-btn" id="mobile-personal-btn">
							<svg width="22px" height="22px" viewBox="0 0 16 16" class="bi bi-person" fill="#fff" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
							</svg>
						</button>
						<button aria-label="<?php $this->msg( 'valkyrie-more-actions' ) ?>" class="mobile-nav-btn" id="mobile-more-btn">
							<svg width="18px" height="18px" viewBox="0 0 16 16" class="bi bi-three-dots-vertical" fill="#fff" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
							</svg>
						</button>
					</div>
				</nav>
				<?php $this->renderNavigation( [ 'PERSONAL' ] ); ?>
				<div id="left-navigation">
					<?php $this->renderNavigation( [ 'NAMESPACES', 'VARIANTS' ] ); ?>
				</div>
				<div id="right-navigation">
					<?php $this->renderNavigation( [ 'VIEWS', 'ACTIONS', 'SEARCH' ] ); ?>
				</div>
				<div id="mobile-navigation">
					<?php $this->renderNavigation( [ 'MOBILE' ] ); ?>
				</div>
			</div>
			<div id="mw-panel">
				<div id="p-logo" role="banner"><a class="mw-wiki-logo" href="<?php
					echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] )
					?>"<?php
					echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) )
					?>></a></div>
				<?php $this->renderPortals( $this->data['sidebar'] ); ?>
			</div>
		</div>
		<?php Hooks::run( 'ValkyrieBeforeFooter' ); ?>
		<div id="footer" role="contentinfo"<?php $this->html( 'userlangattributes' ) ?>>
			<?php
			foreach ( $this->getFooterLinks() as $category => $links ) {
			?>
			<ul id="footer-<?php echo $category ?>">
				<?php
				foreach ( $links as $link ) {
				?>
				<li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
				<?php
				}
				?>
			</ul>
			<?php
			}
			?>
			<?php $footericons = $this->getFooterIcons( 'icononly' );
			if ( count( $footericons ) > 0 ) {
				?>
				<ul id="footer-icons" class="noprint">
					<?php
					foreach ( $footericons as $blockName => $footerIcons ) {
					?>
					<li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
						<?php
						foreach ( $footerIcons as $icon ) {
							echo $this->getSkin()->makeFooterIcon( $icon );
						}
						?>
					</li>
					<?php
					}
					?>
				</ul>
			<?php
			}
			?>
			<div style="clear: both;"></div>
		</div>
		<?php
		$params['html-unported'] = ob_get_contents();
		ob_end_clean();

		// Prepare and output the HTML response
		$templates = new TemplateParser( __DIR__ . '/templates' );
		echo $templates->processTemplate( 'index', $params );
	}

	/**
	 * Render a series of portals
	 *
	 * @param array $portals
	 */
	protected function renderPortals( array $portals ) {
		// Force the rendering of the following portals
		if ( !isset( $portals['TOOLBOX'] ) ) {
			$portals['TOOLBOX'] = true;
		}
		if ( !isset( $portals['LANGUAGES'] ) ) {
			$portals['LANGUAGES'] = true;
		}
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false ) {
				continue;
			}

			// Numeric strings gets an integer when set as key, cast back - T73639
			$name = (string)$name;

			switch ( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					Hooks::run( 'ValkyrieAfterToolbox' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] !== false ) {
						$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					}
					break;
				default:
					$this->renderPortal( $name, $content );
					break;
			}
		}
	}

	/**
	 * @param string $name
	 * @param array|string $content
	 * @param null|string $msg
	 * @param null|string|array $hook
	 */
	protected function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( $msg === null ) {
			$msg = $name;
		}
		$msgObj = $this->getMsg( $msg );
		$labelId = Sanitizer::escapeIdForAttribute( "p-$name-label" );
		?>
		<div class="portal" role="navigation" id="<?php
		echo htmlspecialchars( Sanitizer::escapeIdForAttribute( "p-$name" ) )
		?>"<?php
		echo Linker::tooltip( 'p-' . $name )
		?> aria-labelledby="<?php echo htmlspecialchars( $labelId ) ?>">
			<h3<?php $this->html( 'userlangattributes' ) ?> id="<?php echo htmlspecialchars( $labelId )
				?>"><?php
				echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg );
				?></h3>
			<div class="body">
				<?php
				if ( is_array( $content ) ) {
				?>
				<ul>
					<?php
					foreach ( $content as $key => $val ) {
						echo $this->makeListItem( $key, $val );
					}
					if ( $hook !== null ) {
						// Avoid PHP 7.1 warning
						$skin = $this;
						Hooks::run( $hook, [ &$skin, true ] );
					}
					?>
				</ul>
				<?php
				} else {
					// Allow raw HTML block to be defined by extensions
					echo $content;
				}

				$this->renderAfterPortlet( $name );
				?>
			</div>
		</div>
	<?php
	}

	/**
	 * Render one or more navigations elements by name, automatically reversed by css
	 * when UI is in RTL mode
	 *
	 * @param array $elements
	 */
	protected function renderNavigation( array $elements ) {
		// Render elements
		foreach ( $elements as $name => $element ) {
			switch ( $element ) {
				case 'NAMESPACES':
					?>
					<div id="p-namespaces" role="navigation" class="valkyrieTabs<?php
					if ( count( $this->data['namespace_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-namespaces-label">
						<h3 id="p-namespaces-label"><?php $this->msg( 'namespaces' ) ?></h3>
						<ul<?php $this->html( 'userlangattributes' ) ?>>
							<?php
							foreach ( $this->data['namespace_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item, [
									'valkyrie-wrap' => true,
								] );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'VARIANTS':
					?>
					<div id="p-variants" role="navigation" class="valkyrieMenu<?php
					if ( count( $this->data['variant_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-variants-label">
						<?php
						// Replace the label with the name of currently chosen variant, if any
						$variantLabel = $this->getMsg( 'variants' )->text();
						foreach ( $this->data['variant_urls'] as $item ) {
							if ( isset( $item['class'] ) && stripos( $item['class'], 'selected' ) !== false ) {
								$variantLabel = $item['text'];
								break;
							}
						}
						?>
						<input type="checkbox" class="valkyrieMenuCheckbox" aria-labelledby="p-variants-label" />
						<h3 id="p-variants-label">
							<span><?php echo htmlspecialchars( $variantLabel ) ?></span>
						</h3>
						<ul class="menu">
							<?php
							foreach ( $this->data['variant_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'VIEWS':
					?>
					<div id="p-views" role="navigation" class="valkyrieTabs<?php
					if ( count( $this->data['view_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-views-label">
						<h3 id="p-views-label"><?php $this->msg( 'views' ) ?></h3>
						<ul<?php $this->html( 'userlangattributes' ) ?>>
							<?php
							foreach ( $this->data['view_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item, [
									'valkyrie-wrap' => true,
									'valkyrie-collapsible' => true,
								] );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'ACTIONS':
					?>
					<div id="p-cactions" role="navigation" class="valkyrieMenu<?php
					if ( count( $this->data['action_urls'] ) == 0 ) {
						echo ' emptyPortlet';
					}
					?>" aria-labelledby="p-cactions-label">
						<input type="checkbox" class="valkyrieMenuCheckbox" aria-labelledby="p-cactions-label" />
						<h3 id="p-cactions-label"><span><?php
							$this->msg( 'valkyrie-more-actions' )
						?></span></h3>
						<ul class="menu"<?php $this->html( 'userlangattributes' ) ?>>
							<?php
							foreach ( $this->data['action_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'MOBILE':
					?>
					<div id="p-mobile-more" role="navigation" class="valkyrieMobileMore aria-labelledby="p-mobile-more-label">
						<h3 class="sr-only" id="p-mobile-more-label"><span><?php
							$this->msg( 'valkyrie-more-actions' )
						?></span></h3>
						<ul class="menu"<?php $this->html( 'userlangattributes' ) ?>>
							<!-- NAMESPACE -->
							<?php
							foreach ( $this->data['namespace_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item, [
									'valkyrie-wrap' => true,
								] );
							}
							?>
							<?php
								if ( count( $this->data['variant_urls'] ) != 0 ) {
								echo '<hr />';
								}
							?>
							<!-- VARIANTS -->
							<?php
							foreach ( $this->data['variant_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
							?>
							<?php
								if ( count( $this->data['view_urls'] ) != 0 ) {
								echo '<hr />';
								}
							?>
							<!-- VIEWS -->
							<?php
							foreach ( $this->data['view_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item, [
									'valkyrie-wrap' => true,
									'valkyrie-collapsible' => true,
								] );
							}
							?>
							<?php
								if ( count( $this->data['action_urls'] ) != 0 ) {
								echo '<hr />';
								}
							?>
							<!-- ACTIONS -->
							<?php
							foreach ( $this->data['action_urls'] as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'PERSONAL':
					?>
					<div id="p-personal" role="navigation"<?php
					if ( count( $this->data['personal_urls'] ) == 0 ) {
						echo ' class="emptyPortlet"';
					}
					?> aria-labelledby="p-personal-label">
						<h3 id="p-personal-label"><?php $this->msg( 'personaltools' ) ?></h3>
						<ul<?php $this->html( 'userlangattributes' ) ?>>
							<?php
							$notLoggedIn = '';

							if ( !$this->getSkin()->getUser()->isLoggedIn() &&
								User::groupHasPermission( '*', 'edit' )
							) {
								$notLoggedIn =
									Html::element( 'li',
										[ 'id' => 'pt-anonuserpage' ],
										$this->getMsg( 'notloggedin' )->text()
									);
							}

							$personalTools = $this->getPersonalTools();

							$langSelector = '';
							if ( array_key_exists( 'uls', $personalTools ) ) {
								$langSelector = $this->makeListItem( 'uls', $personalTools[ 'uls' ] );
								unset( $personalTools[ 'uls' ] );
							}

							echo $langSelector;
							echo $notLoggedIn;
							foreach ( $personalTools as $key => $item ) {
								echo $this->makeListItem( $key, $item );
							}
							?>
						</ul>
					</div>
					<?php
					break;
				case 'SEARCH':
					?>
					<div id="p-search" class="valkyrieSearch" role="search">
						<h3<?php $this->html( 'userlangattributes' ) ?>>
							<label for="searchInput"><?php $this->msg( 'search' ) ?></label>
						</h3>
						<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
							<div<?php echo $this->config->get( 'ValkyrieUseSimpleSearch' ) ? ' id="simpleSearch"' : '' ?>>
								<?php
								echo $this->makeSearchInput( [ 'id' => 'searchInput' ] );
								echo Html::hidden( 'title', $this->get( 'searchtitle' ) );
								/* We construct two buttons (for 'go' and 'fulltext' search modes),
								 * but only one will be visible and actionable at a time (they are
								 * overlaid on top of each other in CSS).
								 * * Browsers will use the 'fulltext' one by default (as it's the
								 *   first in tree-order), which is desirable when they are unable
								 *   to show search suggestions (either due to being broken or
								 *   having JavaScript turned off).
								 * * The mediawiki.searchSuggest module, after doing tests for the
								 *   broken browsers, removes the 'fulltext' button and handles
								 *   'fulltext' search itself; this will reveal the 'go' button and
								 *   cause it to be used.
								 */
								echo $this->makeSearchButton(
									'fulltext',
									[ 'id' => 'mw-searchButton', 'class' => 'searchButton mw-fallbackSearchButton' ]
								);
								echo $this->makeSearchButton(
									'go',
									[ 'id' => 'searchButton', 'class' => 'searchButton' ]
								);
								?>
							</div>
						</form>
					</div>
					<?php

					break;
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function makeLink( $key, $item, $options = [] ) {
		$html = parent::makeLink( $key, $item, $options );
		// Add an extra wrapper because our CSS is weird
		if ( isset( $options['valkyrie-wrap'] ) && $options['valkyrie-wrap'] ) {
			$html = Html::rawElement( 'span', [], $html );
		}
		return $html;
	}

	/**
	 * @inheritDoc
	 */
	public function makeListItem( $key, $item, $options = [] ) {
		// For fancy styling of watch/unwatch star
		if (
			$this->config->get( 'ValkyrieUseIconWatch' )
			&& ( $key === 'watch' || $key === 'unwatch' )
		) {
			$item['class'] = rtrim( 'icon ' . $item['class'], ' ' );
			$item['primary'] = true;
		}

		// Add CSS class 'collapsible' to links which are not marked as "primary"
		if (
			isset( $options['valkyrie-collapsible'] ) && $options['valkyrie-collapsible'] ) {
			$item['class'] = rtrim( 'collapsible ' . $item['class'], ' ' );
		}

		return parent::makeListItem( $key, $item, $options );
	}
}
