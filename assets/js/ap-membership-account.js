(function(){
  document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.ap-membership-account');
    if (!container) return;

    const infoDiv    = container.querySelector('#ap-account-info');
    const actionsDiv = container.querySelector('#ap-account-actions');

    wp.apiFetch({
      path: '/artpulse/v1/member/account',
      headers: { 'X-WP-Nonce': ArtPulseApi.nonce }
    })
    .then(data => {
      // Render membership info
      infoDiv.innerHTML = `
        <p><strong>${ArtPulseApi.i18n.levelLabel}:</strong> ${data.level}</p>
        <p><strong>${ArtPulseApi.i18n.expiresLabel}:</strong> ${data.expires}</p>
      `;

      // Render purchase buttons for all other levels
      const levels = ['Basic','Pro','Org'];
      levels.forEach(lvl => {
        if ( lvl !== data.level ) {
          const wrapper = document.createElement('div');
          wrapper.innerHTML = data.purchase_links[lvl];
          actionsDiv.appendChild(wrapper);
        }
      });
    })
    .catch(err => {
      infoDiv.textContent = ArtPulseApi.i18n.errorFetching;
    });
  });
})();
