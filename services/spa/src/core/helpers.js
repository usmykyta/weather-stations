export function degreesToRadians(degrees) {
    return degrees * (Math.PI / 180);
}

export function distanceBetweenCoordinates(lat1, lon1, lat2, lon2) {
    const earthRadiusMiles = 3958.8; // Earth's radius in miles

    const dLat = degreesToRadians(lat2 - lat1);
    const dLon = degreesToRadians(lon2 - lon1);

    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(degreesToRadians(lat1)) *
        Math.cos(degreesToRadians(lat2)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return earthRadiusMiles * c;
}