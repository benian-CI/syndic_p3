// Google Maps : utilise uniquement si une cle est configuree (GOOGLE_MAPS_API_KEY).
// Sinon, ou si Google refuse la cle, app.js retombe sur la carte OpenStreetMap (Leaflet).

const GOOGLE_KEY = window.__googleMapsKey || '';

export function hasGoogleKey() {
    return GOOGLE_KEY !== '';
}

let loadPromise = null;

export function loadGoogleMaps() {
    if (!GOOGLE_KEY) {
        return Promise.reject(new Error('Cle Google Maps absente'));
    }
    if (loadPromise) {
        return loadPromise;
    }

    loadPromise = new Promise((resolve, reject) => {
        window.__googleMapsReady = async () => {
            try {
                await Promise.all([google.maps.importLibrary('maps'), google.maps.importLibrary('places')]);
                resolve(google.maps);
            } catch (error) {
                loadPromise = null;
                reject(error);
            }
        };

        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(GOOGLE_KEY)
            + '&loading=async&language=fr&region=CI&callback=__googleMapsReady';
        script.async = true;
        script.onerror = () => {
            loadPromise = null;
            reject(new Error('Chargement de Google Maps impossible'));
        };
        document.head.appendChild(script);
    });

    return loadPromise;
}

/** Cle invalide, API non activee, facturation absente... Google appelle alors gm_authFailure. */
export function onGoogleAuthFailure(callback) {
    window.gm_authFailure = callback;
}

/** Rectangle (nord/sud/est/ouest) entourant le quartier, avec une marge de 15 %. */
function boundsAround([lat, lng], radiusKm) {
    const km = radiusKm * 1.15;
    const dLat = km / 111.32;
    const dLng = km / (111.32 * Math.cos((lat * Math.PI) / 180));

    return { north: lat + dLat, south: lat - dLat, east: lng + dLng, west: lng - dLng };
}

function distanceMeters(a, b) {
    const rad = (deg) => (deg * Math.PI) / 180;
    const h = Math.sin(rad(b.lat - a.lat) / 2) ** 2
        + Math.cos(rad(a.lat)) * Math.cos(rad(b.lat)) * Math.sin(rad(b.lng - a.lng) / 2) ** 2;

    return 6371000 * 2 * Math.asin(Math.sqrt(h));
}

function drawQuartier(map, quartier) {
    return new google.maps.Circle({
        map,
        center: { lat: quartier.center[0], lng: quartier.center[1] },
        radius: quartier.radiusKm * 1000,
        strokeColor: '#1d4ed8',
        strokeOpacity: 0.9,
        strokeWeight: 2,
        fillColor: '#1d4ed8',
        fillOpacity: 0.04,
        clickable: false,
    });
}

/**
 * Selecteur de position (formulaires rue / syndic partenaire).
 * restricted = true : le repere doit rester dans le quartier et la recherche est limitee a la zone.
 */
export async function initGooglePicker({ container, prefix, quartier, restricted, start, latInput, lngInput, warning }) {
    await loadGoogleMaps();
    container.replaceChildren();

    const bounds = restricted ? boundsAround(quartier.center, quartier.radiusKm) : null;

    const map = new google.maps.Map(container, {
        center: { lat: start.lat, lng: start.lng },
        zoom: start.zoom,
        minZoom: restricted ? 14 : undefined,
        restriction: bounds ? { latLngBounds: bounds, strictBounds: false } : undefined,
        streetViewControl: false,
        fullscreenControl: false,
        mapTypeControl: true,
        clickableIcons: false,
    });

    if (restricted) {
        drawQuartier(map, quartier);
    }

    let marker = null;
    let lastValid = null;

    function setPosition(latlng) {
        if (restricted && distanceMeters(latlng, { lat: quartier.center[0], lng: quartier.center[1] }) > quartier.radiusKm * 1000) {
            if (warning) {
                warning.hidden = false;
            }
            // Un repere deplace hors zone revient a sa derniere position valide.
            if (marker && lastValid) {
                marker.setPosition(lastValid);
            }
            return;
        }
        if (warning) {
            warning.hidden = true;
        }

        lastValid = { lat: latlng.lat, lng: latlng.lng };
        if (!marker) {
            marker = new google.maps.Marker({ map, position: latlng, draggable: true });
            marker.addListener('dragend', () => setPosition(marker.getPosition().toJSON()));
        } else {
            marker.setPosition(latlng);
        }
        latInput.value = latlng.lat.toFixed(7);
        lngInput.value = latlng.lng.toFixed(7);
    }

    if (start.hasExisting) {
        marker = new google.maps.Marker({ map, position: { lat: start.lat, lng: start.lng }, draggable: true });
        lastValid = { lat: start.lat, lng: start.lng };
        marker.addListener('dragend', () => setPosition(marker.getPosition().toJSON()));
    }

    map.addListener('click', (event) => setPosition(event.latLng.toJSON()));

    await attachPlaceSearch({ prefix, map, bounds, setPosition });
}

