const schema = {
    '@context': 'https://schema.org',
    '@type': 'ProfessionalService',
    name: 'EntryWeb',
    url: 'https://entryweb.fr',
    logo: 'https://entryweb.fr/images/logo.png',
    image: 'https://entryweb.fr/images/logo.png',
    description: 'Agence de création de sites web professionnels clé en main pour entrepreneurs, artisans et TPE.',
    telephone: '+33661617965',
    email: 'contact@entryweb.fr',
    address: {
        '@type': 'PostalAddress',
        addressLocality: 'Lyon',
        addressRegion: 'Auvergne-Rhône-Alpes',
        postalCode: '69000',
        addressCountry: 'FR',
    },
    geo: {
        '@type': 'GeoCoordinates',
        latitude: 45.764,
        longitude: 4.8357,
    },
    areaServed: {
        '@type': 'Country',
        name: 'France',
    },
    priceRange: '€€',
    openingHoursSpecification: [
        {
            '@type': 'OpeningHoursSpecification',
            dayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            opens: '09:00',
            closes: '18:00',
        },
    ],
    sameAs: [],
};

const script = document.createElement('script');
script.type = 'application/ld+json';
script.text = JSON.stringify(schema);
document.head.appendChild(script);
