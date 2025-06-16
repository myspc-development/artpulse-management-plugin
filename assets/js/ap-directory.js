(function(){
  document.querySelectorAll('.ap-directory').forEach(initDirectory);

  function initDirectory(container){
    const type       = container.dataset.type;
    const results    = container.querySelector('.ap-directory-results');
    const limitInput = container.querySelector('.ap-filter-limit');
    const applyBtn   = container.querySelector('.ap-filter-apply');
    const selectEl   = container.querySelector('.ap-filter-event-type');

    // Load Event Type terms if needed
    if ( selectEl ) {
      wp.apiFetch({ path: '/wp/v2/artpulse_event_type' })
        .then(terms => {
          selectEl.innerHTML = '<option value="">All</option>';
          terms.forEach(t => {
            const o = document.createElement('option');
            o.value = t.id;
            o.textContent = t.name;
            selectEl.appendChild(o);
          });
        });
    }

    // Core data‐loading function
    function loadData() {
      const params = new URLSearchParams({
        type,
        limit: limitInput.value
      });
      if ( selectEl && selectEl.value ) {
        params.append('event_type', selectEl.value);
      }

      wp.apiFetch({ path: '/artpulse/v1/filter?' + params.toString() })
        .then(posts => {
          results.innerHTML = '';
          posts.forEach(post => {
            const div = document.createElement('div');
            div.className = 'portfolio-item';

            let html = `
              <a href="${post.link}">
                <img src="${post.featured_media_url}" alt="${post.title}" />
                <h3>${post.title}</h3>
            `;
            if ( post.date ) {
              html += `<p class="ap-meta-date">${post.date}</p>`;
            }
            if ( post.location ) {
              html += `<p class="ap-meta-location">${post.location}</p>`;
            }
            html += '</a>';

            div.innerHTML = html;
            results.appendChild(div);
          });

          // Dispatch custom events
          container.dispatchEvent(new CustomEvent('ap:loaded', {
            detail: { type, limit: limitInput.value }
          }));
        });
    }

    // Bind Apply button once
    applyBtn.addEventListener('click', () => {
      loadData();
      container.dispatchEvent(new CustomEvent('ap:filter_applied', {
        detail: {
          type,
          limit: limitInput.value,
          event_type: selectEl?.value || ''
        }
      }));
    });

    // Initial load
    loadData();
  }
})();
