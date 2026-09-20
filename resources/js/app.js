import * as Turbo from '@hotwired/turbo';
import Chart from 'chart.js/auto';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import { hasGoogleKey, initGoogleOverview, initGooglePicker, onGoogleAuthFailure } from './google-maps.js';

Turbo.start();

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

// Google Maps si une cle est configuree ; sinon (ou si Google refuse la cle / est injoignable) OpenStreetMap.
let googleFailed = false;
const useGoogle = () => hasGoogleKey() && !googleFailed;

function fallbackToOpenStreetMap() {
    if (googleFailed) {
        return;
    }
    googleFailed = true;
    console.warn('Google Maps indisponible (cle invalide, API non activee ou facturation absente) : carte OpenStreetMap utilisee.');

    document.querySelectorAll('.map-picker, #overview-map').forEach((element) => element.replaceChildren());
    document.querySelectorAll('gmp-place-autocomplete').forEach((element) => element.remove());
    document.querySelectorAll('.map-search input').forEach((input) => { input.style.display = ''; });

    initMapPicker('street');
    initMapPicker('partner');
    initOverviewMap();
}

onGoogleAuthFailure(fallbackToOpenStreetMap);

// Quartier Rosiers 3e Programme Rive Gauche (config/quartier.php, expose par le layout)
const QUARTIER = window.__quartier ?? { center: [5.3852, -3.9660], radiusKm: 0.65 };
const DEFAULT_MAP_CENTER = QUARTIER.center;

function quartierCircle() {
    return L.circle(QUARTIER.center, {
        radius: QUARTIER.radiusKm * 1000,
        color: '#1d4ed8',
        weight: 2,
        dashArray: '6 6',
        fillOpacity: 0.04,
        interactive: false,
    });
}

const charts = {
    monthly: null,
    annual: null,
};

const maps = {
    pickers: {},
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

function initMapPicker(prefix) {
    maps.pickers[prefix]?.remove();
    delete maps.pickers[prefix];

    const container = document.getElementById(`${prefix}-map-picker`);
    if (!container) {
        return;
    }

    const latInput = document.getElementById(`${prefix}-latitude`);
    const lngInput = document.getElementById(`${prefix}-longitude`);
    const existingLat = parseFloat(container.dataset.lat);
    const existingLng = parseFloat(container.dataset.lng);
    const hasExisting = !Number.isNaN(existingLat) && !Number.isNaN(existingLng);
    const center = hasExisting ? [existingLat, existingLng] : DEFAULT_MAP_CENTER;
    const restricted = container.dataset.restrict === 'quartier';
    const warning = document.getElementById(`${prefix}-map-warning`);

    if (useGoogle()) {
        initGooglePicker({
            container,
            prefix,
            quartier: QUARTIER,
            restricted,
            start: { lat: center[0], lng: center[1], zoom: hasExisting || restricted ? 16 : 13, hasExisting },
            latInput,
            lngInput,
            warning,
        }).catch(fallbackToOpenStreetMap);
        return;
    }

    const map = L.map(container, restricted ? { minZoom: 14 } : {}).setView(center, hasExisting || restricted ? 16 : 13);
    maps.pickers[prefix] = map;

    let quartierBounds = null;
    if (restricted) {
        const circle = quartierCircle().addTo(map);
        quartierBounds = circle.getBounds();
        map.setMaxBounds(quartierBounds.pad(0.15));
    }

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    let marker = hasExisting ? L.marker(center, { draggable: true }).addTo(map) : null;
    let lastValid = hasExisting ? { lat: existingLat, lng: existingLng } : null;

    function setPosition(latlng) {
        if (restricted && map.distance(latlng, QUARTIER.center) > QUARTIER.radiusKm * 1000) {
            if (warning) {
                warning.hidden = false;
            }
            // Un repere deplace hors zone revient a sa derniere position valide.
            if (marker && lastValid) {
                marker.setLatLng(lastValid);
            }
            return;
        }
        if (warning) {
            warning.hidden = true;
        }
        lastValid = { lat: latlng.lat, lng: latlng.lng };
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

    initMapSearch(prefix, map, setPosition, quartierBounds);

    setTimeout(() => map.invalidateSize(), 100);
}

// --- Rues reelles du quartier (OpenStreetMap), chargees une fois puis gardees 7 jours dans le navigateur ---
const QUARTIER_STREETS_KEY = 'quartierStreets:v1';
const QUARTIER_STREETS_TTL = 7 * 24 * 3600 * 1000;
let quartierStreetsPromise = null;

function normalizeText(value) {
    return (value ?? '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
}

// Le serveur Overpass public est parfois surcharge (504) : on reessaie, puis on essaie un miroir.
const OVERPASS_ENDPOINTS = [
    'https://overpass-api.de/api/interpreter',
    'https://overpass.kumi.systems/api/interpreter',
    'https://overpass-api.de/api/interpreter',
];

async function fetchOverpass(query) {
    let lastError = null;

    for (const endpoint of OVERPASS_ENDPOINTS) {
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: new URLSearchParams({ data: query }),
                signal: AbortSignal.timeout(15000),
            });
            if (response.ok) {
                return await response.json();
            }
            lastError = new Error(`Overpass ${response.status}`);
        } catch (error) {
            lastError = error;
        }
    }

    throw lastError;
}
function loadQuartierStreets() {
    if (quartierStreetsPromise) {
        return quartierStreetsPromise;
    }

    const cacheKey = `${QUARTIER_STREETS_KEY}:${QUARTIER.center.join(',')}:${QUARTIER.radiusKm}`;

    quartierStreetsPromise = (async () => {
        try {
            const cached = JSON.parse(localStorage.getItem(cacheKey) ?? 'null');
            if (cached && Date.now() - cached.at < QUARTIER_STREETS_TTL) {
                return cached.streets;
            }
        } catch (error) {
            // cache illisible : on recharge
        }

        const [lat, lng] = QUARTIER.center;
        const query = `[out:json][timeout:25];way(around:${Math.round(QUARTIER.radiusKm * 1000)},${lat},${lng})`
            + '["highway"]["name"]["highway"!~"^(footway|path|steps|cycleway|track)$"];out tags center;';

        const data = await fetchOverpass(query);

        // Une rue = un nom ; on garde le troncon le plus proche du centre du quartier.
        const byName = new Map();
        const center = L.latLng(QUARTIER.center);
        (data.elements ?? []).forEach((element) => {
            if (!element.center || !element.tags?.name) {
                return;
            }
            const point = L.latLng(element.center.lat, element.center.lon);
            const distance = center.distanceTo(point);
            if (distance > QUARTIER.radiusKm * 1000) {
                return;
            }
            const known = byName.get(element.tags.name);
            if (!known || distance < known.distance) {
                byName.set(element.tags.name, { name: element.tags.name, lat: point.lat, lng: point.lng, distance });
            }
        });

        const streets = [...byName.values()]
            .sort((a, b) => a.name.localeCompare(b.name, 'fr'))
            .map(({ name, lat: streetLat, lng: streetLng }) => ({ name, lat: streetLat, lng: streetLng }));

        try {
            localStorage.setItem(cacheKey, JSON.stringify({ at: Date.now(), streets }));
        } catch (error) {
            // stockage indisponible : la liste sera simplement rechargee la prochaine fois
        }

        return streets;
    })().catch((error) => {
        quartierStreetsPromise = null; // un echec reseau ne doit pas etre memorise
        throw error;
    });

    return quartierStreetsPromise;
}

