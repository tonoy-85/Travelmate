// app.js
// Fetch suggestions from api.php and render results

document.addEventListener('DOMContentLoaded', () => {
  const planBtn = document.getElementById('planBtn');
  const goBtn = document.getElementById('goBtn');
  const quickSearch = document.getElementById('quickSearch');

  function doPlan() {
    const destination = document.getElementById('destination').value.trim() || "Cox's Bazar";
    const transportBudget = Number(document.getElementById('transportBudget').value || 0);
    const hotelBudget = Number(document.getElementById('hotelBudget').value || 0);

    const url = `api.php?destination=${encodeURIComponent(destination)}&transportBudget=${transportBudget}&hotelBudget=${hotelBudget}`;

    fetch(url)
      .then(r => r.json())
      .then(data => renderResults(data))
      .catch(err => {
        console.error(err);
        document.getElementById('results').innerHTML = `<div class="result-card">Error fetching suggestions. Make sure you are running this from a PHP-enabled server and api.php is accessible.</div>`;
      });
  }

  function renderResults(data) {
    const out = document.getElementById('results');
    out.innerHTML = ''; // clear

    // Summary
    const summ = document.createElement('div');
    summ.className = 'result-card';
    summ.innerHTML = `<h3>Summary</h3>
      <p><span class="badge">Destination</span> ${data.destination}</p>
      <p><span class="badge">Transport category</span> ${data.transportCategory} (${data.transportAdvice})</p>
      <p><span class="badge">Hotel category</span> ${data.hotelCategory}</p>`;
    out.appendChild(summ);

    // Route suggestions
    const routesCard = document.createElement('div');
    routesCard.className = 'result-card';
    routesCard.innerHTML = `<h3>Route suggestions</h3>`;
    data.routes.forEach(r => {
      const div = document.createElement('div');
      div.style.marginBottom = '8px';
      div.innerHTML = `<strong>${r.name}</strong> — <em>${r.traffic}</em><br/>Advice: ${r.advice}`;
      routesCard.appendChild(div);
    });
    out.appendChild(routesCard);

    // Transport suggestion
    const tCard = document.createElement('div');
    tCard.className = 'result-card';
    tCard.innerHTML = `<h3>Transport suggestion (based on your budget)</h3>
      <p><strong>${data.transportSuggestion.name}</strong> — ${data.transportSuggestion.notes}</p>
      <p>Estimated fare range: ${data.transportSuggestion.estimate}</p>`;
    out.appendChild(tCard);

    // Hotel suggestion
    const hCard = document.createElement('div');
    hCard.className = 'result-card';
    hCard.innerHTML = `<h3>Hotel suggestion (based on your budget)</h3>
      <p><strong>${data.hotelSuggestion.name}</strong> — ${data.hotelSuggestion.notes}</p>
      <p>Estimated price per night: ${data.hotelSuggestion.estimate}</p>`;
    out.appendChild(hCard);

    // Tips
    const tipsCard = document.createElement('div');
    tipsCard.className = 'result-card';
    tipsCard.innerHTML = `<h3>Quick Tips</h3>
      <ul>
        <li>${data.tips.join('</li><li>')}</li>
      </ul>`;
    out.appendChild(tipsCard);
  }

  planBtn.addEventListener('click', doPlan);
  goBtn.addEventListener('click', () => {
    document.getElementById('destination').value = quickSearch.value;
  });

  // Optionally auto run for default values
  // doPlan();
});
