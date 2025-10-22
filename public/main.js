//  Scrollen beim Klick auf Scroll-Button
document.getElementById('scrollButton')?.addEventListener('click', function() {
  document.getElementById('info-section')?.scrollIntoView({
    behavior: 'smooth'
  });
});

// Daten von der API laden 
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

// Chart laden
let chart; 

async function renderChart(range = 30) {
  const ctx = document.getElementById('virusChart').getContext('2d');
  const daten = await getDataFromAPI(range);

  // Wenn keine Daten vorhanden sind --> abbrechen
  if (!daten || daten.length === 0) {
    console.error("Keine Daten empfangen!");
    return;
  }

  // Sortiere der Daten (ältestes Daten links, neuestes Daten rechts)
  daten.sort((a, b) => new Date(a.datum) - new Date(b.datum));

  // vorbereiten Daten für Chart.js
  const values = daten.map(d => d.viruswert);
  const labels = daten.map(d => {
    const date = new Date(d.datum);
    return date.toLocaleDateString('de-CH', {
      day: '2-digit',
      month: '2-digit',
      year: '2-digit'
    });
  });

  // Farben: letzte Säule blue
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

  // Risiko berechnen (basierend auf letztem Messwert) 
  const lastValue = values[values.length - 1]; // aktuellster Wert --> letzte Säule
  const riskEl = document.getElementById('risk-text');

  let riskText = "";
  let riskColor = "";

  // Farbgebung der unterschiedlichen Risiskostufen
  if (lastValue < 5e11) {
    riskText = "MOMENTAN: GERINGES RISIKO";
    riskColor = "#2ecc71"; 
  } else if (lastValue < 1.5e12) {
    riskText = "MOMENTAN: MÄSSIGES RISIKO";
    riskColor = "#f1c40f"; 
  } else {
    riskText = "MOMENTAN: HOHES RISIKO";
    riskColor = "#e74c3c"; 
  }

  // Farb- und Skalierungsanimation
  riskEl.style.transition = "color 0.8s ease, transform 0.4s ease";
  riskEl.textContent = riskText;
  riskEl.style.color = riskColor;
  riskEl.style.transform = "scale(1.15)";
  setTimeout(() => {
    riskEl.style.transform = "scale(1)";
  }, 400);

  // Alten Chart löschen, falls vorhanden
  if (chart) chart.destroy();

  // Neuen Chart zeichnen
  chart = new Chart(ctx, {
    data: {
      labels,
      datasets: [
        {
          type: 'bar',
          label: 'Viruslast im Abwasser (Medianwert)',
          data: values.map(v => Math.round(v)),
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
            label: ctx => `Viruslast: ${ctx.parsed.y.toLocaleString('de-CH')}`
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

// Welcome Screen Steuerung
const welcome = document.getElementById('welcome-screen');
const tracker = document.getElementById('tracker-screen');
const startBtn = document.getElementById('start-btn');

if (startBtn) {
  startBtn.addEventListener('click', () => {
    welcome.classList.add('hidden');
    tracker.classList.remove('hidden');
    renderChart(30); // Standardmässig 30 Tage laden

    // Scrollen direkt zum Tracker-Bereich
    setTimeout(() => {
      tracker.scrollIntoView({ behavior: 'smooth' });
    }, 300);
  });
}

// Buttons für Zeiträume 7/14/30 Tage
const rangeButtons = document.querySelectorAll('.time-btn');
rangeButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    rangeButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const range = Number(btn.dataset.range);
    renderChart(range);
  });
});