function initMapSearch(prefix, map, setPosition, bounds = null) {
    const searchInput = document.getElementById(`${prefix}-map-search`);
    const resultsBox = document.getElementById(`${prefix}-map-search-results`);
    if (!searchInput || !resultsBox) {
        return;
    }

    // Formulaire d'une rue : recherche dans les rues du quartier. Partenaires : recherche libre.
    const localMode = bounds !== null;
    let debounceTimer = null;
    let currentController = null;

    function hideResults() {
        resultsBox.innerHTML = '';
        resultsBox.classList.remove('open');
    }

    function showMessage(text) {
        resultsBox.innerHTML = '';
        const empty = document.createElement('div');
        empty.className = 'map-search-empty';
        empty.textContent = text;
        resultsBox.appendChild(empty);
        resultsBox.classList.add('open');
    }

    function selectResult(result) {
        const latlng = { lat: parseFloat(result.lat), lng: parseFloat(result.lon ?? result.lng) };
        map.setView(latlng, 18);
        setPosition(latlng);
        searchInput.value = result.display_name ?? result.name;
        hideResults();
    }

    function renderResults(results, label = (result) => result.display_name) {
        resultsBox.innerHTML = '';
        results.forEach((result) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'map-search-item';
            item.textContent = label(result);
            item.addEventListener('click', () => selectResult(result));
            resultsBox.appendChild(item);
        });
        resultsBox.classList.add('open');
    }

    async function searchLocal() {
        const term = normalizeText(searchInput.value.trim());

        try {
            showMessage('Chargement des rues du quartier…');
            const streets = await loadQuartierStreets();
            const matches = streets.filter((street) => normalizeText(street.name).includes(term));

            if (matches.length) {
                renderResults(matches, (street) => street.name);
            } else {
                showMessage('Aucune rue du quartier ne correspond. Clique directement sur la carte pour placer le repère.');
            }
        } catch (error) {
            showMessage('Liste des rues indisponible. Clique directement sur la carte pour placer le repère.');
        }
    }

    async function searchRemote(query) {
        currentController?.abort();
        currentController = new AbortController();

        try {
            const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=5&q='
                + encodeURIComponent(query + ', Abidjan, Côte d\'Ivoire');
            const response = await fetch(url, { signal: currentController.signal });
            const results = await response.json();

            if (!results.length) {
                showMessage('Aucun résultat trouvé.');
                return;
            }
            renderResults(results);
        } catch (error) {
            if (error.name !== 'AbortError') {
                hideResults();
            }
        }
    }

    if (localMode) {
        // Liste complete des rues des le clic dans le champ, filtree a chaque frappe.
        searchInput.addEventListener('focus', searchLocal);
        searchInput.addEventListener('input', searchLocal);
        loadQuartierStreets().catch(() => {}); // prechargement
    } else {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 3) {
                hideResults();
                return;
            }

            debounceTimer = setTimeout(() => searchRemote(query), 400);
        });
    }

    const boundFlag = `mapSearchOutsideClickBound${prefix}`;
    if (!document.documentElement.dataset[boundFlag]) {
        document.documentElement.dataset[boundFlag] = '1';
        document.addEventListener('click', (event) => {
            if (!resultsBox.contains(event.target) && event.target !== searchInput) {
                resultsBox.innerHTML = '';
                resultsBox.classList.remove('open');
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

    const streets = window.__mapStreets ?? [];
    const points = streets.filter((s) => s.latitude !== null && s.longitude !== null);

    if (useGoogle()) {
        initGoogleOverview({
            container,
            quartier: QUARTIER,
            streets,
            partners: window.__partnerSyndics ?? [],
            monthLabel: window.__mapMonthLabel,
            escapeHtml,
        }).catch(fallbackToOpenStreetMap);
        return;
    }

    const map = L.map(container).setView(DEFAULT_MAP_CENTER, 15);
    maps.overview = map;
    quartierCircle().addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    const markers = [];
    const streetsLayer = L.layerGroup().addTo(map);
    const partnersLayer = L.layerGroup().addTo(map);

    points.forEach((street) => {
        const hasFinance = street.paidCount !== null && street.paidCount !== undefined;
        let marker;

        if (hasFinance) {
            const rate = street.villasCount ? Math.round((street.paidCount / street.villasCount) * 100) : 0;
            const color = rate >= 80 ? '#16a34a' : rate >= 50 ? '#f59e0b' : '#dc2626';

            marker = L.circleMarker([street.latitude, street.longitude], {
                radius: 10 + Math.min(street.villasCount, 30) * 0.5,
                weight: 2,
                color: '#ffffff',
                fillColor: color,
                fillOpacity: 0.9,
            });

            const amount = new Intl.NumberFormat('fr-FR').format(street.collected ?? 0);
            marker.bindPopup(
                `<strong>${escapeHtml(street.name)}</strong>`
                + `<br>${street.paidCount} / ${street.villasCount} villa(s) à jour (${rate} %)`
                + `<br>${amount} FCFA encaissés`
                + (window.__mapMonthLabel ? `<br><span class="muted">${escapeHtml(window.__mapMonthLabel)}</span>` : '')
            );
        } else {
            marker = L.marker([street.latitude, street.longitude]);
            marker.bindPopup(`<strong>${escapeHtml(street.name)}</strong><br>${street.villasCount} villa(s)`);
        }

        marker.addTo(streetsLayer);
        markers.push(marker);
    });

    const partners = window.__partnerSyndics ?? [];
    const partnerPoints = partners.filter((p) => p.latitude !== null && p.longitude !== null);

    partnerPoints.forEach((partner) => {
        const marker = L.circleMarker([partner.latitude, partner.longitude], {
            radius: 9,
            weight: 2,
            color: '#f59e0b',
            fillColor: '#f59e0b',
            fillOpacity: 0.85,
        }).addTo(partnersLayer);

        const cityLine = partner.city ? `<br>${escapeHtml(partner.city)}` : '';
        const contactLine = partner.contactName ? `<br><span class="muted">${escapeHtml(partner.contactName)}</span>` : '';
        const link = partner.loginUrl
            ? `<br><a href="${escapeHtml(partner.loginUrl)}" target="_blank" rel="noopener noreferrer">Se connecter &rarr;</a>`
            : '';

        marker.bindPopup(`<strong>${escapeHtml(partner.name)}</strong>${cityLine}${contactLine}${link}`);
        markers.push(marker);
    });

    L.control.layers(null, {
        'Rues (quartier)': streetsLayer,
        'Syndics partenaires': partnersLayer,
    }, { collapsed: false }).addTo(map);

    // Cadre d'abord sur les rues du quartier, les partenaires pouvant etre tres eloignes.
    const fitTargets = streetsLayer.getLayers().length ? streetsLayer.getLayers() : markers;
    if (fitTargets.length) {
        map.fitBounds(L.featureGroup(fitTargets).getBounds().pad(0.3), { maxZoom: 16 });
    }

    setTimeout(() => map.invalidateSize(), 100);
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
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
    initMapPicker('street');
    initMapPicker('partner');
    initOverviewMap();
}

setupGlobalListeners();

document.addEventListener('turbo:load', initApp);
document.addEventListener('turbo:frame-load', initApp);
