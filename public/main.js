// Startbutton – Welcome ausblenden, Tracker zeigen
document.getElementById('start-btn').addEventListener('click', () => {
    document.getElementById('welcome-screen').classList.add('hidden');
    document.getElementById('tracker-screen').classList.remove('hidden');
});

// Zeitbereich-Buttons aktivieren
const buttons = document.querySelectorAll('.time-btn');
buttons.forEach(btn => {
    btn.addEventListener('click', () => {
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const range = btn.getAttribute('data-range');
        updateChart(range);
    });
});

// Platzhalter für Diagramm
function updateChart(range) {
    console.log(`Zeitraum gewechselt: Letzte ${range} Tage`);
    // Hier laden wir später echte Daten rein
}
