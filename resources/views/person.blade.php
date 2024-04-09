@extends('layouts.layout')

@section('title', 'Prodmais - Pesquisadores')

@section('content')

<main id="profile" class="c-wrapper-container">
    <div class="c-wrapper-paper">
        <div class="c-wrapper-inner">
            <div id="top"></div>
            <div class="p-profile-header">
                <div class="p-profile-header-one">

                    <div class="c-who-s">
                        <img class="c-who-s-pic"
                            src="https://servicosweb.cnpq.br/wspessoa/servletrecuperafoto?tipo=1&amp;bcv=true&amp;id=" />
                    </div>

                </div>
                <div class="p-profile-header-two">
                    <h1 class="t-h1">
                        {{ $id->name }}
                    </h1>

                    <hr class="c-line" />

                    <div class="p-profile-header-numbers">

                        <div class="d-icon-text u-mx-10">
                            <i class="i i-sm i-articlePublished" title="Trabalhos publicados"
                                alt="Trabalhos publicados"></i>
                            <span class="t">

                            </span>
                        </div>

                        <div class="d-icon-text">
                            <i class="i i-sm i-orientation" title="Orientações " alt="Orientações"></i>

                        </div>
                    </div>

                </div>
                <div class="p-profile-header-three">
                </div>
            </div>


            <div class="profile-tabs" onload="changeTab('1')">
                <div class="c-profmenu">
                    <button id="tab-btn-1" class="c-profmenu-btn" v-on:click="changeTab('1')" title="Sobre" alt="Sobre">
                        <i class="i i-sm i-aboutme c-profmenu-ico"></i>
                        <span class="c-profmenu-text">Sobre</span>
                    </button>

                    <button id=" tab-btn-2" class="c-profmenu-btn" v-on:click="changeTab('2')" title="Produção"
                        alt="Produção">
                        <i class="i i-sm i-prodsymbol c-profmenu-ico"></i>
                        <span class="c-profmenu-text">Produção</span>
                    </button>

                    <button id="tab-btn-3" class="c-profmenu-btn" v-on:click="changeTab('3')" title="Atuação"
                        alt="Atuação">
                        <i class="i i-sm i-working c-profmenu-ico"></i>
                        <span class="c-profmenu-text">Atuação</span>
                    </button>


                    <button id="tab-btn-4" class="c-profmenu-btn" v-on:click="changeTab('4')" title="Ensino"
                        alt="Ensino">
                        <i class="i i-sm i-teaching c-profmenu-ico"></i>
                        <span class="c-profmenu-text">Ensino</span>
                    </button>


                    <button id="tab-btn-5" class="c-profmenu-btn" v-on:click="changeTab('5')" title="Gestão"
                        alt="Gestão">
                        <div class="i i-sm i-managment c-profmenu-ico"></div>
                        <span class="c-profmenu-text">Gestão</span>
                    </button>
                    <button id="tab-btn-6" class="c-profmenu-btn" v-on:click="changeTab('6')" title="Pesquisa"
                        alt="Pesquisa">
                        <div class="i i-sm i-research c-profmenu-ico"></div>
                        <span class="c-profmenu-text">Pesquisa</span>
                    </button>
                </div><!-- end c-profmenu  -->
            </div> <!-- end profile-tabs -->

            <div class="c-wrapper-inner u-m-20">
                <transition name="tabeffect">
                    <div id="tab-one" class="c-tab-content" v-if="tabOpened == '1'">
                        <div class="t-justify">
                            <h3 class="t t-h3">Resumo</h3>
                            <p class="t">
                                {{ $id->resumoCVpt }}
                            </p>
                            <p class="t-right ty-light">Fonte: Lattes CNPq</p>
                        </div>
                        <div class="t-justify">
                            <h3 class="t t-h3">Resumo em Inglês</h3>
                            <p class="t">
                                {{ $id->resumoCVen }}
                            </p>
                            <p class="t-right ty-light">Fonte: Lattes CNPq</p>
                        </div>
                        <h3 class="t t-h3">Nomes em citações bibliográficas</h3>
                        <p class="t-prof"></p>
                        <hr class="c-line u-my-20" />
                        <h3 class="t t-h3">Exportar dados</h3>
                        <p><a href="tools/export_old.php?&format=bibtex&search=vinculo.lattes_id:{{ $id->id }}"
                                target="_blank" rel="nofollow">Exportar produção no formato BIBTEX</a></p>
                        <hr class="c-line u-my-20" />
                        <p class="t t-b">Perfis na web</p>
                        <div class="dh">
                            <?php if (!empty($profile['lattesID'])) : ?>
                            <a href="https://lattes.cnpq.br/{{ $id->id }}" target="_blank" rel="external">
                                <img class="c-socialicon" src="/inc/images/logos/academic/logo_lattes.svg" alt="Lattes"
                                    title="Lattes" />
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($profile['orcid_id'])) : ?>
                            <a href="{{ $id->orcid }}" target="_blank" rel="external">
                                <img class="c-socialicon" src="/inc/images/logos/academic/logo_research_id.svg"
                                    alt="ORCID" title="ORCID" />
                            </a>
                            <?php endif; ?>

                        </div>

                        <hr class="c-line u-my-20" />
                        <h3 class="t t-h3">Tags mais usadas</h3>

                        <hr class="c-line u-my-20" />

                        <div>
                            <h3 class="t t-h3">Idiomas</h3>



                        </div>


                        <hr class="c-line u-my-20" />
                        <h3 class="t t-h3">Formação</h3>

                        <!-- Livre Docência -->

                        <!-- Doutorado -->



                        <!-- Mestrado -->

                        <!-- Graduação -->

                    </div>
                </transition>
                <transition name="tabeffect">
                    <div id="tab-two" class="c-tab-content" v-if="tabOpened == '2'">
                        <div class="profile-pi">
                            <h3 class="t t-h3 u-mb-20">Produção</h3>

                        </div>
                    </div>
                </transition>
                <transition name="tabeffect">
                    <div id="tab-three" class="c-tab-content" v-if="tabOpened == '3'">
                        <h3 class="t t-h3 u-mb-20">Atuações</h3>

                    </div> <!-- end tab-three -->
                </transition>
                <transition name="tabeffect">
                    <div id="tab-four" class="c-tab-content" v-if="tabOpened == '4'">
                        <h3 class="t t-h3 u-mb-20">Ensino</h3>
                        <h3 class="t t-h3 u-mb-20">Orientações e supervisões</h3>




                    </div> <!-- end tab-four -->
                </transition>
                <transition name="tabeffect">
                    <div id="tab-five" class="c-tab-content" v-if="tabOpened == '5'">
                        <h3 class="t t-h3 u-mb-20">Gestão</h3>


                    </div>
                </transition>
                <transition name="tabeffect">
                    <div id="tab-six" class="c-tab-content" v-if="tabOpened == '6'">
                        <h3 class="t t-h3 u-mb-20">Pesquisa</h3>



                        <h3 class="t t-h3 u-mb-20">Outras atividades técnico científicas</h3>

                    </div>
                </transition>

            </div>

            <p class="t t-lastUpdate t-right">Atualização Lattes em {{ $id->lattesDataAtualizacao }}</p>
            <p class="t t-lastUpdate t-right">Processado em </p>

        </div>

</main>

@stop