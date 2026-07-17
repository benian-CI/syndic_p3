import * as Turbo from '@hotwired/turbo';
import Chart from 'chart.js/auto';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

Turbo.start();

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

const DEFAULT_MAP_CENTER = [5.3364, -4.0267]; // Abidjan, Rive Gauche

const charts = {
    monthly: null,
    annual: null,
};

const maps = {
    picker: null,
    overview: null,
};

function closeModal() {
    const modal = document.getElementById('modal');
    if (modal) {
        modal.innerHTML = '';
    }
}

function closeSidebar() {
    document.querySelector('aside')?.classList.remove('open');
    document.querySelector('.backdrop')?.classList.remove('show');
    document.body.style.overflow = '';
}

function initModalControls() {
    document.querySelectorAll('[data-modal-url]').forEach((link) => {
        if (link.dataset.modalUrlBound) {
            return;
        }

        link.dataset.modalUrlBound = '1';
        link.addEventListener('click', async (event) => {
            const modal = document.getElementById('modal');

            if (!modal) {
                return;
            }

            event.preventDefault();

            try {
                const response = await fetch(link.href, {
                    headers: {
                        Accept: 'text/html',
                        'Turbo-Frame': 'modal',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    window.location.href = link.href;
                    return;
                }

                const html = await response.text();
                const documentFragment = new DOMParser().parseFromString(html, 'text/html');
                const incomingFrame = documentFragment.querySelector('turbo-frame#modal');

                modal.innerHTML = incomingFrame ? incomingFrame.innerHTML : html;
                initModalControls();
            } catch {
                window.location.href = link.href;
            }
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach((button) => {
        if (button.dataset.modalCloseBound) {
            return;
        }

        button.dataset.modalCloseBound = '1';
        button.addEventListener('click', closeModal);
    });
}

function initSidebar() {
    const toggle = document.querySelector('.menu-toggle');
    const closeBtn = document.querySelector('.close-menu');
    const backdrop = document.querySelector('.backdrop');
    const aside = document.querySelector('aside');

    if (!toggle || toggle.dataset.sidebarBound) {
        return;
    }

    toggle.dataset.sidebarBound = '1';

    function openSidebar() {
        aside?.classList.add('open');
        backdrop?.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    toggle.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    backdrop?.addEventListener('click', closeSidebar);
}

function initDeleteConfirm() {
    document.querySelectorAll('form.confirm-delete').forEach((form) => {
        if (form.dataset.deleteBound) {
            return;
        }

        form.dataset.deleteBound = '1';
        form.addEventListener('submit', (event) => {
            if (!confirm('Voulez-vous vraiment supprimer cet élément ? Cette action est irréversible.')) {
                event.preventDefault();
            }
        });
    });
}

function destroyCharts() {
    charts.monthly?.destroy();
    charts.annual?.destroy();
    charts.monthly = null;
    charts.annual = null;
}

function initCharts(data) {
    destroyCharts();

    if (!data) {
        return;
    }

    const fmt = (val) => new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XOF',
        maximumFractionDigits: 0,
    }).format(val);

    const fmtCompact = (val) => new Intl.NumberFormat('fr-FR', {
        notation: 'compact',
        maximumFractionDigits: 1,
    }).format(val);

    Chart.defaults.font.family = "'Instrument Sans', ui-sans-serif, system-ui, sans-serif";
    Chart.defaults.font.size = 12;

    const pieCanvas = document.getElementById('monthlyChart');
    if (pieCanvas) {
        charts.monthly = new Chart(pieCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Cotisations', 'Dépenses'],
                datasets: [{
                    data: [data.contributionsThisMonth, data.expensesThisMonth],
                    backgroundColor: ['#1b4da3', '#dc2626'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true, pointStyleWidth: 10, color: '#667085' },
                    },
                    tooltip: {
                        callbacks: { label: (ctx) => ` ${ctx.label} : ${fmt(ctx.parsed)}` },
                    },
                },
            },
        });
    }

    const barCanvas = document.getElementById('annualChart');
    if (barCanvas) {
        const monthlyContributions = data.monthlyContributions
            ?? [data.contributionsThisYear, ...Array(11).fill(0)];
        const monthlyExpenses = data.monthlyExpenses
            ?? [data.expensesThisYear, ...Array(11).fill(0)];

        charts.annual = new Chart(barCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [
                    {
                        label: 'Cotisations',
                        data: monthlyContributions,
                        backgroundColor: 'rgba(27, 77, 163, 0.85)',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                    {
                        label: 'Dépenses',
                        data: monthlyExpenses,
                        backgroundColor: 'rgba(220, 38, 38, 0.75)',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, pointStyleWidth: 10, color: '#667085' },
                    },
                    tooltip: {
                        callbacks: { label: (ctx) => ` ${ctx.dataset.label} : ${fmt(ctx.parsed.y)}` },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#d9e2ec', drawBorder: false },
                        border: { display: false },
                        ticks: { callback: (val) => fmtCompact(val), color: '#667085' },
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#667085' },
                    },
                },
            },
        });
    }
}

function initVillaMapPicker() {
    maps.picker?.remove();
    maps.picker = null;

    const container = document.getElementById('villa-map-picker');
    if (!container) {
        return;
    }

    const latInput = document.getElementById('villa-latitude');
    const lngInput = document.getElementById('villa-longitude');
    const existingLat = parseFloat(container.dataset.lat);
    const existingLng = parseFloat(container.dataset.lng);
    const hasExisting = !Number.isNaN(existingLat) && !Number.isNaN(existingLng);
    const center = hasExisting ? [existingLat, existingLng] : DEFAULT_MAP_CENTER;

    const map = L.map(container).setView(center, hasExisting ? 16 : 13);
    maps.picker = map;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    let marker = hasExisting ? L.marker(center, { draggable: true }).addTo(map) : null;

    function setPosition(latlng) {
        if (!marker) {
            marker = L.marker(latlng, { draggable: true }).addTo(map);
            marker.on('dragend', () => setPosition(marker.getLatLng()));
        } else {
            marker.setLatLng(latlng);
        }
        latInput.value = latlng.lat.toFixed(7);
        lngInput.value = latlng.lng.toFixed(7);
    }

    if (marker) {
        marker.on('dragend', () => setPosition(marker.getLatLng()));
    }

    map.on('click', (event) => setPosition(event.latlng));

    initMapSearch(map, setPosition);

    setTimeout(() => map.invalidateSize(), 100);
}

function initMapSearch(map, setPosition) {
    const searchInput = document.getElementById('villa-map-search');
    const resultsBox = document.getElementById('villa-map-search-results');
    if (!searchInput || !resultsBox) {
        return;
    }

    let debounceTimer = null;
    let currentController = null;

    function hideResults() {
        resultsBox.innerHTML = '';
        resultsBox.classList.remove('open');
    }

    function selectResult(result) {
        const latlng = { lat: parseFloat(result.lat), lng: parseFloat(result.lon) };
        map.setView(latlng, 17);
        setPosition(latlng);
        searchInput.value = result.display_name;
        hideResults();
    }

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 3) {
            hideResults();
            return;
        }

        debounceTimer = setTimeout(async () => {
            currentController?.abort();
            currentController = new AbortController();

            try {
                const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=5&q='
                    + encodeURIComponent(query + ', Abidjan, Côte d\'Ivoire');
                const response = await fetch(url, { signal: currentController.signal });
                const results = await response.json();

                if (!results.length) {
                    resultsBox.innerHTML = '<div class="map-search-empty">Aucun résultat trouvé.</div>';
                    resultsBox.classList.add('open');
                    return;
                }

                resultsBox.innerHTML = '';
                results.forEach((result) => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'map-search-item';
                    item.textContent = result.display_name;
                    item.addEventListener('click', () => selectResult(result));
                    resultsBox.appendChild(item);
                });
                resultsBox.classList.add('open');
            } catch (error) {
                if (error.name !== 'AbortError') {
                    hideResults();
                }
            }
        }, 400);
    });

    if (!document.documentElement.dataset.mapSearchOutsideClickBound) {
        document.documentElement.dataset.mapSearchOutsideClickBound = '1';
        document.addEventListener('click', (event) => {
            const box = document.getElementById('villa-map-search-results');
            const input = document.getElementById('villa-map-search');
            if (box && input && !box.contains(event.target) && event.target !== input) {
                box.innerHTML = '';
                box.classList.remove('open');
            }
        });
    }
}

