$(document).ready(function () {
    $('#btnObtenerUsuariosNuevos').on('click', () => insertarNuevosClientes(true));
    $('#btnImportarTarifaciones').on('click', showImportTarifacionesDialog);
    $('#btnLogout').on('click', showLogoutDialog);
    $('#btnVerCostoMercaderia').on('click', () => showCostoMercaderia(true));
});