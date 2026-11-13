const map = L.map('map', { preferCanvas: true }).setView([50.437, 15.352], 15);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap přispěvatelé | Data parcel ČÚZK'
}).addTo(map);

let parcelsLayer = L.geoJSON(null, {
    style: function (feature) {
        return {
            color: '#2563eb',
            weight: 1.5,
            fillColor: '#3b82f6',
            fillOpacity: 0.15
        };
    },
    onEachFeature: function (feature, layer) {
        layer.on('mouseover', function () {
            this.setStyle({
                weight: 2.5,
                color: '#1d4ed8',
                fillOpacity: 0.4
            });
        });
        layer.on('mouseout', function () {
            parcelsLayer.resetStyle(this);
        });
        layer.on('click', function (e) {
            L.DomEvent.stopPropagation(e);
            showParcelDetail(feature.properties);
        });
    }
}).addTo(map);

const statusBar = document.getElementById('status-bar');
const sidebar = document.getElementById('sidebar');
const sidebarContent = document.getElementById('sidebar-content');
const closeSidebarBtn = document.getElementById('close-sidebar');

closeSidebarBtn.addEventListener('click', () => {
    sidebar.classList.add('hidden');
});

map.on('click', () => {
    sidebar.classList.add('hidden');
});

async function loadParcels() {
    const zoom = map.getZoom();
    const bounds = map.getBounds();
    const bbox = bounds.toBBoxString();
    if (zoom < 16) {
        parcelsLayer.clearLayers();
        statusBar.textContent = '🔍 Pro zobrazení parcel přibližte mapu';
        return;
    }
    statusBar.textContent = 'Načítám parcely...';
    try {
        const response = await fetch(`/api/parcels.php?zoom=${zoom}&bbox=${bbox}`);
        if (!response.ok) {
            throw new Error(`HTTP chyba: ${response.status}`);
        }
        const data = await response.json();
        parcelsLayer.clearLayers();
        if (data.features && data.features.length > 0) {
            parcelsLayer.addData(data);
            statusBar.textContent = `Zobrazeno ${data.features.length} parcel`;
        } else {
            statusBar.textContent = data.message || 'V tomto výřezu nejsou žádné parcely.';
        }
    } catch (err) {
        console.error('Chyba při načítání parcel:', err);
        statusBar.textContent = 'Chyba při načítání dat.';
    }
}

function showParcelDetail(props) {
    const cisloParcely = props.poddeleni_cisla
        ? `${props.kmenove_cislo}/${props.poddeleni_cisla}`
        : `${props.kmenove_cislo}`;
    sidebarContent.innerHTML = `
        <h2 style="margin-bottom: 16px;">Parcela č. ${cisloParcely}</h2>
        
        <div class="parcel-detail-row">
            <div class="parcel-detail-label">Katastrální území</div>
            <div class="parcel-detail-value">${props.ku_nazev || 'Neznámé'} (${props.ku_kod})</div>
        </div>
        <div class="parcel-detail-row">
            <div class="parcel-detail-label">Výměra</div>
            <div class="parcel-detail-value">${Number(props.vymera).toLocaleString('cs-CZ')} m²</div>
        </div>
        <div class="parcel-detail-row">
            <div class="parcel-detail-label">Kód druhu pozemku</div>
            <div class="parcel-detail-value">${props.druh_pozemku_kod || 'Neuvedeno'}</div>
        </div>
        <div class="parcel-detail-row">
            <div class="parcel-detail-label">Identifikátor parcely (ID)</div>
            <div class="parcel-detail-value" style="font-size: 13px; color: #64748b;">${props.id}</div>
        </div>
    `;
    sidebar.classList.remove('hidden');
}
map.on('moveend', loadParcels);
loadParcels();