import ApexCharts from 'apexcharts';
import { GeoJSON, Map, TileLayer } from 'leaflet';

import de from 'apexcharts/dist/locales/de.json';
import en from 'apexcharts/dist/locales/en.json';
import es from 'apexcharts/dist/locales/es.json';
import fr from 'apexcharts/dist/locales/fr.json';
import it from 'apexcharts/dist/locales/it.json';
import ja from 'apexcharts/dist/locales/ja.json';
import nl from 'apexcharts/dist/locales/nl.json';
import pl from 'apexcharts/dist/locales/pl.json';

import 'leaflet/dist/leaflet.css';

const locales = [de, en, es, fr, it, ja, nl, pl];

const mapElement = document.getElementById('map-region');
if (mapElement !== null) {
  const { region } = mapElement.dataset;

  const map = new Map(mapElement);

  const baselayer = new TileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }
  );
  map.addLayer(baselayer);

  fetch(`/api/region/${region}.geojson`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(response => response.json())
    .then(geojson => {
      const layer = new GeoJSON(geojson);
      map.addLayer(layer);

      map.fitBounds(layer.getBounds());
    });
}

const chartElement = document.getElementById('chart-stats');
if (chartElement !== null) {
  const lang = document.querySelector('html')?.lang;
  const { region, series1, series2 } = chartElement.dataset;

  const options = {
    chart: {
      stacked: true,
      locales,
      defaultLocale: typeof lang !== 'undefined' && locales.map(locale => locale.name).includes(lang) ? lang : 'en',
      toolbar: {
        show: false
      },
      type: 'bar',
      height: '100%',
      width: '100%',
      zoom: {
        enabled: false
      }
    },
    legend: {
      position: 'top'
    },
    xaxis: {
      type: 'datetime',
      max: new Date().getTime(),
    },
    noData: {
      text: 'Loading...'
    },
    series: []
  };

  const chart = new ApexCharts(chartElement, options);
  chart.render();

  fetch(`/api/region/${region}/count.json`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(response => response.json())
    .then(json => {
      const series: ApexAxisChartSeries = [
        {
          name: series1,
          data: Object.keys(json).map(key => {
            const datetime = new Date(key);
            datetime.setUTCHours(0);

            return { x: datetime.getTime(), y: (json[key].total - json[key].welcome) };
          })
        },
        {
          name: series2,
          data: Object.keys(json).map(key => {
            const datetime = new Date(key);
            datetime.setUTCHours(0);

            return { x: datetime.getTime(), y: json[key].welcome };
          })
        }
      ];

      chart.updateSeries(series);
    });
}
