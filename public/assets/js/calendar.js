 // Attendez que le DOM soit entièrement chargé avant d'exécuter le code
 document.addEventListener('DOMContentLoaded', function() {
    // Récupérez les données de périodes depuis le backend, encodées en JSON
    var periodes = {{ periodes|json_encode|raw }};

    // Récupérez la date actuelle depuis le backend
    var dateActuelle = new Date("{{ dateActuelle }}");

    // Sélectionnez l'élément du calendrier dans le DOM
    var calendarEl = document.getElementById('calendar');

    // Initialisez un objet FullCalendar avec les options
    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            start: 'title',
            end: 'prev,next',
        },
        // Options du calendrier
        firstDay: 1,
        locale: 'fr',
        initialView: 'dayGridMonth',

        // Convertissez les données de périodes en un format compatible avec FullCalendar
        events: periodes.map(function(periode) {
            // Calculez la date de fin en ajoutant un jour à la date de fin
            var endDate = new Date(periode.end);
            endDate.setDate(endDate.getDate() + 1);

            // Retournez un objet représentant un événement pour FullCalendar
            return {
                start: periode.start,
                end: endDate.getFullYear() + '-' +
                    ('0' + (endDate.getMonth() + 1)).slice(-2) + '-' +
                    ('0' + endDate.getDate()).slice(-2),
                color: '#a4fa82',
                display: 'background'
            };
        }),
    });

    // Affichez le calendrier à l'emplacement spécifié dans le DOM
    calendar.render();
});