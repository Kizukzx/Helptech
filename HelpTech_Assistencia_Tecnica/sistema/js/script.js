function confirmarExclusao(mensagem = 'Deseja realmente excluir este registro?') { return window.confirm(mensagem); }
document.addEventListener('DOMContentLoaded', () => { document.querySelectorAll('[data-confirm]').forEach(el => el.addEventListener('click', e => { if (!confirm(el.dataset.confirm || 'Confirmar ação?')) e.preventDefault(); })); });
