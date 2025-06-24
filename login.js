document.addEventListener('DOMContentLoaded', () => {
  const inputs = document.querySelectorAll('.input-field');
  inputs.forEach(input => {
    input.addEventListener('focus', () => {
      input.classList.add('border-blue-500');
    });
    input.addEventListener('blur', () => {
      input.classList.remove('border-blue-500');
    });
  });
});
