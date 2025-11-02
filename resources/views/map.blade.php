<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mapa - GeoApp</title>
    
    <link rel="stylesheet" href="https://js.arcgis.com/4.35/esri/themes/light/main.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
            font-family: 'Arial', sans-serif;
        }

        #mapView {
            width: 100%;
            height: 100vh;
        }

        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div id="mapView"></div>
    <div class="loading" id="loading">Carregando mapa...</div>

    <script src="https://js.arcgis.com/4.35/"></script>
    <script>
        require([
            "esri/Map",
            "esri/views/MapView",
            "esri/layers/GeoJSONLayer",
            "esri/widgets/Legend"
        ], function(Map, MapView, GeoJSONLayer, Legend) {
            
            const map = new Map({
                basemap: "topo-vector"
            });

            const view = new MapView({
                container: "mapView",
                map: map,
                center: [-47.8825, -15.7942], // Brasília
                zoom: 5
            });

            // Load layers from API
            fetch("{{ route('api.layers.index') }}")
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading').style.display = 'none';
                    
                    if (data.features && data.features.length > 0) {
                        // Create GeoJSON Layer from API data
                        const geojsonLayer = new GeoJSONLayer({
                            url: "{{ route('api.layers.index') }}",
                            copyright: "GeoApp",
                            opacity: 0.8
                        });

                        map.add(geojsonLayer);

                        // Add legend
                        const legend = new Legend({
                            view: view,
                            layerInfos: [{
                                layer: geojsonLayer,
                                title: "Camadas Geográficas"
                            }]
                        });

                        view.ui.add(legend, "bottom-right");

                        // Fit to extent of all layers
                        geojsonLayer.when(function() {
                            geojsonLayer.queryExtent().then(function(extent) {
                                view.goTo(extent);
                            });
                        });
                    } else {
                        console.log('Nenhuma camada encontrada');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar camadas:', error);
                    document.getElementById('loading').textContent = 'Erro ao carregar camadas';
                });
        });
    </script>
</body>
</html>
