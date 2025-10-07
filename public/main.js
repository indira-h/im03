// View switch
const welcome = document.getElementById('welcome-screen');
const tracker = document.getElementById('tracker-screen');
const startBtn = document.getElementById('start-btn');

if (startBtn) {
  startBtn.addEventListener('click', () => {
    welcome.classList.add('hidden');
    tracker.classList.remove('hidden');
    // initial chart
    renderChart(30);
  });
}

// Buttons
const rangeButtons = document.querySelectorAll('.time-btn');
rangeButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    rangeButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const r = Number(btn.dataset.range);
    renderChart(r);
  });
});

let chart; // Chart.js instance

async function getDataFromAPI(range) {
  const res = await fetch(`../api/data.php?range=${range}`);
  const data = await res.json();
  return data;
}

async function renderChart(range) {
  const ctx = document.getElementById('virusChart').getContext('2d');
  const values = await getDataFromAPI(range);
  const labels = values.map((_, i) => `Tag ${i + 1}`);
   // Highlight letzte Säule
  const colors = values.map((_,i)=> i===values.length-1 ? '#49e2f2' : '#2a1830');

  // Trendlinie (einfache lineare Regression)
  const n = values.length;
  const xs = values.map((_,i)=>i+1);
  const sum = a => a.reduce((s,x)=>s+x,0);
  const mean = a => sum(a)/a.length;
  const xbar = mean(xs), ybar = mean(values);
  const m = sum(xs.map((x,i)=>(x-xbar)*(values[i]-ybar))) / sum(xs.map(x => (x-xbar)**2 || 1));
  const b = ybar - m*xbar;
  const trend = xs.map(x => m*x + b);

  // Risiko ableiten
  const lastAvg = values.slice(-Math.min(7, values.length)).reduce((a,b)=>a+b,0) / Math.min(7, values.length);
  const riskEl = document.getElementById('risk-text');
  if (lastAvg > 20)      riskEl.textContent = 'HOHES RISIKO';
  else if (lastAvg > 10) riskEl.textContent = 'MÄSSIGES RISIKO';
  else                   riskEl.textContent = 'GERINGES RISIKO';

  if (chart) chart.destroy();
  chart = new Chart(ctx, {
    data: {
      labels,
      datasets: [
        { // Bars
          type: 'bar',
          label: 'Anzahl Viren im Abwasser',
          data: values,
          backgroundColor: colors,
          borderWidth: 0,
        },
        { // Trend
          type: 'line',
          label: 'Trend',
          data: trend,
          borderColor: '#ff2a2a',
          borderWidth: 2,
          pointRadius: 0,
          tension: 0,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display:false }, ticks: { maxRotation:0, autoSkip:true } },
        y: { grid: { color:'rgba(0,0,0,.08)' }, beginAtZero:true }
      }
    }
  });
}
