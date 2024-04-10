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
                            <a href="https://lattes.cnpq.br/{{ $id->id }}" target="_blank" rel="external">
                                <img class="c-socialicon" src="{{url('/')}}/images/logos/logo_lattes.svg" alt="Lattes"
                                    title="Lattes" />
                            </a>

                            <a href="{{ $id->orcid }}" target="_blank" rel="external">
                                <img class="c-socialicon" src="{{url('/')}}/images/logos/logo_orcid.svg" alt="ORCID"
                                    title="ORCID" />
                            </a>
                        </div>

                        <hr class="c-line u-my-20" />
                        <h3 class="t t-h3">Tags mais usadas</h3>

                        <hr class="c-line u-my-20" />

                        <div>
                            <h3 class="t t-h3">Idiomas</h3>
                            @foreach ($id->idiomas['IDIOMA'] as $idioma)
                            <div class="s-list">
                                <div class="s-list-content">
                                    <p class="t t-b">{{ $idioma['@attributes']["DESCRICAO-DO-IDIOMA"] }}</p>
                                    <p class="t u-mb-05">
                                        Compreende {{ $idioma['@attributes']["PROFICIENCIA-DE-COMPREENSAO"]  }},
                                        Fala {{ $idioma['@attributes']["PROFICIENCIA-DE-FALA"]  }},
                                        Lê {{ $idioma['@attributes']["PROFICIENCIA-DE-LEITURA"]  }},
                                        Escreve {{ $idioma['@attributes']["PROFICIENCIA-DE-ESCRITA"]  }}
                                    </p>
                                </div>
                            </div>
                            @endforeach

                        </div>


                        <hr class="c-line u-my-20" />
                        <h3 class="t t-h3">Formação</h3>



                        <!-- Livre Docência -->

                        <!-- Doutorado -->

                        <!-- Mestrado Profissionalizante -->


                        @if(isset($id->formacao['MESTRADO-PROFISSIONALIZANTE']))

                        <div class="s-list">
                            <div class="s-list-bullet"><i title="formation" class="i i-academic s-list-ico"></i></div>
                            <div class="s-list-content">
                                @if($id->formacao['MESTRADO-PROFISSIONALIZANTE']['@attributes']['STATUS-DO-CURSO'] ==
                                'INCOMPLETO')
                                <p class="t t-b">Status do Curso:
                                    {{ $id->formacao['MESTRADO-PROFISSIONALIZANTE']['@attributes']['STATUS-DO-CURSO'] }}
                                </p>
                                @endif
                                <p class="t t-b">Mestrado Profissionalizante em
                                    {{ $id->formacao['MESTRADO-PROFISSIONALIZANTE']['@attributes']['NOME-CURSO'] }}
                                </p>
                                <p></p>
                                @if($id->formacao['MESTRADO-PROFISSIONALIZANTE']['@attributes']['TITULO-DA-DISSERTACAO-TESE']
                                != '')
                                <p class="ty">
                                    Título da dissertação:
                                    {{ $id->formacao['MESTRADO-PROFISSIONALIZANTE']['@attributes']['TITULO-DA-DISSERTACAO-TESE'] }}
                                </p>
                                @endif
                                <p class="t t-gray"></p>
                                <p class="t t-gray"></p>
                                <p class="t t-gray">Orientação:
                                    {{ $id->formacao['MESTRADO-PROFISSIONALIZANTE']['@attributes']['NOME-COMPLETO-DO-ORIENTADOR'] }}
                                </p>
                                <p class="t t-gray">
                                    {{ $id->formacao['MESTRADO-PROFISSIONALIZANTE']['@attributes']['NOME-INSTITUICAO'] }}
                                </p>
                                <ul class="s-list-tags">
                                    <p class="t t-gray"></p>
                                    <p class="t t-gray">
                                        {{ $id->formacao['MESTRADO-PROFISSIONALIZANTE']['@attributes']['ANO-DE-INICIO'] }}
                                        a
                                        {{ $id->formacao['MESTRADO-PROFISSIONALIZANTE']['@attributes']['ANO-DE-CONCLUSAO'] }}
                                    </p>
                                </ul>
                            </div>
                        </div>
                        @endif


                        <!-- Mestrado -->

                        <!-- Graduação -->


                        @if(isset($id->formacao['GRADUACAO']))

                        @foreach ($id->formacao['GRADUACAO'] as $graduacao)


                        <div class="s-list">
                            <div class="s-list-bullet"><i title="formation" class="i i-academic s-list-ico"></i></div>
                            <div class="s-list-content">
                                @if($graduacao['@attributes']['STATUS-DO-CURSO'] ==
                                'INCOMPLETO')
                                <p class="t t-b">Status do Curso:
                                    {{ $graduacao['@attributes']['STATUS-DO-CURSO'] }}
                                </p>
                                @endif
                                <p class="t t-b">Graduação em
                                    {{ $graduacao['@attributes']['NOME-CURSO'] }}
                                </p>
                                <p></p>
                                @if($graduacao['@attributes']['TITULO-DO-TRABALHO-DE-CONCLUSAO-DE-CURSO']
                                != '')
                                <p class="ty">
                                    Título do Trabalho de Conclusão de Curso:
                                    {{ $graduacao['@attributes']['TITULO-DO-TRABALHO-DE-CONCLUSAO-DE-CURSO'] }}
                                </p>
                                @endif
                                <p class="t t-gray"></p>
                                <p class="t t-gray"></p>
                                @if($graduacao['@attributes']['NOME-DO-ORIENTADOR']
                                != '')
                                <p class="t t-gray">Orientação:
                                    {{ $graduacao['@attributes']['NOME-DO-ORIENTADOR'] }}
                                </p>
                                @endif
                                <p class="t t-gray">
                                    {{ $graduacao['@attributes']['NOME-INSTITUICAO'] }}
                                </p>
                                <ul class="s-list-tags">
                                    <p class="t t-gray"></p>
                                    <p class="t t-gray">
                                        {{ $graduacao['@attributes']['ANO-DE-INICIO'] }}
                                        a
                                        {{ $graduacao['@attributes']['ANO-DE-CONCLUSAO'] }}
                                    </p>
                                </ul>
                            </div>
                        </div>
                        @endforeach

                        @endif


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