/** Recherche Google Places (nouvelle API) a la place du champ de recherche OpenStreetMap. */
async function attachPlaceSearch({ prefix, map, bounds, setPosition }) {
    const searchInput = document.getElementById(`${prefix}-map-search`);
    const wrapper = searchInput?.closest('.map-search');
    if (!searchInput || !wrapper) {
        return;
    }

    const { PlaceAutocompleteElement } = await google.maps.importLibrary('places');

    const options = { includedRegionCodes: ['ci'] };
    if (bounds) {
        options.locationRestriction = bounds;
    }

    const element = new PlaceAutocompleteElement(options);
    element.setAttribute('placeholder', searchInput.getAttribute('placeholder') ?? 'Rechercher un lieu');

    async function onSelect(event) {
        const place = event.placePrediction ? event.placePrediction.toPlace() : event.place;
        if (!place) {
            return;
        }
        await place.fetchFields({ fields: ['location'] });
        if (!place.location) {
            return;
        }
        const latlng = place.location.toJSON();
        map.setCenter(latlng);
        map.setZoom(18);
        setPosition(latlng);
    }

    // 'gmp-select' (versions recentes) ; 'gmp-placeselect' (anciennes versions de l'API)
    element.addEventListener('gmp-select', onSelect);
    element.addEventListener('gmp-placeselect', onSelect);

    searchInput.style.display = 'none';
    wrapper.appendChild(element);
}

/** Carte d'ensemble : rues (colorees selon le recouvrement) et syndics partenaires. */
export async function initGoogleOverview({ container, quartier, streets, partners, monthLabel, escapeHtml }) {
    await loadGoogleMaps();
    container.replaceChildren();

    const map = new google.maps.Map(container, {
        center: { lat: quartier.center[0], lng: quartier.center[1] },
        zoom: 15,
        streetViewControl: false,
        fullscreenControl: true,
        mapTypeControl: true,
        clickableIcons: false,
    });

    drawQuartier(map, quartier);

    const infoWindow = new google.maps.InfoWindow();
    const streetMarkers = [];
    const partnerMarkers = [];
    const fitBounds = new google.maps.LatLngBounds();

    function addMarker(list, options, html) {
        const marker = new google.maps.Marker({ map, ...options });
        marker.addListener('click', () => {
            infoWindow.setContent(html);
            infoWindow.open({ map, anchor: marker });
        });
        list.push(marker);
        return marker;
    }

    streets.filter((s) => s.latitude !== null && s.longitude !== null).forEach((street) => {
        const position = { lat: street.latitude, lng: street.longitude };
        const hasFinance = street.paidCount !== null && street.paidCount !== undefined;
        fitBounds.extend(position);

        if (hasFinance) {
            const rate = street.villasCount ? Math.round((street.paidCount / street.villasCount) * 100) : 0;
            const color = rate >= 80 ? '#16a34a' : rate >= 50 ? '#f59e0b' : '#dc2626';
            const amount = new Intl.NumberFormat('fr-FR').format(street.collected ?? 0);

            addMarker(streetMarkers, {
                position,
                title: street.name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10 + Math.min(street.villasCount, 30) * 0.5,
                    fillColor: color,
                    fillOpacity: 0.9,
                    strokeColor: '#ffffff',
                    strokeWeight: 2,
                },
            }, `<strong>${escapeHtml(street.name)}</strong>`
                + `<br>${street.paidCount} / ${street.villasCount} villa(s) à jour (${rate} %)`
                + `<br>${amount} FCFA encaissés`
                + (monthLabel ? `<br><span style="color:#64748b">${escapeHtml(monthLabel)}</span>` : ''));
        } else {
            addMarker(streetMarkers, { position, title: street.name },
                `<strong>${escapeHtml(street.name)}</strong><br>${street.villasCount} villa(s)`);
        }
    });

    partners.filter((p) => p.latitude !== null && p.longitude !== null).forEach((partner) => {
        const position = { lat: partner.latitude, lng: partner.longitude };
        if (!streetMarkers.length) {
            fitBounds.extend(position);
        }

        const cityLine = partner.city ? `<br>${escapeHtml(partner.city)}` : '';
        const contactLine = partner.contactName ? `<br><span style="color:#64748b">${escapeHtml(partner.contactName)}</span>` : '';
        const link = partner.loginUrl
            ? `<br><a href="${escapeHtml(partner.loginUrl)}" target="_blank" rel="noopener noreferrer">Se connecter &rarr;</a>`
            : '';

        addMarker(partnerMarkers, {
            position,
            title: partner.name,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 9,
                fillColor: '#f59e0b',
                fillOpacity: 0.85,
                strokeColor: '#f59e0b',
                strokeWeight: 2,
            },
        }, `<strong>${escapeHtml(partner.name)}</strong>${cityLine}${contactLine}${link}`);
    });

    // Cases a cocher pour afficher ou masquer chaque couche
    const control = document.createElement('div');
    control.className = 'map-layer-control';
    [['Rues (quartier)', streetMarkers], ['Syndics partenaires', partnerMarkers]].forEach(([label, markers]) => {
        const row = document.createElement('label');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.checked = true;
        checkbox.addEventListener('change', () => markers.forEach((m) => m.setMap(checkbox.checked ? map : null)));
        row.append(checkbox, ` ${label}`);
        control.appendChild(row);
    });
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(control);

    if (!fitBounds.isEmpty()) {
        map.fitBounds(fitBounds, 60);
        google.maps.event.addListenerOnce(map, 'idle', () => {
            if (map.getZoom() > 17) {
                map.setZoom(17);
            }
        });
    }
}
