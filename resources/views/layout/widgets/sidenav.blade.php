<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Core</div>
                <a class="nav-link" href="index.html">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <div class="sb-sidenav-menu-heading">Interface</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                      Layouts
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ route('landingLayoutGet') }}">Landing page </a>
                        <a class="nav-link" href="{{ route('ProdOneLayoutGet') }}">Product One Page </a>
                        <a class="nav-link" href="{{ route('AllCatLayoutGet') }}">All Categories Page</a>
                        <a class="nav-link" href="{{ route('ProdByCat') }}">Products By Category Page </a>
                        <a class="nav-link" href="{{ route('ProdByTag') }}">Product By Tag</a>
                        {{-- <a class="nav-link" href="layout-sidenav-light.html">Light Sidenav</a> --}}
                    </nav>
                </div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#configCollapse" aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                      Configs
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="configCollapse" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="{{ route('ShipmentConfig') }}">Shipment</a>
                        <a class="nav-link" href="{{ route('CurrencyConfig') }}">Currency</a>
                        <a class="nav-link" href="{{ route('MainConfig') }}">Main</a>

                        
                    </nav>
                </div>
            </div>
        </div>
    </nav>
</div>