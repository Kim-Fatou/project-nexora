document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('signupForm');

  if (!form) return;

  form.addEventListener('submit', (event) => {
    event.preventDefault();

    const name = document.getElementById('name')?.value.trim();
    const email = document.getElementById('email')?.value.trim();
    const password = document.getElementById('password')?.value.trim();

    if (!name || !email || !password) {
      alert('Merci de compléter tous les champs pour continuer.');
      return;
    }

    sessionStorage.setItem('nexora_user', JSON.stringify({ name, email }));
    window.location.href = '/matching';
  });
});