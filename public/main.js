// ===== Sanftes Scrollen beim Klick auf Scroll-Button =====
document.getElementById('scrollButton')?.addEventListener('click', function() {
  document.getElementById('info-section')?.scrollIntoView({
    behavior: 'smooth'
  });
});

// ===== Daten von der API laden ===== 
async function getDataFromAPI(range = 30) {
  try {
    const response = await fetch(`../api/data.php?range=${range}`);
    const data = await response.json();
    console.log("Empfangene Daten:", data);
    return data;
  } catch (error) {
    console.error("Fehler beim Laden:", error);
    return [];
  }
}

// ===== Chart rendern =====
let chart; // Chart.js-Instanz

async function renderChart(range = 30) {
  const ctx = document.getElementById('virusChart').getContext('2d');
  const daten = await getDataFromAPI(range);

  // Wenn keine Daten vorhanden sind → abbrechen
  if (!daten || daten.length === 0) {
    console.error("Keine Daten empfangen!");
    return;
  }

  // Daten für Chart.js vorbereiten
  const values = daten.map(d => d.viruswert);
  const labels = daten.map(d => {
    // Datumsformat schöner machen (z. B. "2021-07-03" → "03.07.21")
    const date = new Date(d.datum);
    return date.toLocaleDateString('de-CH', {
      day: '2-digit',
      month: '2-digit',
      year: '2-digit'
    });
  });

  // Farben: letzte Säule cyan
  const colors = values.map((_, i) =>
    i === values.length - 1 ? '#49e2f2' : '#2a1830'
  );

  // Trendlinie berechnen
  const n = values.length;
  const xs = values.map((_, i) => i + 1);
  const sum = a => a.reduce((s, x) => s + x, 0);
  const mean = a => sum(a) / a.length;
  const xbar = mean(xs),
        ybar = mean(values);
  const m =
    sum(xs.map((x, i) => (x - xbar) * (values[i] - ybar))) /
    sum(xs.map(x => (x - xbar) ** 2 || 1));
  const b = ybar - m * xbar;
  const trend = xs.map(x => m * x + b);

  // Risiko berechnen
  const lastAvg =
    values.slice(-Math.min(7, n)).reduce((a, b) => a + b, 0) /
    Math.min(7, n);
  const riskEl = document.getElementById('risk-text');
  if (lastAvg > 60) riskEl.textContent = 'MOMENTAN: HOHES RISIKO';
  else if (lastAvg > 30) riskEl.textContent = 'MOMENTAN: MÄSSIGES RISIKO';
  else riskEl.textContent = 'MOMENTAN: GERINGES RISIKO';

  // Alten Chart zerstören, falls vorhanden
  if (chart) chart.destroy();

  // Neuen Chart zeichnen
  chart = new Chart(ctx, {
    data: {
      labels,
      datasets: [
        {
          type: 'bar',
          label: 'Viruslast im Abwasser (Medianwert)',
          data: values.map(v => Math.round(v)), // gerundet anzeigen
          backgroundColor: colors,
          borderWidth: 0
        },
        {
          type: 'line',
          label: 'Trend',
          data: trend,
          borderColor: '#ff2a2a',
          borderWidth: 2,
          pointRadius: 0,
          tension: 0
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => `Viruslast: ${ctx.parsed.y}`
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { maxRotation: 45, minRotation: 45 }
        },
        y: { grid: { color: 'rgba(0,0,0,.08)' }, beginAtZero: true }
      }
    }
  });
}

// ===== Welcome Screen Steuerung =====
const welcome = document.getElementById('welcome-screen');
const tracker = document.getElementById('tracker-screen');
const startBtn = document.getElementById('start-btn');

if (startBtn) {
  startBtn.addEventListener('click', () => {
    welcome.classList.add('hidden');
    tracker.classList.remove('hidden');
    renderChart(30); // Standardmäßig 30 Tage laden
  });
}

// ===== Buttons für Zeiträume (7/14/30 Tage) =====
const rangeButtons = document.querySelectorAll('.time-btn');

rangeButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    // Aktive Markierung umschalten
    rangeButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const range = Number(btn.dataset.range);
    renderChart(range);
  });
});
