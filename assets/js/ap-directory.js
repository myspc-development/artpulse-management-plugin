(function(){
    document.querySelectorAll('.ap-directory').forEach(initDirectory);
    function initDirectory(container){
        const type        = container.dataset.type;
        const results     = container.querySelector('.ap-directory-results');
        const limitInput  = container.querySelector('.ap-filter-limit');
        const applyBtn    = container.querySelector('.ap-filter-apply');
        const selectEl    = container.querySelector('.ap-filter-event-type');

        // Load taxonomy terms for events
        if(selectEl){
            wp.apiFetch({ path: '/wp/v2/artpulse_event_type' }).then(terms=>{
                selectEl.innerHTML = '<option value=\"\">All</option>';
                terms.forEach(t=>{
                    const o = document.createElement('option');
                    o.value = t.id; o.textContent = t.name;
                    selectEl.appendChild(o);
                });
            });
        }

        applyBtn.addEventListener('click', ()=>{
            const params = new URLSearchParams();
            params.append('type', type);
            params.append('limit', limitInput.value);
            if(selectEl && selectEl.value) params.append('event_type', selectEl.value);

            wp.apiFetch({ path: '/artpulse/v1/filter?' + params.toString() })
            .then(posts=>{
                results.innerHTML = '';
                posts.forEach(post => {
                    const div = document.createElement('div');
                    div.className = 'portfolio-item';
                    div.innerHTML = `
                        <a href="${post.link}">
                          <img src="${post.featured_media_url}" alt="${post.title}" />
                          <h3>${post.title}</h3>
                        </a>`;
                    results.appendChild(div);
                });
            });
        });

        applyBtn.click(); // initial load
    }
})();
