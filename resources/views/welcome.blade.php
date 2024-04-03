@extends('layouts.layout')

@section('title', 'Prodmais')

@section('content')
<main class="p-home-wrapper" id="home">

    <!-- <transition name="homeeffect">
        <div class="c-tips" v-if="showTips">
            <a class="u-skip" href="#aftertips">Pular dicas de pesquisa</a>



            <h4>Dicas de como pesquisar</h4>
            <p>Use _ para busca por radical. Exemplo: biblio_.</p>
            <p>Para buscas exatas, coloque entre "". Exemplo: "Direito civil"</p>
            <p>Por padrão, o sistema utiliza o operador booleano OR. Caso necessite deixar a busca mais específica,
                utilize
                o operador AND (em maiúscula).</p>

            <h4>Busca avançada</h4>
            <p>O botão <img class="c-manual-img__in-text" src="images/manual/btn_busca_avancada.png"
                    alt="botão alternar para busca avançada" height="28px" />, que se
                parece com uma seta apontando para baixo, permite fazer pesquisas com mais critérios, sendo eles,
                programa de pós-graduação, ID lattes do pesquisador, e período.</p>

            <h4>Consultando as categorias disponíveis</h4>
            <p>O botão <img class="c-manual-img__in-text" src="images/manual/btn_mostrar_pesquisa_categoria.png"
                    alt="botão mostrar persquisa por categoria" height="28px" /> lista as produções classificados
                por Programa de Pós-graduação, tipo de produção, tipo de vínculo e base de dados, entre outras.</p>

            <h4>Buscando o perfil de um pesquisador</h4>
            <p>É possível também obter perfis detalhados dos pesquisadores. Esta opção está na opção "Pesquisadores"
                <img class="c-manual-img__in-text" src="images/manual/btn_pesquisadores.png" alt="botão pesquisadores"
                    height="28px" />, no menu principal, no cabeçalho do Prodmais.
            </p>


            <span id="aftertips"></span>
        </div>
    </transition> -->

    <i class="i i-prodmais .p-home-gradient"></i>
    <h2 class="p-home-slogan .p-home-gradient">Prodmais dos pesquisadores vinculados a Programas de Pós-Graduação</h2>

    <div class="p-home-search">

        <form class="p-home-form" class="" action="{{url('works')}}" title="Pesquisa simples" method="get">

            <div class="c-searcher">
                <input id="mainseach" name="search" type="search" placeholder="Pesquise por palavra chave"
                    aria-label="Pesquisar">
                <button class="c-searcher__btn" type="submit" title="Buscar">
                    <i class="i i-lupa c-searcher__btn-ico"></i>
                </button>
            </div>

        </form>
    </div><!-- end p-home-search -->

    <!--     <button class="c-btn--tip p-home__tips-btn" @mouseover="showTips = true" @mouseleave="showTips = false"
        title="Mostrar dicas de pesquisa">
        <i class="i i-btn i-sm i-help"></i>
    </button>
    <a class="u-skip" href="#mainseach">Voltar à barra de pesquisa principal</a> -->

</main>
@stop