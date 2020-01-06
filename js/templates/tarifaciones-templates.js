
const getImportTarifacionesDialogContent = () => {
    return `<p style='color: black'>Copia las columnas <b>No. de Guía</b>, <b>Precio Fob</b> y <b>Arancel</b> (en ese orden)
    en tu archivo Excel para los paquetes deseados, y pegalas en el siguiente campo de texto:</p>
    <div class='form-group' style="width: 70%; margin-left: 15%;">
        <textarea class="form-control" id="inputImportTarifaciones" style="height: 200px; text-align: center;"/>
    </div>`;
};

const getFailedImportedTarifacionBox = ({guideNumber, query, error}) => {
    return `
        <div class="alert alert-warning" role="alert">
            <span>Paquete con número de guía <b>${guideNumber}</b>:</span><br>
            <span><b>Consulta</b>: <span style="color: #769299">${query}</span></span><br>
            <span><b>Error</b>: <span style="color: indianred">${error}</span></span>
        </div>
        <br>
    `;
};

const getFailedImportedTarifacionesDialogContent = (message, failedData) => {
    let errorBoxes = '';
    failedData.forEach(data => {
        errorBoxes += getFailedImportedTarifacionBox(data);
    });

    return `<div style="overflow-y: auto;"><p style='color: black'>${message}</p>${errorBoxes}</div>`;
};