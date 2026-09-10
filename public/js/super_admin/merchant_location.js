document.addEventListener("DOMContentLoaded", function () {
    /*
    |--------------------------------------------------------------------------
    | Merchant Data
    |--------------------------------------------------------------------------
    */

    const merchants = window.merchantLocations || [];

    /*
    |--------------------------------------------------------------------------
    | Default Map Position
    |--------------------------------------------------------------------------
    */

    const defaultLatitude = -2.548926;

    const defaultLongitude = 118.014863;

    /*
    |--------------------------------------------------------------------------
    | Map Initialization
    |--------------------------------------------------------------------------
    */

    const map = L.map("merchantLocationMap").setView(
        [defaultLatitude, defaultLongitude],
        5,
    );

    /*
    |--------------------------------------------------------------------------
    | OpenStreetMap
    |--------------------------------------------------------------------------
    */

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,

        attribution: "&copy; OpenStreetMap contributors",
    }).addTo(map);

    /*
|--------------------------------------------------------------------------
| Merchant Markers
|--------------------------------------------------------------------------
*/

    const markers = [];

    const merchantMarkerData = [];

    merchants.forEach(function (merchant) {
        const latitude = parseFloat(merchant.latitude);

        const longitude = parseFloat(merchant.longitude);

        if (isNaN(latitude) || isNaN(longitude)) {
            return;
        }

        const marker = L.marker([latitude, longitude]).addTo(map);

        /*
    |--------------------------------------------------------------------------
    | Popup
    |--------------------------------------------------------------------------
    */

        marker.bindPopup(`
        <div style="min-width: 220px;">

            <div style="
                font-size: 15px;
                font-weight: 700;
                margin-bottom: 8px;
            ">
                ${escapeHtml(merchant.name ?? "-")}
            </div>


            <div style="
                font-size: 12px;
                color: #64748b;
                margin-bottom: 6px;
            ">
                ${escapeHtml(merchant.address ?? "Alamat belum tersedia")}
            </div>


            <div style="
                font-size: 12px;
                color: #64748b;
                margin-bottom: 6px;
            ">
                ${escapeHtml(merchant.phone ?? "-")}
            </div>


            <div style="
                font-size: 11px;
                color: #94a3b8;
            ">
                ${latitude.toFixed(7)},
                ${longitude.toFixed(7)}
            </div>

        </div>
    `);

        markers.push(marker);

        merchantMarkerData.push({
            merchant: merchant,
            marker: marker,
            latitude: latitude,
            longitude: longitude,
        });
    });

    /*
|--------------------------------------------------------------------------
| Merchant Search
|--------------------------------------------------------------------------
*/

    const merchantSearch = document.getElementById("merchantSearch");

    const showAllMerchants = document.getElementById("showAllMerchants");

    function searchMerchant() {
        const query = merchantSearch.value.trim().toLowerCase();

        if (!query) {
            showAllMerchantLocations();

            return;
        }

        const result = merchantMarkerData.find(function (item) {
            const merchantName = (item.merchant.name ?? "").toLowerCase();

            return merchantName.includes(query);
        });

        if (!result) {
            alert("Merchant tidak ditemukan.");

            return;
        }

        map.setView([result.latitude, result.longitude], 17);

        result.marker.openPopup();
    }

    merchantSearch.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();

            searchMerchant();
        }
    });

    showAllMerchants.addEventListener("click", function () {
        merchantSearch.value = "";

        showAllMerchantLocations();
    });

    /*
    |--------------------------------------------------------------------------
    | Fit Map To Merchant Locations
    |--------------------------------------------------------------------------
    */

    if (markers.length > 0) {
        const bounds = L.featureGroup(markers).getBounds();

        map.fitBounds(bounds, {
            padding: [30, 30],
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Fix Map Size
    |--------------------------------------------------------------------------
    */

    setTimeout(function () {
        map.invalidateSize();
    }, 200);

    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    |
    | Mencegah data merchant langsung dimasukkan
    | sebagai HTML.
    |
    */

    function escapeHtml(value) {
        const div = document.createElement("div");

        div.textContent = value;

        return div.innerHTML;
    }

    /*
    |--------------------------------------------------------------------------
    | Show All Merchants
    |--------------------------------------------------------------------------
    */

    function showAllMerchantLocations() {
        if (!markers.length) {
            return;
        }

        const bounds = L.featureGroup(markers).getBounds();

        map.fitBounds(bounds, {
            padding: [30, 30],
        });
    }
});
