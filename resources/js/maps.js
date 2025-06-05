class GoogleMapsHelper {
    constructor(apiKey) {
        this.apiKey = apiKey;
    }

    // Geocode alamat
    async geocodeAddress(address, city, province, postalCode) {
        try {
            const response = await fetch('/api/maps/geocode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    address: address,
                    city: city,
                    province: province,
                    postal_code: postalCode
                })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Geocoding error:', error);
            return null;
        }
    }

    // Reverse geocode koordinat
    async reverseGeocode(latitude, longitude) {
        try {
            const response = await fetch('/api/maps/reverse-geocode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude
                })
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Reverse geocoding error:', error);
            return null;
        }
    }

    // Auto-fill form dari koordinat
    autoFillFromCoordinates(latitude, longitude, prefix = '') {
        this.reverseGeocode(latitude, longitude).then(result => {
            if (result && result.success) {
                const data = result.data;
                
                document.getElementById(prefix + 'city').value = data.city;
                document.getElementById(prefix + 'province').value = data.province;
                document.getElementById(prefix + 'postal_code').value = data.postal_code;
                
                // Update hidden fields untuk koordinat
                if (document.getElementById(prefix + 'latitude')) {
                    document.getElementById(prefix + 'latitude').value = data.latitude;
                }
                if (document.getElementById(prefix + 'longitude')) {
                    document.getElementById(prefix + 'longitude').value = data.longitude;
                }
            }
        });
    }
}
