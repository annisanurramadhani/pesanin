                 /*
                |--------------------------------------------------------------------------
                | PHONE
                |--------------------------------------------------------------------------
                */

            document
                .getElementById('phone')
                ?.addEventListener('input', function() {

                    this.value =
                        this.value.replace(/\D/g, '');

                });


            /*
            |--------------------------------------------------------------------------
            | MAP INITIALIZATION
            |--------------------------------------------------------------------------
            */

            const latitudeInput =
                document.getElementById('latitude');

            const longitudeInput =
                document.getElementById('longitude');

            const locationStatus =
                document.getElementById('locationStatus');


            const savedLatitude =
                parseFloat(latitudeInput.value);

            const savedLongitude =
                parseFloat(longitudeInput.value);


            /*
            |--------------------------------------------------------------------------
            | Default Map Position
            |--------------------------------------------------------------------------
            |
            | Indonesia sebagai posisi awal.
            |
            */

            const defaultLatitude = -2.548926;

            const defaultLongitude =
                118.014863;


            const hasSavedLocation = !isNaN(savedLatitude) &&
                !isNaN(savedLongitude);


            const initialLatitude =
                hasSavedLocation ?
                savedLatitude :
                defaultLatitude;


            const initialLongitude =
                hasSavedLocation ?
                savedLongitude :
                defaultLongitude;


            const initialZoom =
                hasSavedLocation ?
                16 :
                5;


            const map =
                L.map('merchantMap').setView(
                    [
                        initialLatitude,
                        initialLongitude
                    ],
                    initialZoom
                );


            /*
            |--------------------------------------------------------------------------
            | OpenStreetMap
            |--------------------------------------------------------------------------
            */

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,

                    attribution: '&copy; OpenStreetMap contributors'
                }
            ).addTo(map);


            /*
            |--------------------------------------------------------------------------
            | Marker
            |--------------------------------------------------------------------------
            */

            let marker = null;


            if (hasSavedLocation) {

                marker =
                    L.marker(
                        [
                            savedLatitude,
                            savedLongitude
                        ], {
                            draggable: true
                        }
                    ).addTo(map);

            }


            /*
            |--------------------------------------------------------------------------
            | Set Location
            |--------------------------------------------------------------------------
            */

            function setMerchantLocation(
                latitude,
                longitude
            ) {

                latitudeInput.value =
                    latitude;

                longitudeInput.value =
                    longitude;


                if (!marker) {

                    marker =
                        L.marker(
                            [
                                latitude,
                                longitude
                            ], {
                                draggable: true
                            }
                        ).addTo(map);


                    marker.on(
                        'dragend',
                        handleMarkerDrag
                    );

                } else {

                    marker.setLatLng([
                        latitude,
                        longitude
                    ]);

                }


                map.setView(
                    [
                        latitude,
                        longitude
                    ],
                    17
                );


                locationStatus.textContent =
                    `Lokasi toko ditentukan: ${latitude.toFixed(7)}, ${longitude.toFixed(7)}`;


                locationStatus.classList.remove(
                    'text-slate-400',
                    'text-red-500'
                );

                locationStatus.classList.add(
                    'text-green-600'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Map Click
            |--------------------------------------------------------------------------
            */

            map.on(
                'click',
                function(event) {

                    setMerchantLocation(
                        event.latlng.lat,
                        event.latlng.lng
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Marker Drag
            |--------------------------------------------------------------------------
            */

            function handleMarkerDrag(event) {

                const position =
                    event.target.getLatLng();


                setMerchantLocation(
                    position.lat,
                    position.lng
                );

            }


            if (marker) {

                marker.on(
                    'dragend',
                    handleMarkerDrag
                );

            }


            /*
        |--------------------------------------------------------------------------
        | Search Location
        |--------------------------------------------------------------------------
        */

            const mapSearch =
                document.getElementById('mapSearch');

            const searchLocationButton =
                document.getElementById('searchLocation');


            async function searchLocation() {

                const query =
                    mapSearch.value.trim();


                if (!query) {

                    alert(
                        'Masukkan alamat atau nama tempat terlebih dahulu.'
                    );

                    return;

                }


                const originalContent =
                    searchLocationButton.innerHTML;


                searchLocationButton.disabled =
                    true;


                searchLocationButton.innerHTML =
                    '<i class="fa-solid fa-spinner fa-spin"></i>';


                try {

                    const response =
                        await fetch(
                            'https://nominatim.openstreetmap.org/search?' +
                            new URLSearchParams({
                                q: query,
                                format: 'json',
                                addressdetails: '1',
                                limit: '5',
                                countrycodes: 'id'
                            }), {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );


                    if (!response.ok) {

                        throw new Error(
                            'Gagal mencari lokasi.'
                        );

                    }


                    const results =
                        await response.json();


                    if (!results.length) {

                        alert(
                            'Lokasi tidak ditemukan. Coba gunakan alamat yang lebih lengkap.'
                        );

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Ambil hasil pertama
                    |--------------------------------------------------------------------------
                    */

                    const result =
                        results[0];


                    const latitude =
                        parseFloat(result.lat);


                    const longitude =
                        parseFloat(result.lon);


                    /*
                    |--------------------------------------------------------------------------
                    | Set marker
                    |--------------------------------------------------------------------------
                    */

                    setMerchantLocation(
                        latitude,
                        longitude
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Isi alamat toko otomatis
                    |--------------------------------------------------------------------------
                    */

                    const addressInput =
                        document.getElementById('address');


                    if (
                        addressInput &&
                        result.display_name
                    ) {

                        addressInput.value =
                            result.display_name;

                    }


                } catch (error) {

                    console.error(error);

                    alert(
                        'Terjadi kesalahan saat mencari lokasi. Silakan coba lagi.'
                    );


                } finally {

                    searchLocationButton.disabled =
                        false;


                    searchLocationButton.innerHTML =
                        originalContent;

                }

            }


            searchLocationButton.addEventListener(
                'click',
                searchLocation
            );


            mapSearch.addEventListener(
                'keydown',
                function(event) {

                    if (event.key === 'Enter') {

                        event.preventDefault();

                        searchLocation();

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Device Location
            |--------------------------------------------------------------------------
            */

            document
                .getElementById('useDeviceLocation')
                .addEventListener(
                    'click',
                    function() {

                        const button =
                            this;

                        const originalContent =
                            button.innerHTML;


                        if (!navigator.geolocation) {

                            alert(
                                'Browser Anda tidak mendukung fitur lokasi.'
                            );

                            return;

                        }


                        button.disabled =
                            true;


                        button.innerHTML =
                            '<i class="fa-solid fa-spinner fa-spin"></i> Mengambil lokasi...';


                        navigator.geolocation.getCurrentPosition(

                            function(position) {

                                const latitude =
                                    position.coords.latitude;

                                const longitude =
                                    position.coords.longitude;


                                // Simpan koordinat dan pindahkan marker
                                setMerchantLocation(
                                    latitude,
                                    longitude
                                );


                                // Cari alamat berdasarkan koordinat
                                fetch(
                                        'https://nominatim.openstreetmap.org/reverse?' +
                                        new URLSearchParams({
                                            lat: latitude,
                                            lon: longitude,
                                            format: 'json',
                                            addressdetails: '1'
                                        }), {
                                            headers: {
                                                'Accept': 'application/json'
                                            }
                                        }
                                    )
                                    .then(response => response.json())
                                    .then(data => {

                                        const addressInput =
                                            document.getElementById('address');


                                        if (
                                            addressInput &&
                                            data.display_name
                                        ) {

                                            addressInput.value =
                                                data.display_name;

                                        }

                                    })
                                    .catch(error => {

                                        console.error(
                                            'Gagal mendapatkan alamat:',
                                            error
                                        );

                                    })
                                    .finally(() => {

                                        button.disabled =
                                            false;

                                        button.innerHTML =
                                            originalContent;

                                    });

                            },


                            function() {

                                button.disabled =
                                    false;


                                button.innerHTML =
                                    originalContent;


                                alert(
                                    'Lokasi perangkat tidak dapat diperoleh. Silakan tentukan titik lokasi toko secara manual pada peta.'
                                );

                            },


                            {
                                enableHighAccuracy: true,

                                timeout: 15000,

                                maximumAge: 0
                            }

                        );

                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Fix Map Size
            |--------------------------------------------------------------------------
            */

            setTimeout(
                function() {

                    map.invalidateSize();

                },
                200
            );
    