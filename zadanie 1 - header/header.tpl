{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{$headerTopName = 'header-top'}
{$headerBottomName = 'header-bottom'}

{block name='header_banner'}
  <div class="header__banner">
    {hook h='displayBanner'}
  </div>
{/block}

{block name='header_nav'}
  <nav class="{$headerTopName} border-bottom py-2 bg-white">
    <div class="container-fluid custom-header-container d-none d-md-flex justify-content-between align-items-center">
      <div class="header-top__links d-flex align-items-center gap-2 text-muted">
        <a href="#" class="text-dark text-decoration-underline">Centrum pomocy</a>
        <span class="px-1 text-secondary">/</span>
        <a href="#" class="text-dark text-decoration-underline">Strefa wiedzy</a>
        <span class="px-1 text-secondary">/</span>
        <a href="#" class="text-dark text-decoration-underline">Kontakt</a>
      </div>
      <div class="header-top__text fw-bold text-dark text-uppercase small">
        Lorem ipsum
      </div>
    </div>
  </nav>
{/block}

{block name='header_bottom'}
  <div class="{$headerBottomName} main-header-black-borders bg-white">
    <div class="container-fluid custom-header-container h-100 position-relative">
      <div class="row h-100 align-items-stretch g-0">
        
        <div class="col-md-3 d-flex align-items-center logo py-3">
          {if $shop.logo_details}
            {if $page.page_name == 'index'}<h1 class="{$headerBottomName}__h1 mb-0">{/if}
              {renderLogo}
            {if $page.page_name == 'index'}</h1>{/if}
          {else}
            <span class="logo-text-style">LOGO</span>
          {/if}
        </div>

        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center">
          <form method="get" action="{$urls.pages.search}" class="custom-search-form d-flex align-items-center">
            <input type="hidden" name="controller" value="search">
            <input type="text" name="s" placeholder="Wpisz nazwę lub kod produktu">
            <button type="submit" class="d-flex align-items-center">
              <span class="search-btn-text">Szukaj</span>
              <span class="material-icons search-icon">search</span>
            </button>
          </form>
        </div>

        <div class="col-md-3 d-none d-md-flex justify-content-end align-items-stretch">
          <div class="d-flex align-items-stretch header-icons-grid">
            
            <div class="custom-header-btn no-border">
              <a href="#">
                <span class="material-icons">favorite_border</span>
                <span>Ulubione</span>
              </a>
            </div>

            <div class="custom-header-btn">
              <a href="{$urls.pages.my_account}">
                <span class="material-icons">person_outline</span>
                <span>Panel</span>
              </a>
            </div>

            <div class="custom-header-btn">
              <a href="{$urls.pages.cart}?action=show">
                <span class="material-icons">shopping_bag</span>
                <span>Koszyk</span>
              </a>
            </div>

          </div>
        </div>

        <div class="d-md-none d-flex align-items-center justify-content-end gap-3 w-100 pe-3 position-absolute start-0 top-50 translate-y-middle" style="pointer-events: none;">
          <div class="search__mobile d-flex col-auto" style="pointer-events: auto;">
            <a href="#" role="button" data-bs-toggle="offcanvas" data-bs-target="#searchCanvas">
              <span class="material-icons text-dark">search</span>
            </a>
          </div>
          <div id="_mobile_user_info" class="d-flex col-auto" style="pointer-events: auto;">
            <i class="material-icons text-dark">&#xE7FD;</i>
          </div>
          <div id="_mobile_cart" class="d-flex col-auto" style="pointer-events: auto;">
            <i class="material-icons text-dark">shopping_cart</i>
            <span class="badge bg-dark">{$cart.products_count}</span>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="search__offcanvas js-search-offcanvas offcanvas offcanvas-top h-auto d-md-none" data-bs-backdrop="false" data-bs-scroll="true" tabindex="-1" id="searchCanvas">
    <div class="offcanvas-header">
      <div id="_mobile_search" class="search__container"></div>
      <button type="button" class="btn-close text-reset ms-1" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
  </div>

  {hook h='displayNavFullWidth'}
{/block}