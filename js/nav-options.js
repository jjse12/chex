$(document).ready(function () {
    $('#btnImportarTarifaciones').on('click', showImportTarifacionesDialog);
    $('#btnLogout').on('click', showLogoutDialog);
    $('#btnVerCostoMercaderia').on('click', () => showCostoMercaderia(true));
});