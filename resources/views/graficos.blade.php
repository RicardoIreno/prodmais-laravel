@extends('layouts.layout')

@section('title', 'Prodmais - Gráficos')

@section('content')

<div class="c-wrapper-container">
    <div class="c-wrapper-paper">

        @if (
        $request->has('name')||
        $request->has('type')||
        $request->has('datePublished')||
        $request->has('author')||
        $request->has('about')||
        $request->has('isPartOf_name')||
        $request->has('releasedEvent')||
        $request->has('inLanguage')||
        $request->has('issn')||
        $request->has('sourceOrganization')||
        $request->has('publisher')
        )
        <div class="alert alert-light" role="alert">
            Filtro ativo: <br>
            @foreach ($request->all() as $key => $value)
            @if ($key != 'page')
            <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                @php
                if ($key == 'author') {
                $key_name = 'Autor';
                }
                if ($key == 'name') {
                $key_name = 'Título';
                }
                if ($key == 'about') {
                $key_name = 'Assunto';
                }
                if ($key == 'type') {
                $key_name = 'Tipo';
                }
                if ($key == 'datePublished') {
                $key_name = 'Ano de publicação';
                }
                if ($key == 'isPartOf_name') {
                $key_name = 'Publicação';
                }
                if ($key == 'releasedEvent') {
                $key_name = 'Nome do evento';
                }
                if ($key == 'inLanguage') {
                $key_name = 'Idioma';
                }
                if ($key == 'issn') {
                $key_name = 'ISSN';
                }
                if ($key == 'publisher') {
                $key_name = 'Editora';
                }
                if ($key == 'sourceOrganization') {
                $key_name = 'Instituição';
                }
                @endphp
                <a type="button" class="btn btn-outline-warning mb-1" href="/graficos">
                    {{ $key_name }}: {{ $value }} (X)
                </a>
            </div>
            <a href="{{ route('works.graficos') }}" class="btn btn-warning">Limpar todos os filtros</a>
            @endif
            @endforeach
        </div>
        @endif




        <div class="container text-center">
            <h2 class="t t-h1">Gráficos</h2>
            <div class="row">
                <div class="col">
                    <h3>Quantidade de obras por ano</h3>
                    <svg id="chart"></svg>
                </div>
                <div class="col">
                    <h3>Quantidade de obras por tipo</h3>
                    <svg id="chart2"></svg>
                </div>
            </div>

        </div>
    </div>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3-cloud/1.2.5/d3.layout.cloud.min.js"></script>
    <style>
        .y-label {
            font-size: 12px;
            text-decoration: none;
            fill: black;
        }

        .bar-label {
            font-size: 12px;
            text-decoration: none;
            fill: black;
        }

        .tag {
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 4px;
            background-color: #337ab7;
            color: #fff;
        }
    </style>
    <script>
        // Dados
        var data = @json($datePublishedData);

        // Configuração do gráfico
        var margin = {
            top: 20,
            right: 20,
            bottom: 30,
            left: 100
        };
        var width = 500;
        var height = 500;

        var svg = d3.select("#chart")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        // Ordenar os dados por year
        data.sort(function(a, b) {
            return d3.descending(a.year, b.year);
        });

        // Escala para o eixo X
        var xScale = d3.scaleLinear()
            .domain([0, d3.max(data, function(d) {
                return d.total;
            })])
            .range([0, width]);

        // Escala para o eixo Y
        var yScale = d3.scaleBand()
            .domain(data.map(function(d) {
                return d.year;
            }))
            .range([0, height])
            .padding(0.1);

        // Barras coloridas
        svg.selectAll(".bar")
            .data(data)
            .enter().append("rect")
            .attr("class", "bar")
            .attr("x", 0)
            .attr("y", function(d) {
                return yScale(d.year);
            })
            .attr("width", function(d) {
                return xScale(d.total);
            })
            .attr("height", yScale.bandwidth())
            .attr("fill", function(d, i) {
                // Definir uma cor com base no índice da barra
                var colorScale = d3.scaleSequential()
                    .domain([0, data.length - 1])
                    .interpolator(d3.interpolateRainbow);
                return colorScale(i);
            });

        // Rótulos externos das barras
        svg.selectAll(".bar-label")
            .data(data)
            .enter().append("text")
            .attr("class", "bar-label")
            .attr("x", function(d) {
                return xScale(d.total) + 20;
            })
            .attr("y", function(d) {
                return yScale(d.year) + yScale.bandwidth() / 2;
            })
            .attr("dy", "0.35em")
            .attr("text-anchor", "end")
            .attr("fill", "white")
            .text(function(d) {
                return d.total;
            });

        // Rótulos externos do eixo Y (year)
        svg.selectAll(".y-label")
            .data(data)
            .enter()
            .append("a")
            .attr("xlink:href", function(d) {
                return "graficos?datePublished=" + d.year;
            })
            .append("text")
            .attr("class", "y-label")
            .attr("x", -5)
            .attr("y", function(d) {
                return yScale(d.year) + yScale.bandwidth() / 2;
            })
            .attr("dy", "0.35em")
            .attr("text-anchor", "end")
            .text(function(d) {
                return d.year;
            });
    </script>



    <script>
        function wrap(text, width) {
            text.each(function() {
                var text = d3.select(this),
                    words = text.text().split(/\s+/).reverse(),
                    word,
                    line = [],
                    lineNumber = 0,
                    lineHeight = 1.1, // ems
                    x = text.attr("x"),
                    y = text.attr("y"),
                    dy = 0, //parseFloat(text.attr("dy")),
                    tspan = text.text(null)
                    .append("tspan")
                    .attr("x", x)
                    .attr("y", y)
                    .attr("dy", dy + "em");
                while (word = words.pop()) {
                    line.push(word);
                    tspan.text(line.join(" "));
                    if (tspan.node().getComputedTextLength() > width) {
                        line.pop();
                        tspan.text(line.join(" "));
                        line = [word];
                        tspan = text.append("tspan")
                            .attr("x", x)
                            .attr("y", y)
                            .attr("dy", ++lineNumber * lineHeight + dy + "em")
                            .text(word);
                    }
                }
            });
        }

        // Dados
        var data = @json($typeData);

        // Configuração do gráfico
        var margin = {
            top: 20,
            right: 20,
            bottom: 30,
            left: 150
        };
        var width = 400;
        var height = 500;

        var svg = d3.select("#chart2")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        // Ordenar os dados por type
        data.sort(function(a, b) {
            return d3.descending(a.type, b.type);
        });

        // Escala para o eixo X
        var xScale = d3.scaleLinear()
            .domain([0, d3.max(data, function(d) {
                return d.total;
            })])
            .range([0, width]);

        // Escala para o eixo Y
        var yScale = d3.scaleBand()
            .domain(data.map(function(d) {
                return d.type;
            }))
            .range([0, height])
            .padding(0.1);

        // Barras coloridas
        svg.selectAll(".bar")
            .data(data)
            .enter().append("rect")
            .attr("class", "bar")
            .attr("x", 0)
            .attr("y", function(d) {
                return yScale(d.type);
            })
            .attr("width", function(d) {
                return xScale(d.total);
            })
            .attr("height", yScale.bandwidth())
            .attr("fill", function(d, i) {
                // Definir uma cor com base no índice da barra
                var colorScale = d3.scaleSequential()
                    .domain([0, data.length - 1])
                    .interpolator(d3.interpolateRainbow);
                return colorScale(i);
            });

        // Rótulos externos das barras
        svg.selectAll(".bar-label")
            .data(data)
            .enter().append("text")
            .attr("class", "bar-label")
            .attr("x", function(d) {
                return xScale(d.total) + 20;
            })
            .attr("y", function(d) {
                return yScale(d.type) + yScale.bandwidth() / 2;
            })
            .attr("dy", "0.35em")
            .attr("text-anchor", "end")
            .attr("fill", "black")
            .text(function(d) {
                return d.total;
            });

        // Rótulos externos do eixo Y (type)
        svg.selectAll(".y-label")
            .data(data)
            .enter()
            .append("a")
            .attr("xlink:href", function(d) {
                return "graficos?type=" + d.type;
            })
            .append("text")
            .attr("class", "y-label")
            .attr("x", -5)
            .attr("y", function(d) {
                return yScale(d.type) + yScale.bandwidth() / 2;
            })
            .attr("dy", "0.35em")
            .attr("text-anchor", "end")
            .text(function(d) {
                return d.type;
            })
            .call(wrap, 150);
    </script>

</div>




@endsection