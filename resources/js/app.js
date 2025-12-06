import './bootstrap';

import Alpine from 'alpinejs';
import '@fortawesome/fontawesome-free/css/all.min.css';
import '@fortawesome/fontawesome-free/js/all.js';

window.Alpine = Alpine;

Alpine.start();

// Import Chart.js for charts
import Chart from 'chart.js/auto';
window.Chart = Chart;