function initOverviewMap() {
    maps.overview?.remove();
    maps.overview = null;

    const container = document.getElementById('overview-map');
    if (!container) {
        return;
    }

    const villas = window.__mapVillas ?? [];
    const points = villas.filter((v) => v.latitude !== null && v.longitude !== null);

    const map = L.map(container).setView(DEFAULT_MAP_CENTER, 13);
    maps.overview = map;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    const markers = [];
    points.forEach((villa) => {
        const marker = L.marker([villa.latitude, villa.longitude]).addTo(map);
        marker.bindPopup(`<strong>Villa ${villa.number}</strong><br>${villa.ownerName}<br>${villa.streetName ?? ''}`);
        markers.push(marker);
    });

    if (markers.length) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.2));
    }

    setTimeout(() => map.invalidateSize(), 100);
}

function setupGlobalListeners() {
    if (document.documentElement.dataset.appGlobalsBound) {
        return;
    }

    document.documentElement.dataset.appGlobalsBound = '1';

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
            closeModal();
        }
    });
}

function initApp() {
    initModalControls();
    initSidebar();
    initDeleteConfirm();
    initCharts(window.__dashboardData);
    initVillaMapPicker();
    initOverviewMap();
}

setupGlobalListeners();

document.addEventListener('turbo:load', initApp);
document.addEventListener('turbo:frame-load', initApp);
