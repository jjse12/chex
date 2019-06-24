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
    `- Total a pagar: Q${costoTotal}`  + '<ENTER><ENTER>'
  message +=
    'Esta tarifa aplica únicamente pago en efectivo. No aplica para paquetes con artículos especiales, (Drones, teléfonos etc). <ENTER><ENTER>';




  /********* TEXTO DE DESPEDIDA *********/
  message +=
    'Por favor confírmanos si te los enviamos a ruta o pasas a recogerlos a oficina ' +
    '(Recuerda que si solicitas servicio a domicilio, debes agregar el costo del envío tu pago).<ENTER><ENTER>' +
    'Gracias por tu preferencia. <ENTER>' +
    'Chispudito Express';

  return message;
};