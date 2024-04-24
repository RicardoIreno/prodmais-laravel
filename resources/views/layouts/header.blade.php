<header class="siteheader">
    <div class="siteheader__content">

        <a href="{{url('/')}}">
            <i class="i i-prodmaisheader siteheader__logo"></i>
        </a>

        <div class="sitemenu-container">
            <!-- <a class="u-skip" href="#skipmenu">Pular menu principal</a> -->
            <input class="sitemenu-btn-check" type="checkbox" id="checkbox_toggle">
            <label class="sitemenu-btn-ico" for="checkbox_toggle">☰</label>
            <!-- <div class="u-skip" id="skipmenu"></div> -->

            <nav class="sitemenu" title="Menu do prodmais" aria-labelledby="Menu principal">

                <ul class="sitemenu-list">

                    <li class="sitemenu-item" title="Home">
                        <a class="sitemenu-link" href="{{url('/')}}" title="Home">
                            Home
                            <i class="i i-home sitemenu-ico"></i>
                        </a>
                    </li>

                    <li class=" sitemenu-item">
                        <a class="sitemenu-link" href="{{url('/')}}/people" title="Pesquisadores">
                            Pesquisadores
                            <i class="i i-aboutme sitemenu-ico"></i>
                        </a>
                    </li>

                    <li class=" sitemenu-item">
                        <a class="sitemenu-link" href="{{url('/')}}/works" title="Produções">
                            Produções
                            <i class="i i-category sitemenu-ico"></i>
                        </a>
                    </li>
                    <!-- <li class=" sitemenu-item">
                        <a class="sitemenu-link" href="ppgs.php" title="Programas de pós graduação">
                            PPGs
                            <i class="i i-ppg-logo sitemenu-ico"></i>
                        </a>
                    </li> -->
                    <li class=" sitemenu-item">
                        <a class="sitemenu-link" href="{{url('/')}}/projetos" title="Projetos de pesquisa">
                            Projetos de Pesquisa
                            <i class="i i-project sitemenu-ico"></i>
                        </a>
                    </li>

                    <!-- <li class=" sitemenu-item">
                        <a class="sitemenu-link" href="{{url('/')}}/upload" title="Upload">
                            Upload
                            <i class="i i-project sitemenu-ico"></i>
                        </a>
                    </li> -->

                    <li class="sitemenu-item">
                        <a class="sitemenu-link" href="{{url('/')}}/graficos" title="Gráficos">
                            Gráficos
                            <i class="i i-dashboard sitemenu-ico"></i>
                        </a>
                    </li>

                    <li class="sitemenu-item">
                        <a class="sitemenu-link" href="{{url('/')}}/sobre" title="Sobre o Prodmais">
                            Sobre
                            <i class="i i-about sitemenu-ico"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>