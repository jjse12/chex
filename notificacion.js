getNotificationMessage = (nombreCliente, paquetes, pesoTotal, costoTotal) => {

    let message =
    /********* TEXTO DE SALUDO *********/
        `Buen día ${nombreCliente}, de parte de Chispudito Express te informamos que los ` +
        'siguientes paquetes ya están disponibles en nuestra bodega de Guatemala:<ENTER><ENTER>';

    paquetes.map(paquete => {
    /********* TEXTO DE PAQUETES (ESTE TEXTO SE ESCRIBE POR CADA PAQUETE SELECCIONADO) *********/
        message +=
        `*  Tracking: ${paquete.tracking}`   + '<ENTER> ' +
        `   Peso: ${paquete.libras} lb.`     + '<ENTER>';
    });

    /********* TEXTO TOTALES *********/
    message += '<ENTER>' +
        `- Paquetes: ${paquetes.length}`   +  '<ENTER>'  +
        `- Peso total: ${pesoTotal} lb.`  +  '<ENTER>'  +
        `- Total a pagar: Q${costoTotal} (tarifa aplica en *efectivo*)`  + '<ENTER><ENTER>' ;

    /********* TEXTO DE DESPEDIDA *********/
    message +=
        'Por favor confírmanos si te los enviamos a ruta o pasas a recogerlos a oficina ' +
        '(recuerda que si solicitas servicio ruta, debes agregar al total el costo del envío).<ENTER><ENTER>' +
        'Gracias por preferirnos. Chispudito Express';

    return message;
};