const getSlug = () => {
    const path = window.location.pathname; // /auth/cashier/kios/0197745b-c7c6-7170-b49e-8dd95f68230c
    const parts = path.split('/'); // ['','auth','cashier','kios','0197745b-c7c6-7170-b49e-8dd95f68230c']
    const uuid = parts[parts.length - 1]; // ambil elemen terakhir

    return uuid
}

export { getSlug };
